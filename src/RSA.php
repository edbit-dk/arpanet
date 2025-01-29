<?php

namespace Lib;

class RSA
{
    // Encrypt a string using Hybrid RSA-AES
    public static function encode($plaintext, $publicKeyPath)
    {
        // Generate a random AES key (32 bytes for AES-256)
        $aesKey = openssl_random_pseudo_bytes(32);
        
        // Generate a random IV (Initialization Vector) for AES CBC mode
        $iv = openssl_random_pseudo_bytes(16);

        // Encrypt the plaintext using AES-256-CBC
        $ciphertext = openssl_encrypt($plaintext, "AES-256-CBC", $aesKey, OPENSSL_RAW_DATA, $iv);

        // Read the RSA public key
        $publicKey = file_get_contents($publicKeyPath);

        // Encrypt the AES key using RSA
        openssl_public_encrypt($aesKey, $encryptedAESKey, $publicKey);

        // Combine the encrypted AES key, IV, and ciphertext
        return base64_encode($encryptedAESKey . "::" . $iv . "::" . $ciphertext);
    }

    // Decrypt a string using Hybrid RSA-AES
    public static function decode($encryptedData, $privateKeyPath)
    {
        // Decode the base64-encoded encrypted data
        $decodedData = base64_decode($encryptedData);

        // Split into components: Encrypted AES Key, IV, and Ciphertext
        list($encryptedAESKey, $iv, $ciphertext) = explode("::", $decodedData);

        // Read the RSA private key
        $privateKey = file_get_contents($privateKeyPath);

        // Decrypt the AES key using RSA
        openssl_private_decrypt($encryptedAESKey, $aesKey, $privateKey);

        // Decrypt the ciphertext using AES-256-CBC
        return openssl_decrypt($ciphertext, "AES-256-CBC", $aesKey, OPENSSL_RAW_DATA, $iv);
    }

    public static function keys($keySize = 2048)
    {
        $config = [
            "private_key_bits" => $keySize,
            "private_key_type" => OPENSSL_KEYTYPE_RSA,
        ];

        // Generate key pair
        $res = openssl_pkey_new($config);
        
        // Extract private key
        openssl_pkey_export($res, $privateKey);

        // Extract public key
        $publicKey = openssl_pkey_get_details($res)["key"];

        return [
            "private" => $privateKey,
            "public" => $publicKey
        ];

        //file_put_contents("private.pem", $keys['private']);
        //file_put_contents("public.pem", $keys['public']);¨

        // Example usage:
        // $publicKeyPath = "public.pem";  // Public key file
        // $privateKeyPath = "private.pem"; // Private key file
        // $plaintext = "This is a secure message!";

        // Encrypt the message
        // $encryptedText = RSA::enc($plaintext, $publicKeyPath);
        // echo "Encrypted: " . $encryptedText . "\n";

        // Decrypt the message
        // $decryptedText = RSA::dec($encryptedText, $privateKeyPath);
        // echo "Decrypted: " . $decryptedText . "\n";

    }
}
