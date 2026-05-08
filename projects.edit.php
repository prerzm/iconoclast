<?php

/** HiperMedica **/

# include configuration file
include_once ("includes/inc.init.php");

# vars & filters
$projectId = (int)aget('id');

# queries
$record = get_project($projectId);
$directors = get_directors_all();

?>
<?php include("inc.header.main.php"); ?>

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
                                <h2 style="color:#1b54a3;">Proyectos</h2>
                            </div>
                        </div>
                        <!-- breadcrumb -->
                        <div class="navbar">
                            <div class="navbar-inner">
                                <ul class="breadcrumb">
                                    <i class="icon-chevron-left hide-sidebar"><a href="#" title="Hide Sidebar" rel="tooltip">&nbsp;</a></i>
                                    <i class="icon-chevron-right show-sidebar" style="display:none;"><a href="#" title="Show Sidebar" rel="tooltip">&nbsp;</a></i>
                                    <li><a href="index.php">Inicio</a> <span class="divider">/</span></li>
                                    <li><a href="projects.php">Proyectos</a> <span class="divider">/</span></li>
                                    <li class="active">Editar</li>
                                </ul>
                            </div>
                        </div>
                        <!-- ./breadcrumb -->
                    </div>
                    <!-- row -->
                    <div class="row-fluid">
                        <!-- block -->
                        <div class="block">
                            <div class="navbar navbar-inner block-header">
                                <div class="muted pull-left">Editar</div>
                            </div>
                            <div class="block-content collapse in">

                                <!-- add-form-->
                                <form id="form_add" method="post" action="mod/projects.php">
                                <input type="hidden" name="cmd" value="update">
                                <input type="hidden" name="id" value="<?=$projectId;?>">
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
                                            <label class="control-label"># de Proyecto<span class="required">*</span></label>
                                            <div class="controls">
                                                <input type="text" name="clave" data-required="1" class="span10 m-wrap" value="<?=$record['clave'];?>" />
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">Año del Proyecto<span class="required">*</span></label>
                                            <div class="controls">
                                                <input type="text" name="ano" data-required="1" class="span10 m-wrap" value="<?=$record['ano'];?>" />
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">Nombre<span class="required">*</span></label>
                                            <div class="controls">
                                                <input type="text" name="titulo" data-required="1" class="span10 m-wrap" value="<?=$record['titulo'];?>" />
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">Cliente</label>
                                            <div class="controls">
                                                <input type="text" name="cliente" data-required="1" class="span10 m-wrap" value="<?=$record['cliente'];?>" />
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">Fecha de Inicio</label>
                                            <div class="controls">
                                                <input type="text" name="fechaInicio" data-required="1" class="span10 m-wrap datepicker" value="<?=$record['fechaInicio'];?>" />
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">Fecha de Fin</label>
                                            <div class="controls">
                                                <input type="text" name="fechaFin" data-required="1" class="span10 m-wrap datepicker" value="<?=$record['fechaFin'];?>" />
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">Lugar</label>
                                            <div class="controls">
                                                <input type="text" name="lugar" data-required="1" class="span10 m-wrap" value="<?=$record['lugar'];?>" />
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">Director</label>
                                            <div class="controls">
                                                <select class="span10 m-wrap" name="directorId">
                                                    <option value="<?=$record['directorId'];?>" selected><?=$record['director'];?></option>
                                                    <?=form_select_options($directors, "proveedorId", "razonSocial", $record['directorId']);?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">Productor</label>
                                            <div class="controls">
                                                <input type="text" name="productor" data-required="1" class="span10 m-wrap" value="<?=$record['productor'];?>" />
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">Productor en línea</label>
                                            <div class="controls">
                                                <input type="text" name="productorLinea" data-required="1" class="span10 m-wrap" value="<?=$record['productorLinea'];?>" />
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">&nbsp;</label>
                                            <div class="controls">
                                                <button type="submit" class="btn btn-primary"><i class="icon-pencil icon-white"></i> Guardar</button>
                                                <button type="reset" class="btn btn-inverse" onclick="window.location='projects.php';"><i class="icon-arrow-left icon-white"></i> Cancelar</button>
                                                <?php if($record['activo']==1) { ?>
                                                    <a href="#alertDeactivate" data-toggle="modal" class="btn"><i class="icon-eye-close"></i> Ocultar</a>
                                                    <div id="alertDeactivate" class="modal hide">
                                                        <div class="modal-header">
                                                            <button data-dismiss="modal" class="close" type="button">&times;</button>
                                                            <h3>Ocultar</h3>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p>Al ocultar, el registro no será visible en los demás módulos.</p>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <a class="btn btn-primary" href="mod/projects.php?cmd=off&id=<?=$projectId;?>">Confirmar</a>
                                                            <a data-dismiss="modal" class="btn" href="#">Cancelar</a>
                                                        </div>
                                                    </div>
                                                <?php } else { ?>
                                                    <a href="#alertActivate" data-toggle="modal" class="btn"><i class="icon-eye-open"></i> Mostrar</a>
                                                    <div id="alertActivate" class="modal hide">
                                                        <div class="modal-header">
                                                            <button data-dismiss="modal" class="close" type="button">&times;</button>
                                                            <h3>Mostrar</h3>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p>Al mostrar, el registro será visible en todos los módulos.</p>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <a class="btn btn-primary" href="mod/projects.php?cmd=on&id=<?=$projectId;?>">Confirmar</a>
                                                            <a data-dismiss="modal" class="btn" href="#">Cancelar</a>
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                                <?php if($global_perms['DELETE']) { ?>
                                                    <a href="#myAlert" data-toggle="modal" class="btn btn-danger"><i class="icon-remove icon-white"></i> Eliminar</a>
                                                <?php } ?>
                                                <div id="myAlert" class="modal hide">
                                                    <div class="modal-header">
                                                        <button data-dismiss="modal" class="close" type="button">&times;</button>
                                                        <h3>Eliminar</h3>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>Está seguro que desea eliminar este registro?</p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <a class="btn btn-primary" href="mod/projects.php?cmd=del&id=<?=$projectId;?>">Confirmar</a>
                                                        <a data-dismiss="modal" class="btn" href="#">Cancelar</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </fieldset>
                                </form>
                                <!-- ./add-form -->
                                
                            </div>
                        </div>
                        <!-- /block -->
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
        <link rel="stylesheet" href="vendors/datepicker.css" media="screen">
        <script type="text/javascript" src="vendors/bootstrap-datepicker.js"></script>
        <script>

            $(document).ready(function() {

                $('#form_add').validate({
                    errorClass: 'help-inline',
                    rules: {
                        clave: {
                            minlength: 3,
                            required: true
                        },
                        titulo: {
                            minlength: 3,
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

                $(".datepicker").datepicker();

            });

        </script>

<?php include("inc.footer.php"); ?>