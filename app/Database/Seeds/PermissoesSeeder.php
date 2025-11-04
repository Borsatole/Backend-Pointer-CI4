<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PermissoesSeeder extends Seeder
{
    public function run()
    {
        $data = [
            // Usuários
            [
                'slug' => 'usuario.criar',
                'descricao' => 'Permite criar novos usuários.',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'slug' => 'usuario.visualizar',
                'descricao' => 'Permite visualizar a lista de usuários.',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'slug' => 'usuario.editar',
                'descricao' => 'Permite editar informações de usuários.',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'slug' => 'usuario.excluir',
                'descricao' => 'Permite excluir usuários.',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            // Papeis
            [
                'slug' => 'papeis.criar',
                'descricao' => 'Permite criar novos papeis.',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'slug' => 'papeis.visualizar',
                'descricao' => 'Permite visualizar a lista de papeis.',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'slug' => 'papeis.editar',
                'descricao' => 'Permite editar informações de papeis.',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'slug' => 'papeis.excluir',
                'descricao' => 'Permite excluir papeis.',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],

            // Clientes
            [
                'slug' => 'cliente.criar',
                'descricao' => 'Permite criar novos clientes.',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'slug' => 'cliente.visualizar',
                'descricao' => 'Permite visualizar a lista de clientes.',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'slug' => 'cliente.editar',
                'descricao' => 'Permite editar informações de clientes.',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'slug' => 'cliente.excluir',
                'descricao' => 'Permite excluir clientes.',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],

            // Veículos
            [
                'slug' => 'veiculo.criar',
                'descricao' => 'Permite cadastrar novos veículos.',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'slug' => 'veiculo.visualizar',
                'descricao' => 'Permite visualizar a lista de veículos.',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'slug' => 'veiculo.editar',
                'descricao' => 'Permite editar informações de veículos.',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'slug' => 'veiculo.excluir',
                'descricao' => 'Permite remover veículos do sistema.',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('permissoes')->insertBatch($data);
    }
}
