<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Entities\Grupo;

class Grupos extends BaseController
{

    private $grupoModel;
    private $grupoPermissaoModel;
    private $permissaoModel;

    public function __construct()
    {
        $this->grupoModel = new \App\Models\GrupoModel();
        $this->grupoPermissaoModel = new \App\Models\GrupoPermissaoModel();
        $this->permissaoModel = new \App\Models\PermissaoModel();
    }

    public function index()
    {
        $data = [
            'titulo' => 'Listando os Grupos de Acesso ao Sistema',
        ];

        return view('Admin/Grupos/index', $data);
    }

    public function recuperaGrupos()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }
        $atributos = [
            'id',
            'nome',
            'descricao',
            'exibir',
            'deletado_em',
        ];

        $grupos = $this->grupoModel->select($atributos)
            ->withDeleted(true)
            ->orderBy('id', 'DESC')
            ->findAll();


        // Receber o Array de Objetos de Usuarios
        $data = [];

        foreach ($grupos as $grupo) {


            $data[] = [
                'nome'  => anchor("admin/grupos/exibir/$grupo->id", esc($grupo->nome), 'title="Exibir Grupo: ' . esc($grupo->nome) . '"'),
                'descricao' => esc($grupo->descricao),
                'exibir' => $grupo->exibeSituacao(),

            ];
        }

        $retorno = [
            'data' => $data,
        ];

        return $this->response->setJSON($retorno);
    }

    public function criar()
    {
        $grupo = new Grupo();

        $data = [
            'titulo' => "Criando novo Grupo de Acesso: ",
            'grupo' => $grupo,
        ];

        return view('Admin/Grupos/criar', $data);
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
        $grupo = new Grupo($post);


        if ($this->grupoModel->save($grupo)) {

            $btnCriar = anchor("admin/grupos/criar", 'Cadastrar novo Grupo de Acesso', ['class' => 'btn btn-danger mt-2']);

            session()->setFlashdata('sucesso', "Grupo de Acesso cadastrado com sucesso! <br> $btnCriar");

            // Retornamos o último ID inserido na tabela de Usuarios (recem criado)
            $retorno['id'] = $this->grupoModel->getInsertID();

            return $this->response->setJSON($retorno);
        }

        // Retornamos os erros de Validação
        $retorno['erro'] = 'Por favor verifique os ERROS abaixo e tente novamente!';
        $retorno['erros_model'] = $this->grupoModel->errors();

        // Retorno para o AJAX Request
        return $this->response->setJSON($retorno);
    }

    public function exibir(int $id = null)
    {
        $grupo = $this->buscarGrupoOu404($id);

        $data = [
            'titulo' => "Detalhando o Grupo de Acesso: " . esc($grupo->nome),
            'grupo' => $grupo,
        ];

        return view('Admin/Grupos/exibir', $data);
    }

    public function editar(int $id = null)
    {
        $grupo = $this->buscarGrupoOu404($id);

        if ($grupo->id < 3) {
            return redirect()->back()->with('atencao', 'O grupo&nbsp;<b>' . esc($grupo->nome) . '&nbsp;</b>não pode ser editado');
        }

        $data = [
            'titulo' => "Editando o Grupo de Acesso: " . esc($grupo->nome),
            'grupo' => $grupo,
        ];

        return view('Admin/Grupos/editar', $data);
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
        $grupo = $this->buscarGrupoOu404($post['id']);


        // Garatimos que os clientes Administradores e Clientes não podem ser editados
        if ($grupo->id < 3) {

            $retorno['erro'] = 'Por favor verifique os ERROS abaixo e tente novamente!';
            $retorno['erros_model'] = ['grupo' => 'O grupo&nbsp;<b class="text-white">' . esc($grupo->nome) . '&nbsp;</b>não pode ser editado'];
            // Retorno para o AJAX Request
            return $this->response->setJSON($retorno);
        }
        // Preenchemos os atributos do usuário com os valores do POST
        $grupo->fill($post);

        if ($grupo->hasChanged() == false) {
            $retorno['info'] = 'Não há dados para serem atualizados';

            // Retorno para o AJAX Request
            return $this->response->setJSON($retorno);
        }

        if ($this->grupoModel->protect(false)->save($grupo)) {

            session()->setFlashdata('sucesso', 'Dados salvos com sucesso.');

            return $this->response->setJSON($retorno);
        }

        // Retornamos os erros de Validação
        $retorno['erro'] = 'Por favor verifique os ERROS abaixo e tente novamente!';
        $retorno['erros_model'] = $this->grupoModel->errors();

        // Retorno para o AJAX Request
        return $this->response->setJSON($retorno);
    }

    public function excluir(int $id = null)
    {
        $grupo = $this->buscarGrupoOu404($id);

        // Garatimos que os clientes Administradores e Clientes não podem ser exclídos
        if ($grupo->id < 3) {
            return redirect()->back()
                ->with('atencao', 'O grupo&nbsp;<b>' . esc($grupo->nome) . '&nbsp;</b>não pode ser excludio');
        }

        if ($grupo->deletado_em != null) {
            return redirect()->back()
                ->with('atencao', 'O Grupo <b>&nbsp;' . esc($grupo->nome) . '&nbsp;</b> já econtra-se excluído!');
        }

        if ($this->request->getMethod() === 'post') {

            // Exclui o GRUPO
            $this->grupoModel->delete($grupo->id);

            return redirect()->to(site_url("admin/grupos"))
                ->with('sucesso', 'Grupo&nbsp;<b>' . esc($grupo->nome) . '</b>&nbsp;excluído com sucesso!');
        }

        $data = [
            'titulo' => "Excluidno o Grupo de Acesso: " . esc($grupo->nome),
            'grupo' => $grupo,
        ];

        return view('Admin/Grupos/excluir', $data);
    }

    public function desfazerExclusao(int $id = null)
    {
        $grupo = $this->buscarGrupoOu404($id);

        if ($grupo->deletado_em == null) {
            return redirect()->back()
                ->with('atencao', "Apenas Grupos excluídos podem ser recuperados!");
        }

        $grupo->deletado_em = null;
        $this->grupoModel->protect(false)->save($grupo);

        return redirect()->back()
            ->with('sucesso', 'Grupo <b>&nbsp;' . esc($grupo->nome) . '&nbsp;</b>recuperado com sucesso!');
    }

    public function permissoes(int $id = null)
    {
        $grupo = $this->buscarGrupoOu404($id);
        // Grupo Administrador
        if ($grupo->id == 1) {
            return redirect()->back()
                ->with('atencao', 'Não é necessário atribir ou remover permissões de acesso para o grupo&nbsp;<b>' . esc($grupo->nome) . '&nbsp;</b>, pois esse grupo é Administrador');
        }
        // Grupo de Clientes
        if ($grupo->id == 2) {
            return redirect()->back()
                ->with('atencao', 'Não é necessário atribir ou remover permissões de acesso para o grupo de <b>Cliente</b>');
        }

        if ($grupo->id > 2) {
            $grupo->permissoes = $this->grupoPermissaoModel->recuperaPermissoesDoGrupo($grupo->id, 5);
            $grupo->pager = $this->grupoPermissaoModel->pager;
        }



        $data = [
            'titulo' => "Gerenciando as permissões do Grupo de Acesso: " . esc($grupo->nome),
            'grupo' => $grupo,
        ];

        if (!empty($grupo->permissoes)) {
            $permissoesExistentes = array_column($grupo->permissoes, 'permissao_id');

            $data['permissoesDisponiveis'] = $this->permissaoModel->whereNotIn('id', $permissoesExistentes)->findAll();
        } else {
            // Se caiu aqui é porque o Grupo não possui nenhuma permissão
            $data['permissoesDisponiveis'] = $this->permissaoModel->findAll();
        }

        return view('Admin/Grupos/permissoes', $data);
    }

    public function salvarPermissoes()
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
        $grupo = $this->buscarGrupoOu404($post['id']);

        if(empty($post['permissao_id'])){

            // Retornamos os erros de Validação
            $retorno['erro'] = 'Por favor verifique os ERROS abaixo e tente novamente!';
            $retorno['erros_model'] = ['permissao_id' => 'Escolha uma ou mais permissões antes de salvar'];

            // Retorno para o AJAX Request
            return $this->response->setJSON($retorno);
        }

        // Receber as permissões do POST
        $permissaoPush = [];

        foreach($post['permissao_id'] as $permissao){

            array_push($permissaoPush, [
                'grupo_id'      => $grupo->id,
                'permissao_id'  => $permissao
            ]);
        }

        $this->grupoPermissaoModel->insertBatch($permissaoPush);

        session()->setFlashdata('sucesso', 'Dados salvos com sucesso.');

        return $this->response->setJSON($retorno);
    }

    public function removePermissao(int $principal_id = null)
    {
        

        if ($this->request->getMethod() === 'post') {

            // Exclui a PERMISSÃO ($principal_id)
            $this->grupoPermissaoModel->delete($principal_id);

            return redirect()->back()
                            ->with('sucesso', 'Permissão removida com sucesso!');
        }

        // NÃO É POST
        return redirect()->back();
    }




    /**
     *Método recpera o Grupo de Acesso
     * @param int $id
     * @return objeto usuario
     */
    private function buscarGrupoOu404(int $id = null)
    {

        if (!$id || !$grupo = $this->grupoModel->withDeleted(true)->where('id', $id)->first()) {

            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Não encontramos o grupo $id");
        }

        return $grupo;
    }
}
