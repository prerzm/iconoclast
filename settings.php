<?php

# include configuration file
include_once ("includes/inc.init.php");
include_once ("includes/class.settings.php");

# queries
$results = get_settings();
$cats = get_settings_cats();
$first_cat = (isset($results[0])) ? $results[0]->getCat() : '';

?>
<?php include("inc.header.main.php"); ?>

        <div class="container-fluid">
            
            <!-- row top -->
            <div class="row-fluid">
                <!-- sidebar -->
                <div class="span3" id="sidebar">
                    <div class="row-fluid">

                        <?php if($global_perms['ADD']) { ?>
                        <!-- add -->
                        <div class="block">
                            <div class="navbar navbar-inner block-header">
                                <div class="muted pull-left">Agregar</div>
                            </div>
                            <div class="block-content collapse in">

                                <form id="form_add" method="post" action="mod/settings.php">
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
                                            <label class="control-label">Clave<span class="required">*</span></label>
                                            <div class="controls">
                                                <input type="text" name="configKey" data-required="1" class="span10 m-wrap"/>
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">Nombre<span class="required">*</span></label>
                                            <div class="controls">
                                                <input type="text" name="configName" data-required="1" class="span10 m-wrap"/>
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">Tipo</label>
                                            <div class="controls">
                                                <select class="span10 m-wrap" id="configType" name="configType">
                                                    <option value="Text">Texto</option>
                                                    <option value="Radio">Radio</option>
                                                    <option value="Combo">Combo</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">Campo público<span class="required">*</span></label>
                                            <div class="controls">
                                                <label><input type="radio" name="configPublic" value="0" checked="checked"> No</label>
                                                <label><input type="radio" name="configPublic" value="1"> Si</label>
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">Opciones/Valores</label>
                                            <div class="controls">
                                                <textarea name="configOptions"></textarea>
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">&nbsp;</label>
                                            <div class="controls">
                                                <button type="submit" class="btn btn-primary">Aregar</button>
                                            </div>
                                        </div>
                                    </fieldset>
                                </form>
                                
                            </div>
                        </div>
                        <?php } ?>

                    </div>
                </div>
                <!-- ./sidebar -->
                
                <!-- content span -->
                <div class="<?=($global_perms['ADD']) ? 'span9' : 'span12';?>" id="content">
                    <div class="row-fluid">
                        <!-- alerts -->
                        <?php display_alerts(); ?>
                        <!-- ./alerts -->
                        <div class="block">
                            <div class="navbar navbar-inner block-header">
                                <h2 style="color:#1b54a3;">Configuración del Sistema</h2>
                            </div>
                        </div>
                        <!-- breadcrumb -->
                        <div class="navbar">
                            <div class="navbar-inner">
                                <ul class="breadcrumb">
                                    <i class="icon-chevron-left hide-sidebar"><a href="#" title="Hide Sidebar" rel="tooltip">&nbsp;</a></i>
                                    <i class="icon-chevron-right show-sidebar" style="display:none;"><a href="#" title="Show Sidebar" rel="tooltip">&nbsp;</a></i>
                                    <li><a href="index.php">Inicio</a> <span class="divider">/</span></li>
                                    <li class="active">Configuración del Sistema</li>
                                </ul>
                            </div>
                        </div>
                        <!-- ./breadcrumb -->
                    </div>
                    <!-- row -->
                    <div class="row-fluid">

                        <!-- form mass-edit -->
                        <form method="post" action="mod/settings.php">
                        <input type="hidden" name="cmd" value="update">

                        <!-- block -->
                        <div class="block">
                            <div class="navbar navbar-inner block-header">
                                <div class="muted pull-left">Resultados</div>
                            </div>
                            <div class="block-content collapse in">
                                <div class="span12">

                                    <div class="table-toolbar">
                                        <div class="btn-group">
                                            <?php for($i=0; $i<count($cats); $i++) { ?>
                                                <?php if($i==0) { ?>
                                                    <a href="#" id="button_<?=$cats[$i]['configCat'];?>" class="btn btn-primary" onclick="change_cat('<?=$cats[$i]['configCat'];?>');"><?=$cats[$i]['configCat'];?></a>
                                                <?php } else { ?>
                                                    <a href="#" id="button_<?=$cats[$i]['configCat'];?>" class="btn" onclick="change_cat('<?=$cats[$i]['configCat'];?>');"><?=$cats[$i]['configCat'];?></a>
                                                <?php } ?>
                                            <?php } ?>
                                        </div>
                                    </div>

                                    <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="results">
                                        <thead>
                                            <tr>
                                                <th>Configuración</th>
                                                <?=($global_perms['FULL']) ? '<th>Clave</th>' : '';?>
                                                <th>Valor</th>
                                                <?php if($global_perms['DELETE'] ) { ?><th>&nbsp;</th><?php } ?>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if($results) { ?>
                                                <?php foreach($results as $config) { ?>
                                                    <tr class="row_<?=$config->getCat();?>" <?=($config->getCat()!=$first_cat) ? 'style="display:none;"' : '';?>>
                                                        <td><?=$config->getName();?></td>
                                                        <?=($global_perms['FULL']) ? '<td>'.$config->getKey().'</td>' : '';?>
                                                        <td><?=$config->displayField();?></td>
                                                        <?php if($global_perms['DELETE'] ) { ?>
                                                        <td>
                                                            <a href="#myAlert_<?=$config->getId();?>" data-toggle="modal" class="btn btn-small btn-danger"><i class="icon-remove icon-white"></i> Eliminar</a>
                                                            <div id="myAlert_<?=$config->getId();?>" class="modal hide">
                                                                <div class="modal-header">
                                                                    <button data-dismiss="modal" class="close" type="button">&times;</button>
                                                                    <h3>Eliminar</h3>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <p>Está seguro que desea eliminar este registro?</p>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <a class="btn btn-primary" href="mod/settings.php?cmd=del&id=<?=$config->getId();?>">Confirmar</a>
                                                                    <a data-dismiss="modal" class="btn" href="#">Cancelar</a>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <?php } ?>
                                                    </tr>
                                                <?php } ?>
                                            <?php } ?>
                                        </tbody>
						            </table>

                                    <?php if($global_perms['EDIT']) { ?>
                                    <div class="table-toolbar">
                                        <div class="btn-group">
                                            <button type="submit" class="btn btn-primary">Guardar</button>
                                        </div>
                                    </div>
                                    <?php } ?>

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
                <?php foreach($cats as $cat) { ?>
                    $("#button_<?=$cat['configCat'];?>").removeClass('btn-primary');
                    $(".row_<?=$cat['configCat'];?>").hide();
                <?php } ?>

                $("#button_"+cat).addClass('btn-primary');
                $(".row_"+cat).show();
            }

        </script>

<?php include("inc.footer.php"); ?>