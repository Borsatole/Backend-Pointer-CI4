<?php

namespace App\Models;

use App\Traits\CrudTrait;
use App\Traits\PaginacaoTrait;
use CodeIgniter\Model;

class ChamadosModel extends Model
{
    use PaginacaoTrait, CrudTrait;
    protected $table = 'chamados';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'id_condominio',
        'titulo',
        'descricao',
        'responsavel',
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
    protected $validationRules = [
        // 'id_condominio' => 'required|is_natural_no_zero',
        // 'titulo' => 'required|string|max_length[255]',
        // 'descricao' => 'required|string',
        // 'responsavel' => 'required|is_natural_no_zero',
    ];
    protected $validationMessages = [

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


    public function buscaPersonalizada()
    {
        return $this->select('chamados.*, condominios.nome AS condominio_nome, usuarios.nome AS responsavel_nome')
            ->join('condominios', 'condominios.id = chamados.id_condominio', 'left')
            ->join('usuarios', 'usuarios.id = chamados.responsavel', 'left');
    }


    // public function buscaChamadoPeloId(int $id): self
    // {
    //     $this->builder()
    //         ->select('chamados.*, condominios.nome AS condominio_nome, usuarios.nome AS responsavel_nome')
    //         ->join('condominios', 'condominios.id = chamados.id_condominio', 'left')
    //         ->join('usuarios', 'usuarios.id = chamados.responsavel', 'left')
    //         ->where('chamados.id', $id);

    //     return $this;
    // }
}
