<?php

namespace App\Traits;

trait PaginacaoTrait
{
    public function listarComPaginacao(
        int $limite = 10,
        int $pagina = 1,
        array $filtros = [],
        ?string $data_inicio = null,
        ?string $data_fim = null,
        string $campoData = 'created_at'
    ): array {
        $builder = $this;

        foreach ($filtros as $campo => $valor) {
            if (!empty($valor)) {
                $builder->like($campo, $valor);
            }
        }

        if (!empty($data_inicio)) {
            $builder->where("$campoData >= ", $data_inicio . ' 00:00:00');
        }

        if (!empty($data_fim)) {
            $builder->where("$campoData <= ", $data_fim . ' 23:59:59');
        }

        $builder->orderBy($campoData, 'DESC');

        $registros = $builder->paginate($limite, 'default', $pagina);
        $paginacao = [
            'total' => $builder->pager->getTotal(),
            'porPagina' => $builder->pager->getPerPage(),
            'paginaAtual' => $builder->pager->getCurrentPage(),
            'ultimaPagina' => $builder->pager->getPageCount()
        ];

        return [
            'registros' => $registros,
            'paginacao' => $paginacao
        ];
    }

    public function listarSemPaginacao(array $filtros = []): array
    {
        $builder = $this;

        foreach ($filtros as $campo => $valor) {
            if (!empty($valor)) {
                $builder->like($campo, $valor);
            }
        }

        return [
            'registros' => $builder->findAll(),
        ];
    }

}
