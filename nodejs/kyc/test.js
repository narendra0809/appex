const axios = require('axios');
const crypto = require('crypto');
const fs = require('fs');

// ==========================================
// 1. CONFIGURATION (Apne Credentials Yahan Dalein)
// ==========================================
const CONFIG = {
    API_KEY: '0ec5895babf84ea8841d29ef69f2f1cd',
    AES_KEY: '3qygPsdo4w9bv24H3bQmt4asOpI0dwf6', // Base64 format mein
    USERNAME: 'WEBKASHISHJ',
    POSCODE: 'KASHISHJ',
    PASSWORD: 'Cvlkra@1234',
    // Endpoints
    URL_TOKEN: 'https://api.kracvl.com/int/api/GetToken',
    URL_SOLICIT: 'https://api.kracvl.com/int/api/SolicitImage'
};

// ==========================================
// 2. ENCRYPTION UTILITIES
// ==========================================

/**
 * CVL Payload Encryption: IV and Encrypted Text separated by colon
 */
function encryptPayload(plainText, aesKeyBase64) {
    const key = Buffer.from(aesKeyBase64, 'base64');
    const iv = crypto.randomBytes(16);
    const cipher = crypto.createCipheriv('aes-256-cbc', key, iv);
    
    let encrypted = cipher.update(plainText, 'utf8', 'base64');
    encrypted += cipher.final('base64');
    
    // URL safe conversion (Optional but recommended for CVL)
    const ivBase64 = iv.toString('base64');
    return `${ivBase64}:${encrypted}`;
}

/**
 * Decrypt JSON response
 */
function decryptResponse(encryptedPayload, aesKeyBase64) {
    try {
        const parts = encryptedPayload.replace(/"/g, '').split(':');
        if (parts.length !== 2) return null;

        const iv = Buffer.from(parts[0], 'base64');
        const encryptedText = parts[1];
        const key = Buffer.from(aesKeyBase64, 'base64');

        const decipher = crypto.createDecipheriv('aes-256-cbc', key, iv);
        let decrypted = decipher.update(encryptedText, 'base64', 'utf8');
        decrypted += decipher.final('utf8');
        return JSON.parse(decrypted);
    } catch (error) {
        console.error("Decryption Error:", error.message);
        return null;
    }
}

// ==========================================
// 3. API CORE FUNCTIONS
// ==========================================

/**
 * STEP 1: Generate JWT Token
 */
async function getAuthToken() {
    const rawData = JSON.stringify({
        username: CONFIG.USERNAME,
        poscode: CONFIG.POSCODE,
        password: CONFIG.PASSWORD
    });

    const encryptedBody = encryptPayload(rawData, CONFIG.AES_KEY);

    try {
        const response = await axios.post(CONFIG.URL_TOKEN, `"${encryptedBody}"`, {
            headers: {
                'api_key': CONFIG.API_KEY,
                'Content-Type': 'application/json',
                'user-agent': 'CustomUsrAgnt'
            }
        });

        const decrypted = decryptResponse(response.data, CONFIG.AES_KEY);
        if (decrypted && decrypted.success === "1") {
            console.log("Token Generated Successfully. Valid till:", decrypted.validity);
            return decrypted.token;
        } else {
            console.error("Token Failed:", decrypted ? decrypted.error_message : "Unknown error");
            return null;
        }
    } catch (error) {
        console.error("Token Request Error:", error.message);
    }
}

/**
 * STEP 2: Download KYC ZIP File
 */
async function downloadKYCDocument(token, panNo, refNo) {
    const rawData = JSON.stringify({
        "PAN_NO": panNo,
        "POS_CODE": CONFIG.POSCODE,
        "RTA_CODE": CONFIG.POSCODE, // Passcode for intermediary
        "KRA_CODE": "CVLKRA",
        "REF_NO": refNo
    });

    const encryptedBody = encryptPayload(rawData, CONFIG.AES_KEY);

    try {
        const response = await axios.post(CONFIG.URL_SOLICIT, `"${encryptedBody}"`, {
            headers: {
                'Token': token,
                'Content-Type': 'application/json',
                'user-agent': 'CustomUsrAgnt'
            },
            responseType: 'arraybuffer' // Binary file download ke liye zaroori
        });

        // Check if response is JSON (Error) or Stream (Success)
        const contentType = response.headers['content-type'];
        
        if (contentType.includes('application/json')) {
            const errorRaw = Buffer.from(response.data).toString();
            const errorDecrypted = decryptResponse(errorRaw, CONFIG.AES_KEY);
            console.log("Download Error:", errorDecrypted.error_message);
        } else {
            // ZIP File Save karein
            const filename = `${panNo}_KYC.zip`;
            fs.writeFileSync(filename, response.data);
            console.log(`Success! File saved as: ${filename}`);
        }
    } catch (error) {
        console.error("Download Request Error:", error.message);
    }
}

// ==========================================
// 4. EXECUTION
// ==========================================
async function run() {
    const token = await getAuthToken();
    if (token) {
        // PAN aur REF_NO badlein
        await downloadKYCDocument(token, "ABCDE1234F", "REF_001");
    }
}

run();