<div class="form-group">
    <label class="form-control-label">Nome</label>
    <input type="text" class="form-control" name="nome" value="<?php echo esc($grupo->nome); ?>">
</div>

<div class="form-group">
    <label class="form-control-label">Descrição</label>
    <textarea type="text" class="form-control" name="descricao"><?php echo esc($grupo->descricao); ?></textarea>
</div>


<div class="custom-control custom-checkbox">

    <input type="hidden" name="exibir" value="0">

    <input type="checkbox" class="custom-control-input" id="exibir" value="1" name="exibir" <?php if ($grupo->exibir == true) :  ?> checked <?php endif; ?>>
    <label class="custom-control-label" for="exibir">Exibir Grupo de Acesso</label>
    <?php if ($grupo->deletado_em == null) :  ?>
        <a tabindex="0" style="text-decoration: none;" role="button" data-toggle="popover" data-trigger="focus" title="Importante" data-content="Marque essa opção se pretende usar os usuários desse grupo como <b>Responsável Técnico</b> pela ordem de serviço"><i class="fa-solid fa-circle-question fa-lg"></i></a>
    <?php endif;  ?>
</div>