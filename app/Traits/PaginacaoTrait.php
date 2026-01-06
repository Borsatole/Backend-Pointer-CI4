<?php

namespace App\Traits;

use CodeIgniter\Database\Exceptions\DatabaseException;

trait PaginacaoTrait
{
    /**
     * Aplica filtros seguros na query
     */
    public function aplicarFiltros($builder, array $params, string $campoData = 'created_at')
    {
        // 🔒 Obtém campos permitidos automaticamente do Model
        $camposPermitidos = $this->obterCamposPermitidos();

        $campoBase = str_contains($campoData, '.')
            ? explode('.', $campoData)[1]
            : $campoData;

        if (!in_array($campoBase, $camposPermitidos, true)) {
            throw new DatabaseException("Campo de data inválido: {$campoData}");
        }

        // 🔹 FILTROS EXATOS (WHERE =) - PRIORIDADE ALTA
        if (!empty($params['filtros_exatos']) && is_array($params['filtros_exatos'])) {
            foreach ($params['filtros_exatos'] as $campo => $valor) {
                // 🔒 Valida se o campo existe na tabela
                if (!in_array($campo, $camposPermitidos, true)) {
                    continue; // Ignora campos não permitidos
                }

                if ($valor !== '' && $valor !== null) {
                    $builder->where($campo, $valor); // ✅ Usa WHERE ao invés de LIKE
                }
            }
        }

        // 🔹 FILTROS DINÂMICOS COM LIKE
        if (!empty($params['filtros']) && is_array($params['filtros'])) {
            foreach ($params['filtros'] as $campo => $valor) {
                // 🔒 Valida se o campo existe na tabela
                if (!in_array($campo, $camposPermitidos, true)) {
                    continue; // Ignora campos não permitidos
                }

                if ($valor !== '' && $valor !== null) {
                    $builder->like($campo, $valor);
                }
            }
        }

        // 🔹 FILTRO POR DIA (data_inicio OU data_fim)
        if (!empty($params['dia']) && $this->validarData($params['dia'])) {
            $builder->groupStart()
                ->where('data_inicio', $params['dia'])
                ->orWhere('data_fim', $params['dia'])
                ->where('status', 'ativo')
                ->groupEnd();
        }

        // 🔹 FILTRO POR DATA COM VALIDAÇÃO
        if (!empty($params['data_minima']) && $this->validarData($params['data_minima'])) {
            $builder->where("{$campoData} >=", $params['data_minima'] . ' 00:00:00');
        }

        if (!empty($params['data_maxima']) && $this->validarData($params['data_maxima'])) {
            $builder->where("{$campoData} <=", $params['data_maxima'] . ' 23:59:59');
        }

        // 🔹 ORDENAÇÃO COM VALIDAÇÃO
        if (!empty($params['order_by'])) {

            $campoOrder = $params['order_by'];

            // Se não veio qualificado, usa o mesmo prefixo do campoData
            if (!str_contains($campoOrder, '.') && str_contains($campoData, '.')) {
                $tabela = explode('.', $campoData)[0];
                $campoOrder = "{$tabela}.{$campoOrder}";
            }

            $campoBase = str_contains($campoOrder, '.')
                ? explode('.', $campoOrder)[1]
                : $campoOrder;

            if (in_array($campoBase, $camposPermitidos, true)) {
                $dir = strtoupper($params['order_dir'] ?? 'ASC');
                $dir = in_array($dir, ['ASC', 'DESC'], true) ? $dir : 'ASC';

                $builder->orderBy($campoOrder, $dir);
            }
        }


        return $builder;
    }

    /**
     * 🔒 Obtém campos permitidos do Model
     */
    protected function obterCamposPermitidos(): array
    {
        $campos = [];

        // Opção 1: Se o Model define $allowedFields (comum no CodeIgniter)
        if (property_exists($this, 'allowedFields') && !empty($this->allowedFields)) {
            $campos = $this->allowedFields;
        }

        // Opção 2: Busca campos diretamente da estrutura da tabela (cache recomendado)
        if (empty($campos)) {
            $campos = $this->buscarCamposDaTabela();
        }

        // Sempre inclui campos de timestamp
        $camposComuns = ['id', 'created_at', 'updated_at', 'deleted_at'];
        $campos = array_unique(array_merge($campos, $camposComuns));

        return $campos;
    }

    /**
     * 🔒 Busca campos da tabela via database
     * ⚠️ Use cache em produção!
     */
    protected function buscarCamposDaTabela(): array
    {
        static $cache = [];

        $tabela = $this->table ?? '';

        if (empty($tabela)) {
            return [];
        }

        // Cache simples em memória
        if (isset($cache[$tabela])) {
            return $cache[$tabela];
        }

        try {
            $campos = $this->db->getFieldNames($tabela);
            $cache[$tabela] = $campos;
            return $campos;
        } catch (\Exception $e) {
            log_message('error', "Erro ao buscar campos da tabela {$tabela}: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Lista registros com paginação
     */
    public function listarComPaginacao(array $params, string $campoData = 'created_at'): array
    {
        $limite = (int) ($params['limite'] ?? 10);
        $pagina = (int) ($params['pagina'] ?? 1);

        $limite = min(max($limite, 1), 500);
        $pagina = max($pagina, 1);

        $builder = $this;
        $builder = $this->aplicarFiltros($builder, $params, $campoData);

        $registros = $builder->paginate($limite, 'default', $pagina);

        return [
            'registros' => $registros,
            'paginacao' => [
                'total' => $builder->pager->getTotal(),
                'porPagina' => $builder->pager->getPerPage(),
                'paginaAtual' => $builder->pager->getCurrentPage(),
                'ultimaPagina' => $builder->pager->getPageCount(),
            ]
        ];
    }

    public function listarSemPaginacao(array $params, string $campoData = 'created_at'): array
    {
        $builder = $this;
        $builder = $this->aplicarFiltros($builder, $params, $campoData);

        return [
            'registros' => $builder->findAll()
        ];
    }


    /**
     * 🔒 Valida formato de data (Y-m-d)
     */
    protected function validarData(string $data): bool
    {
        $formato = 'Y-m-d';
        $d = \DateTime::createFromFormat($formato, $data);
        return $d && $d->format($formato) === $data;
    }
}