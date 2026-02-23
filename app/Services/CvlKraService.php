<?php

namespace App\Services;

use App\Helpers\CvlCrypto;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * CVL KRA KYC Service - Production Ready
 * Matches Node.js index.js implementation exactly
 */
class CvlKraService
{
    private Client $httpClient;
    private CvlCrypto $crypto;
    private string $apiKey;
    private string $aesKey;
    private string $aesKey256; // Raw UTF-8 key for AES-256 (SolicitImage)
    private string $username;
    private string $posCode;
    private string $password;
    private string $apiBaseUrl;

    // KYC Status codes (matching Node.js constants)
    private const IMAGE_ELIGIBLE_STATUS = ['01', '11', '12'];
    private const NON_IMAGE_STATUS = ['02', '04', '05', '07'];

    public function __construct()
    {
        // Force IPv4 - CVL API only supports IPv4 whitelisting
        // Use numeric values for constants (in case they're not defined on some PHP installations)
        // CURLOPT_IPRESOLVE = 113, CURL_IPRESOLVE_V4 = 1
        $curlOptions = [];
        if (defined('CURLOPT_IPRESOLVE') && defined('CURL_IPRESOLVE_V4')) {
            $curlOptions[CURLOPT_IPRESOLVE] = CURL_IPRESOLVE_V4;
        } else {
            // Use numeric values as fallback
            $curlOptions[113] = 1;  // CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4
        }

        $this->httpClient = new Client([
            'timeout' => 30,
            'connect_timeout' => 10,
            'http_errors' => false,
            'force_ip_resolve' => 'v4',  // Force IPv4 only (Guzzle option)
            'curl' => $curlOptions,
        ]);

        $this->crypto = new CvlCrypto();
        
        // Log the IPv4 force status
        Log::info('CVL HTTP Client Config', [
            'force_ip_resolve' => 'v4',
            'curl_options' => $curlOptions,
            'php_version' => PHP_VERSION,
        ]);
        
        // Credentials from .env (matching Node.js CREDENTIALS)
        $this->apiKey = config('services.cvl_kra.api_key', env('CVL_API_KEY', ''));
        $this->aesKey = config('services.cvl_kra.aes_key', env('CVL_AES_KEY', ''));
        // AES-256 key for SolicitImage - use raw key (32 bytes) or fall back to AES key
        $this->aesKey256 = config('services.cvl_kra.aes_key_256', env('CVL_AES_KEY', $this->aesKey));
        $this->username = config('services.cvl_kra.username', env('CVL_USER_NAME', ''));
        $this->posCode = config('services.cvl_kra.pos_code', env('CVL_POS_CODE', ''));
        $this->password = config('services.cvl_kra.password', env('CVL_PASSWORD', ''));
        $this->apiBaseUrl = config('services.cvl_kra.base_url', env('CVL_API_BASE_URL', ''));
    }

    /**
     * Get environment information
     */
    public function getEnvironmentInfo(): array
    {
        $environment = config('services.cvl_kra.environment', 'LIVE');
        return [
            'environment' => $environment,
            'is_production' => strtolower($environment) === 'production',
            'base_url' => $this->apiBaseUrl,
            'configured' => !empty($this->apiKey) && !empty($this->aesKey),
        ];
    }

    /**
     * Get outbound IP address (using IPv4)
     */
    private function getOutboundIp(): string
    {
        try {
            // Use ifconfig.me to detect outbound IP (force IPv4)
            $client = new Client([
                'timeout' => 5,
                'force_ip_resolve' => 'v4',
                'curl' => [113 => 1],  // CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4
            ]);
            $response = $client->get('https://api4.my-ip.io/ip');
            return trim($response->getBody()->getContents());
        } catch (\Exception $e) {
            try {
                // Fallback to IPv4-specific service
                $response = $client->get('https://ipv4.icanhazip.com');
                return trim($response->getBody()->getContents());
            } catch (\Exception $e2) {
                return 'unknown';
            }
        }
    }

    /**
     * Get PAN status only (simplified)
     */
    public function getPanStatus(string $pan, string $dob): array
    {
        $result = $this->getKycDetails($pan, $dob);
        
        if ($result['success']) {
            return [
                'success' => true,
                'status' => $result['kyc_status'] ?? 'UNKNOWN',
                'pan' => strtoupper($pan),
            ];
        }
        
        return $result;
    }

    /**
     * Format DOB to DD-MM-YYYY format
     */
    private function formatDob(string $dob): string
    {
        // Handle different input formats
        $dob = trim($dob);
        
        // If already in DD-MM-YYYY format, return as is
        if (preg_match('/^\d{2}-\d{2}-\d{4}$/', $dob)) {
            return $dob;
        }

        // D-M-YYYY or DD-M-YYYY (e.g. 21-5-1997) - normalize to DD-MM-YYYY
        if (preg_match('/^(\d{1,2})-(\d{1,2})-(\d{4})$/', $dob, $matches)) {
            return str_pad($matches[1], 2, '0', STR_PAD_LEFT) . '-' . str_pad($matches[2], 2, '0', STR_PAD_LEFT) . '-' . $matches[3];
        }
        
        // If in DD/MM/YYYY format, convert to DD-MM-YYYY
        if (preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $dob, $matches)) {
            return $matches[1] . '-' . $matches[2] . '-' . $matches[3];
        }
        
        // If in YYYY-MM-DD format, convert to DD-MM-YYYY
        if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $dob, $matches)) {
            return $matches[3] . '-' . $matches[2] . '-' . $matches[1];
        }
        
        // Return as is if no pattern matches
        return $dob;
    }

    /**
     * Get authentication token (matches Node.js getToken)
     */
    public function getToken(): array
    {
        try {
            $endpoint = $this->apiBaseUrl . '/GetToken';
            
            $payload = [
                'username' => $this->username,
                'poscode' => $this->posCode,
                'password' => $this->password,
            ];

            // Check outbound IP before making request
            $outboundIp = $this->getOutboundIp();
            Log::info('CVL GetToken Request', [
                'endpoint' => $endpoint,
                'username' => $this->username,
                'outbound_ip' => $outboundIp,
                'is_ipv4' => filter_var($outboundIp, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) ? 'YES' : 'NO',
            ]);

            $response = $this->httpClient->post($endpoint, [
                // Wrap in quotes like Node.js: `"${encryptAES(...)}"`
                'body' => '"' . $this->crypto->encrypt(json_encode($payload)) . '"',
                'headers' => [
                    'Content-Type' => 'application/json',
                    'api_key' => $this->apiKey,
                    'user-agent' => 'CustomUsrAgnt',
                ],
            ]);

            $responseBody = $response->getBody()->getContents();
            
            Log::info('CVL Token Raw Response', ['body' => substr($responseBody, 0, 200)]);
            
            // Check for Cloudflare/Network errors (error code: 1009, 1020, etc.)
            if (preg_match('/error code:\s*\d+/i', $responseBody)) {
                Log::error('CVL API Network Error', [
                    'body' => $responseBody,
                    'hint' => 'This is a Cloudflare/network error. The server IP might be blocked by CVL.'
                ]);
                return [
                    'success' => false, 
                    'error' => 'Network access denied (Error 1009). Your server IP is blocked by CVL API. Please contact CVL support to whitelist your server IP.',
                    'error_code' => 'NETWORK_BLOCKED'
                ];
            }
            
            // CVL API returns encrypted data wrapped in quotes: "IV:ENCRYPTED_DATA"
            // Remove outer quotes and decrypt
            $responseBody = trim($responseBody, '"');
            
            // Decrypt the response
            $decrypted = $this->crypto->decryptAndParse($responseBody);
            
            if ($decrypted === null) {
                Log::error('CVL Token Decrypt Failed', ['body' => substr($responseBody, 0, 100)]);
                return ['success' => false, 'error' => 'Failed to decrypt token response'];
            }
            
            Log::info('CVL Token Decrypted', ['data' => json_encode($decrypted)]);
            
            // Check success
            if (isset($decrypted['success']) && $decrypted['success'] === '1') {
                $token = $decrypted['token'] ?? null;
                if ($token) {
                    Log::info('CVL Token obtained', ['token_preview' => substr($token, 0, 20) . '...']);
                    return ['success' => true, 'token' => $token];
                }
            }
            
            $errorMsg = $decrypted['error_message'] ?? 'Unknown error';
            Log::error('CVL Token Error', ['error' => $errorMsg]);
            return ['success' => false, 'error' => $errorMsg];

        } catch (GuzzleException $e) {
            Log::error('CVL Token Guzzle Error', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => 'Network error: ' . $e->getMessage()];
        } catch (Exception $e) {
            Log::error('CVL Token Exception', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Get KYC Details (matches Node.js main function structure)
     */
    public function getKycDetails(string $pan, string $dob): array
    {
        $tokenResult = $this->getToken();
        if (!$tokenResult['success']) {
            return $tokenResult;
        }

        $token = $tokenResult['token'];
        $endpoint = $this->apiBaseUrl . '/SolicitPANDetailsFetchALLKRA';
        
        // Build payload matching Node.js structure
        $payload = [
            'APP_REQ_ROOT' => [
                'APP_PAN_INQ' => [
                    'APP_PAN_NO' => strtoupper($pan),
                    'APP_DOB_INCORP' => $dob,
                    'APP_POS_CODE' => $this->posCode,
                    'APP_RTA_CODE' => $this->posCode,
                    'APP_KRA_CODE' => 'CVLKRA',
                    'FETCH_TYPE' => 'I',
                ]
            ]
        ];

        Log::info('CVL KYC Request', [
            'endpoint' => $endpoint,
            'pan' => $pan,
        ]);

        try {
            $response = $this->httpClient->post($endpoint, [
                // Wrap in quotes like Node.js: `"${encryptAES(...)}"`
                'body' => '"' . $this->crypto->encrypt(json_encode($payload)) . '"',
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Token' => $token,
                    'user-agent' => 'CustomUsrAgnt',
                ],
            ]);

            $responseBody = $response->getBody()->getContents();
            $responseStatus = $response->getStatusCode();

            if ($responseStatus !== 200) {
                Log::error('CVL API Error', ['status' => $responseStatus, 'body' => substr($responseBody, 0, 200)]);
                return ['success' => false, 'error' => 'API returned status: ' . $responseStatus];
            }

            // Decrypt response
            $decrypted = $this->crypto->decryptAndParse($responseBody);
            
            if ($decrypted === null) {
                // Try JSON decode
                $jsonDecoded = json_decode($responseBody, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $decrypted = $jsonDecoded;
                } else {
                    $decrypted = ['raw_response' => $responseBody];
                }
            }

            // Handle nested resdtls if present (matching Node.js)
            if (isset($decrypted['resdtls']) && is_string($decrypted['resdtls'])) {
                $nestedDecrypted = $this->crypto->decryptAndParse($decrypted['resdtls']);
                if ($nestedDecrypted !== null) {
                    $decrypted = array_merge($decrypted, $nestedDecrypted);
                }
            }

            // Save Data JSON (matching Node.js)
            Storage::put('kyc_results/' . $pan . '_Data.json', json_encode($decrypted, JSON_PRETTY_PRINT));
            Log::info('CVL Data JSON Saved', ['path' => $pan . '_Data.json']);

            // Handle documents only if decrypted is an array
            $documents = [];
            if (is_array($decrypted)) {
                $documents = $this->handleDocuments($decrypted, strtoupper($pan));
            }

            return [
                'success' => true,
                'data' => $decrypted,
                'kyc_status' => is_array($decrypted) ? $this->getKycStatus($decrypted) : 'UNKNOWN',
                'documents' => $documents,
                'raw_response' => $responseBody,
            ];

        } catch (GuzzleException $e) {
            Log::error('CVL KYC Guzzle Error', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => 'Network error: ' . $e->getMessage()];
        } catch (Exception $e) {
            Log::error('CVL KYC Exception', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Extract KYC status (matches Node.js getKycStatus)
     */
    private function getKycStatus(array $data): string
    {
        return $data['KYC_DATA']['APP_STATUS']
            ?? $data['APP_STATUS']
            ?? $data['APP_PAN_INQ_RES']['APP_STATUS']
            ?? 'UNKNOWN';
    }

    /**
     * Handle documents (matches Node.js handleDocuments)
     */
    private function handleDocuments(array $data, string $pan): array
    {
        $status = $this->getKycStatus($data);
        Log::info('CVL KYC Status', ['pan' => $pan, 'status' => $status]);

        if (in_array($status, self::IMAGE_ELIGIBLE_STATUS)) {
            Log::info('CVL Extracting documents...');
            $documents = $this->recursiveImageFinder($data, $pan);
            return [
                'success' => true,
                'status' => $status,
                'documents' => $documents,
                'count' => count($documents),
            ];
        } elseif (in_array($status, self::NON_IMAGE_STATUS)) {
            Log::info('CVL Documents not allowed for this status');
            return [
                'success' => false,
                'status' => $status,
                'message' => 'Documents not allowed for this KYC status',
            ];
        }

        return [
            'success' => false,
            'status' => $status,
            'message' => 'Unknown status – manual review',
        ];
    }

    /**
     * Recursively find and save base64 documents (matches Node.js recursiveImageFinder)
     */
    private function recursiveImageFinder(array $obj, string $pan): array
    {
        $documents = [];
        $counter = 0;

        foreach ($obj as $key => $value) {
            if (is_string($value) && strlen($value) > 1000) {
                $counter++;
                $this->detectAndSaveFile($value, $pan, $counter);
                
                $ext = $this->detectExtension($value);
                $documents[] = [
                    'filename' => "{$pan}_Document_{$counter}.{$ext}",
                    'path' => 'kyc_documents/' . $pan . '_Document_' . $counter . '.' . $ext,
                    'extension' => $ext,
                ];
            } elseif (is_array($value) && $value !== null) {
                $nestedDocs = $this->recursiveImageFinder($value, $pan);
                $documents = array_merge($documents, $nestedDocs);
            }
        }

        return $documents;
    }

    /**
     * Detect file extension and save (matches Node.js detectAndSaveFile)
     */
    private function detectAndSaveFile(string $base64, string $pan, int $idx): bool
    {
        $ext = $this->detectExtension($base64);
        $filename = "{$pan}_Document_{$idx}.{$ext}";
        
        return $this->saveBase64File($base64, $filename);
    }

    /**
     * Detect file extension from base64 signature
     */
    private function detectExtension(string $base64): string
    {
        if (str_starts_with($base64, 'JVBERi0')) return 'pdf';
        if (str_starts_with($base64, '/9j/')) return 'jpg';
        if (str_starts_with($base64, 'iVBORw')) return 'png';
        if (str_starts_with($base64, 'UEsDB')) return 'zip';
        if (str_starts_with($base64, 'SUkqAA') || str_starts_with($base64, 'TU0AKg')) return 'tiff';
        return 'bin';
    }

    /**
     * Save base64 encoded file
     */
    private function saveBase64File(string $base64, string $filename): bool
    {
        try {
            $buffer = base64_decode($base64);
            if ($buffer === false) {
                Log::warning('CVL Failed to decode base64', ['filename' => $filename]);
                return false;
            }

            $path = 'kyc_documents/' . $filename;
            Storage::put($path, $buffer);
            Log::info('CVL Document saved', ['filename' => $filename]);
            return true;

        } catch (Exception $e) {
            Log::error('CVL Error saving document', ['filename' => $filename, 'error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Complete KYC verification (matches Node.js main flow)
     * 1. Get Token
     * 2. Call SolicitPANDetailsFetchALLKRA to get KYC data + REF_NO
     * 3. Call SolicitImage with REF_NO to get ZIP documents
     * 4. Save ZIP to storage and return data
     */
    public function verifyKyc(string $pan, string $dob): array
    {
        Log::info('CVL KYC Verification Started', [
            'pan' => $pan, 
            'env' => config('services.cvl_kra.environment')
        ]);

        try {
            // Step 1: Get Token
            $tokenResult = $this->getToken();
            if (!$tokenResult['success']) {
                return $tokenResult;
            }
            $token = $tokenResult['token'];

            Log::info('CVL Token obtained');

            // Step 2: Fetch KYC Details
            $kycData = $this->fetchKycData($token, $pan, $dob);
            if (!$kycData['success']) {
                return $kycData;
            }

            $kycResponse = $kycData['data'];
            
            // Extract REF_NO from response - check multiple paths
            $refNo = $kycResponse['KYC_DATA']['APP_INTERNAL_REF'] 
                ?? $kycResponse['APP_INTERNAL_REF'] 
                ?? $kycResponse['APP_PAN_INQ_RES']['APP_INTERNAL_REF']
                ?? $kycResponse['resdtls']['KYC_DATA']['APP_INTERNAL_REF']
                ?? null;

            if (!$refNo) {
                Log::error('CVL REF_NO not found in response', ['data' => $kycResponse]);
                return [
                    'success' => false, 
                    'error' => 'APP_INTERNAL_REF not found in KYC response'
                ];
            }

            Log::info('CVL REF_NO Found', ['ref_no' => $refNo]);

            // Step 3: Download ZIP Documents
            $zipResult = $this->downloadDocuments($token, strtoupper($pan), $refNo);
            
            // Step 4: Extract main data from KYC response
            $extractedData = $this->extractKycData($kycResponse);

            $result = [
                'success' => true,
                'pan' => strtoupper($pan),
                'dob' => $dob,
                'kyc_data' => $kycResponse,
                'extracted_data' => $extractedData,
                'ref_no' => $refNo,
                'kyc_status' => $extractedData['status'] ?? 'UNKNOWN',
            ];

            // Add ZIP info if downloaded successfully
            if ($zipResult['success']) {
                $result['zip_filename'] = $zipResult['filename'];
                $result['zip_path'] = $zipResult['path'];
                $result['zip_size'] = $zipResult['size'];
            } else {
                $result['zip_error'] = $zipResult['error'];
            }

            Log::info('CVL KYC Verification Completed', [
                'pan' => $pan,
                'status' => $result['kyc_status'],
                'zip_downloaded' => $zipResult['success'],
            ]);

            return $result;

        } catch (Exception $e) {
            Log::error('CVL KYC Exception', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Fetch KYC data (Step 2 of the flow)
     */
    private function fetchKycData(string $token, string $pan, string $dob): array
    {
        $endpoint = $this->apiBaseUrl . '/SolicitPANDetailsFetchALLKRA';
        
        // Convert DOB to DD-MM-YYYY format if needed
        $dobFormatted = $this->formatDob($dob);
        
        $payload = [
            'APP_REQ_ROOT' => [
                'APP_PAN_INQ' => [
                    'APP_PAN_NO' => strtoupper($pan),
                    'APP_DOB_INCORP' => $dobFormatted,
                    'APP_POS_CODE' => $this->posCode,
                    'APP_RTA_CODE' => $this->posCode,
                    'APP_KRA_CODE' => 'CVLKRA',
                    'FETCH_TYPE' => 'I',
                ]
            ]
        ];

        Log::info('CVL FetchKycData Request', [
            'endpoint' => $endpoint,
            'pan' => $pan,
            'dob' => $dobFormatted,
        ]);

        try {
            // Encrypt payload - match Node.js exactly
            $encryptedPayload = $this->crypto->encrypt(json_encode($payload));
            
            $response = $this->httpClient->post($endpoint, [
                'body' => '"' . $encryptedPayload . '"',
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Token' => $token,
                    'user-agent' => 'CustomUsrAgnt',
                ],
            ]);

            $responseBody = $response->getBody()->getContents();
            $responseStatus = $response->getStatusCode();

            // Check for Cloudflare/Network errors (error code: 1009, 1020, etc.)
            if (preg_match('/error code:\s*\d+/i', $responseBody)) {
                Log::error('CVL API Network Error', [
                    'body' => $responseBody,
                    'hint' => 'This is a Cloudflare/network error. The server IP might be blocked by CVL.'
                ]);
                return [
                    'success' => false, 
                    'error' => 'Network access denied (Error 1009). Your server IP is blocked by CVL API.',
                    'error_code' => 'NETWORK_BLOCKED'
                ];
            }

            if ($responseStatus !== 200) {
                Log::error('CVL FetchKycData API Error', [
                    'status' => $responseStatus, 
                    'body' => substr($responseBody, 0, 200)
                ]);
                return ['success' => false, 'error' => 'API returned status: ' . $responseStatus];
            }

            // Get encrypted data from response - match Node.js exactly
            $encryptedData = null;
            $responseData = json_decode($responseBody, true);
            
            // Node.js handles both: resdtls field OR string response
            if (is_array($responseData) && isset($responseData['resdtls'])) {
                $encryptedData = $responseData['resdtls'];
                Log::info('CVL FetchKycData: Using resdtls field');
            } elseif (is_string($responseData)) {
                // Response is a string, remove quotes like Node.js
                $encryptedData = str_replace('"', '', $responseData);
                Log::info('CVL FetchKycData: Using string response');
            } elseif (is_string($responseBody) && strpos($responseBody, ':') !== false) {
                // Raw response body is encrypted string
                $encryptedData = str_replace('"', '', $responseBody);
            }

            if (!$encryptedData) {
                Log::error('CVL FetchKycData: No encrypted data found', ['response' => substr($responseBody, 0, 200)]);
                return ['success' => false, 'error' => 'No encrypted data in response'];
            }
            
            // Save raw encrypted data for debugging
            Storage::put('kyc_results/' . $pan . '_Encrypted.dat', $encryptedData);

            // Decrypt the response
            $decrypted = $this->crypto->decryptAndParse($encryptedData);
            
            if ($decrypted === null) {
                // Try raw JSON parse
                $decrypted = json_decode($encryptedData, true);
                if (!$decrypted) {
                    return ['success' => false, 'error' => 'Failed to decrypt response'];
                }
            }

            // Handle nested resdtls - it may already be JSON string (decrypted)
            if (isset($decrypted['resdtls']) && is_string($decrypted['resdtls'])) {
                // Try to parse as JSON first (it might not be encrypted)
                $nestedData = json_decode($decrypted['resdtls'], true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    // It's already a JSON string, not encrypted
                    $decrypted = array_merge($decrypted, $nestedData);
                    Log::info('CVL Parsed nested resdtls as JSON');
                } else {
                    // Try decrypting (might still be encrypted)
                    $nestedDecrypted = $this->crypto->decryptAndParse($decrypted['resdtls']);
                    if ($nestedDecrypted !== null) {
                        $decrypted = array_merge($decrypted, $nestedDecrypted);
                        Log::info('CVL Decrypted nested resdtls');
                    }
                }
            }

            // Save Data JSON
            Storage::put('kyc_results/' . $pan . '_KYC_DATA.json', json_encode($decrypted, JSON_PRETTY_PRINT));
            Log::info('CVL KYC Data JSON Saved');

            return [
                'success' => true,
                'data' => $decrypted,
            ];

        } catch (GuzzleException $e) {
            Log::error('CVL FetchKycData Guzzle Error', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => 'Network error: ' . $e->getMessage()];
        } catch (Exception $e) {
            Log::error('CVL FetchKycData Exception', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Download ZIP documents (Step 3 of the flow)
     */
    private function downloadDocuments(string $token, string $pan, string $refNo): array
    {
        $endpoint = $this->apiBaseUrl . '/SolicitImage';
        
        $payload = [
            'PAN_NO' => $pan,
            'POS_CODE' => $this->posCode,
            'RTA_CODE' => $this->posCode,
            'KRA_CODE' => 'CVLKRA',
            'REF_NO' => $refNo,
        ];

        Log::info('CVL DownloadDocuments Request', [
            'endpoint' => $endpoint,
            'pan' => $pan,
            'ref_no' => $refNo,
        ]);

        try {
            // Encrypt payload - wrap in quotes like Node.js
            $encryptedPayload = $this->crypto->encrypt(json_encode($payload));

            $response = $this->httpClient->post($endpoint, [
                'body' => '"' . $encryptedPayload . '"',
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Token' => $token,
                    'user-agent' => 'CustomUsrAgnt',
                ],
            ]);

            $responseStatus = $response->getStatusCode();

            if ($responseStatus !== 200) {
                Log::error('CVL DownloadDocuments API Error', [
                    'status' => $responseStatus,
                ]);
                return ['success' => false, 'error' => 'API returned status: ' . $responseStatus];
            }

            // Get IV from response header
            $ivHeader = $response->getHeaderLine('x-encryption-iv-base64url');

            if (empty($ivHeader)) {
                Log::warning('CVL DownloadDocuments: IV header not found');
                return ['success' => false, 'error' => 'IV Header not found in response'];
            }

            // Get binary response
            $binaryData = $response->getBody()->getContents();

            // Decrypt binary response
            $decryptedFile = $this->crypto->decryptBinary($binaryData, $ivHeader);

            // Save ZIP file
            $filename = $pan . '_KYC_DOCUMENTS.zip';
            $path = 'kyc_documents/' . $filename;
            Storage::put($path, $decryptedFile);

            Log::info('CVL ZIP Downloaded', [
                'pan' => $pan,
                'filename' => $filename,
                'size' => strlen($decryptedFile),
            ]);

            return [
                'success' => true,
                'filename' => $filename,
                'path' => $path,
                'size' => strlen($decryptedFile),
            ];

        } catch (GuzzleException $e) {
            Log::error('CVL DownloadDocuments Guzzle Error', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => 'Network error: ' . $e->getMessage()];
        } catch (Exception $e) {
            Log::error('CVL DownloadDocuments Exception', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Extract main data from KYC response
     */
    private function extractKycData(array $data): array
    {
        // Check multiple paths for KYC_DATA
        $kycData = $data['KYC_DATA'] ?? $data['resdtls']['KYC_DATA'] ?? $data;
        
        return [
            'name' => $kycData['APP_HOLDER_NAME'] ?? $kycData['APP_NAME'] ?? null,
            'father_name' => $kycData['APP_FATHER_NAME'] ?? $kycData['APP_F_NAME'] ?? null,
            'dob' => $kycData['APP_DOB'] ?? $kycData['APP_DOB_DT'] ?? null,
            'pan' => $kycData['APP_PAN_NO'] ?? null,
            'status' => $kycData['APP_STATUS'] ?? 'UNKNOWN',
            'address' => $kycData['APP_ADDRESS'] ?? 
                ($kycData['APP_COR_ADD1'] ?? '') . ' ' . ($kycData['APP_COR_ADD2'] ?? '') . ' ' . ($kycData['APP_COR_ADD3'] ?? ''),
            'state' => $kycData['APP_STATE'] ?? $kycData['APP_COR_STATE'] ?? null,
            'city' => $kycData['APP_CITY'] ?? $kycData['APP_COR_CITY'] ?? null,
            'pincode' => $kycData['APP_PINCODE'] ?? $kycData['APP_COR_PINCD'] ?? null,
            'mobile' => $kycData['APP_MOB_NO'] ?? null,
            'email' => $kycData['APP_EMAIL'] ?? null,
        ];
    }

}
