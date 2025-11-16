<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Traits\PaginacaoTrait;
use App\Traits\CrudTrait;


class EnderecosModel extends Model
{
    use PaginacaoTrait;
    use CrudTrait;

    protected $table = 'enderecos';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'cliente_id',
        'cep',
        'logradouro',
        'numero',
        'complemento',
        'bairro',
        'cidade',
        'estado',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [
        'id' => 'int',
        'cliente_id' => 'int',
    ];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [];
    protected $validationMessages = [];
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


    public function deletarPorCliente(int $clienteId): bool
    {
        return $this->where('cliente_id', $clienteId)->delete();
    }

    public function buscarPorCliente(int $clienteId): array
    {
        return $this->where('cliente_id', $clienteId)->findAll();
    }


}
