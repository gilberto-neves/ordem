<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Usuario extends Entity
{
    protected $datamap = [];
    protected $dates   = [
        'criado_em',
        'atualizado_em',
        'deletado_em'
    ];
    
    public function exibeSituacao()
    {
        if($this->deletado_em != null){
            // Usuário Excluído
            $icone = '<span class="text-white">Excluído</span>&nbsp;<i class="fa-solid fa-arrow-rotate-left"></i>&nbsp;Desfazer';

            $situacao = anchor("admin/usuarios/desfazerexclusao/$this->id", $icone, ['class' => 'btn btn-outline-success btn-sm']);

            return $situacao;

        }

        if($this->ativo == true){

            return '<i class="fa-solid fa-user-check text-success"></i>&nbsp;Ativo';
        }else{
            return '<i class="fas fa-user-lock text-danger"></i>&nbsp;Inativo';
        }
    }

    /**
     * Método que verifica se a senha é valida
     *
     * @param string $password
     * @return boolean
     */
    public function verificaPassword(string $password):bool
    {
        return password_verify($password, $this->password_hash);
    }
}
