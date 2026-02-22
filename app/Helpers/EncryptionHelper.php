<?php

namespace App\Helpers;

class EncryptionHelper {
    public static function encrypt_str($data){
        $key = "pelatihandisnaker";
        $cipher = "aes-256-cbc";
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($cipher));
        $encrypted = openssl_encrypt($data, $cipher, $key, 0, $iv);
        $hexEncrypted = bin2hex($encrypted . '::' . $iv);
        return self::formatUUID($hexEncrypted);
    }

    public static function decrypt_str($data){
        try {
            
        $key = "pelatihandisnaker";
        $cipher = "aes-256-cbc";
        $hexData = self::removeUUIDFormatting($data);
        $decoded = hex2bin($hexData);
        list($encrypted_data, $iv) = explode('::', $decoded, 2);
            return @openssl_decrypt($encrypted_data, $cipher, $key, 0, $iv);
        } catch (\Throwable $th) {
            return null;
        }
    }

    private static function formatUUID($hex) {
        return substr($hex, 0, 8) . '-' .
               substr($hex, 8, 4) . '-' .
               substr($hex, 12, 4) . '-' .
               substr($hex, 16, 4) . '-' .
               substr($hex, 20);
    }

    private static function removeUUIDFormatting($uuid) {
        return str_replace('-', '', $uuid);
    }
}
