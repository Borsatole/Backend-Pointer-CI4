<?php

namespace App\Services;

use App\Models\ClienteModel;
use App\Exceptions\ClienteException;
use Config\Database;

class ClienteService
{
    private ClienteModel $clientesModel;
    private $db;

    public function __construct()
    {
        $this->clientesModel = new ClienteModel();
        $this->db = Database::connect();
    }

    /**
     * Lista clientes com paginação e filtros opcionais (datas inclusas)
     */
    public function listar(int $limite = 10, int $pagina = 1, array $filtros = [], ?string $data_inicio = null, ?string $data_fim = null): array
    {
        return $this->clientesModel->listarComPaginacao(
            $limite,
            $pagina,
            $filtros,
            $data_inicio,
            $data_fim
        );
    }

    /**
     * Busca um cliente pelo ID
     */
    public function buscar(int $id): array
    {
        $cliente = $this->clientesModel->buscarPorId($id);

        if (!$cliente) {
            throw ClienteException::naoEncontrado();
        }

        return $cliente;
    }

    /**
     * Cria um novo cliente
     */
    public function criar(array $dados): array
    {
        if (empty($dados['nome'])) {
            throw ClienteException::nomeObrigatorio();
        }

        $this->db->transStart();

        try {
            $this->clientesModel->insert($dados);
            $clienteId = $this->clientesModel->getInsertID();

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw ClienteException::erroCriar();
            }

            return $this->buscar($clienteId);

        } catch (\Throwable $e) {
            $this->db->transRollback();
            throw $e;
        }
    }

    /**
     * Atualiza um cliente existente
     */
    public function atualizar(int $id, array $dados): array
    {
        $clienteExistente = $this->clientesModel->buscarPorId($id);

        if (!$clienteExistente) {
            throw ClienteException::naoEncontrado();
        }

        $this->db->transStart();

        try {
            if (!$this->clientesModel->update($id, $dados)) {
                throw ClienteException::erroAtualizar($this->clientesModel->errors());
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw ClienteException::erroAtualizar();
            }

            return $this->buscar($id);

        } catch (\Throwable $e) {
            $this->db->transRollback();
            throw $e;
        }
    }

    /**
     * Exclui um cliente
     */
    public function deletar(int $id): bool
    {
        $cliente = $this->clientesModel->buscarPorId($id);

        if (!$cliente) {
            throw ClienteException::naoEncontrado();
        }

        $this->db->transStart();

        try {
            if (!$this->clientesModel->delete($id)) {
                throw ClienteException::erroDeletar();
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw ClienteException::erroDeletar();
            }

            return true;

        } catch (\Throwable $e) {
            $this->db->transRollback();
            throw $e;
        }
    }
}
