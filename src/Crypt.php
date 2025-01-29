<?php

namespace Lib;

class Crypt
{
    // Encode plaintext with a key and optional metadata
    public static function encode($plaintext, $key, $filename = '', $headers = [])
    {
        // Collect metadata from headers or default
        $metadata = [];

        if (!empty($headers)) {
            // Use provided headers
            $metadata = $headers;
        } else {
            // Default metadata
            $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'Unknown IP';
            $timestamp = date('D M j H:i:s Y');
            $metadata = [
                $ipAddress,
                $filename,
                $timestamp
            ];
        }

        // Create the structured data
        $data = [
            'key' => $key,
            'metadata' => $metadata,
            'plaintext' => $plaintext
        ];

        // Encode the data as JSON
        return base64_encode(json_encode($data));
    }

    // Decode the encoded data with a key
    public static function decode($ciphertext, $key)
    {
        // Decode the Base64 encoded string
        $decodedJson = base64_decode($ciphertext);
        $data = json_decode($decodedJson, true);

        // Always show headers
        $headers = self::head($data);

        // If the key matches, return headers + plaintext
        if ($data['key'] === $key) {
            return $headers . "\n" . $data['plaintext'];
        }

        // If the key is incorrect, return headers with warning
        return $headers . "\n$ciphertext";
    }

    // Extract headers from the decoded data
    public static function head($data)
    {
        // Convert metadata to a string format
        $headers = "";
        foreach ($data['metadata'] as $head) {
            $headers .= "$head|";
        }

        return $headers;
    }
}
