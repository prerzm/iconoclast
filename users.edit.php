<?php

/** HiperMedica **/

# include configuration file
include_once ("includes/inc.init.php");

# vars & filters
$userId = (int)aget('id');

# queries
$record = sql_select_row("SELECT * FROM ".TABLE_USERS." WHERE usuarioId = $userId");
$companies = get_companies();
$user_companies = sql_select("SELECT c.* FROM ".TABLE_COMPANIES." c, ".TABLE_USERS_COMPANIES." uc WHERE c.companyId = uc.companyId AND uc.usuarioId = $userId");
$roles = get_roles();

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
                                <h2 style="color:#1b54a3;">Usuarios</h2>
                            </div>
                        </div>
                        <!-- breadcrumb -->
                        <div class="navbar">
                            <div class="navbar-inner">
                                <ul class="breadcrumb">
                                    <i class="icon-chevron-left hide-sidebar"><a href="#" title="Hide Sidebar" rel="tooltip">&nbsp;</a></i>
                                    <i class="icon-chevron-right show-sidebar" style="display:none;"><a href="#" title="Show Sidebar" rel="tooltip">&nbsp;</a></i>
                                    <li><a href="index.php">Inicio</a> <span class="divider">/</span></li>
                                    <li>Sistema <span class="divider">/</span></li>
                                    <li><a href="users.php">Usuarios</a> <span class="divider">/</span></li>
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
                                <form id="form_add" method="post" action="mod/users.php">
                                <input type="hidden" name="cmd" value="user_update">
                                <input type="hidden" name="id" value="<?=$userId;?>">
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
                                                <input type="text" name="name" data-required="1" class="span10 m-wrap" value="<?=$record['nombre'];?>" />
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">Email<span class="required">*</span></label>
                                            <div class="controls">
                                                <input type="text" name="email" class="span10 m-wrap" value="<?=$record['email'];?>" />
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">Contraseña<span class="required">*</span></label>
                                            <div class="controls">
                                                <input type="password" name="password" data-required="1" class="span10 m-wrap"/>
                                                <p class="help-block">Solo si desea cambiar</p>
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">Compañías<span class="required">*</span></label>
                                            <div class="controls">
                                                <?php foreach($companies as $c) { ?>
                                                    <label><input type="checkbox" name="companies[]" value="<?=$c['companyId'];?>" <?=(get_user_in_company($c['companyId'], $user_companies)) ? 'checked' : '';?>> <?=$c['razonSocial'];?> (<?=$c['nombre'];?>)</label>
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">Rol<span class="required">*</span></label>
                                            <div class="controls">
                                                <select class="span10 m-wrap" id="roleId" name="roleId">
                                                    <?=form_select_options($roles, "rolId", "rol", $record['rolId']);?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">&nbsp;</label>
                                            <div class="controls">
                                                <button type="submit" class="btn btn-primary"><i class="icon-pencil icon-white"></i> Guardar</button>
                                                <button type="reset" class="btn btn-inverse" onclick="window.location='users.php';"><i class="icon-arrow-left icon-white"></i> Cancelar</button>
                                                <?php if($record['deleted']==0 && $global_perms['DELETE'] ) { ?>
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
                                                        <a class="btn btn-primary" href="mod/users.php?cmd=user_del&id=<?=$userId;?>">Confirmar</a>
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
                </div><!-- ./content span12 -->

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
                        name: {
                            minlength: 6,
                            required: true
                        },
                        email: {
                            required: true,
                            email: true
                        },
                        password: {
                            minlength: 8
                        },
                        roleId: {
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