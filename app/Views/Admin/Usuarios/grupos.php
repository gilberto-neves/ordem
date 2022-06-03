<?php echo $this->extend('Admin/Layout/principal');  ?>


<?php echo $this->section('titulo'); ?>
<?php echo $titulo; ?>
<?php echo $this->endSection(); ?>



<!-- Aqui enviamos para o template principal os estilos -->
<?php echo $this->section('estilos'); ?>
<link rel="stylesheet" href="<?php echo site_url('assets/plugins/selectize/'); ?>selectize.bootstrap4.css">">
<style>
    /* Estilizando o select para acompanhar a formatação do template */

    .selectize-input,
    .selectize-control.single .selectize-input.input-active {
        background: #282b2f !important;
    }

    .selectize-dropdown,
    .selectize-input,
    .selectize-input input {
        color: #777;
    }

    .selectize-input {
        /*        height: calc(2.4rem + 2px);*/
        border: 1px solid #444951;
        border-radius: 0;
    }
</style>

<?php echo $this->endSection(); ?>




<!-- Aqui enviamos para o template principal os conteúdos -->
<?php echo $this->section('conteudo'); ?>

<div class="row">
    <div class="col-lg-8">
        <div class="user-block block">

            <?php if (empty($gruposDisponiveis)) :  ?>

                <?php if ($usuario->full_control == false) : ?>

                    <p class="contributions text-danger mt-0">Esse usuário já faz parte de todos os grupos disponíveis!</p>

                <?php else : ?>

                    <p class="contributions text-white mt-0">Esse usuário já faz parte ddo Grupo Administrador, para associá-lo à outros grupo, primeiro remova-o do Grupo Administrador.</p>

                <?php endif; ?>
            <?php else : ?>

                <!-- Exibirá os retornos do Backend -->
                <div id="response">

                </div>

                <?php echo form_open('Admin/Grupos', ['id' => 'form'], ['id' => "$usuario->id"]); ?>

                <div class="form-group">
                    <label class="form-control-label">Escolha uma ou mais grupos de acesso.</label>
                    <select name="grupo_id[]" class="selectize" id="selectize" multiple>

                        <option value="">Escolha...</option>

                        <?php foreach ($gruposDisponiveis as $grupo) : ?>
                            <option value="<?php echo $grupo->id; ?>"><?php echo esc($grupo->nome); ?></option>
                        <?php endforeach; ?>

                    </select>

                </div>


                <div class="form-group mt-5 mb-2">
                    <input type="submit" id="btn-salvar" value="Salvar" class="btn btn-success mr-2">
                    <a href="<?php echo site_url("admin/usuarios/exibir/$usuario->id")  ?>" class="btn btn-secondary ml-2">Voltar</a>

                </div>

                <?php echo form_close();   ?>
            <?php endif; ?>
        </div>

    </div>
    <div class="col-lg-12">
        <div class="user-block block text-center">

            <?php if (empty($usuario->grupos)) : ?>
                <p class="contributions text-danger mt-0">Esse usuário ainda não faz parte de nenhum grupo de acesso!</p>
            <?php else :  ?>
                <div class="table-responsive">
                    <table class="table table-striped mb-3">
                        <thead>
                            <tr>
                                <th>Grupo de Acesso</th>
                                <th>Descrição</th>
                                <th>Excluir</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($usuario->grupos as $info) :  ?>
                                <tr>
                                    <td><?php echo esc($info->nome); ?></td>
                                    <td><?php echo esc($info->descricao); ?></td>
                                    <td>
                                        <?php
                                        $atributos = [
                                            'onSubmit'  => "return confirm('Tem certeza da exclusão do Grupo de Acesso?');",
                                        ];
                                        ?>
                                        <?php echo form_open("admin/usuarios/removegrupo/$info->principal_id", $atributos); ?>
                                        <button type="submit" href="#" class="btn btn-sm btn-danger">Excluir</button>
                                        <?php echo form_close(); ?>
                                    </td>

                                </tr>
                            <?php endforeach;  ?>
                        </tbody>
                    </table>
                    <?php echo $usuario->pager->links('default', 'bootstrap_pagination');  ?>
                </div>
            <?php endif;  ?>


        </div> <!--  /block -->
    </div>

</div>
<?php echo $this->endSection(); ?>





<!-- Aqui enviamos para o template principal os scriptis -->
<?php echo $this->section('scripts'); ?>

<script type="text/javascript" src="<?php echo site_url('assets/plugins/selectize/'); ?>selectize.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.13.5/js/standalone/selectize.js" integrity="sha512-JFjt3Gb92wFay5Pu6b0UCH9JIOkOGEfjIi7yykNWUwj55DBBp79VIJ9EPUzNimZ6FvX41jlTHpWFUQjog8P/sw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
    $(document).ready(function() {

        $(".selectize").selectize({

            create: true,
            sortField: "text",
        });

        $("#form").on('submit', function(e) {

            e.preventDefault();

            $.ajax({

                type: 'POST',
                url: '<?php echo site_url('admin/usuarios/salvargrupos');  ?>',
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

                        window.location.href = "<?php echo site_url("admin/usuarios/grupos/$usuario->id"); ?>";

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