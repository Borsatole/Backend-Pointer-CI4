<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Traits\PaginacaoTrait;


class ClienteModel extends Model
{
    use PaginacaoTrait;

    protected $table = 'clientes';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected array $casts = [
        'id' => 'int',
    ];

    protected $allowedFields = [
        'nome',
        'razao_social',
        'telefone',
        'celular',
        'email',
        'cidade',
        'estado',
        'created_at',
        'updated_at'
    ];

    // timestamps automáticos (opcional)
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // validações (opcional)
    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = true;


    public function buscarPorId(int $id): ?array
    {
        return $this->find($id);
    }

}


