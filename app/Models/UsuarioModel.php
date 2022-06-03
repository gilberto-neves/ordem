<?php

namespace App\Models;

use CodeIgniter\Model;

class UsuarioModel extends Model
{
    protected $table            = 'usuarios';
    protected $returnType       = 'App\Entities\Usuario';
    protected $useSoftDeletes   = true;
    protected $allowedFields    = [
        'nome',
        'email',
        'password',
        'reset_hash',
        'reset_expira_em',
        'imagem',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $createdField  = 'criado_em';
    protected $updatedField  = 'atualizado_em';
    protected $deletedField  = 'deletado_em';

    // Validation
    protected $validationRules      = [
        'nome' => 'required|min_length[3]|max_length[120]',
        'email' => 'required|valid_email|max_length[230]|is_unique[usuarios.email]',
        // 'cpf' => 'required|is_unique[usuarios.cpf]|exact_length[14]|validaCpf',
        // 'telefone' => 'required',
        'password' => 'required|min_length[6]',
        'password_confirmation' => 'required_with[password]|matches[password]',
    ];
    protected $validationMessages   = [
        'nome' => [
            'required' => 'O campo NOME é obrigatório',
            'min_length' => 'O NOME deve ter pelo menos 3 caracteres',
            'max_length' => 'O NOME deve ter no máximo 120 caracteres',
        ],
        'email' => [
            'required' => 'O campo E-MAIL é obrigatório',
            'is_unique' => 'Desculpe, esse e-mail já existe',
            'max_length' => 'O e-mail deve ter no máximo 230 caracteres',
        ],
        // 'cpf' => [
        //     'required' => 'O campo CPF é obrigatório',
        //     'is_unique' => 'Desculpe, esse CPF já existe',
        // ],
        // 'telefone' => [
        //     'required' => 'O campo TELEFONE é obrigatório',
        // ],
        'password' => [
            'required' => 'O campo SENHA é obrigatório',
            'min_length' => 'A senha deve conter pelo menos 6 digitos',
        ],
        'password_confirmation' => [
            'required_with' => 'Confirme a sua senha',
            'matches' => 'As duas senhas devem ser iguais',
        ],
    ];

    // Callbacks
    protected $beforeInsert   = ['hashPassword'];
    protected $beforeUpdate   = ['hashPassword'];


    public function buscaUsuarioPorEmail(string $email)
    {
        return $this->where('email', $email)->where('deletado_em', null)->first();
    }

    /**
     * Método recupera o usuário para logar na aplicação
     *
     * @param array $data
     * @return null|object
     */
    protected function hashPassword(array $data)
    {
        if (isset($data['data']['password'])) {
            $data['data']['password_hash'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);

            // Removemos dos dados a serem salvos
            unset($data['data']['password']);
            unset($data['data']['password_confirmation']);
        }


        return $data;
    }
}
