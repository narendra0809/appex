<?php

namespace App\Console\Commands;

use App\Helpers\CvlCrypto;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * CVL KRA KYC Test Command
 * Production Ready - Matches Node.js index.js exactly
 * 
 * Usage: php artisan cvl:test <PAN> <DOB>
 * Example: php artisan cvl:test DBFPP6463M 08-09-1997
 */
class TestCvlKra extends Command
{
    protected $signature = 'cvl:test {pan} {dob}';
    protected $description = 'Test CVL KRA API - Production Ready';

    private string $apiBaseUrl;
    private string $apiKey;
    private string $aesKey;
    private string $username;
    private string $posCode;
    private string $password;

    // Status codes (matching Node.js)
    private const IMAGE_ELIGIBLE_STATUS = ['01', '11', '12'];
    private const NON_IMAGE_STATUS = ['02', '04', '05', '07'];

    public function __construct()
    {
        parent::__construct();
        
        $this->apiBaseUrl = config('services.cvl_kra.base_url', env('CVL_API_BASE_URL', ''));
        $this->apiKey = config('services.cvl_kra.api_key', env('CVL_API_KEY', ''));
        $this->aesKey = config('services.cvl_kra.aes_key', env('CVL_AES_KEY', ''));
        $this->username = config('services.cvl_kra.username', env('CVL_USERNAME', ''));
        $this->posCode = config('services.cvl_kra.pos_code', env('CVL_POS_CODE', ''));
        $this->password = config('services.cvl_kra.password', env('CVL_PASSWORD', ''));
    }

    public function handle(): int
    {
        $pan = strtoupper($this->argument('pan'));
        $dob = $this->argument('dob');
        
        $this->info("===========================================");
        $this->info("CVL KRA KYC Test - Production Ready");
        $this->info("===========================================");
        $this->newLine();
        
        $environment = config('services.cvl_kra.environment', 'UAT');
        $this->info("Environment: {$environment}");
        $this->info("API URL: {$this->apiBaseUrl}");
        $this->info("Username: {$this->username}");
        $this->info("POS Code: {$this->posCode}");
        $this->info("AES Key: " . substr($this->aesKey, 0, 10) . '...');
        $this->newLine();
        $this->info("Processing PAN: {$pan}, DOB: {$dob}");
        $this->newLine();

        // Force IPv4 - CVL API only supports IPv4 whitelisting
        // Use numeric values for constants (CURLOPT_IPRESOLVE = 113, CURL_IPRESOLVE_V4 = 1)
        $curlOptions = [];
        if (defined('CURLOPT_IPRESOLVE') && defined('CURL_IPRESOLVE_V4')) {
            $curlOptions[CURLOPT_IPRESOLVE] = CURL_IPRESOLVE_V4;
        } else {
            $curlOptions[113] = 1;  // CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4
        }

        $httpClient = new Client([
            'timeout' => 30,
            'connect_timeout' => 10,
            'http_errors' => false,
            'force_ip_resolve' => 'v4',
            'curl' => $curlOptions,
        ]);

        $crypto = new CvlCrypto();

        // Step 1: Get Token (matching Node.js)
        $this->info("Step 1: Getting Authentication Token...");
        try {
            $payload = [
                'username' => $this->username,
                'poscode' => $this->posCode,
                'password' => $this->password,
            ];

            $response = $httpClient->post($this->apiBaseUrl . '/GetToken', [
                'body' => json_encode($crypto->encrypt(json_encode($payload))),
                'headers' => [
                    'Content-Type' => 'application/json',
                    'api_key' => $this->apiKey,
                ],
            ]);

            $tokenBody = trim($response->getBody()->getContents(), '"');
            
            if (strpos($tokenBody, ':') !== false) {
                $parts = explode(':', $tokenBody);
                if (count($parts) >= 2) {
                    $token = $parts[1];
                    $this->info("✓ Token received");
                    $this->info("Token: " . substr($token, 0, 50) . '...');
                }
            } else {
                $this->warn("⚠ Unexpected token format");
                return 1;
            }
        } catch (\Exception $e) {
            $this->error("✗ Token Error: " . $e->getMessage());
            return 1;
        }
        $this->newLine();

        // Step 2: Get KYC Details (matching Node.js main loop)
        $this->info("Step 2: Fetching KYC Details...");
        try {
            $payload = [
                'APP_REQ_ROOT' => [
                    'APP_PAN_INQ' => [
                        'APP_PAN_NO' => $pan,
                        'APP_DOB_INCORP' => $dob,
                        'APP_POS_CODE' => $this->posCode,
                        'APP_RTA_CODE' => $this->posCode,
                        'APP_KRA_CODE' => 'CVLKRA',
                        'FETCH_TYPE' => 'I',
                    ]
                ]
            ];

            $response = $httpClient->post($this->apiBaseUrl . '/SolicitPANDetailsFetchALLKRA', [
                'body' => json_encode($crypto->encrypt(json_encode($payload))),
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Token' => $token,
                ],
            ]);

            $responseBody = $response->getBody()->getContents();
            
            // Decrypt response
            $decrypted = $crypto->decryptAndParse($responseBody);
            
            if ($decrypted === null) {
                $jsonDecoded = json_decode($responseBody, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $decrypted = $jsonDecoded;
                } else {
                    $decrypted = ['raw_response' => $responseBody];
                }
            }

            // Handle nested resdtls
            if (isset($decrypted['resdtls']) && is_string($decrypted['resdtls'])) {
                $nestedDecrypted = $crypto->decryptAndParse($decrypted['resdtls']);
                if ($nestedDecrypted !== null) {
                    $decrypted = array_merge($decrypted, $nestedDecrypted);
                }
            }

            // Save Data JSON
            Storage::put('test/' . $pan . '_Data.json', json_encode($decrypted, JSON_PRETTY_PRINT));
            $this->info("✓ Data JSON Saved");
            $this->newLine();

            // Step 3: Check Status and Extract Documents (matching Node.js)
            $status = $decrypted['KYC_DATA']['APP_STATUS']
                ?? $decrypted['APP_STATUS']
                ?? $decrypted['APP_PAN_INQ_RES']['APP_STATUS']
                ?? 'UNKNOWN';

            $this->info("Step 3: KYC Status - {$status}");

            if (in_array($status, self::IMAGE_ELIGIBLE_STATUS)) {
                $this->info("Extracting documents...");
                $documents = $this->recursiveImageFinder($decrypted, $pan);
                $this->info("✓ Documents saved: " . count($documents));
                
                foreach ($documents as $doc) {
                    $this->info("  - {$doc['filename']}");
                }
            } elseif (in_array($status, self::NON_IMAGE_STATUS)) {
                $this->info("✓ Documents not allowed for this status");
            } else {
                $this->info("⚠ Unknown status – manual review");
            }

        } catch (\Exception $e) {
            $this->error("✗ PAN Error: " . $e->getMessage());
            return 1;
        }

        $this->newLine();
        $this->info("===========================================");
        $this->info("Test Completed Successfully");
        $this->info("===========================================");
        
        return 0;
    }

    /**
     * Recursively find and save base64 documents (matching Node.js)
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
     * Detect file extension and save (matching Node.js)
     */
    private function detectAndSaveFile(string $base64, string $pan, int $idx): void
    {
        $ext = $this->detectExtension($base64);
        $filename = "{$pan}_Document_{$idx}.{$ext}";
        
        try {
            $buffer = base64_decode($base64);
            if ($buffer !== false) {
                Storage::put('test/' . $filename, $buffer);
                $this->info("✓ Saved: {$filename}");
            }
        } catch (\Exception $e) {
            $this->error("✗ Failed to save {$filename}: " . $e->getMessage());
        }
    }

    /**
     * Detect file extension from base64 (matching Node.js)
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
}
