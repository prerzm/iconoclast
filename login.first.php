<?php

# include configuration file
include_once ('includes/inc.config.php');

# includes
include ('includes/lib.misc.php');
include ('includes/lib.sessions.php');
include ('includes/lib.vars.php');

# session
session_secure_start();

# form token
session_set_data( array("token" => sec_generate_random_token() ) );

?>
<!DOCTYPE html>
<html lang="es-MX">

	<head>
		<meta charset="utf-8">
		<title>Servicios ABP</title>
		<!-- Bootstrap -->
		<link href="bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
		<link href="bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet" media="screen">
		<link href="assets/styles.css" rel="stylesheet" media="screen">
		<link href="assets/DT_bootstrap.css" rel="stylesheet" media="screen">
		<link href="css/gabm.css" rel="stylesheet" media="screen">
		<!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
		<!--[if lt IE 9]>
			<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->
		<script src="vendors/modernizr-2.6.2-respond-1.1.0.min.js"></script>
		<script src="vendors/jquery-1.9.1.min.js"></script>
		<script src="bootstrap/js/bootstrap.min.js"></script>
		<script src="assets/scripts.js"></script>
	</head>

	<body id="login">

		<div class="container">

			<form name="form_register" id="form_register" class="form-signin" method="post" action="mod/login.php">
			<input type="hidden" name="cmd" value="recover">
			<input type="hidden" name="token" value="<?php print session_get_data("token"); ?>">

				<h2 class="form-signin-heading">Registro</h2>
				<div class="alert alert-error hide">
					<button class="close" data-dismiss="alert"></button>
					Hubo un problema. Favor de revisar la información.
				</div>
				<div class="alert alert-success hide">
					<button class="close" data-dismiss="alert"></button>
					La información es válida!
				</div>

                <?php display_alerts(); ?>
                
                <p>Favor de ingresar su RFC o correo electrónico y se le enviará un correo a la cuenta registrada previamente para que pueda establecer su contraseña.</p>

				<div class="control-group">
					<div class="controls">
						<input type="text" name="rfcemail" data-required="1" class="input-block-level" placeholder="RFC o Email" /><br>
					</div>
				</div>
				<div class="control-group">
					<div class="controls">
						<label for="aviso">
							<input type="checkbox" id="aviso" name="aviso" data-required="1" value="1" style="margin-top:0px;" />&nbsp;He leído y acepto el <a href="avisoprivacidad.html" target="_blank">Aviso de Privacidad</a>
						</label>
					</div>
				</div>

				<p style="text-align:center;"><a href="login.php">Iniciar Sesión</a></p><br>

				<button class="btn btn-large btn-primary" type="submit">Continuar</button>

			</form>

		</div> <!-- /container -->

		<!-- extra js -->
		<script type="text/javascript" src="vendors/jquery-validation/dist/jquery.validate.min.js"></script>

		<script>

			$(document).ready(function() {

				$('#form_register').validate({
					errorClass: 'help-inline',
					rules: {
						rfcemail: {
							minlength: 12,
							required: true
						},
						aviso: {
							required: true
						}
					},
					focusCleanup: false,

					highlight: function(label) {
						$(label).closest('.control-group').removeClass('success').addClass('error');
					},
					success: function(label) {
						label
							.addClass('valid')
							.closest('.control-group').addClass('success');
					},
					errorPlacement: function(error, element) {
						error.appendTo( element.parents ('.controls') );
					}
				});

				$('.form').eq (0).find ('input').eq (0).focus ();

			});

		</script>

	</body>

</html>