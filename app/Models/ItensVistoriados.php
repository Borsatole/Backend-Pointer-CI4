<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Traits\PaginacaoTrait;
use App\Traits\CrudTrait;

class ItensVistoriados extends Model
{
    use PaginacaoTrait, CrudTrait;
    protected $table = 'itensvistoriados';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'id_vistoria',
        'id_item_condominio',
        'situacao_encontrada',
        'observacoes'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [
        'id' => 'int',
        'id_vistoria' => 'int',
        'id_item_condominio' => 'int',

    ];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
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

    function deletarPorVistoria(int $id)
    {
        return $this->where('id_vistoria', $id)->delete();
    }

    public function listarPorVistoria($idVistoria)
    {
        return $this->select('
            itensvistoriados.*, 
            itensparavistorias.nome_item AS nome_item
        ')
            ->join('itensparavistorias', 'itensparavistorias.id = itensvistoriados.id_item_condominio', 'left')
            ->where('itensvistoriados.id_vistoria', $idVistoria)
            ->findAll();
    }

}
