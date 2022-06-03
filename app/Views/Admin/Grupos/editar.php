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

                <!-- Exibirá os retornos do Backend -->
                <div id="response">

                </div>

                <?php echo form_open('Admin/Grupos', ['id' => 'form'], ['id' => "$grupo->id"]); ?>

                <?php echo $this->include('Admin/Grupos/_form');  ?>

                <div class="form-group mt-5 mb-2">
                    <input type="submit" id="btn-salvar" value="Salvar" class="btn btn-success mr-2">
                    <a href="<?php echo site_url("admin/grupos/exibir/$grupo->id")  ?>" class="btn btn-secondary ml-2">Voltar</a>

                </div>

                <?php echo form_close();   ?>
            </div>


        </div> <!--  /block -->
    </div>

</div>
<?php echo $this->endSection(); ?>





<!-- Aqui enviamos para o template principal os scriptis -->
<?php echo $this->section('scripts'); ?>

<script>
    $(document).ready(function() {
        $("#form").on('submit', function(e) {

            e.preventDefault();

            $.ajax({

                type: 'POST',
                url: '<?php echo site_url('admin/grupos/atualizar');  ?>',
                data: new FormData(this),
                dataType: 'json',
                contentType: false,
                cache: false,
                processData: false,
                beforeSend: function() {
                    $("#response").html('');
                    $("#btn-salvar").val('Aguarde...');

                },
                success: function(response) {
                    $("#btn-salvar").val('Salvar');
                    $("#btn-salvar").removeAttr("disabled");

                    $('[name=csrf_ordem]').val(response.token);

                    if (!response.erro) {

                        if (response.info) {
                            $("#response").html('<div class="alert alert-info">' + response.info + '</div>');

                        } else {

                            // Tudo certo com a atualização do Usuário
                            // Podemos agora redirecioná-lo tranquilamente

                            window.location.href = "<?php echo site_url("admin/grupos/exibir/$grupo->id"); ?>";
                        }

                    } else {
                        // Existem erros de validação!

                        $("#response").html('<div class="alert alert-danger">' + response.erro + '</div>');

                        if (response.erros_model) {

                            $.each(response.erros_model, function(key, value) {

                                $("#response").append('<ul class="list-unstyled"><li class="text-danger"> ' + value + ' </li></ul>');

                            });
                        }
                    }
                },

                error: function() {
                    alert('Não foi possível processar a solicitação. Por favor entre em contato com o suporte!');
                    $("#btn-salvar").val('Salvar');
                    $("#btn-salvar").removeAttr("disabled");
                },
            });

        });

        $("#form").submit(function() {

            $(this).find(":submit").attr('disabled', 'disabled');

        });

    });
</script>

<?php echo $this->endSection(); ?>