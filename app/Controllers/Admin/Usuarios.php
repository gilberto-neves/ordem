<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Entities\Usuario;



class Usuarios extends BaseController
{
    private $usuarioModel;
    private $grupoUsuarioModel;
    private $grupoModel;

    public function __construct()
    {
        $this->usuarioModel = new \App\Models\UsuarioModel();
        $this->grupoUsuarioModel = new \App\Models\GrupoUsuarioModel();
        $this->grupoModel = new \App\Models\GrupoModel();
    }
    public function index()
    {
        $data = [
            'titulo' => 'Listando os Usuários do Sistema',
        ];

        return view('Admin/Usuarios/index', $data);
    }

    public function recuperaUsuarios()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }
        $atributos = [
            'id',
            'nome',
            'email',
            'ativo',
            'imagem',
            'deletado_em',
        ];

        $usuarios = $this->usuarioModel->select($atributos)
                            ->withDeleted(true)
                            ->orderBy('id', 'DESC')
                            ->findAll();


        // Receber o Array de Objetos de Usuarios
        $data = [];

        foreach ($usuarios as $usuario) {

            // Definimos o caminho da imagem do Usuário
            if($usuario->imagem != null){
                // Tem imagem
                $imagem = [
                    'src'   => site_url("admin/usuarios/imagem/$usuario->imagem"),
                    'class' => 'rounded-circle img-fluid',
                    'alt'   => esc($usuario->nome),
                    'width' => '50',
                ];
            }else{
                // Não tem imagem
                $imagem = [
                    'src'   => site_url("admin/img/avatar.jpg"),
                    'class' => 'rounded-circle img-fluid',
                    'alt'   => 'Usuário sem Imagem',
                    'width' => '50',
                ];
            }
            $data[] = [
                'imagem' => $usuario->imagem = img($imagem),
                'nome'  => anchor("admin/usuarios/exibir/$usuario->id", esc($usuario->nome), 'title="Exibir usuário: ' . esc($usuario->nome) . '"'),
                'email' => esc($usuario->email),
                'ativo' => $usuario->exibeSituacao(),

            ];
        }

        $retorno = [
            'data' => $data,
        ];

        return $this->response->setJSON($retorno);
    }

    public function criar()
    {
        $usuario = new Usuario();

        $data = [
            'titulo' => "Criando Novo Usuário: ",
            'usuario' => $usuario,
        ];

        return view('Admin/Usuarios/criar', $data);
    }

    public function cadastrar()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }


        // Envio o hash do token do FORM
        $retorno['token'] = csrf_hash();


        // Recupero o POST da Requisição
        $post = $this->request->getPost();


        // Crio novo objeto da Entidade Usuario
        $usuario = new Usuario($post);


        if ($this->usuarioModel->protect(false)->save($usuario)) {

            $btnCriar = anchor("admin/usuarios/criar", 'Cadastrar novo usuário', ['class' => 'btn btn-danger mt-2']);

            session()->setFlashdata('sucesso', "Usuário cadastrado com sucesso! <br> $btnCriar");

            // Retornamos o último ID inserido na tabela de Usuarios (recem criado)
            $retorno['id'] = $this->usuarioModel->getInsertID();

            return $this->response->setJSON($retorno);
        }

        // Retornamos os erros de Validação
        $retorno['erro'] = 'Por favor verifique os ERROS abaixo e tente novamente!';
        $retorno['erros_model'] = $this->usuarioModel->errors();

        // Retorno para o AJAX Request
        return $this->response->setJSON($retorno);
    }

    public function exibir(int $id = null)
    {
        $usuario = $this->buscarUsuarioOu404($id);

        $data = [
            'titulo' => "Detalhando o Usuário: " . esc($usuario->nome),
            'usuario' => $usuario,
        ];

        return view('Admin/Usuarios/exibir', $data);
    }

    public function editar(int $id = null)
    {


        $usuario = $this->buscarUsuarioOu404($id);

        $data = [
            'titulo' => "Editando o Usuário: " . esc($usuario->nome),
            'usuario' => $usuario,
        ];

        return view('Admin/Usuarios/editar', $data);
    }

    public function atualizar()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }


        // Envio o hash do token do FORM
        $retorno['token'] = csrf_hash();


        // Recupero o POST da Requisição
        $post = $this->request->getPost();
        // echo '<pre>';
        // print_r($post);
        // exit;

        // Validando a existência do usuário
        $usuario = $this->buscarUsuarioOu404($post['id']);

        // Se não for informado a senha, removemos do POST
        // Se não fizermos dessa forma, o hashPassword fará o Hash de uma string vazia
        if (empty($post['password'])) {

            unset($post['password']);
            unset($post['password_confirmation']);
        }

        // Preenchemos os atributos do usuário com os valores do POST
        $usuario->fill($post);

        if ($usuario->hasChanged() == false) {
            $retorno['info'] = 'Não há dados para serem atualizados';

            // Retorno para o AJAX Request
            return $this->response->setJSON($retorno);
        }

        if ($this->usuarioModel->protect(false)->save($usuario)) {

            session()->setFlashdata('sucesso', 'Dados salvos com sucesso.');

            return $this->response->setJSON($retorno);
        }

        // Retornamos os erros de Validação
        $retorno['erro'] = 'Por favor verifique os ERROS abaixo e tente novamente!';
        $retorno['erros_model'] = $this->usuarioModel->errors();

        // Retorno para o AJAX Request
        return $this->response->setJSON($retorno);
    }

    public function editarImagem(int $id = null)
    {


        $usuario = $this->buscarUsuarioOu404($id);

        $data = [
            'titulo' => "Alterando a imagem do Usuário: " . esc($usuario->nome),
            'usuario' => $usuario,
        ];

        return view('Admin/Usuarios/editar_imagem', $data);
    }

    public function upload()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }


        // Envio o hash do token do FORM
        $retorno['token'] = csrf_hash();

        $validacao = service('validation');

        $regras = [
            'imagem' => 'uploaded[imagem]|max_size[imagem,1024]|ext_in[imagem,png,jpg,jpeg,webp]',
            
        ];

        $mensagens = [   // Errors
            'imagem' => [
                'uploaded' => 'Por favor escolha uma imagem.',
                'max_size' => 'Tamanho máximo permitido da Imagem: 1 MB.',
                'ext_in' => 'Somente arquivos png, jpg, jpeg e  webp são permitidos.',
            ],
            
        ];

        $validacao->setRules($regras, $mensagens);

        if($validacao->withRequest($this->request)->run() == false){

            // Retornamos os erros de Validação
            $retorno['erro'] = 'Por favor verifique os ERROS abaixo e tente novamente!';
            $retorno['erros_model'] = $validacao->getErrors();

            // Retorno para o AJAX Request
            return $this->response->setJSON($retorno);

        }

        
        // Recupero o POST da Requisição
        $post = $this->request->getPost();


        // Validando a existência do usuário
        $usuario = $this->buscarUsuarioOu404($post['id']);

        // Recuperamos a imagem que veio no POST
        $imagem = $this->request->getFile('imagem');

        list($largura, $altura) = getimagesize($imagem->getPathName());

        if($largura < "300" || $altura < "300"){

            // Retornamos os erros de Validação
            $retorno['erro'] = 'Por favor verifique os ERROS abaixo e tente novamente!';
            $retorno['erros_model'] = ['dimensao' => 'A imagem não pode ser menor do que 300 x 300 px'];

            // Retorno para o AJAX Request
            return $this->response->setJSON($retorno);

        }

        $caminhoImagem = $imagem->store('usuarios');

        $caminhoImagem = WRITEPATH . "uploads/$caminhoImagem";

        // Podemos manipular a imagem que esta salva no diretório

        // Redimensionamos a imagem para 300 x 300 e para ficar no centro
        $this->manipulaImagem($caminhoImagem, $usuario->id);


        // A partir daqui podemos atualizar a tabela de Usuarios

        // Recupero a possível Imagem Antiga
        $imagemAntiga = $usuario->imagem;

        $usuario->imagem = $imagem->getName();

        $this->usuarioModel->save($usuario);

        if($imagemAntiga != null){
            $this->removeImagemDoFileSystem($imagemAntiga);
        }

        session()->setFlashdata('sucesso', 'Imagem atualizada com sucesso!');

        // Retorno para o AJAX Request
        return $this->response->setJSON($retorno);
    }

    public function imagem(string $imagem = null)
    {
        if($imagem != null){
            $this->exibeArquivo('usuarios', $imagem);
        }
    }

    public function excluir(int $id = null)
    {
        $usuario = $this->buscarUsuarioOu404($id);

        if($usuario->deletado_em != null){
            return redirect()->back()
                             ->with('atencao', "O usuario <b>$usuario->nome</b> já econtra-se excluído!");
        }

        if($this->request->getMethod() === 'post'){

            // Exclui o usuário
            $this->usuarioModel->delete($usuario->id);
            
            // Deletamos a imagem do Filesystem
            if($usuario->imagem != null){
                $this->removeImagemDoFileSystem($usuario->imagem);
            }

            $usuario->imagem = null;
            $usuario->ativo = false;
            $this->usuarioModel->protect(false)->save($usuario);

            return redirect()->to(site_url("admin/usuarios"))
                             ->with('sucesso', "Usuario <b>$usuario->nome<b> excluído com sucesso!");
        }

        $data = [
            'titulo' => "Excluidno o Usuário: " . esc($usuario->nome),
            'usuario' => $usuario,
        ];

        return view('Admin/Usuarios/excluir', $data);
    }

    public function desfazerExclusao(int $id = null)
    {
        $usuario = $this->buscarUsuarioOu404($id);

        if ($usuario->deletado_em == null) {
            return redirect()->back()
                            ->with('atencao', "Apenas usuários excluídos podem ser recuperados!");
        }

        $usuario->deletado_em = null;
        $this->usuarioModel->protect(false)->save($usuario);

        return redirect()->back()
            ->with('sucesso', "Usuário <b>$usuario->nome</b> recuperado com sucesso!");

    }

    public function grupos(int $id = null)
    {

        $usuario = $this->buscarUsuarioOu404($id);

        $usuario->grupos = $this->grupoUsuarioModel->recuperaGruposDoUsuario($usuario->id, 5);
        $usuario->pager = $this->grupoUsuarioModel->pager;


        $data = [
            'titulo' => "Gerenciando os grupos de acesso do usuário: " . esc($usuario->nome),
            'usuario' => $usuario,
        ];

        // Quando um usuário for cliente, podemos retornar para a view de exibição de usuário, informando
        // que adiciona-lo a outros grupos ou remove-los do grupo de clientes.
        $grupoCliente = 2;
        if(in_array($grupoCliente, array_column($usuario->grupos, 'grupo_id'))){
            return redirect()->to(site_url("admin/usuarios/exibir/$usuario->id"))
                            ->with('info', "Esse usuário é um cliente, porntanto não pode ser atribuido a outros grupos ou excluído desse!");
        }

        $grupoAdmin = 1;
        if (in_array($grupoAdmin, array_column($usuario->grupos, 'grupo_id'))) {

            $usuario->full_control = true; // Está no Grupo de Administradores, portanto já podemos retornar a view

            return view('Admin/Usuarios/grupos', $data);
        }

        $usuario->full_control = false; // Não esta no Grupo de Administradores, podemos seguir com o processamento

        if(!empty($usuario->grupos)){

            // Recuperamos os grupos que o Usuario ainda não faz parte

            $gruposExistentes = array_column($usuario->grupos, 'grupo_id');

            $data['gruposDisponiveis'] = $this->grupoModel
                                                ->where('id !=', 2) // Não recuperamos o Grupo de Clientes
                                                ->whereNotIn('id', $gruposExistentes)
                                                ->findAll();
        }else{

            // Recuperamos todos os grupos, com exceção do Grupo ID 2, que é de clientes
            $data['gruposDisponiveis'] = $this->grupoModel
                                        ->where('id !=', 2) // Não recuperamos o Grupo de Clientes
                                        ->findAll();
        }

       

        return view('Admin/Usuarios/grupos', $data);
    }

    public function salvarGrupos()
    {

        // Envio o hash do token do FORM
        $retorno['token'] = csrf_hash();


        // Recupero o POST da Requisição
        $post = $this->request->getPost();
        // echo '<pre>';
        // print_r($post);
        // exit;

        // Validando a existência do usuário
        $usuario = $this->buscarUsuarioOu404($post['id']);

        if (empty($post['grupo_id'])) {

            // Retornamos os erros de Validação
            $retorno['erro'] = 'Por favor verifique os ERROS abaixo e tente novamente!';
            $retorno['erros_model'] = ['grupo_id' => 'Escolha um ou mais grupos antes de salvar'];

            // Retorno para o AJAX Request
            return $this->response->setJSON($retorno);
        }

        if(in_array(2, $post['grupo_id'])){

            // Retornamos os erros de Validação
            $retorno['erro'] = 'Por favor verifique os ERROS abaixo e tente novamente!';
            $retorno['erros_model'] = ['grupo_id' => 'O grupo Clientes não pode ser atribuido de forma manual'];

            // Retorno para o AJAX Request
            return $this->response->setJSON($retorno);

        }


        if (in_array(1, $post['grupo_id'])){

            $grupoAdmin = [
                'grupo_id'      => 1,
                'usuario_id'  => $usuario->id
            ];

            $this->grupoUsuarioModel->insert($grupoAdmin);
            $this->grupoUsuarioModel->where('grupo_id !=', 1)
                                    ->where('usuario_id', $usuario->id)
                                    ->delete();

            session()->setFlashdata('sucesso', 'Dados salvos com sucesso.');
            session()->setFlashdata('info', 'Para usuário do Grupo Administrador não há necessidade de atribuir outros grupos, pois esse grupo já tem acesso total!.');

            return $this->response->setJSON($retorno);
        }

        // Receber as permissões do POST
        $grupoPush = [];

        foreach ($post['grupo_id'] as $grupo) {

            array_push($grupoPush, [
                'grupo_id'      => $grupo,
                'usuario_id'  => $usuario->id
            ]);
        }

        $this->grupoUsuarioModel->insertBatch($grupoPush);

        session()->setFlashdata('sucesso', 'Dados salvos com sucesso.');

        return $this->response->setJSON($retorno);



    }

    public function removeGrupo(int $principal_id = null)
    {
        if($this->request->getMethod() === 'post'){
          
            $grupoUsuario = $this->buscarGrupoUsuarioOu404($principal_id);

            if($grupoUsuario->grupo_id == 2){
                return redirect()->to(site_url("admin/usuarios/exibir/$grupoUsuario->usuario_id"))->with("info", "Não é permitida a exclusão do usuário do grupo de Clientes.");
            }

            $this->grupoUsuarioModel->delete($principal_id);

            return redirect()->back()->with("sucesso", "Usuário removido do Grupo de Acesso com sucesso!");
        }

        // Não é POST
        return redirect()->back();
    }



    /**
     *
     * @param int $id
     * @return objeto usuario
     */
    private function buscarUsuarioOu404(int $id = null)
    {

        if (!$id || !$usuario = $this->usuarioModel->withDeleted(true)->where('id', $id)->first()) { // REVER ESSA PARTE

            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Não encontramos o usuário $id");
        }

        return $usuario;
    }

    /**
     * Método que recupera o registro do grupo de acesso associado ao usuario
     *
     * @param integer|null $principal_id
     * @return Exception|object
     */
    private function buscarGrupoUsuarioOu404(int $principal_id = null)
    {

        if (!$principal_id || !$grupoUsuario = $this->grupoUsuarioModel->find($principal_id)) {

            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Não encontramos o registro de associação ao grupo de acesso $principal_id");
        }

        return $grupoUsuario;
    }

    private function manipulaImagem(string $caminhoImagem, int $usuario_id)
    {
        // Redimensionamos a imagem para 300 x 300 e para ficar no centro
        service('image')
            ->withFile($caminhoImagem)
            ->fit(300, 300, 'center')
            ->save($caminhoImagem);

        // Adicionar Marca d'agua de texto
        $anoAtual = date('Y');
        \Config\Services::image('imagick')
            ->withFile($caminhoImagem)
            ->text("ORDEM - $anoAtual - User ID $usuario_id", [
                'color'      => '#fff',
                'opacity'    => 0.5,
                'withShadow' => false,
                'hAlign'     => 'center',
                'vAlign'     => 'bottom',
                'fontSize'   => 12
            ])
            ->save($caminhoImagem);
    }

    private function removeImagemDoFileSystem(string $imagem)
    {
        $caminhoImagem = WRITEPATH . "uploads/usuarios/$imagem";

        if(is_file($caminhoImagem)){
            unlink($caminhoImagem);
        }
    }
}
