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

    <div class="col-lg-6">
        <div class="block">

            <div class="block-body">


                <?php echo form_open("admin/usuarios/excluir/$usuario->id"); ?>

                <div class="alert alert-warning" role="alert">
                    Tem certeza da Exclusão do Registro?
                </div>

                
                <div class="form-group mt-5 mb-2">
                    <input type="submit" id="btn-salvar" value="Sim" class="btn btn-success mr-2">
                    <a href="<?php echo site_url("admin/usuarios/exibir/$usuario->id")  ?>" class="btn btn-secondary ml-2">Cancelar</a>

                </div>

                <?php echo form_close();   ?>
            </div>


        </div> <!--  /block -->
    </div>

</div>
<?php echo $this->endSection(); ?>





<!-- Aqui enviamos para o template principal os scriptis -->
<?php echo $this->section('scripts'); ?>



<?php echo $this->endSection(); ?>