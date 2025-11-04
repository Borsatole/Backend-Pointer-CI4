<?php

return [
    1 => [ // Administrador
        ['id' => 1, 'nome' => 'Dashboard', 'rota' => '/', 'icone' => 'dashboard'],
        ['id' => 3, 'nome' => 'Clientes', 'rota' => '/clientes', 'icone' => 'clientes'],
        ['id' => 6, 'nome' => 'Níveis de Usuários', 'rota' => '/acesso-niveis', 'icone' => 'permissoes'],
        ['id' => 2, 'nome' => 'Estoque', 'rota' => '/estoque', 'icone' => 'estoque'],
        
    ],

    2 => [ // Padrão
        ['id' => 1, 'nome' => 'Dashboard', 'rota' => '/', 'icone' => 'dashboard'],
        ['id' => 6, 'nome' => 'Níveis de Usuários', 'rota' => '/acesso-niveis', 'icone' => 'permissoes'],
    ],
];
