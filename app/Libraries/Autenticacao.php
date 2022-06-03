<?php 

namespace App\Libraries;

class Autenticacao{

    private $usuario;
    private $usuarioModel;

    public function __construct()
    {
        $this->usuarioModel = new \App\Models\UsuarioModel();

    }

    /**
     * Método que realiza o login na aplicação
     *
     * @param string $email
     * @param string $password
     * @return boolean
     */
    public function login(string $email, string $password):bool
    {
        //Buscamos o Usuário
        $usuario = $this->usuarioModel->buscaUsuarioPorEmail($email);

        if($usuario == null){
            return false;
        }

        // Verificamos se a senha é válida
        if($usuario->verificaPassword($password) === false){
            return false;
        }

        //Verificamos se o Usuário esta ativo e pode logar na aplicação
        if($usuario->ativo == false){
            return false;
        }

        $this->logaUsuario($usuario);

        return true;
    }

    /**
     * Método de LogOut
     *
     * @return void
     */
    public function logOut():void
    {
        session()->destroy();
    }

    public function pegaUsuarioLogado()
    {
        if($this->usuario === null){

            $this->usuario = $this->pegaUsuarioDaSessao();

        }

        return $this->usuario;
    }

    /**
     * Método que verifica se o Usuário esta logado
     *
     * @return boolean
     */
    public function estaLogado():bool
    {
        return $this->pegaUsuarioLogado() != null;
    }

    /**
     * Método que recupera da sessão e valida o Usuário logado
     *
     * @return null|object
     */
    private function pegaUsuarioDaSessao()
    {
        if(session()->has('usuario_id') == false){

            return null;

        }

        //Busco o Usuário na Base de Dados
        $usuario = $this->usuarioModel->find(session()->get('usuario_id'));

        //Validamos se o Usuário existe e se tem permissão de logar na aplicação
        if($usuario == null || $usuario->ativo == false){
            return null;
        }

        return $usuario; // Retornamos o Objeto Usuario

    }

    /**
     * Método que insere na sessão o ID do Usuário
     *
     * @param object $usuario
     * @return void
     */
    private function logaUsuario(object $usuario):void
    {
        //Recuperamos a instância da sessão
        $session = session();

        //Antes de inserirmos o ID do usuario na sessão
        //devemos gerar um novo ID da sessão
        $session->regenerate();

        //Setamos na sessão o ID do Usuário
        $session->set('usuario_id', $usuario->id);


    }

    

}
