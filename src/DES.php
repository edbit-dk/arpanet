<?php

namespace Lib;

class DES 
{
    private static string $cipher = 'DES-CBC'; // Original DES with CBC mode
    private static int $keyLength = 8; // Key length for DES

    // Encrypt text
    public static function encode(string $plaintext, string $key): string
    {
        // Ensure the key is the correct length
        $key = substr(hash('sha256', $key, true), 0, self::$keyLength);

        // Generate a random initialization vector (IV)
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length(self::$cipher));

        // Encrypt the plaintext
        $ciphertext = openssl_encrypt($plaintext, self::$cipher, $key, OPENSSL_RAW_DATA, $iv);

        // Combine the IV and ciphertext for storage
        return base64_encode($iv . $ciphertext);
    }

    // Decrypt text
    public static function decode(string $ciphertextBase64, string $key): string
    {
        // Decode the base64-encoded input
        $ciphertext = base64_decode($ciphertextBase64);

        // Ensure the key is the correct length
        $key = substr(hash('sha256', $key, true), 0, self::$keyLength);

        // Extract the IV and ciphertext
        $ivLength = openssl_cipher_iv_length(self::$cipher);
        $iv = substr($ciphertext, 0, $ivLength);
        $ciphertext = substr($ciphertext, $ivLength);

        // Decrypt the ciphertext
        $plaintext = openssl_decrypt($ciphertext, self::$cipher, $key, OPENSSL_RAW_DATA, $iv);

        return $plaintext ?: '';
    }
}