<?php

function parse_get($get) {
    parse_str( parse_url( $_POST[$get], PHP_URL_QUERY), $query);
    return $query;
}


/**
 * Function to count the number of matched characters in two strings.
 *
 * @param string $str1 The first string to compare.
 * @param string $str2 The second string to compare.
 * @return int The count of matched characters.
 */
function count_match_chars($str1, $str2) {
    $count = 0;
    $charCount = [];

    // Populate the charCount array with the frequency of each character in str2
    for ($i = 0; $i < strlen($str2); $i++) {
        $char = $str2[$i];
        if (isset($charCount[$char])) {
            $charCount[$char]++;
        } else {
            $charCount[$char] = 1;
        }
    }

    // Loop through each character in str1 and count matches
    for ($i = 0; $i < strlen($str1); $i++) {
        $char = $str1[$i];
        if (isset($charCount[$char]) && $charCount[$char] > 0) {
            $count++;
            $charCount[$char]--;
        }
    }

    return $count;
}


/**
 * Function to echo "#" character a specified number of times.
 *
 * @param $count The number of times to echo "#".
 */
function str_char_repeat($count, $char = '# ') {
    return str_repeat($char, $count);
}

/**
 * Generate a random string, using a cryptographically secure 
 * pseudorandom number generator (random_int)
 *
 * This function uses type hints now (PHP 7+ only), but it was originally
 * written for PHP 5 as well.
 * 
 * For PHP 7, random_int is a PHP core function
 * For PHP 5.x, depends on https://github.com/paragonie/random_compat
 * 
 * @param int $length      How many characters do we want?
 * @param string $keyspace A string of all possible characters
 *                         to select from
 * @return string
 */
function random_str(
    int $length = 64,
    string $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'
): string {
    if ($length < 1) {
        throw new \RangeException("Length must be a positive integer");
    }
    $pieces = [];
    $max = mb_strlen($keyspace, '8bit') - 1;
    for ($i = 0; $i < $length; ++$i) {
        $pieces []= $keyspace[random_int(0, $max)];
    }
    return implode('', $pieces);
}


function random_username($string) {
    return vsprintf('%s%s%d', [...sscanf(strtolower("$string-"), '%s %2s'), random_int(1, 99)]);
}