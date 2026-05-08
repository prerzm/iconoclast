<?php

/** HiperMedica **/

# include configuration file
include_once ("includes/inc.init.php");

# vars & filters
$directorId = (int)aget('id');

# queries
$record = get_director($directorId);

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
                                <h2 style="color:#1b54a3;">Directores</h2>
                            </div>
                        </div>
                        <!-- breadcrumb -->
                        <div class="navbar">
                            <div class="navbar-inner">
                                <ul class="breadcrumb">
                                    <i class="icon-chevron-left hide-sidebar"><a href="#" title="Hide Sidebar" rel="tooltip">&nbsp;</a></i>
                                    <i class="icon-chevron-right show-sidebar" style="display:none;"><a href="#" title="Show Sidebar" rel="tooltip">&nbsp;</a></i>
                                    <li><a href="index.php">Inicio</a> <span class="divider">/</span></li>
                                    <li><a href="directors.php">Directores</a> <span class="divider">/</span></li>
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
                                <form id="form_add" method="post" action="mod/directors.php">
                                <input type="hidden" name="cmd" value="update">
                                <input type="hidden" name="id" value="<?=$directorId;?>">
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
                                                <input type="text" name="directorNombre" data-required="1" class="span10 m-wrap" value="<?=$record['directorNombre'];?>" />
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">email</label>
                                            <div class="controls">
                                                <input type="text" name="email" class="span10 m-wrap" value="<?=$record['email'];?>" />
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">&nbsp;</label>
                                            <div class="controls">
                                                <button type="submit" class="btn btn-primary"><i class="icon-pencil icon-white"></i> Guardar</button>
                                                <button type="reset" class="btn btn-inverse" onclick="window.location='directors.php';"><i class="icon-arrow-left icon-white"></i> Cancelar</button>
                                                <?php if($record['activo']==1 && $global_perms['DELETE']) { ?>
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
                                                            <a class="btn btn-primary" href="mod/directors.php?cmd=off&id=<?=$directorId;?>">Confirmar</a>
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
                                                            <a class="btn btn-primary" href="mod/directors.php?cmd=on&id=<?=$directorId;?>">Confirmar</a>
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
                                                        <a class="btn btn-primary" href="mod/directors.php?cmd=del&id=<?=$directorId;?>">Confirmar</a>
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
        <script>

            $(document).ready(function() {

                $('#form_add').validate({
                    errorClass: 'help-inline',
                    rules: {
                        directorNombre: {
                            minlength: 5,
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

<?php include("inc.footer.php"); ?>