<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Traits\RequestFilterTrait;
use App\Traits\TratarErroTrait;



class ItensParaVistoriaController extends BaseController
{
    use TratarErroTrait;
    use RequestFilterTrait;

    /** 🔹 Nome da classe do Service (pode ser trocado) */
    private const SERVICE = \App\Services\ItensParaVistoriaService::class;

    private $service;

    public function __construct()
    {
        $serviceClass = self::SERVICE;
        $this->service = new $serviceClass();
    }

    public function index()
    {
        try {
            $params = $this->getRequestFilters($this->request, [
                'pagination' => true,
                'ordering' => true,
                'dates' => true,
            ]);

            $resultado = $this->service->listar($params);

            return $this->response->setJSON([
                'success' => true,
                ...$resultado,
                'filtros' => $params['filtros'],
            ]);

        } catch (\Exception $e) {
            return $this->tratarErro($e);
        }
    }

    public function show($id = null)
    {
        try {
            $registro = $this->service->buscar((int) $id);

            return $this->response->setJSON([
                'success' => true,
                'registro' => $registro
            ]);

        } catch (\Exception $e) {
            return $this->tratarErro($e);
        }
    }



    public function create()
    {
        try {
            $data = $this->request->getJSON(true);
            $registro = $this->service->criar($data);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Criado com sucesso',
                'registro' => $registro
                // 'registro' => $teste
            ])->setStatusCode(201);

        } catch (\Exception $e) {
            return $this->tratarErro($e);
        }
    }

    public function update($id = null)
    {
        try {
            $data = $this->request->getJSON(true);
            $registro = $this->service->atualizar((int) $id, $data);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Atualizado com sucesso',
                'registro' => $registro
            ]);

        } catch (\Exception $e) {
            return $this->tratarErro($e);
        }
    }

    public function delete($id = null)
    {
        try {
            $this->service->deletar((int) $id);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Deletado com sucesso'
            ]);

        } catch (\Exception $e) {
            return $this->tratarErro($e);
        }
    }

}
