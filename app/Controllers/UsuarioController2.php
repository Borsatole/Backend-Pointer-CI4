<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Services\UsuarioService;
use App\Exceptions\UsuarioException;


class UsuarioController2 extends BaseController
{
    protected UsuarioService $UsuarioService;


    public function __construct()
    {
        $this->UsuarioService = new UsuarioService();
    }

    public function index()
    {
        $usuarioLogado = $this->request->user ?? null;


        $limite = intval($this->request->getVar('limite') ?? 10);
        $pagina = intval($this->request->getVar('pagina') ?? 1);



        // Pega todos os filtros da URL (exceto limite/pagina)
        $filtros = $this->request->getGet();
        unset($filtros['limite'], $filtros['pagina']);



        try {
            $resultado = $this->UsuarioService->listar($limite, $pagina, $filtros);
            return $this->response->setJSON([
                'success' => true,
                ...$resultado,
                'filtros' => $filtros
            ]);

        } catch (UsuarioException $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage(),
            ])->setStatusCode($e->getCode());
        }

    }
}
