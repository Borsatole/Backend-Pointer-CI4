<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Traits\PaginacaoTrait;

class NiveisModel extends Model
{


    protected $table = 'niveis';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = ['nome'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'nome' => 'required|min_length[3]|max_length[100]',
    ];

    protected $validationMessages = [
        'nome' => [
            'required' => 'O campo nome é obrigatório',
            'min_length' => 'O nome deve ter no mínimo 3 caracteres',
            'max_length' => 'O nome deve ter no máximo 100 caracteres',
        ]
    ];

    protected $skipValidation = false;
    protected $allowCallbacks = true;

    // ========================================
    // MÉTODOS SIMPLES - APENAS ACESSO A DADOS
    // ========================================

    /**
     * Verifica se existe um nível com o nome informado
     * 
     * @param string $nome
     * @param int|null $idExcluir ID para excluir da busca (útil no update)
     * @return array|null
     */
    public function buscarPorNome(string $nome, ?int $idExcluir = null): ?array
    {
        $builder = $this->where('nome', $nome);

        if ($idExcluir !== null) {
            $builder->where('id !=', $idExcluir);
        }

        return $builder->first();
    }

    /**
     * Verifica se existe um nível pelo nome (retorna boolean)
     * 
     * @param string $nome
     * @param int|null $idExcluir
     * @return bool
     */
    public function existePorNome(string $nome, ?int $idExcluir = null): bool
    {
        return $this->buscarPorNome($nome, $idExcluir) !== null;
    }

    /**
     * Busca nível por ID
     * 
     * @param int $id
     * @return array|null
     */
    public function buscarPorId(int $id): ?array
    {
        return $this->find($id);
    }

    /**
     * Cria um novo nível (APENAS inserção no banco)
     * 
     * @param array $dados
     * @return int|false ID do nível criado ou false
     */
    public function criar(array $dados)
    {
        if ($this->insert($dados)) {
            return $this->getInsertID();
        }
        return false;
    }

    /**
     * Atualiza um nível
     * 
     * @param int $id
     * @param array $dados
     * @return bool
     */
    public function atualizar(int $id, array $dados): bool
    {
        return $this->update($id, $dados);
    }

    /**
     * Deleta um nível
     * 
     * @param int $id
     * @return bool
     */
    public function deletar(int $id): bool
    {
        return $this->delete($id);
    }

    /**
     * Lista níveis com paginação
     * 
     * @param int $limite
     * @param int $pagina
     * @return array
     */
    public function listarComPaginacao(int $limite = 10, int $pagina = 1): array
    {
        return $this->orderBy('id', 'DESC')
            ->paginate($limite, 'default', $pagina);
    }
}