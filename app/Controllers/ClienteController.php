<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Services\ClienteService;
use App\Exceptions\ClienteException;

class ClienteController extends BaseController
{
    private $clienteService;

    public function __construct()
    {
        $this->clienteService = new ClienteService();
    }


    public function index()
    {
        try {
            $limite = intval($this->request->getGet('limite') ?? 10);
            $pagina = intval($this->request->getGet('pagina') ?? 1);

            $data_inicio = $this->request->getGet('data_inicio');
            $data_inicio = !empty($data_inicio) ? $data_inicio : null;

            $data_fim = $this->request->getGet('data_fim');
            $data_fim = !empty($data_fim) ? $data_fim : null;

            // Pega todos os filtros da URL (exceto limite/pagina)
            $filtros = $this->request->getGet();

            // Remove filtros inválidos
            unset(
                $filtros['limite'],
                $filtros['pagina'],
                $filtros['data_inicio'],
                $filtros['data_fim']
            );

            $resultado = $this->clienteService->listar($limite, $pagina, $filtros, $data_inicio, $data_fim);

            return $this->response->setJSON([
                'success' => true,
                ...$resultado,
                'filtros' => $filtros,
                // 'recebidos' => $this->request->getGet()
            ]);

        } catch (\Exception $e) {
            return $this->tratarErro($e);
        }
    }

    public function show($id = null)
    {
        try {
            $cliente = $this->clienteService->buscar((int) $id);

            return $this->response->setJSON([
                'success' => true,
                'Registros' => $cliente
            ]);

        } catch (ClienteException $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ])->setStatusCode($e->getCode());

        } catch (\Exception $e) {
            return $this->tratarErro($e);
        }
    }

    public function create()
    {
        try {
            $data = $this->request->getJSON(true);
            $cliente = $this->clienteService->criar($data);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Cliente criado com sucesso',
                'registro' => $cliente
            ])->setStatusCode(201);

        } catch (ClienteException $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ])->setStatusCode($e->getCode());

        } catch (\Exception $e) {
            return $this->tratarErro($e);
        }
    }

    public function update($id = null)
    {
        try {
            $data = $this->request->getJSON(true);

            // Se as permissões vieram como string JSON, decodifica
            if (isset($data['permissoes']) && is_string($data['permissoes'])) {
                $data['permissoes'] = json_decode($data['permissoes'], true);
            }

            $cliente = $this->clienteService->atualizar((int) $id, $data);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Cliente atualizado com sucesso',
                'registro' => $cliente
            ]);

        } catch (ClienteException $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ])->setStatusCode($e->getCode());

        } catch (\Exception $e) {
            return $this->tratarErro($e);
        }
    }

    public function delete($id = null)
    {
        try {
            $this->clienteService->deletar((int) $id);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Cliente deletado com sucesso'
            ]);

        } catch (ClienteException $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ])->setStatusCode($e->getCode());

        } catch (\Exception $e) {
            return $this->tratarErro($e);
        }
    }
    private function tratarErro(\Exception $e): \CodeIgniter\HTTP\Response
    {
        log_message('error', '[ClientesController] ' . $e->getMessage());

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Erro interno do servidor',
            'error' => ENVIRONMENT === 'development' ? $e->getMessage() : null
        ])->setStatusCode(500);
    }
}