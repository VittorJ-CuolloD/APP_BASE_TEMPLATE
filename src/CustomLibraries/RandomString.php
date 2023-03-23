<?php

namespace App\CustomLibraries;

/**
 * RandomString allow to generate random strings, encode them and decode them.
 *
 * Methods:
 *   * generate
 *   * encode
 *   * decode
 *
 * @author Diego García <diego_garcia@doctaforum.com>
 */
class RandomString
{

    private static $vocabulary = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
    private static $symbols = '-_%';


    /** It generates a random string
     *
     * @return string Return string.
     */
    public static function generate()
    {
        $chars = [RandomString::$vocabulary, RandomString::$vocabulary, RandomString::$vocabulary, RandomString::$vocabulary, RandomString::$vocabulary, RandomString::$vocabulary, RandomString::$symbols];

        $pass = [];
        $rand = rand(12, 16);

        for ($i = 0; $i < $rand; $i++) {
            $randArray = rand(0, 6);

            $n = rand(0, strlen($chars[$randArray]) - 1);

            $pass[] = $chars[$randArray][$n];
        }

        return implode($pass) . time();
    }


    /** It encodes a string with custom algoritm
     *
     * @return string Return encoded string.
     */
    public static function encode(string $string)
    {
        $string = bin2hex($string);
        $encodedString = base64_encode($string);

        return $encodedString;
    }

    /** It decodes a string encodes with RandomString::encode()
     *
     * @return string Return decoded string.
     */
    public static function decode(string $string)
    {
        $string = base64_decode($string);
        $decodedString = hex2bin($string);

        return $decodedString;
    }
}