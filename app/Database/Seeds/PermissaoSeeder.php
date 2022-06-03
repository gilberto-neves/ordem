<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PermissaoSeeder extends Seeder
{
    public function run()
    {
        $permissaoModel = new \App\Models\PermissaoModel();

        $permissoes = [
            [
                'nome' => 'listar_usuarios',
            ],
            [
                'nome' => 'criar_usuarios',
            ],
            [
                'nome' => 'editar_usuarios',
            ],
            [
                'nome' => 'excluir_usuarios',
            ],

        ];

        foreach($permissoes as $permissao){
            $permissaoModel->protect(false)->insert($permissao);
        }

        echo 'Permissões criadas com sucesso';
    }
}
