<?php require 'header.php'; ?>

<h1>Crear Transacción</h1>

<div class="">
	<div class="row">
		<div class="col-sm-12">
			<form action="<?php echo url('transacciones/store') ?>" class="form" id="formTransaction" method="POST" enctype="multipart/form-data">

				<div class="form-group col-sm-12">
					<div class="row">
						<div class="col-sm-6">
							<label for="bankCode">Selecciona el banco</label>
							<select name="bankCode" id="bankCode" class="form-control">
								<?php foreach ($banks as $key => $bank): ?>
									<option value="<?php echo $bank->bankCode?>"
										<?php if ($bank->bankCode == 1022): ?>
											<?php echo ' selected="selected"'; ?>
										<?php endif ?>
										>
										<?php echo $bank->bankName ?>
									</option>
								<?php endforeach ?>
							</select>
						</div>

						<div class="form-group col-sm-6">
							<label for="bankInterface">Tipo de Persona</label>
							<select name="bankInterface" id="bankInterface" class="form-control">
								<option value="0">Natural</option>
								<option value="1">Empresas</option>
							</select>
						</div>
					</div>
				</div>

				<div class="form-group col-sm-12">
					<div class="row">
						<div class="col-sm-6">
							<label for="document_type">Tipo de documento</label>
							<select name="documentType" id="document_type" class="form-control">
								<option value="CC">Cédula de ciudanía colombiana</option>
								<option value="CE">Cédula de extranjería</option>
								<option value="TI">Tarjeta de identidad</option>
								<option value="PPN">Pasaporte</option>
								<option value="NIT">Número de identificación tributaria</option>
								<option value="SSN">Social Security Number</option>
							</select>
						</div>

						<div class="form-group col-sm-6">
							<label for="document">Número de Documento</label>
							<input type="text" name="document" class="form-control" value="1020447057">
						</div>
					</div>
				</div>

				<div class="form-group col-sm-12">
					<div class="row">
						<div class="col-sm-6">
							<label for="firstName">Nombres</label>
							<input type="text" name="firstName" class="form-control" value="Cristian">
						</div>

						<div class="form-group col-sm-6">
							<label for="lastName">Apellidos</label>
							<input type="text" name="lastName" class="form-control" value="Galeano">
						</div>
					</div>
				</div>

				<div class="form-group col-sm-12">
					<div class="row">
						<div class="col-sm-6">
							<label for="emailAddress">Correo</label>
							<input type="text" name="emailAddress" class="form-control" value="cristian.galeano1913@gmail.com">
						</div>

						<div class="form-group col-sm-6">
							<label for="company">Empresa</label>
							<input type="text" name="company" class="form-control" value="Empresa de Prueba">
						</div>
					</div>
				</div>

				<div class="form-group col-sm-12">
					<div class="row">
						<div class="col-sm-6">
							<label for="address">Dirección</label>
							<input type="text" name="address" class="form-control" value="calle 34 # 33-33">
						</div>

						<div class="form-group col-sm-6">
							<label for="province">Departamento</label>
							<input type="text" name="province" class="form-control" value="Antioquia">
						</div>
					</div>
				</div>

				<div class="form-group col-sm-12">
					<div class="row">
						<div class="col-sm-6">
							<label for="city">Ciudad</label>
							<input type="text" name="city" class="form-control" value="Medellín">
						</div>

						<div class="form-group col-sm-6">
							<label for="country">País</label>
							<input type="text" name="country" class="form-control" value="Colombia">
						</div>
					</div>
				</div>

				<div class="form-group col-sm-12">
					<div class="row">
						<div class="col-sm-6">
							<label for="phone">Teléfono</label>
							<input type="text" name="phone" class="form-control" value="2363112">
						</div>

						<div class="form-group col-sm-6">
							<label for="mobile">Teléfono Celular</label>
							<input type="text" name="mobile" class="form-control" value="3192783398">
						</div>
					</div>
				</div>

				<div class="form-group col-sm-12">
					<center>
						<button class="btn btn-success">
							Crear Transacción
						</button>
					</center>
				</div>
			</form>
		</div>
	</div>
</div>

<?php require 'footer.php'; ?>