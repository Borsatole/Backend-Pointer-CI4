<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Services\ItensLocacoesService;
use App\Exceptions\ItensLocacoesException;
use App\Traits\TratarErroTrait;


class ItensLocacoesController extends BaseController
{
    use TratarErroTrait;
    private $itensLocacoesService;
    public function __construct()
    {
        $this->itensLocacoesService = new ItensLocacoesService();
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

            $order_by = $this->request->getGet('order_by');
            $order_by = !empty($order_by) ? $order_by : 'id';

            $order_dir = $this->request->getGet('order_dir');
            $order_dir = !empty($order_dir) ? $order_dir : 'asc';



            // Pega todos os filtros da URL (exceto limite/pagina)
            $filtros = $this->request->getGet();

            // Remove filtros inválidos
            unset(
                $filtros['limite'],
                $filtros['pagina'],
                $filtros['data_inicio'],
                $filtros['data_fim'],
                $filtros['order_by'],
                $filtros['order_dir']
            );

            $resultado = $this->itensLocacoesService->listar($limite, $pagina, $filtros, $data_inicio, $data_fim);

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
            $itemLocacao = $this->itensLocacoesService->buscar((int) $id);

            return $this->response->setJSON([
                'success' => true,
                'Registros' => $itemLocacao
            ]);

        } catch (ItensLocacoesException $e) {
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
            $itemLocacao = $this->itensLocacoesService->criar($data);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Item Locação criado com sucesso',
                'registro' => $itemLocacao
            ])->setStatusCode(201);

        } catch (ItensLocacoesException $e) {
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

            $itemLocacao = $this->itensLocacoesService->atualizar((int) $id, $data);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Item Locação atualizado com sucesso',
                'registro' => $itemLocacao
            ]);

        } catch (ItensLocacoesException $e) {
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
            $this->itensLocacoesService->deletar((int) $id);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Item Locação deletado com sucesso'
            ]);

        } catch (ItensLocacoesException $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ])->setStatusCode($e->getCode());

        } catch (\Exception $e) {
            return $this->tratarErro($e);
        }
    }
    // private function tratarErro(\Exception $e): \CodeIgniter\HTTP\Response
    // {
    //     log_message('error', '[ClientesController] ' . $e->getMessage());

    //     return $this->response->setJSON([
    //         'success' => false,
    //         'message' => 'Erro interno do servidor',
    //         'error' => ENVIRONMENT === 'development' ? $e->getMessage() : null
    //     ])->setStatusCode(500);
    // }
}
