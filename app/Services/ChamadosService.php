<?php

namespace App\Services;

use CodeIgniter\HTTP\Files\UploadedFile;

use App\Exceptions\MessagesException;
use App\Models\ChamadosModel;
use App\Models\ChamadosImgModel;

use App\Traits\ImagesTrait;
use Config\Database;



class ChamadosService
{
    private ChamadosModel $model;
    private ChamadosImgModel $chamadosImgModel;
    use ImagesTrait;
    private $db;


    public function __construct()
    {
        $this->model = new ChamadosModel();
        $this->chamadosImgModel = new ChamadosImgModel();

        $this->db = Database::connect();
        helper('email');
    }

    public function listar(array $params): array
    {
        $registro = isset($params['pagina'], $params['limite'])
            ? $this->model->buscaPersonalizada()->listarComPaginacao($params)
            : $this->model->listarSemPaginacao($params);

        foreach ($registro['registros'] as &$item) {
            $item['imagens'] = (new ChamadosImgModel())
                ->where('chamado_id', $item['id'])
                ->findAll();
        }

        return $registro;
    }

    public function buscar(int $id): array
    {
        $registro = $this->model->buscarPorId($id);

        $registro['imagens'] = (new ChamadosImgModel())
            ->where('chamado_id', $id)
            ->findAll();

        if (!$registro) {
            throw MessagesException::naoEncontrado($id);
        }

        return $registro;
    }

    public function criar(array $dados, array $files): array
    {

        // $this->validarCampoObrigatorio($dados, 'locacao_item_id');

        $permitidos = $this->model->allowedFields;
        $dadosCriar = $this->filtrarCamposPermitidos($dados, $permitidos);

        if (empty($dadosCriar)) {
            throw MessagesException::erroCriar(['Nenhum campo válido foi enviado.']);
        }


        $this->db->transStart();

        if (!$this->model->criar($dadosCriar)) {
            throw MessagesException::erroCriar($this->model->errors());
        }

        $id = $this->model->getInsertID();

        // 🔹 Upload das imagens
        if (isset($files['imagens'])) {
            $this->salvarImagens($files['imagens'], $id);

        }

        $this->db->transComplete();

        if (!$this->db->transStatus()) {
            throw MessagesException::erroAtualizar(['Erro na transação']);
        }


        return $this->buscar($id);
    }

    public function atualizar(int $id, array $dados): array
    {
        // Verifica se registro existe
        $registro = $this->model->buscarPorId($id)
            ?? throw MessagesException::naoEncontrado($id);

        $itens_vistoriados = $dados['itens_vistoriados'] ?? null;

        $permitidos = $this->model->allowedFields;
        $dadosAtualizar = $this->filtrarCamposPermitidos($dados, $permitidos);

        // Inicia transação
        $this->db->transStart();

        // Atualiza vistoria somente se houver campos válidos
        if (!empty($dadosAtualizar)) {
            if (!$this->model->atualizar($id, $dadosAtualizar)) {
                $this->db->transRollback();
                throw MessagesException::erroAtualizar($this->model->errors());
            }
        }

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            throw MessagesException::erroAtualizar(['Falha ao atualizar vistoria.']);
        }

        // Retorna o registro completo atualizado
        return $this->buscar($id);
    }

    public function deletar(int $id): bool
    {
        $this->db->transStart();

        $registro = $this->model->buscarPorId($id)
            ?? throw MessagesException::naoEncontrado($id);

        // $locacao_item_id = $registro['locacao_item_id'];

        $imagens = $this->chamadosImgModel->where('chamado_id', $id)->findAll();
        foreach ($imagens as $imagem) {

            $this->deletarImagens($imagem['arquivo']);
            $this->chamadosImgModel->delete($imagem['id']);
        }

        if (!$this->model->deletar($id)) {
            $this->db->transRollback();
            throw MessagesException::erroDeletar();
        }



        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            throw MessagesException::erroDeletar();
        }

        return true;
    }

    public function deletarImagem(int $id): void
    {
        $imgModel = new ChamadosImgModel();

        $imagem = $imgModel->find($id);

        if (!$imagem) {
            throw MessagesException::naoEncontrado($id);
        }

        $this->deletarImagens($imagem['arquivo']);

        if (!$imgModel->delete($id)) {
            throw MessagesException::erroDeletar();
        }

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

    // private function salvarImagens($imagens): void
    // {
    //     if ($imagens instanceof UploadedFile) {
    //         $imagens = [$imagens];
    //     }

    //     foreach ($imagens as $imagem) {

    //         if (!$imagem->isValid()) {
    //             continue;
    //         }

    //         if ($imagem->hasMoved()) {
    //             continue;
    //         }

    //         $nome = $imagem->getRandomName();

    //         $imagem->move(WRITEPATH . 'uploads/chamados', $nome);

    //         // $this->db->table('chamados_imagens')->insert([
    //         //     'chamado_id' => $chamadoId,
    //         //     'arquivo' => $nome,
    //         //     'created_at' => date('Y-m-d H:i:s')
    //         // ]);
    //     }
    // }

}

