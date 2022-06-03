<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Grupo extends Entity
{
    protected $dates   = [
        'criado_em',
        'atualizado_em',
        'deletado_em'
    ];

    public function exibeSituacao()
    {
        if ($this->deletado_em != null) {
            // Grupo Excluído
            $icone = '<span class="text-white">Excluído</span>&nbsp;<i class="fa-solid fa-arrow-rotate-left"></i>&nbsp;Desfazer';

            $situacao = anchor("admin/grupos/desfazerexclusao/$this->id", $icone, ['class' => 'btn btn-outline-success btn-sm']);

            return $situacao;
        }

        if ($this->exibir == true) {

            return '<i class="fa-solid fa-eye text-success"></i>&nbsp;Exibir grupo';
        } else {
            return'<i class="fa-solid fa-eye-slash text-danger"></i>&nbsp;Não exibir grupo';
        }
    }
}
