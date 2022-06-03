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
    <?php if ($grupo->id < 3) :  ?>
        <div class="md-12">
            <div class="alert alert-info" role="alert">
                <h4 class="alert-heading">Importante!</h4>
                <p>O grupo <b><?php echo esc($grupo->nome); ?></b> não pode ser editado ou excluído, pois não pode ter suas permissões revogadas.</p>
            </div>
        </div>
    <?php endif;  ?>
    <div class="col-lg-4">
        <div class="user-block block text-center">

            <h5 class="card-titel mt-2"><?php echo esc($grupo->nome); ?></h5>
            <p class="contributions mt-0"><?php echo $grupo->exibeSituacao(); ?>&nbsp;&nbsp;

            </p>
            <?php if ($grupo->deletado_em == null) :  ?>
                <a tabindex="0" style="text-decoration: none;" role="button" data-toggle="popover" data-trigger="focus" title="Importante" data-content="Esse Grupo <?php echo ($grupo->exibir == true ? 'será' : 'não será');  ?> exibido como opção na hora de definir um <b>Responsável Técnico</b> pela ordem de serviço"><i class="fa-solid fa-circle-question fa-lg"></i></a>
            <?php endif;  ?>
            <p class="card-text"><?php echo esc($grupo->descricao); ?></p>
            <p class="card-text">Criado:&nbsp;<?php echo $grupo->criado_em->humanize(); ?></p>
            <p class="card-text">Atualizado:&nbsp;<?php echo $grupo->atualizado_em->humanize(); ?></p>

            <!-- Example single danger button -->
            <?php if ($grupo->id > 2) :  ?>
                <div class="btn-group mr-2">
                    <button type="button" class="btn btn-danger dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Ações
                    </button>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="<?php echo site_url("admin/grupos/editar/$grupo->id");  ?>">Editar Grupo de Acesso</a>
                        <?php if($grupo->id > 2): ?>
                            <a class="dropdown-item" href="<?php echo site_url("admin/grupos/permissoes/$grupo->id");  ?>">Editar Permissões do Grupo de Acesso</a>
                        <?php endif;  ?>
                        <div class="dropdown-divider"></div>

                        <?php if ($grupo->deletado_em == null) :  ?>
                            <a class="dropdown-item" href="<?php echo site_url("admin/grupos/excluir/$grupo->id")  ?>">Excluir Grupo de Acesso</a>
                        <?php else : ?>
                            <a class="dropdown-item" href="<?php echo site_url("admin/grupos/desfazerexclusao/$grupo->id")  ?>">Recuperar Grupo de Acesso</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif;  ?>
            <a href="<?php echo site_url('admin/grupos')  ?>" class="btn btn-secondary">Voltar</a>
        </div> <!--  /block -->
    </div>

</div>
<?php echo $this->endSection(); ?>





<!-- Aqui enviamos para o template principal os scriptis -->
<?php echo $this->section('scripts'); ?>


<?php echo $this->endSection(); ?>