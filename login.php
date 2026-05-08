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
        <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
        <!--[if lt IE 9]>
        <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->
        <script src="js/vendor/modernizr-2.6.2-respond-1.1.0.min.js"></script>
        <link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon">
        <link rel="icon" href="images/favicon.ico" type="image/x-icon">
    </head>

    <body id="login">

        <div class="container">

            <form class="form-signin" method="post" action="mod/login.php">
            <input type="hidden" name="cmd" value="login">
            <input type="hidden" name="token" value="<?php print session_get_data("token"); ?>">

                <h2 class="form-signin-heading">Iniciar Sesión</h2>
				<div class="alert alert-error hide">
					<button class="close" data-dismiss="alert"></button>
					Hubo un problema. Favor de revisar la información.
				</div>
				<div class="alert alert-success hide">
					<button class="close" data-dismiss="alert"></button>
					La información es válida!
				</div>

                <?php display_alerts(); ?>

                <input type="text" id="rfcemail" name="user" class="input-block-level" placeholder="RFC o Email">
                <input type="password" id="password" name="password" class="input-block-level" placeholder="Contraseña">
                <div class="control-group">
					<div class="controls">
						<label for="aviso">
							<input type="checkbox" id="aviso" name="aviso" data-required="1" value="1" style="margin-top:0px;" />&nbsp;He leído y acepto el <a href="avisoprivacidad.html" target="_blank">Aviso de Privacidad</a>
						</label>
					</div>
				</div>

                <p style="text-align:center;"><a href="login.first.php">Ingresar por primera vez</a></p>
                <p style="text-align:center;"><a href="login.recover.php">Recuperar contraseña</a></p>

                <button class="btn btn-large btn-primary" type="submit">Ingresar</button>
                <a href="#alertHelp" data-toggle="modal" class="btn btn-large btn-info" style="float:right;">Ayuda</a>
                <div style="margin-top:20px;text-align:center;">
                    <img src="images/PrimoSelloCert.gif" />
                </div>
                <div id="alertHelp" class="modal hide">
                    <div class="modal-header">
                        <button data-dismiss="modal" class="close" type="button">&times;</button>
                        <h3>Ayuda</h3>
                    </div>
                    <div class="modal-body" style="padding:0%;">
                        <img src="images/help/help01.png" />
                    </div>
                    <div class="modal-footer">
                        <a data-dismiss="modal" class="btn btn-primary" href="#alertRegister1" data-toggle="modal">Primera Vez</a>
                        <a data-dismiss="modal" class="btn btn-info" href="#alertRecover1" data-toggle="modal">Recuperar Contraseña</a>
                        <a data-dismiss="modal" class="btn" href="#">Cancelar</a>
                    </div>
                </div>
                <div id="alertRegister1" class="modal hide">
                    <div class="modal-header">
                        <button data-dismiss="modal" class="close" type="button">&times;</button>
                        <h3>Ayuda - Ingresar por primera vez</h3>
                    </div>
                    <div class="modal-body">
                        <img src="images/help/helpReg01.png" />
                    </div>
                    <div class="modal-footer">
                        <a data-dismiss="modal" class="btn btn-primary" href="#alertRegister2" data-toggle="modal">Siguiente</a>
                        <a data-dismiss="modal" class="btn" href="#">Cerrar</a>
                    </div>
                </div>
                <div id="alertRegister2" class="modal hide">
                    <div class="modal-header">
                        <button data-dismiss="modal" class="close" type="button">&times;</button>
                        <h3>Ayuda - Ingresar por primera vez</h3>
                    </div>
                    <div class="modal-body" style="padding:0%;">
                        <img src="images/help/helpReg02.png" />
                    </div>
                    <div class="modal-footer">
                        <a data-dismiss="modal" class="btn btn-primary" href="#alertRegister3" data-toggle="modal">Siguiente</a>
                        <a data-dismiss="modal" class="btn" href="#">Cerrar</a>
                    </div>
                </div>
                <div id="alertRegister3" class="modal hide">
                    <div class="modal-header">
                        <button data-dismiss="modal" class="close" type="button">&times;</button>
                        <h3>Ayuda - Ingresar por primera vez</h3>
                    </div>
                    <div class="modal-body" style="padding:0%;">
                        <img src="images/help/helpReg03.png" />
                    </div>
                    <div class="modal-footer">
                        <a data-dismiss="modal" class="btn btn-primary" href="#alertRegister4" data-toggle="modal">Siguiente</a>
                        <a data-dismiss="modal" class="btn" href="#">Cerrar</a>
                    </div>
                </div>
                <div id="alertRegister4" class="modal hide">
                    <div class="modal-header">
                        <button data-dismiss="modal" class="close" type="button">&times;</button>
                        <h3>Ayuda - Ingresar por primera vez</h3>
                    </div>
                    <div class="modal-body" style="padding:0%;">
                        <img src="images/help/helpReg04.png" />
                    </div>
                    <div class="modal-footer">
                        <a data-dismiss="modal" class="btn" href="#">Cerrar</a>
                    </div>
                </div>

                <div id="alertRecover1" class="modal hide">
                    <div class="modal-header">
                        <button data-dismiss="modal" class="close" type="button">&times;</button>
                        <h3>Ayuda - Recuperar contraseña</h3>
                    </div>
                    <div class="modal-body">
                        <img src="images/help/helpRec01.png" />
                    </div>
                    <div class="modal-footer">
                        <a data-dismiss="modal" class="btn btn-primary" href="#alertRecover2" data-toggle="modal">Siguiente</a>
                        <a data-dismiss="modal" class="btn" href="#">Cerrar</a>
                    </div>
                </div>
                <div id="alertRecover2" class="modal hide">
                    <div class="modal-header">
                        <button data-dismiss="modal" class="close" type="button">&times;</button>
                        <h3>Ayuda - Recuperar contraseña</h3>
                    </div>
                    <div class="modal-body" style="padding:0%;">
                        <img src="images/help/helpRec02.png" />
                    </div>
                    <div class="modal-footer">
                        <a data-dismiss="modal" class="btn btn-primary" href="#alertRecover3" data-toggle="modal">Siguiente</a>
                        <a data-dismiss="modal" class="btn" href="#">Cerrar</a>
                    </div>
                </div>
                <div id="alertRecover3" class="modal hide">
                    <div class="modal-header">
                        <button data-dismiss="modal" class="close" type="button">&times;</button>
                        <h3>Ayuda - Recuperar contraseña</h3>
                    </div>
                    <div class="modal-body" style="padding:0%;">
                        <img src="images/help/helpRec03.png" />
                    </div>
                    <div class="modal-footer">
                        <a data-dismiss="modal" class="btn btn-primary" href="#alertRegister4" data-toggle="modal">Siguiente</a>
                        <a data-dismiss="modal" class="btn" href="#">Cerrar</a>
                    </div>
                </div>
                <div id="alertRecover4" class="modal hide">
                    <div class="modal-header">
                        <button data-dismiss="modal" class="close" type="button">&times;</button>
                        <h3>Ayuda - Recuperar contraseña</h3>
                    </div>
                    <div class="modal-body" style="padding:0%;">
                        <img src="images/help/helpRec04.png" />
                    </div>
                    <div class="modal-footer">
                        <a data-dismiss="modal" class="btn" href="#">Cerrar</a>
                    </div>
                </div>

            </form>

        </div> <!-- /container -->

		<!-- extra js -->
        <script src="vendors/jquery-1.9.1.min.js"></script>
        <script src="bootstrap/js/bootstrap.min.js"></script>
		<script type="text/javascript" src="vendors/jquery-validation/dist/jquery.validate.min.js"></script>

		<script>

			$(document).ready(function() {

				$('#form-signin').validate({
					errorClass: 'help-inline',
					rules: {
						rfcemail: {
							minlength: 12,
							required: true
						},
						password: {
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