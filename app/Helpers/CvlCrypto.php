<?php

namespace App\Helpers;

use Exception;

/**
 * CVL KRA Crypto Helper
 * Handles AES-192-CBC encryption/decryption for CVL API
 * CVL uses: AES-192-CBC with base64-encoded key
 */
class CvlCrypto
{
    private string $aesKey;
    private string $aesKeyBytes;

    public function __construct()
    {
        $this->aesKey = config('services.cvl_kra.aes_key', env('CVL_AES_KEY', ''));
        
        // Match Node.js: base64 decode the key
        $decoded = base64_decode($this->aesKey, true);
        
        if ($decoded !== false && in_array(strlen($decoded), [16, 24, 32])) {
            $this->aesKeyBytes = $decoded;
        } else {
            // Fallback: use raw key
            $this->aesKeyBytes = $this->aesKey;
        }
        
        \Illuminate\Support\Facades\Log::info('CVL Crypto Init', [
            'key_length' => strlen($this->aesKey),
            'decoded_length' => strlen($this->aesKeyBytes),
        ]);
    }

    /**
     * Decrypt CVL API response
     * Format: "IV_BASE64:CIPHER_BASE64"
     */
    public function decrypt(string $encrypted): ?string
    {
        try {
            // Remove all surrounding quotes if present (handle nested quotes like "IV:CIPHER")
            while (strlen($encrypted) >= 2 && $encrypted[0] === '"' && $encrypted[strlen($encrypted) - 1] === '"') {
                $encrypted = substr($encrypted, 1, -1);
            }

            // Debug: Log the cleaned encrypted string
            \Illuminate\Support\Facades\Log::info('CVL Decrypt Debug - Input', [
                'original_length' => strlen($encrypted),
                'encrypted_preview' => substr($encrypted, 0, 50),
                'aes_key_length' => strlen($this->aesKey),
                'aes_key_bytes_length' => strlen($this->aesKeyBytes),
            ]);

            // Check format: must contain colon separator
            if (!str_contains($encrypted, ':')) {
                \Illuminate\Support\Facades\Log::warning('CVL Decrypt: No colon separator found', ['data' => substr($encrypted, 0, 50)]);
                return null;
            }

            // Split IV and ciphertext
            [$ivB64Url, $cipherB64] = explode(':', $encrypted, 2);

            // Both IV and Cipher use URL-safe base64 (with - and _ characters)
            $iv = $this->urlSafeB64Decode($ivB64Url);
            $cipher = $this->urlSafeB64Decode($cipherB64);

            if ($iv === false || $cipher === false) {
                \Illuminate\Support\Facades\Log::warning('CVL Decrypt: Base64 decode failed');
                return null;
            }

            // Determine algorithm based on key length
            $algo = 'AES-192-CBC';
            if (strlen($this->aesKeyBytes) === 16) {
                $algo = 'AES-128-CBC';
            } elseif (strlen($this->aesKeyBytes) === 32) {
                $algo = 'AES-256-CBC';
            }

            // Debug info
            \Illuminate\Support\Facades\Log::info('CVL Decrypt Debug', [
                'iv_length' => strlen($iv),
                'cipher_length' => strlen($cipher),
                'aes_key_bytes_length' => strlen($this->aesKeyBytes),
                'algo' => $algo,
                'openssl_error' => openssl_error_string()
            ]);

            // Decrypt using AES
            $plain = openssl_decrypt(
                $cipher,
                $algo,
                $this->aesKeyBytes,
                OPENSSL_RAW_DATA,
                $iv
            );

            if ($plain === false) {
                $error = openssl_error_string();
                \Illuminate\Support\Facades\Log::warning('CVL Decrypt: OpenSSL decrypt failed', ['error' => $error]);
                return null;
            }

            return $plain;

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('CVL Decrypt Exception', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * URL-safe base64 decode
     */
    public function urlSafeB64Decode(string $input): string|false
    {
        // Add padding if needed
        $remainder = strlen($input) % 4;
        if ($remainder) {
            $input .= str_repeat('=', 4 - $remainder);
        }
        return base64_decode(strtr($input, '-_', '+/'));
    }

    /**
     * Decrypt and parse CVL response
     * Returns parsed JSON array or null
     */
    public function decryptAndParse(string $encrypted): ?array
    {
        $decrypted = $this->decrypt($encrypted);
        
        if ($decrypted === null) {
            return null;
        }

        $data = json_decode($decrypted, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            \Illuminate\Support\Facades\Log::warning('CVL Decrypt: JSON parse failed', ['error' => json_last_error_msg()]);
            return null;
        }

        return $data;
    }

    /**
     * Encrypt data (for API requests if needed)
     */
    public function encrypt(string $data): string
    {
        $iv = openssl_random_pseudo_bytes(16);
        
        $encrypted = openssl_encrypt(
            $data,
            'AES-192-CBC',
            $this->aesKeyBytes,
            OPENSSL_RAW_DATA,
            $iv
        );

        if ($encrypted === false) {
            throw new Exception('Encryption failed: ' . openssl_error_string());
        }

        // Return in CVL format: IV_URLSAFE_BASE64:CIPHER_BASE64
        $ivB64 = rtrim(strtr(base64_encode($iv), '+/', '-_'), '=');
        $cipherB64 = base64_encode($encrypted);
        
        return $ivB64 . ':' . $cipherB64;
    }

    /**
     * Decrypt binary response (for SolicitImage ZIP download)
     * Uses the same AES key but handles binary data
     */
    public function decryptBinary(string $encryptedBuffer, string $ivBase64Url): string
    {
        try {
            // Convert URL-safe base64 to standard base64
            $iv = $this->urlSafeB64Decode($ivBase64Url);
            
            // Decode the binary data (it's not base64 encoded, it's raw encrypted bytes)
            // Wait, looking at Node.js code - the buffer is already encrypted bytes, not base64
            // But the IV is URL-safe base64
            
            // Actually in the Node.js code:
            // const zipBuffer = decryptBinary(imageRes.data, ivHeader);
            // imageRes.data is the raw binary response (arraybuffer)
            // ivHeader is URL-safe base64
            
            // So we need to use the key as-is (base64 decoded for AES-192)
            $key = $this->aesKeyBytes;
            
            // Determine algorithm based on key length
            $algo = 'AES-192-CBC';
            if (strlen($key) === 16) {
                $algo = 'AES-128-CBC';
            } elseif (strlen($key) === 32) {
                $algo = 'AES-256-CBC';
            }

            $decrypted = openssl_decrypt(
                $encryptedBuffer,
                $algo,
                $key,
                OPENSSL_RAW_DATA,
                $iv
            );

            if ($decrypted === false) {
                $error = openssl_error_string();
                \Illuminate\Support\Facades\Log::warning('CVL decryptBinary: OpenSSL decrypt failed', ['error' => $error]);
                return '';
            }

            return $decrypted;

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('CVL decryptBinary Exception', ['error' => $e->getMessage()]);
            return '';
        }
    }
}
