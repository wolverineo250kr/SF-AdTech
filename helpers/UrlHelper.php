<?php

namespace Helpers;

use Defuse\Crypto\Crypto;

class UrlHelper
{
    private static $encryptionKey = 'N1komuN1Slofrgt3g334g34g34gva';

    public static function getUrlRoute(): string
    {
        $uri = $_SERVER['REQUEST_URI'];
        $uri = preg_replace('/\?.*/', '', $uri) . "\n";
        $uri = trim($uri);

        return $uri;
    }

    public static function encryptData($data): string
    {
        return base64_encode($data);
        //    return Crypto::encryptWithPassword($data, self::$encryptionKey); // надежно но строки длинные
    }

    public static function decryptData($encryptedData): string
    {
        return base64_decode($encryptedData);
        //  return Crypto::decryptWithPassword($encryptedData, self::$encryptionKey);  // надежно но строки длинные
    }

    public static function getEncryptionKey()
    {
        return self::$encryptionKey;
    }

    static public function base(): string
    {
        $hostName = explode('.', $_SERVER['HTTP_HOST']);
        $zone = array_pop($hostName);
        $name = array_pop($hostName);
        return implode('.', [$name, $zone]);
    }

    static public function host(): string
    {
        return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
    }
}