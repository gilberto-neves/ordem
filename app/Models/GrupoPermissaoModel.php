<?php

namespace App\Models;

use CodeIgniter\Model;

class GrupoPermissaoModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'grupos_permissoes';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['grupo_id', 'permissao_id'];


    /**
     * Método que recupera as permissões do grupo de acesso
     *
     * @param integer $grupo_id
     * @param integer $quantidade_paginacao
     * @return array|null 
     */
    public function recuperaPermissoesDoGrupo(int $grupo_id, int $quantidade_paginacao)
    {
        $atributos = [
            'grupos_permissoes.id AS principal_id', // Será usado como identificador principal da permissão na hora de remove-lá
            'grupos.id AS grupo_id',
            'permissoes.id AS permissao_id',
            'permissoes.nome',
        ];

        return $this->select($atributos)
                    ->join('grupos', 'grupos.id = grupos_permissoes.grupo_id')
                    ->join('permissoes', 'permissoes.id = grupos_permissoes.permissao_id')
                    ->where('grupos_permissoes.grupo_id', $grupo_id)
                    ->groupBy('permissoes.nome')
                    ->paginate($quantidade_paginacao);
    }
}
