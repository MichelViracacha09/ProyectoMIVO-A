<?php

namespace App\Libraries;

/**
 * Token firmado HS256 (formato JWT) sin librerías externas.
 * La clave vive en .env: api.tokenSecret
 */
class ApiToken
{
    public const TTL = 3600; // 1 hora

    private static function secret(): string
    {
        $secret = (string) env('api.tokenSecret');
        if (strlen($secret) < 32) {
            throw new \RuntimeException('api.tokenSecret no está configurado en .env (mínimo 32 caracteres).');
        }
        return $secret;
    }

    private static function b64(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private static function unb64(string $data): string|false
    {
        return base64_decode(strtr($data, '-_', '+/'), true);
    }

    public static function encode(array $claims): string
    {
        $now     = time();
        $claims += ['iat' => $now, 'exp' => $now + self::TTL];

        $head = self::b64(json_encode(['alg' => 'HS256', 'typ' => 'JWT']));
        $body = self::b64(json_encode($claims));
        $sig  = self::b64(hash_hmac('sha256', "$head.$body", self::secret(), true));

        return "$head.$body.$sig";
    }

    /**
     * Devuelve los claims si la firma y la expiración son válidas; null en otro caso.
     */
    public static function decode(string $token): ?array
    {
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return null;
        }
        [$head, $body, $sig] = $parts;

        $expected = self::b64(hash_hmac('sha256', "$head.$body", self::secret(), true));
        if (!hash_equals($expected, $sig)) {
            return null;
        }

        $header = json_decode((string) self::unb64($head), true);
        if (($header['alg'] ?? null) !== 'HS256') {
            return null;
        }

        $claims = json_decode((string) self::unb64($body), true);
        if (!is_array($claims) || (int) ($claims['exp'] ?? 0) < time()) {
            return null;
        }

        return $claims;
    }
}
