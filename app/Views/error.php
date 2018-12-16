<?php include 'header.php'; ?>

<div class="container-fluid">
	<div class="jumbotron">
		<div class="col-sm-12">
			<center>
				<?php if (isset($error)): ?>
					<h2 class="text-center">
					Ha Ocurrido un error:
				</h2>

				<p class="alert alert-danger">
					<?php echo $error['message']; ?>
				</p>

				<p>
				</p>
				<?php endif ?>
				<a href="<?php echo url('transacciones') ?>" class="btn btn-success">
					Ver Historial
				</a>
			</center>
		</div>
	</div>
</div>

<?php include 'footer.php'; ?>