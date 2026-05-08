<?php

# include configuration file
include_once ("includes/inc.init.php");

# vars & filters
$contractId = (int)aget('id');

# queries
$nombre = apost('nombre');

?>
<?php include("inc.header.main.php"); ?>

<link rel="stylesheet" type="text/css" href="vendors/bootstrap-wysihtml5/src/bootstrap-wysihtml5.css">

        <div class="container-fluid">
            
            <!-- row top -->
            <div class="row-fluid">
                <!-- sidebar -->
                <div class="span3 hide" id="sidebar">
                    <div class="row-fluid">
                    </div>
                </div>
                <!-- ./sidebar -->
                
                <!-- content span12 -->
                <div class="span12" id="content">

                    <div class="row-fluid">
                        <!-- alerts -->
                        <?php display_alerts(); ?>
                        <!-- ./alerts -->
                        <div class="block">
                            <div class="navbar navbar-inner block-header">
                                <h2 style="color:#1b54a3;">Agregar contrato <?=$nombre;?></h2>
                            </div>
                        </div>
                        <!-- breadcrumb -->
                        <div class="navbar">
                            <div class="navbar-inner">
                                <ul class="breadcrumb">
                                    <i class="icon-chevron-left hide-sidebar"><a href="#" title="Hide Sidebar" rel="tooltip">&nbsp;</a></i>
                                    <i class="icon-chevron-right show-sidebar" style="display:none;"><a href="#" title="Show Sidebar" rel="tooltip">&nbsp;</a></i>
                                    <li><a href="index.php">Inicio</a> <span class="divider">/</span></li>
                                    <li><a href="contracts.php">Contratos</a> <span class="divider">/</span></li>
                                    <li class="active">Agregar</li>
                                </ul>
                            </div>
                        </div>
                        <!-- ./breadcrumb -->
                    </div>
                    <!-- row -->

                    <div class="row-fluid">

                        <div class="span9">

                            <!-- block -->
                            <div class="block">

                                <div class="navbar navbar-inner block-header">
                                    <div class="muted pull-left">Contrato</div>
                                </div>

                                <div class="block-content collapse in">

                                    <!-- add-form-->
                                    <form id="form_add" method="post" action="mod/contracts.php">
                                    <input type="hidden" name="cmd" value="add">
                                        <fieldset>
                                            <div class="alert alert-error hide">
                                                <button class="close" data-dismiss="alert"></button>
                                                Hubo un problema. Favor de revisar la información.
                                            </div>
                                            <div class="alert alert-success hide">
                                                <button class="close" data-dismiss="alert"></button>
                                                La información es válida!
                                            </div>
                                            <div class="control-group">
                                                <label class="control-label">Nombre<span class="required">*</span></label>
                                                <div class="controls">
                                                    <input type="text" name="nombre" class="span10 m-wrap" value="<?=$nombre;?>" />
                                                </div>
                                            </div>
                                            <div class="control-group">
                                                <div class="controls">
                                                    <textarea id="bootstrap-editor" name="contrato" placeholder="Texto del contrato ..." style="width:98%;height:400px;"></textarea>
                                                </div>
                                            </div>
                                            <div class="control-group">
                                                <label class="control-label">&nbsp;</label>
                                                <div class="controls">
                                                    <button type="reset" class="btn btn-inverse" onclick="window.location='contracts.php';"><i class="icon-arrow-left icon-white"></i> Cancelar</button>
                                                    <button type="submit" class="btn btn-primary"><i class="icon-pencil icon-white"></i> Guardar</button>
                                                </div>
                                            </div>
                                        </fieldset>
                                    </form>
                                    <!-- ./add-form -->
                                    
                                </div>

                            </div>
                            <!-- /block -->

                        </div><!-- /span -->

                        <div class="span3">

                            <!-- block -->
                            <div class="block">

                                <div class="navbar navbar-inner block-header">
                                    <div class="muted pull-left">Valores</div>
                                </div>

                                <div class="block-content collapse in">

                                    <table style="width:90%;">
                                        <tr><td colspan="2"><strong>Estilo</strong></td></tr>
                                        <tr><td><strong>Negritas</strong></td><td>[ ]</td></tr>
                                        <tr><td>&nbsp;</td><td>&nbsp;</td></tr>
                                        <tr><td colspan="2"><strong>Proveedor</strong></td></tr>
                                        <tr><td><strong>RFC</strong></td><td>PROVEEDOR_RFC</td></tr>
                                        <tr><td><strong>Razón Social</strong></td><td>PROVEEDOR_RAZON_SOCIAL</td></tr>
                                        <tr><td><strong>Email</strong></td><td>PROVEEDOR_EMAIL</td></tr>
                                        <tr><td><strong>Banco</strong></td><td>PROVEEDOR_BANCO</td></tr>
                                        <tr><td><strong>Cuenta</strong></td><td>PROVEEDOR_CUENTA</td></tr>
                                        <tr><td><strong>CLABE</strong></td><td>PROVEEDOR_CLABE</td></tr>
                                        <tr><td>&nbsp;</td><td>&nbsp;</td></tr>
                                        <tr><td colspan="2"><strong>Proyecto</strong></td></tr>
                                        <tr><td><strong>Clave</strong></td><td>PROYECTO_CLAVE</td></tr>
                                        <tr><td><strong>Nombre</strong></td><td>PROYECTO_NOMBRE</td></tr>
                                        <tr><td><strong>Cliente</strong></td><td>PROYECTO_CLIENTE</td></tr>
                                        <tr><td><strong>Fecha te filmación</strong></td><td>PROYECTO_FECHA_FILMACION</td></tr>
                                        <tr><td><strong>Productor</strong></td><td>PROYECTO_PRODUCTOR</td></tr>
                                        <tr><td><strong>Director</strong></td><td>PROYECTO_DIRECTOR</td></tr>
                                    </table>
                                    
                                </div>

                            </div>
                            <!-- /block -->

                        </div><!-- /span -->

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
        <?php /*<script src="vendors/bootstrap-wysihtml5/lib/js/wysihtml5-0.3.0.js"></script>
        <script src="vendors/bootstrap-wysihtml5/src/bootstrap-wysihtml5.js"></script>*/ ?>
        <script>

            $(document).ready(function() {

                $('#form_add').validate({
                    errorClass: 'help-inline',
                    rules: {
                        nombre: {
                            minlength: 10,
                            required: true
                        },
                        contrato: {
                            minlength: 100,
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

                // Bootstrap editor
                //$('#bootstrap-editor').wysihtml5();
                //$('#bootstrap-editor-firma').wysihtml5();

            });

        </script>

<?php include("inc.footer.php"); ?>