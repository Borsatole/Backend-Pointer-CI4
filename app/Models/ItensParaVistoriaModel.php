<?php

namespace App\Models;

use App\Traits\CrudTrait;
use App\Traits\PaginacaoTrait;
use CodeIgniter\Model;


class ItensParaVistoriaModel extends Model
{
    use PaginacaoTrait, CrudTrait;

    protected $table = 'itensparavistorias';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'id_condominio',
        'nome_item',
        'periodo_dias',
        'ultima_vistoria',
        'situacao'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [
        'id' => 'int',
        'id_condominio' => 'int',
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


    public function buscarPorCondominio($id_condominio)
    {
        return $this->where('id_condominio', $id_condominio)->findAll();
    }
}
