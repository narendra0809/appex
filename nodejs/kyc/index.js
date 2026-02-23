require('dotenv').config();

const axios = require('axios');
const crypto = require('crypto');
const fs = require('fs');

/* =========================================
   USER
========================================= */
const USER = {
    pan: 'BLSPJ0470P',
    dob: '21-5-1997'
};

/* =========================================
   ENV
========================================= */
const CREDENTIALS = {
    apiKey: process.env.CVL_API_KEY,
    aesKey: process.env.CVL_AES_KEY,
    username: process.env.CVL_USER_NAME,
    poscode: process.env.CVL_POS_CODE,
    password: process.env.CVL_PASSWORD
};

const BASE = process.env.CVL_API_BASE_URL;

const URLS = {
    token: `${BASE}/GetToken`,
    panFetch: `${BASE}/SolicitPANDetailsFetchALLKRA`,
    solicitImage: `${BASE}/SolicitImage`
};

/* =========================================
   AES HELPERS
========================================= */
function getKey() {
    const key = Buffer.from(CREDENTIALS.aesKey, 'base64');

    if (![16, 24, 32].includes(key.length)) {
        throw new Error(
            `Invalid AES key length: ${key.length} bytes`
        );
    }

    return key;
}

function getAlgo(key) {
    if (key.length === 16) return 'aes-128-cbc';
    if (key.length === 24) return 'aes-192-cbc';
    if (key.length === 32) return 'aes-256-cbc';
}

function encryptAES(data) {
    const key = getKey();
    const iv = crypto.randomBytes(16);

    const cipher = crypto.createCipheriv(
        getAlgo(key),
        key,
        iv
    );

    let encrypted = cipher.update(
        JSON.stringify(data),
        'utf8',
        'base64'
    );

    encrypted += cipher.final('base64');

    return `${iv.toString('base64')}:${encrypted}`;
}

function decryptAES(enc) {
    const [ivStr, dataStr] = enc.split(':');

    const key = getKey();
    const iv = Buffer.from(ivStr, 'base64');

    const decipher = crypto.createDecipheriv(
        getAlgo(key),
        key,
        iv
    );

    let decrypted = decipher.update(
        dataStr,
        'base64',
        'utf8'
    );

    decrypted += decipher.final('utf8');

    return decrypted;
}

function decryptBinary(buffer, ivBase64) {
    const key = getKey();
    const iv = Buffer.from(ivBase64, 'base64');

    const decipher = crypto.createDecipheriv(
        getAlgo(key),
        key,
        iv
    );

    let decrypted = decipher.update(buffer);
    decrypted = Buffer.concat([
        decrypted,
        decipher.final()
    ]);

    return decrypted;
}


/* =========================================
   MAIN FLOW
========================================= */
(async () => {

    try {

        console.log("🔐 Getting Token...");

        const tokenRes = await axios.post(
            URLS.token,
            `"${encryptAES({
                username: CREDENTIALS.username,
                poscode: CREDENTIALS.poscode,
                password: CREDENTIALS.password
            })}"`,
            {
                headers: {
                    'Content-Type': 'application/json',
                    api_key: CREDENTIALS.apiKey,
                    'user-agent': 'CustomUsrAgnt'
                }
            }
        );

        const tokenData = JSON.parse(
            decryptAES(tokenRes.data.replace(/"/g, ''))
        );

        if (tokenData.success !== "1")
            throw new Error(tokenData.error_message);

        const token = tokenData.token;

        console.log("✅ Token Received\n");


        /* ==========================
           STEP 1 – FETCH KYC
        ========================== */

        console.log("📄 Fetching KYC...");

        const panRes = await axios.post(
            URLS.panFetch,
            `"${encryptAES({
                APP_REQ_ROOT: {
                    APP_PAN_INQ: {
                        APP_PAN_NO: USER.pan,
                        APP_DOB_INCORP: USER.dob,
                        APP_POS_CODE: CREDENTIALS.poscode,
                        APP_RTA_CODE: CREDENTIALS.poscode,
                        APP_KRA_CODE: "CVLKRA",
                        FETCH_TYPE: "I"
                    }
                }
            })}"`,
            {
                headers: {
                    'Content-Type': 'application/json',
                    Token: token,
                    'user-agent': 'CustomUsrAgnt'
                }
            }
        );

        let encryptedPan;

if (panRes.data?.resdtls) {
    encryptedPan = panRes.data.resdtls;
} else if (typeof panRes.data === 'string') {
    encryptedPan = panRes.data.replace(/"/g, '');
} else {
    throw new Error("Invalid PAN response structure");
}

const decryptedPan = decryptAES(encryptedPan);
let panData = JSON.parse(decryptedPan);

if (typeof panData.resdtls === 'string') {
    panData = JSON.parse(panData.resdtls);
}

fs.writeFileSync(
    `${USER.pan}_KYC_DATA.json`,
    JSON.stringify(panData, null, 2)
);

// 🔥 Correct Path
const refNo = panData?.KYC_DATA?.APP_INTERNAL_REF;

if (!refNo)
    throw new Error("APP_INTERNAL_REF not found in KYC response");

console.log("🔁 REF_NO Found:", refNo);



        /* ==========================
           STEP 2 – SOLICIT IMAGE
        ========================== */

        console.log("📥 Downloading Documents...");

        const imageRes = await axios.post(
            URLS.solicitImage,
            `"${encryptAES({
                PAN_NO: USER.pan,
                POS_CODE: CREDENTIALS.poscode,
                RTA_CODE: CREDENTIALS.poscode,
                KRA_CODE: "CVLKRA",
                REF_NO: refNo
            })}"`,
            {
                headers: {
                    'Content-Type': 'application/json',
                    Token: token,
                    'user-agent': 'CustomUsrAgnt'
                },
                responseType: 'arraybuffer'
            }
        );

        const ivHeader = imageRes.headers['x-encryption-iv-base64url'];

        if (!ivHeader)
            throw new Error("IV header missing");

        const zipBuffer = decryptBinary(imageRes.data, ivHeader);

        fs.writeFileSync(
            `${USER.pan}_KYC_DOCUMENTS.zip`,
            zipBuffer
        );

        console.log("✅ ZIP Downloaded Successfully");

    } catch (err) {
        console.error("❌ ERROR:", err.message);
    }

})();
