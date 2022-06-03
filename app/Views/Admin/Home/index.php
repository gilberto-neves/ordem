<?php echo $this->extend('Admin/Layout/principal');  ?>


<?php echo $this->section('titulo'); ?>
<?php echo $titulo; ?>
<?php echo $this->endSection(); ?>



<!-- Aqui enviamos para o template principal os estilos -->
<?php echo $this->section('estilos'); ?>

<?php echo $this->endSection(); ?>




<!-- Aqui enviamos para o template principal os conteúdos -->
<?php echo $this->section('conteudo'); ?>
<h1>Tela Home</h1>

<?php echo $this->endSection(); ?>





<!-- Aqui enviamos para o template principal os scriptis -->
<?php echo $this->section('scripts'); ?>


<?php echo $this->endSection(); ?>