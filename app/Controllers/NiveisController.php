<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Services\NivelService;
use App\Exceptions\NivelException;
use App\Traits\TratarErroTrait;


class NiveisController extends BaseController
{
    use TratarErroTrait;
    private $nivelService;

    public function __construct()
    {
        // ✅ Agora APENAS o service é necessário
        $this->nivelService = new NivelService();
    }


    public function index()
    {
        try {
            $limite = intval($this->request->getVar('limite') ?? 10);
            $pagina = intval($this->request->getVar('pagina') ?? 1);

            // Pega todos os filtros da URL (exceto limite/pagina)
            $filtros = $this->request->getGet();
            unset($filtros['limite'], $filtros['pagina']);

            $resultado = $this->nivelService->listar($limite, $pagina, $filtros);

            return $this->response->setJSON([
                'success' => true,
                'registros' => $resultado['registros'],
                'paginacao' => $resultado['paginacao'],
                'filtros' => $filtros
            ]);

        } catch (\Exception $e) {
            return $this->tratarErro($e);
        }
    }

    /**
     * Busca um nível específico
     * GET /niveis/{id}
     */
    public function show($id = null)
    {
        try {
            $nivel = $this->nivelService->buscar((int) $id);

            return $this->response->setJSON([
                'success' => true,
                'Registros' => $nivel
            ]);

        } catch (NivelException $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ])->setStatusCode($e->getCode());

        } catch (\Exception $e) {
            return $this->tratarErro($e);
        }
    }

    /**
     * Cria um novo nível
     * POST /niveis
     * Body: { "nome": "Nome do Nível" }
     */
    public function create()
    {
        try {
            $data = $this->request->getJSON(true);
            $nivel = $this->nivelService->criar($data);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Nível criado com sucesso',
                'registro' => $nivel
            ])->setStatusCode(201);

        } catch (NivelException $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ])->setStatusCode($e->getCode());

        } catch (\Exception $e) {
            return $this->tratarErro($e);
        }
    }

    /**
     * Atualiza um nível existente
     * PUT /niveis/{id}
     * Body: { "nome": "Novo Nome", "permissoes": [...] }
     */
    public function update($id = null)
    {
        try {
            $data = $this->request->getJSON(true);

            // Se as permissões vieram como string JSON, decodifica
            if (isset($data['permissoes']) && is_string($data['permissoes'])) {
                $data['permissoes'] = json_decode($data['permissoes'], true);
            }

            $nivel = $this->nivelService->atualizar((int) $id, $data);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Nível atualizado com sucesso',
                'registro' => $nivel
            ]);

        } catch (NivelException $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ])->setStatusCode($e->getCode());

        } catch (\Exception $e) {
            return $this->tratarErro($e);
        }
    }

    /**
     * Deleta um nível
     * DELETE /niveis/{id}
     */
    public function delete($id = null)
    {
        try {
            $this->nivelService->deletar((int) $id);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Nível deletado com sucesso'
            ]);

        } catch (NivelException $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ])->setStatusCode($e->getCode());

        } catch (\Exception $e) {
            return $this->tratarErro($e);
        }
    }

    /**
     * Tratamento genérico de erros
     */
    // private function tratarErro(\Exception $e): \CodeIgniter\HTTP\Response
    // {
    //     log_message('error', '[NiveisController] ' . $e->getMessage());

    //     return $this->response->setJSON([
    //         'success' => false,
    //         'message' => 'Erro interno do servidor',
    //         'error' => ENVIRONMENT === 'development' ? $e->getMessage() : null
    //     ])->setStatusCode(500);
    // }
}