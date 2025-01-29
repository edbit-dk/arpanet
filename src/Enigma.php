<?php

namespace Lib;

// Caesar encrypt
class Enigma 
{
    // Static method to encrypt the string
    public static function encode($plaintext, $key)
    {
        // Calculate the shift based on the key
        $shift = self::shift($key);

        return self::encrypt($plaintext, $shift);
    }

    // Static method to decrypt the string
    public static function decode($ciphertext, $key)
    {
        // Calculate the shift based on the key
        $shift = self::shift($key);

        return self::decrypt($ciphertext, $shift);
    }

    // Static method to calculate the shift based on the key
    private static function shift($key)
    {
        $shift = 0;

        // Sum the ASCII values of each character in the key
        foreach (str_split($key) as $char) {
            $shift += ord($char);
        }

        // Ensure the shift is within a valid range (0-25)
        return $shift % 26;
    }

    // Static method to encrypt the text using Caesar Cipher
    private static function encrypt($text, $shift)
    {
        $encrypted = '';

        // Loop through each character in the string
        foreach (str_split($text) as $char) {
            // Encrypt uppercase letters
            if (ctype_upper($char)) {
                $encrypted .= chr((ord($char) + $shift - 65) % 26 + 65);
            }
            // Encrypt lowercase letters
            elseif (ctype_lower($char)) {
                $encrypted .= chr((ord($char) + $shift - 97) % 26 + 97);
            }
            // Leave non-alphabetic characters unchanged
            else {
                $encrypted .= $char;
            }
        }

        return $encrypted;
    }

    // Static method to decrypt the text using Caesar Cipher
    private static function decrypt($text, $shift)
    {
        $decrypted = '';

        // Loop through each character in the string
        foreach (str_split($text) as $char) {
            // Decrypt uppercase letters
            if (ctype_upper($char)) {
                $decrypted .= chr((ord($char) - $shift - 65 + 26) % 26 + 65);
            }
            // Decrypt lowercase letters
            elseif (ctype_lower($char)) {
                $decrypted .= chr((ord($char) - $shift - 97 + 26) % 26 + 97);
            }
            // Leave non-alphabetic characters unchanged
            else {
                $decrypted .= $char;
            }
        }

        return $decrypted;
    }
}