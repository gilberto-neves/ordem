<div class="form-group">
    <label class="form-control-label">Nome Completo</label>
    <input type="text" class="form-control" name="nome" value="<?php echo esc($usuario->nome); ?>">
</div>
<div class="form-group">
    <label class="form-control-label">Email</label>
    <input type="email" placeholder="Insira o E-mail de Acesso" class="form-control" name="email" value="<?php echo esc($usuario->email); ?>">
</div>
<div class=" form-group">
    <label class="form-control-label">Senha</label>
    <input type="password" placeholder="Senha" class="form-control" name="password">
</div>
<div class=" form-group">
    <label class="form-control-label">Conifrme a Senha</label>
    <input type="password" placeholder="Repita a senha" class="form-control" name="password_confirmation">
</div>

<div class="custom-control custom-checkbox">

    <input type="hidden" name="ativo" value="0">

    <input type="checkbox" class="custom-control-input" id="ativo" value="1" name="ativo"
        <?php if($usuario->ativo == true):  ?>
            checked
            
        <?php endif  ;?>
    >
    <label class="custom-control-label" for="ativo">Usuário Ativo</label>
</div>

