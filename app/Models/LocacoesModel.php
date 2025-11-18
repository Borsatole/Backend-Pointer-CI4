<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Traits\PaginacaoTrait;
use App\Traits\CrudTrait;

class LocacoesModel extends Model
{
    use PaginacaoTrait, CrudTrait;
    protected $table = 'locacoes';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;

    protected $allowedFields = [
        'cliente_id',
        'locacao_item_id',
        'endereco_id',
        'data_inicio',
        'data_fim',
        'preco_total',
        'forma_pagamento',
        'observacoes',
        'status',
    ];


    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [
        'id' => 'int',
        'cliente_id' => 'int',
        'locacao_item_id' => 'int',
        'endereco_id' => 'int',
    ];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [
        'cliente_id' => 'required|is_natural_no_zero',
        'locacao_item_id' => 'required|is_natural_no_zero',
        'endereco_id' => 'required|is_natural_no_zero',
        'forma_pagamento' => 'in_list[debito,credito,dinheiro]',
    ];
    protected $validationMessages = [
        'forma_pagamento' => '{field} deve ser um dos seguintes: debito,credito,dinheiro',
    ];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = [];
    protected $afterInsert = [];
    protected $beforeUpdate = [];
    protected $afterUpdate = [];
    protected $beforeFind = [];
    protected $afterFind = [];
    protected $beforeDelete = [];
    protected $afterDelete = [];


    public function buscarLocacaoPorItemId(int $itemId): ?array
    {
        return $this->select('locacoes.*, enderecos.*, clientes.nome as cliente_nome')
            ->join('enderecos', 'enderecos.id = locacoes.endereco_id')
            ->join('clientes', 'clientes.id = locacoes.cliente_id')
            ->where('locacoes.locacao_item_id', $itemId)
            ->where('locacoes.status', 'ativo')
            ->first();
    }

    public function verificaSeJaEstaLocado(int $itemId): ?array
    {
        return $this->select('locacoes.*')
            ->where('locacoes.locacao_item_id', $itemId)
            ->where('locacoes.status', 'ativo')
            ->first();
    }


}
