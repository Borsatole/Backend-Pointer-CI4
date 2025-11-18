<?php

namespace App\Services;

use App\Models\LocacoesModel;
use App\Models\ItensLocacoesModel;
use App\Exceptions\MessagesException;
use Config\Database;

class LocacoesService
{
    private LocacoesModel $model;
    private ItensLocacoesModel $itemModel;
    private $db;

    public function __construct()
    {
        $this->model = new LocacoesModel();
        $this->itemModel = new ItensLocacoesModel();
        $this->db = Database::connect();
    }


    public function listar(array $params): array
    {
        return isset($params['pagina'], $params['limite'])
            ? $this->model->listarComPaginacao($params)
            : $this->model->listarSemPaginacao($params);
    }


    public function buscar(int $id): array
    {
        $registro = $this->model->buscarPorId($id);

        if (!$registro) {
            throw MessagesException::naoEncontrado($id);
        }

        return $registro;
    }


    public function criar(array $dados): array
    {
        $this->validarCampoObrigatorio($dados, 'locacao_item_id');

        $permitidos = $this->model->allowedFields;
        $dadosCriar = $this->filtrarCamposPermitidos($dados, $permitidos);

        if (empty($dadosCriar)) {
            throw MessagesException::erroCriar(['Nenhum campo válido foi enviado.']);
        }

        $locacao_item_id = $dadosCriar['locacao_item_id'];

        if ($this->model->verificaSeJaEstaLocado($locacao_item_id)) {
            throw MessagesException::erroGenerico('Item já está locado.');
        }

        $this->itemModel->mudarStatusItem($locacao_item_id, 'locado');

        if (!$this->model->criar($dadosCriar)) {
            throw MessagesException::erroCriar($this->model->errors());
        }

        $id = $this->model->getInsertID();
        return $this->buscar($id);
    }

    public function atualizar(int $id, array $dados): array
    {
        $registro = $this->model->buscarPorId($id)
            ?? throw MessagesException::naoEncontrado($id);

        $permitidos = $this->model->allowedFields;

        $dadosAtualizar = $this->filtrarCamposPermitidos($dados, $permitidos);

        $status = $dadosAtualizar['status'] ?? null;

        if ($status === 'finalizado') {
            $locacao_item_id = $registro['locacao_item_id'];
            $this->itemModel->mudarStatusItem($locacao_item_id, 'disponivel');
        }

        if (empty($dadosAtualizar)) {
            throw MessagesException::erroAtualizar(['Nenhum campo válido foi enviado.']);
        }

        if (!$this->model->atualizar($id, $dadosAtualizar)) {
            throw MessagesException::erroAtualizar($this->model->errors());
        }

        return $this->buscar($id);
    }

    public function deletar(int $id): bool
    {
        $this->db->transStart();

        $registro = $this->model->buscarPorId($id)
            ?? throw MessagesException::naoEncontrado($id);

        $locacao_item_id = $registro['locacao_item_id'];

        if (!$this->model->deletar($id)) {
            $this->db->transRollback();
            throw MessagesException::erroDeletar();
        }

        if (!$this->itemModel->mudarStatusItem($locacao_item_id, 'disponivel')) {
            $this->db->transRollback();
            throw MessagesException::erroDeletar();
        }

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            throw MessagesException::erroDeletar();
        }

        return true;
    }


    private function validarCampoObrigatorio(array $dados, string $campo): void
    {
        if (empty($dados[$campo])) {
            throw MessagesException::campoObrigatorio($campo);
        }
    }

    private function filtrarCamposPermitidos(array $dados, array $permitidos): array
    {
        return array_intersect_key($dados, array_flip($permitidos));
    }
}

