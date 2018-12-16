<?php include 'header.php'; ?>

<div class="container-fluid">
	<div class="jumbotron">
		<div class="col-sm-12">
			<center>
				<h2 class="text-center">
					Transacción <?php echo $pay->responseReasonText ?>
				</h2>

				<p>
					ID De la transacción: <?php echo $pay->transactionID ?>
				</p>

				<a href="<?php echo url('transacciones') ?>" class="btn btn-success">
					Ver Historial
				</a>
			</center>
		</div>
	</div>
</div>

<?php include 'footer.php'; ?>