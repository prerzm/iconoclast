<?php

# include configuration file
include_once ("includes/inc.init.php");
include_once ("includes/class.settings.php");

# queries
$company_id = (int)aget('id');
$company_info = get_company_info($company_id);
$regimen = sql_select("SELECT regimenId, CONCAT(claveRegimen, ' - ', regimen) AS regimen FROM ".TABLE_SAT_REGIMEN_FISCAL." ORDER BY claveRegimen ASC");
$forma = sql_select("SELECT pagoFormaId, CONCAT(claveFormaPago, ' - ', pagoForma) AS pagoForma FROM ".TABLE_SAT_FORMA_PAGO." ORDER BY claveFormaPago ASC");
$metodo = sql_select("SELECT pagoMetodoId, CONCAT(claveMetodoPago, ' - ', pagoMetodo) AS pagoMetodo FROM ".TABLE_SAT_METODO_PAGO." ORDER BY claveMetodoPago ASC");
$uso = sql_select("SELECT usoCfdiId, CONCAT(claveUso, ' - ', uso) AS uso FROM ".TABLE_SAT_USO_CFDI." ORDER BY claveUso ASC");

?>
<?php include("inc.header.main.php"); ?>

		<div class="container-fluid">
			
			<!-- row top -->
			<div class="row-fluid">
				
				<!-- content span -->
				<div class="span12" id="content">
					<div class="row-fluid">
						<!-- alerts -->
						<?php display_alerts(); ?>
						<!-- ./alerts -->
						<div class="block">
							<div class="navbar navbar-inner block-header">
								<h2 style="color:#1b54a3;">Configuración de <?=$company_info['razonSocial'];?></h2>
							</div>
						</div>
						<!-- breadcrumb -->
						<div class="navbar">
							<div class="navbar-inner">
								<ul class="breadcrumb">
									<i class="icon-chevron-left hide-sidebar"><a href="#" title="Hide Sidebar" rel="tooltip">&nbsp;</a></i>
									<i class="icon-chevron-right show-sidebar" style="display:none;"><a href="#" title="Show Sidebar" rel="tooltip">&nbsp;</a></i>
									<li><a href="index.php">Inicio</a> <span class="divider">/</span></li>
									<li><a href="companies.php">Empresas</a> <span class="divider">/</span></li>
									<li class="active">Configuración de la Empresa</li>
								</ul>
							</div>
						</div>
						<!-- ./breadcrumb -->
					</div>
					<!-- row -->
					<div class="row-fluid">

						<!-- form mass-edit -->
						<form method="post" action="mod/companies.php" enctype="multipart/form-data">
						<input type="hidden" name="cmd" value="update">
                        <input type="hidden" name="id" value="<?=$company_id;?>">
						<?php if((bool)$company_info['extranjera']==false) { ?>
							<input type="hidden" name="field[taxid]" value="">
						<?php } ?>

						<!-- block -->
						<div class="block">
							<div class="navbar navbar-inner block-header">
								<div class="muted pull-left">Resultados</div>
							</div>
							<div class="block-content collapse in">
								<div class="span12">

									<div class="table-toolbar">
										<div class="btn-group">
											<a href="#" id="button_Info" class="btn btn-primary" onclick="change_cat('Info');">Información</a>
											<a href="#" id="button_Facturas" class="btn" onclick="change_cat('Facturas');">Facturas</a>
											<a href="#" id="button_Contratos" class="btn" onclick="change_cat('Contratos');">Contratos</a>
											<a href="#" id="button_Pagos" class="btn" onclick="change_cat('Pagos');">Pagos</a>
											<?php /*
											<a href="#" id="button_Valores" class="btn" onclick="change_cat('Valores');">Valores</a>
											*/ ?>
										</div>
									</div>

									<table cellpadding="0" cellspacing="0" class="table table-striped table-bordered" id="results">
										<thead>
											<tr>
												<th>Configuración</th>
												<th>Valor</th>
											</tr>
										</thead>
										<tbody>
											<!-- Info -->
											<tr class="row_Info" >
												<td>Dirección de la compañía</td>
												<td><input type="text" name="field[direccion]" id="direccion" class="span6 m-wrap" value="<?=$company_info['direccion'];?>"></td>
											</tr>
											<tr class="row_Info" >
												<td>País</td>
												<td><input type="text" name="field[pais]" id="pais" class="span6 m-wrap" value="<?=$company_info['pais'];?>"></td>
											</tr>
											<tr class="row_Info" >
												<td>Código Postal</td>
												<td><input type="text" name="field[cp]" id="cp" class="span6 m-wrap" value="<?=$company_info['cp'];?>"></td>
											</tr>
											<?php if((bool)$company_info['extranjera']) { ?>
												<tr class="row_Info" >
													<td>TAX ID:</td>
													<td><input type="text" name="field[taxid]" id="taxid" class="span6 m-wrap" value="<?=$company_info['taxid'];?>"></td>
												</tr>
											<?php } ?>
											<tr class="row_Info" >
												<td>Régimen fiscal</td>
												<td>
													<select class="span5 m-wrap" name="regimenId">
														<?=form_select_options($regimen, "regimenId", "regimen", $company_info['regimenId']);?>
													</select>
												</td>
											</tr>
											<tr class="row_Info" >
												<td>Correo de la compañía</td>
												<td><input type="text" name="field[email]" id="email" class="span6 m-wrap" value="<?=$company_info['email'];?>"></td>
											</tr>
											
											<!-- Facturas -->
											<tr class="row_Facturas" style="display:none;">
												<td>Facturas a revisión - Forma de Pago por omisión</td>
												<td>
													<select name="field[revisionPagoFormaId]" id="revisionPagoFormaId" class="span6 m-wrap">
														<?=form_select_options($forma, "pagoFormaId", "pagoForma", $company_info['revisionPagoFormaId']);?>
													</select>
												</td>
											</tr>
											<tr class="row_Facturas" style="display:none;">
												<td>Facturas a revisión - Método de Pago por omisión</td>
												<td>
													<select name="field[revisionPagoMetodoId]" id="revisionPagoMetodoId" class="span6 m-wrap">
														<?=form_select_options($metodo, "pagoMetodoId", "pagoMetodo", $company_info['revisionPagoMetodoId']);?>
													</select>
												</td>
											</tr>
											<tr class="row_Facturas" style="display:none;">
												<td>Facturas a revisión - Uso del CFDI por omisión</td>
												<td>
													<select name="field[revisionUsoCfdiId]" id="revisionUsoCfdiId" class="span6 m-wrap">
														<?=form_select_options($uso, "usoCfdiId", "uso", $company_info['revisionUsoCfdiId']);?>
													</select>
												</td>
											</tr>
											<tr class="row_Facturas" style="display:none;">
												<td>Facturas de comprobación - Forma de Pago por omisión</td>
												<td>
													<select name="field[comprobacionPagoFormaId]" id="comprobacionPagoFormaId" class="span6 m-wrap">
														<?=form_select_options($forma, "pagoFormaId", "pagoForma", $company_info['comprobacionPagoFormaId']);?>
													</select>
												</td>
											</tr>
											<tr class="row_Facturas" style="display:none;">
												<td>Facturas de comprobación - Método de Pago por omisión</td>
												<td>
													<select name="field[comprobacionPagoMetodoId]" id="comprobacionPagoMetodoId" class="span6 m-wrap">
														<?=form_select_options($metodo, "pagoMetodoId", "pagoMetodo", $company_info['comprobacionPagoMetodoId']);?>
													</select>
												</td>
											</tr>
											<tr class="row_Facturas" style="display:none;">
												<td>Facturas de comprobación - Uso del CFDI por omisión</td>
												<td>
													<select name="field[comprobacionUsoCfdiId]" id="comprobacionUsoCfdiId" class="span6 m-wrap">
														<?=form_select_options($uso, "usoCfdiId", "uso", $company_info['comprobacionUsoCfdiId']);?>
													</select>
												</td>
											</tr>

											<!-- Contratos -->
											<tr class="row_Contratos" style="display:none;">
												<td>Generar contrato a Proveedores</td>
												<td>
													<label><input type="radio" name="field[generarContrato]" value="1" <?=($company_info['generarContrato']==1) ? 'checked' : '' ;?>> Si</label>
													<label><input type="radio" name="field[generarContrato]" value="0" <?=($company_info['generarContrato']==0) ? 'checked' : '' ;?>> No</label>
												</td>
											</tr>
											<tr class="row_Contratos" style="display:none;">
												<td>
													Firma para contratos<br>
													La firma debe ser de 190x135 pixeles y tener fondo transparente.
												</td>
												<td>
													<img src="files/signatures/<?=$company_info['firmaContratos'];?>" /><br>
													<input type="file" name="firmaContratos" id="firmaContratos" accept="image/png, image/gif, image/jpeg" />
												</td>
											</tr>

											<!-- Pagos -->
											<tr class="row_Pagos" style="display:none;">
												<td>Día de pago a proveedores</td>
												<td>
													<select name="field[pagoDiaDePago]" id="pagoDiaDePago" class="span6 m-wrap">
														<option value="1" <?=($company_info['pagoDiaDePago']==1) ? 'selected' : '' ;?>>Lunes</option>
														<option value="2" <?=($company_info['pagoDiaDePago']==2) ? 'selected' : '' ;?>>Martes</option>
														<option value="3" <?=($company_info['pagoDiaDePago']==3) ? 'selected' : '' ;?>>Miércoles</option>
														<option value="4" <?=($company_info['pagoDiaDePago']==4) ? 'selected' : '' ;?>>Jueves</option>
														<option value="5" <?=($company_info['pagoDiaDePago']==5) ? 'selected' : '' ;?>>Viernes</option>
													</select>
												</td>
											</tr>
											<tr class="row_Pagos" style="display:none;">
												<td>Número de días para pago a Proveedores</td>
												<td><input type="text" name="field[pagoDias]" id="pagoDias" class="span6 m-wrap" value="<?=$company_info['pagoDias'];?>"></td>
											</tr>
										</tbody>
									</table>

									<div class="table-toolbar">
										<div class="btn-group">
											<button type="submit" class="btn btn-primary">Guardar</button>
										</div>
									</div>
									
								</div>
							</div>
						</div>
						<!-- /block -->

						</form>

					</div>
					<!-- ./row -->
				</div><!-- ./content span9 -->

			</div><!-- ./row top -->

			<hr>
			<footer>
				<p> <?=SITE_FOOTER_COPY;?></p>
			</footer>
		</div><!--/.fluid-container-->

		<!-- extra js -->
		<script type="text/javascript" src="vendors/jquery-validation/dist/jquery.validate.min.js"></script>
		<script type="text/javascript" src="vendors/datatables/js/jquery.dataTables.min.js"></script>
		<script type="text/javascript" src="assets/DT_bootstrap.js"></script>

		<script>

			function change_cat(cat) {
				$("#button_Info").removeClass('btn-primary');
				$(".row_Info").hide();
				$("#button_Facturas").removeClass('btn-primary');
				$(".row_Facturas").hide();
				$("#button_Pagos").removeClass('btn-primary');
				$(".row_Pagos").hide();
				$("#button_Contratos").removeClass('btn-primary');
				$(".row_Contratos").hide();
				$("#button_Valores").removeClass('btn-primary');
				$(".row_Valores").hide();
				
				$("#button_"+cat).addClass('btn-primary');
				$(".row_"+cat).show();
			}

		</script>

<?php include("inc.footer.php"); ?>