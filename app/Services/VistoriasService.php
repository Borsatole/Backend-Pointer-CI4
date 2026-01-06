<?php

namespace App\Services;


use App\Exceptions\MessagesException;
use App\Models\VistoriasModel;
use App\Models\ItensParaVistoriaModel;
use App\Models\ItensVistoriados;
use App\Models\CondominiosModel;
use Config\Database;



class VistoriasService
{
    private VistoriasModel $model;
    private ItensParaVistoriaModel $itensParaVistoria;
    private ItensVistoriados $itensVistoriados;
    private CondominiosModel $condominios;
    private $db;
    

    public function __construct()
    {
        $this->model = new VistoriasModel();
        $this->itensParaVistoria = new ItensParaVistoriaModel();
        $this->itensVistoriados = new ItensVistoriados();
        $this->condominios = new CondominiosModel();
        $this->db = Database::connect();
        helper('email');
    }

    public function listar(array $params): array
    {
        $registros = $this->model
            ->comItens()
            ->listarComPaginacao($params);

        // adiciona itens vistoriados com nome
        foreach ($registros['registros'] as &$vistoria) {
            $vistoria['itens_vistoriados'] = $this->itensVistoriados
                ->listarPorVistoria($vistoria['id']);
        }

        return $registros;
    }

    public function buscar(int $id): array
    {
        // $registro = $this->model->buscarPorId($id);
        $registro = $this->model->buscaVistoriaPeloId($id)->first();

        if (!$registro) {
            throw MessagesException::naoEncontrado($id);
        }

        // adiciona itens vistoriados com nome
        $registro['itens_vistoriados'] = $this->itensVistoriados
            ->listarPorVistoria($id);


        return $registro;
    }

    public function criar(array $dados): array
    {
        $this->validarCampoObrigatorio($dados, 'id_condominio');

        $permitidos = $this->model->allowedFields;
        $dadosCriar = $this->filtrarCamposPermitidos($dados, $permitidos);

        if (empty($dadosCriar)) {
            throw MessagesException::erroCriar(['Nenhum campo válido foi enviado.']);
        }

        $itens_vistoriados = $dados['itens_vistoriados'];

        if (empty($itens_vistoriados)) {
            throw MessagesException::erroCriar(['Nenhum item vistoriado foi enviado.']);
        }

        $this->db->transStart();

        if (!$this->model->criar($dadosCriar)) {
            throw MessagesException::erroCriar($this->model->errors());
        }

        $id = $this->model->getInsertID();

        foreach ($itens_vistoriados as $item) {

            $dadosItem = [
                'id_vistoria' => $id,
                'id_item_condominio' => $item['id'],
                'situacao_encontrada' => $item['situacao_encontrada'] ?? null,
                'observacoes' => $item['observacoes'] ?? null,
            ];

            if ($dadosItem['situacao_encontrada']) {
                $this->itensParaVistoria->atualizar(
                $item['id'],
                [
                    'situacao' => $dadosItem['situacao_encontrada'],
                    'ultima_vistoria' => date('Y-m-d H:i:s'),
                ]
            );
            }

            

            

            
            $this->itensVistoriados->criar($dadosItem);
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

        // Atualiza itens vistoriados
        if (!empty($itens_vistoriados)) {

            // remove itens antigos
            if (!$this->itensVistoriados->deletarPorVistoria($id)) {
                $this->db->transRollback();
                throw MessagesException::erroAtualizar(['Erro ao remover itens antigos.']);
            }

            // adiciona novos itens
            foreach ($itens_vistoriados as $item) {
                $item['id_vistoria'] = $id;


                if(!empty($item['situacao_encontrada'])){


                    if ($item['situacao_encontrada'] == 'Defeito') {

                        $condominio = $this->condominios->buscarPorId($registro['id_condominio']);
                        $item_condominio = $this->itensParaVistoria->buscarPorId($item['id_item_condominio']);
                        $vistoria = $this->model->buscarPorId($id);
                        $dataVistoriaBrasileira = date('d/m/Y', strtotime($vistoria['created_at']));
                    
                        enviarEmailSimples(
                            "borsatole@gmail.com",
                            "{$condominio['nome']}: {$item_condominio['nome_item']} precisa ser corrigido",
                            "A vistoria realizada no dia {$dataVistoriaBrasileira} 
                            detectou o seguinte item que precisa ser corrigido: {$item_condominio['nome_item']}"
                        );
                        
                    }
                    
                    

                     $this->itensParaVistoria->atualizar(
                        $item['id_item_condominio'],
                        [
                            'situacao' => $item['situacao_encontrada'] ?? "",
                            'ultima_vistoria' => date('Y-m-d H:i:s'),
                        ]
                    );
                }
               
                


                if (!$this->itensVistoriados->criar($item)) {
                    $this->db->transRollback();
                    throw MessagesException::erroAtualizar(['Erro ao adicionar item vistoriado.']);
                }
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

        if (!$this->itensVistoriados->deletarPorVistoria($id)) {
            $this->db->transRollback();
            throw MessagesException::erroDeletar();
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

