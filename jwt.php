<?php

function base64url_encode($data) {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

function base64url_decode($data) {
    return base64_decode(strtr($data, '-_', '+/'));
}

$jwtSecret = 'super_secret_key_123';

function jwt_encode(array $payload, int $expSeconds = 3600): string {

    global $jwtSecret;

    $header = ['typ' => 'JWT', 'alg' => 'HS256'];
    $payload['exp'] = time() + $expSeconds;

    $base64Header  = base64url_encode(json_encode($header));
    $base64Payload = base64url_encode(json_encode($payload));

    $signature = hash_hmac(
        'sha256',
        "$base64Header.$base64Payload",
        $jwtSecret,
        true
    );

    return "$base64Header.$base64Payload." . base64url_encode($signature);
}

function jwt_decode(string $jwt): array {

    global $jwtSecret;

    $parts = explode('.', $jwt);
    if (count($parts) !== 3) {
        throw new Exception('Token non valido');
    }

    [$header, $payload, $signature] = $parts;

    $validSignature = base64url_encode(
        hash_hmac('sha256', "$header.$payload", $jwtSecret, true)
    );

    if (!hash_equals($validSignature, $signature)) {
        throw new Exception('Firma non valida');
    }

    $data = json_decode(base64url_decode($payload), true);

    if ($data['exp'] < time()) {
        throw new Exception('Token scaduto');
    }

    return $data;
}
