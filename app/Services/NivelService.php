<?php

namespace App\Services;

use App\Models\NiveisModel;
use App\Models\PermissoesModel;
use App\Models\NiveisXPermissoes;
use App\Exceptions\NivelException;

class NivelService
{
    private $niveisModel;
    private $permissoesModel;
    private $niveisXPermissoesModel;
    private $db;

    public function __construct()
    {
        $this->niveisModel = new NiveisModel();
        $this->permissoesModel = new PermissoesModel();
        $this->niveisXPermissoesModel = new NiveisXPermissoes();
        $this->db = \Config\Database::connect();
    }

    /**
     * Lista todos os níveis com paginação e permissões
     */
    public function listar(int $limite = 10, int $pagina = 1, array $filtros = []): array
    {
        $niveis = $this->niveisModel->listarComPaginacao($limite, $pagina);
        $pager = $this->niveisModel->pager;

        // Adiciona permissões a cada nível
        foreach ($niveis as &$nivel) {
            $nivel['permissoes'] = $this->permissoesModel->BuscarPermissoesPeloNivel($nivel['id']);
        }

        return [
            'registros' => $niveis,
            'paginacao' => [
                'total' => $pager->getTotal(),
                'porPagina' => $limite,
                'paginaAtual' => $pager->getCurrentPage(),
                'ultimaPagina' => $pager->getPageCount(),
            ]
        ];
    }

    /**
     * Busca um nível específico com suas permissões
     */
    public function buscar(int $id): array
    {
        $nivel = $this->niveisModel->buscarPorId($id);

        if (!$nivel) {
            throw NivelException::naoEncontrado();
        }

        // Adiciona as permissões
        $nivel['permissoes'] = $this->permissoesModel->BuscarPermissoesPeloNivel($nivel['id']);

        return $nivel;
    }

    /**
     * Cria um novo nível com todas as permissões (allow = 0)
     * 
     * AQUI é onde a ORQUESTRAÇÃO acontece:
     * 1. Valida
     * 2. Cria nível
     * 3. Vincula permissões
     * 4. Retorna nível completo
     */
    public function criar(array $dados): array
    {
        // 1️⃣ Validação de negócio
        if (empty($dados['nome'])) {
            throw NivelException::nomeObrigatorio();
        }

        // 2️⃣ Verifica duplicidade
        if ($this->niveisModel->existePorNome($dados['nome'])) {
            throw NivelException::nomeDuplicado();
        }

        // 3️⃣ Inicia transação (garante atomicidade)
        $this->db->transStart();

        try {
            // 4️⃣ Cria o nível (Model apenas insere)
            $nivelId = $this->niveisModel->criar(['nome' => $dados['nome']]);

            if (!$nivelId) {
                throw NivelException::erroCriar($this->niveisModel->errors());
            }

            // 5️⃣ Vincula TODAS as permissões com allow = 0 (lógica de negócio)
            $this->vincularTodasPermissoes($nivelId);

            // 6️⃣ Finaliza transação
            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw NivelException::erroCriar();
            }

            // 7️⃣ Retorna o nível completo com permissões
            return $this->buscar($nivelId);

        } catch (\Exception $e) {
            $this->db->transRollback();
            throw $e;
        }
    }

    /**
     * Atualiza um nível existente (nome e/ou permissões)
     */
    public function atualizar(int $id, array $dados): array
    {
        // 1️⃣ Validação
        if (empty($dados['nome'])) {
            throw NivelException::nomeObrigatorio();
        }

        // 2️⃣ Verifica se existe
        $nivelExistente = $this->niveisModel->buscarPorId($id);
        if (!$nivelExistente) {
            throw NivelException::naoEncontrado();
        }

        // 3️⃣ Verifica duplicidade (exceto o próprio registro)
        if ($this->niveisModel->existePorNome($dados['nome'], $id)) {
            throw NivelException::nomeDuplicado();
        }

        // 4️⃣ Inicia transação
        $this->db->transStart();

        try {
            // 5️⃣ Atualiza o nome
            if (!$this->niveisModel->atualizar($id, ['nome' => $dados['nome']])) {
                throw NivelException::erroAtualizar($this->niveisModel->errors());
            }

            // 6️⃣ Atualiza permissões se fornecidas
            if (isset($dados['permissoes']) && is_array($dados['permissoes'])) {
                $this->atualizarPermissoes($id, $dados['permissoes']);
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw NivelException::erroAtualizar();
            }

            return $this->buscar($id);

        } catch (\Exception $e) {
            $this->db->transRollback();
            throw $e;
        }
    }

    /**
     * Deleta um nível e suas permissões
     */
    public function deletar(int $id): bool
    {
        // 1️⃣ Verifica se existe
        if (!$this->niveisModel->buscarPorId($id)) {
            throw NivelException::naoEncontrado();
        }

        // 2️⃣ Inicia transação
        $this->db->transStart();

        try {
            // 3️⃣ Remove vínculos de permissões primeiro
            $this->niveisXPermissoesModel->where('nivel_id', $id)->delete();

            // 4️⃣ Remove o nível
            if (!$this->niveisModel->deletar($id)) {
                throw NivelException::erroDeletar();
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw NivelException::erroDeletar();
            }

            return true;

        } catch (\Exception $e) {
            $this->db->transRollback();
            throw $e;
        }
    }

    // ========================================
    // MÉTODOS PRIVADOS (LÓGICA INTERNA)
    // ========================================

    /**
     * Vincula todas as permissões ao nível com allow = 0
     * Esta é uma REGRA DE NEGÓCIO, por isso está no Service
     */
    private function vincularTodasPermissoes(int $nivelId): void
    {
        $todasPermissoes = $this->permissoesModel->findAll();

        foreach ($todasPermissoes as $permissao) {
            $this->niveisXPermissoesModel->insert([
                'nivel_id' => $nivelId,
                'permissao_id' => $permissao['id'],
                'allow' => 0
            ]);
        }
    }

    /**
     * Atualiza as permissões de um nível
     * Remove antigas e insere novas
     */
    private function atualizarPermissoes(int $nivelId, array $permissoes): void
    {
        // Remove permissões antigas
        $this->niveisXPermissoesModel->where('nivel_id', $nivelId)->delete();

        // Insere novas permissões
        foreach ($permissoes as $permissao) {
            $this->niveisXPermissoesModel->insert([
                'nivel_id' => $nivelId,
                'permissao_id' => $permissao['id'],
                'allow' => $permissao['allow'] ?? 0
            ]);
        }
    }
}