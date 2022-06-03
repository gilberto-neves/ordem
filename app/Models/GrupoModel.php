<?php

namespace App\Models;

use CodeIgniter\Model;

class GrupoModel extends Model
{
    protected $table            = 'grupos';
    protected $returnType       = 'App\Entities\Grupo';
    protected $useSoftDeletes   = true;
    protected $allowedFields    = ['nome', 'descricao', 'exibir'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'criado_em';
    protected $updatedField  = 'atualizado_em';
    protected $deletedField  = 'deletado_em';

    // Validation
    protected $validationRules      = [
        'nome' => 'required|max_length[120]|is_unique[grupos.nome,id,{id}]',
        'descricao' => 'required|max_length[240]',
        // 'telefone' => 'required',
        
    ];
    protected $validationMessages   = [
        'nome' => [
            'required' => 'O campo NOME é obrigatório',
            'max_length' => 'O NOME deve ter no máximo 120 caracteres',
            'is_unique' => 'Já existe um Grupo com esse NOME, por favor escolha outro!'
        ],
        'descricao' => [
            'required' => 'O campo DESCRIÇÃO é obrigatório',
            'max_length' => 'A descrição deve ter no máximo 240 caracteres',
        ],
        
    ];
}
