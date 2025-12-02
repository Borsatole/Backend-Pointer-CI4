<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Autenticacao implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $header = $request->getHeaderLine('Authorization');

        if (!$header) {
            return service('response')->setJSON([
                'success' => false,
                'message' => 'Token não fornecido'
            ])->setStatusCode(401);
        }

        // Espera formato: "Bearer TOKEN"
        if (!preg_match('/Bearer\s(\S+)/', $header, $matches)) {
            return service('response')->setJSON([
                'success' => false,
                'message' => 'Token inválido'
            ])->setStatusCode(401);
        }

        $token = $matches[1];

        try {
            $secret = env('JWT_SECRET'); // pegue do .env
            $decoded = JWT::decode($token, new Key($secret, 'HS256'));

            // Armazena o usuário decodificado na request para usar nos controllers
            $request->user = $decoded;

            $req = service('request');
            $req->user = $decoded;


        } catch (\Exception $e) {
            return service('response')->setJSON([
                'success' => false,
                'message' => 'Token inválido ou expirado'
            ])->setStatusCode(401);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Não é necessário aqui
    }
}
