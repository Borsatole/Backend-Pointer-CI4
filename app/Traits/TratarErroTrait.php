<?php

namespace App\Traits;
use CodeIgniter\HTTP\ResponseInterface;


trait TratarErroTrait
{
    private function tratarErro(\Exception $e): \CodeIgniter\HTTP\Response
    {
        log_message('error', '[Controller Generico] ' . $e->getMessage());

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Erro interno do servidor',
            'error' => ENVIRONMENT === 'development' ? $e->getMessage() : null
        ])->setStatusCode(500);
    }
}