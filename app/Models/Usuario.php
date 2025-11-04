<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Traits\PaginacaoTrait;

class Usuario extends Model
{
    use PaginacaoTrait;
    protected $table = 'usuarios';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $protectFields = true;
    protected $allowedFields = [
        'nome',
        'email',
        'senha',
        'ativo',
        'nivel',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [
        'ativo' => 'boolean',
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
        'nome' => 'required|min_length[3]|max_length[255]',
        'email' => 'required|valid_email|is_unique[usuarios.email,id,{id}]',
        'senha' => 'required|min_length[6]',
    ];
    protected $validationMessages = [
        'nome' => [
            'required' => 'O nome é obrigatório.',
            'min_length' => 'O nome deve ter no mínimo 3 caracteres.',
            'max_length' => 'O nome deve ter no máximo 255 caracteres.',
        ],
        'email' => [
            'required' => 'O email é obrigatório.',
            'valid_email' => 'O email deve ser válido.',
            'is_unique' => 'O email já está em uso.',
        ],
        'senha' => [
            'required' => 'A senha é obrigatória.',
            'min_length' => 'A senha deve ter no mínimo 6 caracteres.',
        ],
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


    public function restore($id = null)
    {
        if ($id === null) {
            return false;
        }
        return $this->update($id, ['deleted_at' => null]);
    }

    // public function listarComPaginacao(int $limite = 10, int $pagina = 1, array $filtros = []): array
    // {
    //     $usuarios = $this->orderBy('id', 'DESC');

    //     // pega os nomes das colunas da tabela
    //     $colunas = $this->db->getFieldNames($this->table);

    //     foreach ($filtros as $campo => $valor) {
    //         if (in_array($campo, $colunas)) {
    //             $usuarios->like($campo, $valor);
    //         }
    //     }

    //     return $usuarios->paginate($limite, 'default', $pagina);
    // }

}

