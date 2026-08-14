<?php
declare(strict_types=1);

namespace App\Middleware;

use System\Request;

final class JwtMiddleware implements \System\Middleware
{
    public function handle(): bool
    {
        $secret = (string) (defined('API_JWT_SECRET') ? API_JWT_SECRET : '');
        $token = (new Request())->bearerToken();
        if ($secret === '' || $token === null) return false;

        $parts = explode('.', $token);
        if (count($parts) !== 3) return false;
        [$encodedHeader, $encodedPayload, $encodedSignature] = $parts;
        $header = $this->decode($encodedHeader);
        $payload = $this->decode($encodedPayload);
        if (($header['alg'] ?? null) !== 'HS256' || $payload === null || $header === null) return false;

        $expected = $this->base64url(hash_hmac(
            'sha256',
            $encodedHeader . '.' . $encodedPayload,
            $secret,
            true
        ));
        if (!hash_equals($expected, $encodedSignature)) return false;

        $now = time();
        $leeway = (int) (defined('API_JWT_LEEWAY') ? API_JWT_LEEWAY : 0);
        if (isset($payload['exp']) && (!is_numeric($payload['exp']) || $now > (int) $payload['exp'] + $leeway)) return false;
        if (isset($payload['nbf']) && (!is_numeric($payload['nbf']) || $now + $leeway < (int) $payload['nbf'])) return false;

        $_SERVER['AUTH_JWT_PAYLOAD'] = json_encode($payload, JSON_UNESCAPED_UNICODE);
        return true;
    }

    private function decode(string $value): ?array
    {
        $padding = strlen($value) % 4;
        if ($padding > 0) $value .= str_repeat('=', 4 - $padding);
        $decoded = base64_decode(strtr($value, '-_', '+/'), true);
        if ($decoded === false) return null;
        try {
            $data = json_decode($decoded, true, 512, JSON_THROW_ON_ERROR);
            return is_array($data) ? $data : null;
        } catch (\JsonException) {
            return null;
        }
    }

    private function base64url(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }
}
