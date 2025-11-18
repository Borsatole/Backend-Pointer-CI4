<?php

namespace App\Services;

use App\Models\ClienteModel;
use App\Exceptions\MessagesException;
use App\Models\EnderecosModel;
use Config\Database;



class ClienteService
{


    /** 🔹 Nome do Model usado pelo Service */
    private ClienteModel $model;

    private EnderecosModel $enderecosModel;
    private $db;

    public function __construct()
    {
        $this->model = new ClienteModel();
        $this->enderecosModel = new EnderecosModel();
        $this->db = Database::connect();
    }

    public function listar(array $params): array
    {
        $registro = isset($params['pagina'], $params['limite'])
            ? $this->model->listarComPaginacao($params)
            : $this->model->listarSemPaginacao($params);

        foreach ($registro['registros'] as $i => $cliente) {
            $registro['registros'][$i]['enderecos'] =
                $this->enderecosModel->buscarPorCliente($cliente['id']) ?? [];
        }

        return $registro;
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
        $this->model->buscarPorId($id)
            ?? throw MessagesException::naoEncontrado($id);

        if (!$this->model->deletar($id)) {
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

