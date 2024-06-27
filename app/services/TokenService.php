<?php

namespace App\Services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;

class TokenService {
    private static $claveSecreta = 'T3sT$JWT';
    private static $tipoEncriptacion = 'HS256';

    public static function crearToken($datos) {
        $ahora = time();
        $payload = [
            'iat' => $ahora,
            'exp' => $ahora + 60000,
            'aud' => self::aud(),
            'data' => $datos,
            'app' => "La Comanda"
        ];
        return JWT::encode($payload, self::$claveSecreta, self::$tipoEncriptacion);
    }

    public static function verificarToken($token) {
        if (empty($token)) {
            throw new Exception("El token está vacío");
        }

        try {
            $decodificado = JWT::decode($token, new Key(self::$claveSecreta, self::$tipoEncriptacion));
        } catch (Exception $e) {
            throw new Exception("Token inválido: " . $e->getMessage());
        }

        if ($decodificado->aud !== self::aud()) {
            throw new Exception("No es el usuario válido");
        }
    }

    public static function obtenerPayLoad($token) {
        if (empty($token)) {
            throw new Exception("El token está vacío");
        }
        return JWT::decode($token, new Key(self::$claveSecreta, self::$tipoEncriptacion));
    }

    public static function obtenerData($token) {
        return self::obtenerPayLoad($token)->data;
    }

    private static function aud() {
        $aud = '';

        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $aud = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $aud = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $aud = $_SERVER['REMOTE_ADDR'];
        }

        $aud .= @$_SERVER['HTTP_USER_AGENT'];
        $aud .= gethostname();

        return sha1($aud);
    }
}
