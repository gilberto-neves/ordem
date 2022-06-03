<?php echo $this->extend('Admin/Layout/principal');  ?>


<?php echo $this->section('titulo'); ?>
<?php echo $titulo; ?>
<?php echo $this->endSection(); ?>



<!-- Aqui enviamos para o template principal os estilos -->
<?php echo $this->section('estilos'); ?>

<?php echo $this->endSection(); ?>




<!-- Aqui enviamos para o template principal os conteúdos -->
<?php echo $this->section('conteudo'); ?>

<div class="row">

    <div class="col-lg-4">
        <div class="user-block block text-center">
            <div>
                <?php if ($usuario->imagem == null) : ?>

                    <img src="<?php echo site_url('admin/img/avatar.jpg')  ?>" alt="Usuário sem imagem" class="card-img-top" style="width: 90%;">

                <?php else : ?>

                    <img src="<?php echo site_url("admin/usuarios/imagem/$usuario->imagem");  ?>" alt="<?php echo esc($usuario->nome); ?>" class="card-img-top" style="width: 90%;">

                <?php endif; ?>

                <a href="<?php echo site_url("admin/usuarios/editarimagem/$usuario->id"); ?>" class="btn btn-outline-primary btn-sm mt-3">Alterar imagem</a>
            </div>
            <hr class="border-secondary">

            <h5 class="card-titel mt-2"><?php echo esc($usuario->nome); ?></h5>
            <p class="card-text"><?php echo esc($usuario->email); ?></p>
            <p class="contributions mt-0">Situação:&nbsp; <?php echo $usuario->exibeSituacao(); ?></p>
            <p class="card-text">Criado:&nbsp;<?php echo $usuario->criado_em->humanize(); ?></p>
            <p class="card-text">Atualizado:&nbsp;<?php echo $usuario->atualizado_em->humanize(); ?></p>

            <!-- Example single danger button -->
            <div class="btn-group">
                <button type="button" class="btn btn-danger dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Ações
                </button>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="<?php echo site_url("admin/usuarios/editar/$usuario->id");  ?>">Editar</a>
                    <a class="dropdown-item" href="<?php echo site_url("admin/usuarios/grupos/$usuario->id");  ?>">Gerenciar os Grupos de Acesso</a>
                    <div class="dropdown-divider"></div>

                    <?php if ($usuario->deletado_em == null) :  ?>
                        <a class="dropdown-item" href="<?php echo site_url("admin/usuarios/excluir/$usuario->id")  ?>">Excluir Usuário</a>
                    <?php else : ?>
                        <a class="dropdown-item" href="<?php echo site_url("admin/usuarios/desfazerexclusao/$usuario->id")  ?>">Recuperar Usuário</a>
                    <?php endif; ?>
                </div>
            </div>
            <a href="<?php echo site_url('admin/usuarios')  ?>" class="btn btn-secondary ml-2">Voltar</a>
        </div> <!--  /block -->
    </div>

</div>
<?php echo $this->endSection(); ?>





<!-- Aqui enviamos para o template principal os scriptis -->
<?php echo $this->section('scripts'); ?>


<?php echo $this->endSection(); ?>