<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Services\AuthService;
use App\Exceptions\AuthException;
use CodeIgniter\HTTP\ResponseInterface;

class AuthController extends BaseController
{
    protected AuthService $AuthService;

    public function __construct()
    {
        $this->AuthService = new AuthService();
    }

    /**
     * @return ResponseInterface
     */
    public function login(): ResponseInterface
    {
        try {
            $request = service('request');
            $email = $request->getVar('email');
            $senha = $request->getVar('password');

            if (empty($email) || empty($senha)) {
                throw new AuthException('Email e senha são obrigatórios', 400);
            }

            $resultado = $this->AuthService->autenticar($email, $senha);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Login realizado com sucesso',
                'usuario' => $resultado['usuario'],
                'menu' => $resultado['menu'],
                'token' => $resultado['token'],
                'expirationTime' => $resultado['expirationTime'],
                'serverTime' => time(),
            ]);
        } catch (\Exception $e) {
            return $this->tratarErro($e);
        }
    }

  
    public function validarToken(): ResponseInterface
    {
        
        try {
            $authHeader = service('request')->getHeaderLine('Authorization');
            $dadosToken = $this->AuthService->validarToken($authHeader);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Token válido',
                'data' => $dadosToken
            ]);
        } catch (\Exception $e) {
            return $this->tratarErro($e);
        }
    }

    private function tratarErro(\Exception $e): ResponseInterface
    {
        $status = ($e->getCode() >= 100 && $e->getCode() < 600)
            ? $e->getCode()
            : 500;

        // log_message('error', "[AuthController] {$e->getMessage()}");

        return $this->response->setJSON([
            'success' => false,
            'message' => "Não foi possível conectar ao servidor",
            // 'message' => $e->getMessage(),
            'error' => ENVIRONMENT === 'development' ? $e->getTraceAsString() : null,
        ])->setStatusCode($status);
    }
}
