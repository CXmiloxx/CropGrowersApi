<?php
include 'vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Token {
    public static function createToken($correo, $id, $rol, $nombre) {
        $payload = array(
            "correo" => $correo,
            "id" => $id,
            "rol" => $rol,
            "nombre" => $nombre,
            "iat" => time(),
            "exp" => time() + (30 * 24 * 60 * 60)
        );
        return JWT::encode($payload, "@G#76skO*&M9zQd!bL45RxJ7#kN", "HS256");
    }

    public static function decode($token) {
        $token = str_replace('Bearer ', '', $token);
        return JWT::decode($token, new Key( "@G#76skO*&M9zQd!bL45RxJ7#kN", "HS256"));
    }

    public static function validateToken($token) {
        try {
            $decoded = self::decode($token);
            $id = $decoded->id;
            $correo = $decoded->correo;
            $rol = $decoded->rol;
            $nombre = $decoded->nombre;

            return [
                "valid" => true,
                "id" => $id,
                "correo" => $correo,
                "rol" => $rol,
                "nombre" => $nombre,
            ];
        } catch (\Exception $e) {
            return [
                "valid" => false,
                "error" => $e->getMessage()
            ];
        }
    }


}
