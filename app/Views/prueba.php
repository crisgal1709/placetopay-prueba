<?php require 'header.php'; ?>

<form action="<?php echo url('index/pruebaFiles'); ?>" method="POST" enctype="multipart/form-data" >

	<input type="file" name="archivos">

	<button>Enviar</button>
	
</form>
<?php require 'footer.php'; ?>