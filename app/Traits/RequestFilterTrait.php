<?php

namespace App\Traits;

trait RequestFilterTrait
{
    /**
     * Processa filtros da requisição GET com opções flexíveis.
     *
     * @param \CodeIgniter\HTTP\IncomingRequest $request
     * @param array $options  Opções para ativar/desativar partes do filtro:
     *                        [
     *                          'pagination' => true/false,
     *                          'dates'      => true/false,
     *                          'ordering'   => true/false,
     *                          'dynamic'    => true/false
     *                        ]
     */
    public function getRequestFilters($request, array $options = []): array
    {
        // Valores padrão das opções
        $options = array_merge([
            'pagination' => false,
            'dates' => false,
            'ordering' => false,
            'dynamic' => false
        ], $options);

        $result = [
            'limite' => null,
            'pagina' => null,
            'data_inicio' => null,
            'data_fim' => null,
            'order_by' => null,
            'order_dir' => null,
            'filtros' => [],
        ];

        $all = $request->getGet();

        // 🔹 PAGINAÇÃO
        if ($options['pagination']) {
            $result['limite'] = intval($request->getGet('limite') ?? 10);
            $result['pagina'] = intval($request->getGet('pagina') ?? 1);
        }

        // 🔹 DATAS
        if ($options['dates']) {
            $result['dia'] = $request->getGet('dia') ?: null;
            $result['data_minima'] = $request->getGet('data_minima') ?: null;
            $result['data_maxima'] = $request->getGet('data_maxima') ?: null;
        }

        // 🔹 ORDENAÇÃO
        if ($options['ordering']) {
            $result['order_by'] = $request->getGet('order_by') ?: 'id';
            $result['order_dir'] = $request->getGet('order_dir') ?: 'desc';
        }

        // Campos que precisam ser ignorados nos filtros dinâmicos
        $ignore = [];

        if ($options['pagination']) {
            $ignore[] = 'limite';
            $ignore[] = 'pagina';
        }

        if ($options['dates']) {
            $ignore[] = 'data_minima';
            $ignore[] = 'data_maxima';
        }

        if ($options['ordering']) {
            $ignore[] = 'order_by';
            $ignore[] = 'order_dir';
        }

        // 🔹 FILTROS DINÂMICOS
        if ($options['dynamic']) {
            foreach ($all as $key => $value) {
                if (!in_array($key, $ignore)) {
                    $result['filtros'][$key] = $value;
                }
            }
        }

        return $result;
    }
}
