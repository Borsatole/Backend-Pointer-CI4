<?php

namespace App\Models;

use CodeIgniter\Model;

class PermissoesModel extends Model
{
    protected $table = 'permissoes';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = ['slug', 'descricao', 'created_at', 'updated_at'];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [
        'id' => 'int',
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


    public function usuarioTemPermissao($nivelId, $slug)
    {
        return $this->select('np.allow')
            ->join('niveis_permissoes np', 'np.permissao_id = permissoes.id')
            ->where('np.nivel_id', $nivelId)
            ->where('permissoes.slug', $slug)
            ->where('np.allow', 1)
            ->first() !== null;
    }

    public function BuscarPermissoesPeloNivel($nivelId)
{
    $permissoes = $this->select('
            permissoes.id,
            permissoes.slug,
            permissoes.descricao,
            permissoes.created_at,
            permissoes.updated_at,
            np.allow
        ')
        ->join('niveis_permissoes np', 'np.permissao_id = permissoes.id', 'inner')
        ->where('np.nivel_id', $nivelId)
        ->orderBy('permissoes.slug', 'ASC')
        ->findAll();

    // Agrupar permissões por módulo
    $permissoesAgrupadas = [];
    
    foreach ($permissoes as $permissao) {
        // Extrair o módulo do slug (parte antes do ponto)
        $partes = explode('.', $permissao['slug']);
        $modulo = $partes[0] ?? 'outros';
        
        // Criar o array do módulo se não existir
        if (!isset($permissoesAgrupadas[$modulo])) {
            $permissoesAgrupadas[$modulo] = [];
        }
        
        // Adicionar a permissão ao módulo
        $permissoesAgrupadas[$modulo][] = [
            'id' => $permissao['id'],
            'slug' => $permissao['slug'],
            'descricao' => $permissao['descricao'],
            'created_at' => $permissao['created_at'],
            'updated_at' => $permissao['updated_at'],
            'allow' => (bool)$permissao['allow']
        ];
    }
    
    return $permissoesAgrupadas;
}


}
