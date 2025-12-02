<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Traits\PaginacaoTrait;
use App\Traits\CrudTrait;

class VistoriasModel extends Model
{
    use PaginacaoTrait, CrudTrait;
    protected $table = 'vistorias';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'id_condominio',
        'data_vistoria',
        'responsavel',
        'observacao_geral',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [
        'id' => 'int',
        'id_condominio' => 'int',
        'responsavel' => 'int',
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

    public function buscaComNome()
    {
        $this->builder()
            ->select('vistorias.*, condominios.nome AS condominio_nome, usuarios.nome AS responsavel_nome')
            ->join('usuarios', 'usuarios.id = vistorias.responsavel', 'left')
            ->join('condominios', 'condominios.id = vistorias.id_condominio', 'left');

        return $this;
    }

    public function buscaVistoriaPeloId(int $id): self
    {
        $this->builder()
            ->select('vistorias.*, condominios.nome AS condominio_nome, usuarios.nome AS responsavel_nome')
            ->join('condominios', 'condominios.id = vistorias.id_condominio', 'left')
            ->join('usuarios', 'usuarios.id = vistorias.responsavel', 'left')
            ->where('vistorias.id', $id);

        return $this;
    }

}
