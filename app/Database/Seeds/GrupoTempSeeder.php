<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class GrupoTempSeeder extends Seeder
{
    public function run()
    {
        $grupoModel = new \App\Models\GrupoModel();

        $grupos = [
            [ // ID: 1
                'nome'      => 'Administrador',
                'descricao' => 'Grupo com acesso total ao sistema',
                'exibir'    => false, 
            ],
            [ // ID: 2
                'nome'      => 'Clientes',
                'descricao' => 'Esse grupo é destinado a clientes, pois os mesmos podem logar no sistema e acessas as suas ordens de serviços',
                'exibir'    => false,
            ],
            [ 
                'nome'      => 'Atendentes',
                'descricao' => 'Esse grupo acessa o sistema para realizar atendimento aos clientes',
                'exibir'    => false,
            ],
        ];

        foreach($grupos as $grupo){
            $grupoModel->insert($grupo);
        }

        echo "Grupos criados com sucesso!";
    }
}
