<?php

/** HiperMedica **/

# include configuration file
include_once ("includes/inc.init.php");

# queries
$results = sql_select("SELECT u.usuarioId, u.nombre, u.email, r.rol FROM ".TABLE_USERS." u, ".TABLE_ROLES." r WHERE u.rolId = r.rolId AND u.deleted = 0");
$companies = get_companies();
$roles = get_roles();

?>
<?php include("inc.header.main.php"); ?>

        <div class="container-fluid">
            
            <!-- row top -->
            <div class="row-fluid">

                <!-- sidebar -->
                <div class="span3 <?=(!$global_perms['ADD']) ? 'hide' : '';?>" id="sidebar">
                    <div class="row-fluid">
                        <div class="block">
                            <div class="navbar navbar-inner block-header">
                                <div class="muted pull-left">Agregar</div>
                            </div>
                            <div class="block-content collapse in">

                                <?php if($global_perms['ADD']) { ?>
                                <!-- add-form-->
                                <form id="form_add" method="post" action="mod/users.php">
                                <input type="hidden" name="cmd" value="user_add">
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
                                                <input type="text" name="name" data-required="1" class="span10 m-wrap"/>
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">Email<span class="required">*</span></label>
                                            <div class="controls">
                                                <input type="text" name="email" class="span10 m-wrap"/>
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">Contraseña<span class="required">*</span></label>
                                            <div class="controls">
                                                <input type="password" name="password" data-required="1" class="span10 m-wrap"/>
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">Compañías<span class="required">*</span></label>
                                            <div class="controls">
                                                <?php foreach($companies as $c) { ?>
                                                    <label><input type="checkbox" name="companies[]" value="<?=$c['companyId'];?>"> <?=$c['razonSocial'];?></label>
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">Rol<span class="required">*</span></label>
                                            <div class="controls">
                                                <select class="span10 m-wrap" id="roleId" name="roleId">
                                                    <?=form_select_options($roles, "rolId", "rol");?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">&nbsp;</label>
                                            <div class="controls">
                                                <button type="submit" class="btn btn-primary">Agregar</button>
                                                <button type="reset" class="btn">Limpiar</button>
                                            </div>
                                        </div>
                                    </fieldset>
                                </form>
                                <!-- ./add-form -->
                                <?php } ?>
                                
                            </div>
                        </div>
                    </div>
                </div>
                <!-- ./sidebar -->
                
                <!-- content span9 -->
                <div class="<?=($global_perms['ADD']) ? 'span9' : 'span12';?>" id="content">
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
                                    <li>Administración <span class="divider">/</span></li>
                                    <li class="active">Usuarios</li>
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
                                <div class="muted pull-left">Resultados</div>
                            </div>
                            <div class="block-content collapse in">
                                <div class="span12">
                                    <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="results">
                                        <thead>
                                            <tr>
                                                <th>Nombre</th>
                                                <th>Email</th>
                                                <th>Rol</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if($results) { ?>
                                                <?php for($i=0; $i<count($results); $i++) { ?>
                                                    <tr>
                                                        <td>
                                                            <?php if($global_perms['EDIT']) { ?>
                                                                <a href="users.edit.php?id=<?=$results[$i]['usuarioId'];?>"><?=$results[$i]['nombre'];?></a>
                                                            <?php } else { ?>
                                                                <?=$results[$i]['nombre'];?>
                                                            <?php } ?>
                                                        </td>
                                                        <td><?=$results[$i]['email'];?></span></td>
                                                        <td><?=$results[$i]['rol'];?></td>
                                                    </tr>
                                                <?php } ?>
                                            <?php } ?>
                                        </tbody>
						            </table>
                                </div>
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
        <script type="text/javascript" src="vendors/datatables/js/jquery.dataTables.min.js"></script>
        <script type="text/javascript" src="assets/DT_bootstrap.js"></script>
        <script>

            $(document).ready(function() {

                <?php if(isset($results) && $results) { ?>
                $('#results').dataTable( {
                    "sDom": "<'row'<'span6'l><'span6'f>r>t<'row'<'span6'i><'span6'p>>",
                    "sPaginationType": "bootstrap",
                } );
                <?php } ?>

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