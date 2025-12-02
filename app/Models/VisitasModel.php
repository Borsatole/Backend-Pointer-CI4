<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Traits\PaginacaoTrait;
use App\Traits\CrudTrait;

class VisitasModel extends Model
{
    use PaginacaoTrait, CrudTrait;
    protected $table = 'visitas';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'id_condominio',
        'entrada',
        'saida',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [
        'id' => 'int',
        'id_condominio' => 'int',

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


    public function buscaComNomeCondominio()
    {
        $this->builder()
            ->select('visitas.*, condominios.nome AS condominio_nome')
            ->join('condominios', 'condominios.id = visitas.id_condominio');


        return $this;
    }



    public function buscaCondominioPeloId(int $id): self
    {
        $this->builder()
            ->select('visitas.*, condominios.nome AS condominio_nome')
            ->join('condominios', 'condominios.id = visitas.id_condominio')
            ->where('visitas.id', $id);

        $registro = $this;

        return $this;
    }

}
