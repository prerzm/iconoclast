<?php

# include configuration file
include_once ("includes/inc.init.php");
include_once ("includes/class.dbupdate.php");

# db update
$db = new DBUpdate(true);
$update = $db->hasChanges();

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
                                <h2 style="color:#1b54a3;">Mantenimiento</h2>
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
                                    <li class="active">Mantenimiento</li>
                                </ul>
                            </div>
                        </div>
                        <!-- ./breadcrumb -->
                    </div>

                    <!-- row -->
                    <div class="row-fluid">
                        
                        <div class="span4">
                            <!-- block -->
                            <div class="block">
                                <div class="navbar navbar-inner block-header">
                                    <div class="muted pull-left">Actualizar Base de Datos</div>
                                </div>
                                <div class="block-content collapse in">

                                    <form id="form_update" method="post" action="mod/system.php" enctype="multipart/form-data">
                                    <input type="hidden" name="cmd" value="updatedb">
                                        <fieldset>
                                            <?php if($update) { ?>
                                                <div class="alert alert-error hide">
                                                    <button class="close" data-dismiss="alert"></button>
                                                    Hubo un problema. Favor de revisar la información.
                                                </div>
                                                <div class="alert alert-success hide">
                                                    <button class="close" data-dismiss="alert"></button>
                                                    La información es válida!
                                                </div>
                                                <div class="control-group">
                                                    <label class="control-label">Aplicar cambios</label>
                                                    <div class="controls">
                                                        <label><input type="radio" name="debug" value="0"> Sí, aplicar los cambios</label>
                                                        <label><input type="radio" name="debug" value="1" checked> No, solo mostrarlos</label>
                                                    </div>
                                                </div>
                                                <div class="control-group">
                                                    <label class="control-label">&nbsp;</label>
                                                    <div class="controls">
                                                        <button type="submit" class="btn btn-primary">Continuar</button>
                                                    </div>
                                                </div>
                                            <?php } else { ?>
                                                No existe archivo para actualizar la base de datos.
                                            <?php } ?>
                                        </fieldset>
                                    </form>
                                
                                </div>
                            </div>
                        </div>

                        <div class="span4">
                            <!-- block -->
                            <div class="block">
                                <div class="navbar navbar-inner block-header">
                                    <div class="muted pull-left">Respaldar Base de Datos</div>
                                </div>
                                <div class="block-content collapse in">

                                <form id="form_backup" method="post" action="mod/system.php">
                                    <input type="hidden" name="cmd" value="backupdb">
                                        <fieldset>
                                            <div class="control-group">
                                                <div class="controls">
                                                    <button type="submit" class="btn btn-primary">Respaldar Base de Datos</button>
                                                </div>
                                            </div>
                                        </fieldset>
                                    </form>
                                
                                    <form id="form_backup" method="post" action="mod/system.php">
                                    <input type="hidden" name="cmd" value="transfernames">
                                        <fieldset>
                                            <div class="control-group">
                                                <div class="controls">
                                                    <button type="submit" class="btn btn-primary">Cambiar nombres de archivos de transfers</button>
                                                </div>
                                            </div>
                                        </fieldset>
                                    </form>
                                
                                </div>
                            </div>
                        </div>

                        <div class="span4">
                            <!-- block -->
                            <div class="block">
                                <div class="navbar navbar-inner block-header">
                                    <div class="muted pull-left">Limpiar Base de Datos</div>
                                </div>
                                <div class="block-content collapse in">

                                    <?php if($global_perms['EDIT']) { ?>
                                    <!-- cuentas -->
                                    <a href="#resetCuentas" data-toggle="modal" class="btn btn-warning" style="margin-right:10px;margin-top:10px;"><i class="icon-remove icon-white"></i> Eliminar cuentas</a>
                                    <div id="resetCuentas" class="modal hide">
                                        <div class="modal-header">
                                            <button data-dismiss="modal" class="close" type="button">&times;</button>
                                            <h3>Eliminar cuentas</h3>
                                        </div>
                                        <div class="modal-body">
                                            <p>Está seguro que desea eliminar todos los cuentas?</p>
                                        </div>
                                        <div class="modal-footer">
                                            <a class="btn btn-danger" href="mod/system.php?cmd=del_cuentas">Confirmar</a>
                                            <a data-dismiss="modal" class="btn" href="#">Cancelar</a>
                                        </div>
                                    </div>
                                    <!-- directores -->
                                    <a href="#resetdirectores" data-toggle="modal" class="btn btn-warning" style="margin-right:10px;margin-top:10px;"><i class="icon-remove icon-white"></i> Eliminar directores</a>
                                    <div id="resetdirectores" class="modal hide">
                                        <div class="modal-header">
                                            <button data-dismiss="modal" class="close" type="button">&times;</button>
                                            <h3>Eliminar directores</h3>
                                        </div>
                                        <div class="modal-body">
                                            <p>Está seguro que desea eliminar todos los directores?</p>
                                        </div>
                                        <div class="modal-footer">
                                            <a class="btn btn-danger" href="mod/system.php?cmd=del_directores">Confirmar</a>
                                            <a data-dismiss="modal" class="btn" href="#">Cancelar</a>
                                        </div>
                                    </div>
                                    <!-- gastos -->
                                    <a href="#resetgastos" data-toggle="modal" class="btn btn-warning" style="margin-right:10px;margin-top:10px;"><i class="icon-remove icon-white"></i> Eliminar gastos</a>
                                    <div id="resetgastos" class="modal hide">
                                        <div class="modal-header">
                                            <button data-dismiss="modal" class="close" type="button">&times;</button>
                                            <h3>Eliminar gastos</h3>
                                        </div>
                                        <div class="modal-body">
                                            <p>Está seguro que desea eliminar todos los gastos?</p>
                                        </div>
                                        <div class="modal-footer">
                                            <a class="btn btn-danger" href="mod/system.php?cmd=del_gastos">Confirmar</a>
                                            <a data-dismiss="modal" class="btn" href="#">Cancelar</a>
                                        </div>
                                    </div>
                                    <!-- presupuestos -->
                                    <a href="#resetBudgets" data-toggle="modal" class="btn btn-warning" style="margin-right:10px;margin-top:10px;"><i class="icon-remove icon-white"></i> Eliminar presupuestos</a>
                                    <div id="resetBudgets" class="modal hide">
                                        <div class="modal-header">
                                            <button data-dismiss="modal" class="close" type="button">&times;</button>
                                            <h3>Eliminar Presupuestos</h3>
                                        </div>
                                        <div class="modal-body">
                                            <p>Está seguro que desea eliminar todos los presupuestos?</p>
                                        </div>
                                        <div class="modal-footer">
                                            <a class="btn btn-danger" href="mod/system.php?cmd=del_budgets">Confirmar</a>
                                            <a data-dismiss="modal" class="btn" href="#">Cancelar</a>
                                        </div>
                                    </div>
                                    <!-- proyectos -->
                                    <a href="#resetproyectos" data-toggle="modal" class="btn btn-warning" style="margin-right:10px;margin-top:10px;"><i class="icon-remove icon-white"></i> Eliminar proyectos</a>
                                    <div id="resetproyectos" class="modal hide">
                                        <div class="modal-header">
                                            <button data-dismiss="modal" class="close" type="button">&times;</button>
                                            <h3>Eliminar proyectos</h3>
                                        </div>
                                        <div class="modal-body">
                                            <p>Está seguro que desea eliminar todos los proyectos?</p>
                                        </div>
                                        <div class="modal-footer">
                                            <a class="btn btn-danger" href="mod/system.php?cmd=del_proyectos">Confirmar</a>
                                            <a data-dismiss="modal" class="btn" href="#">Cancelar</a>
                                        </div>
                                    </div>
                                    <!-- proveedores -->
                                    <a href="#resetproveedores" data-toggle="modal" class="btn btn-warning" style="margin-right:10px;margin-top:10px;"><i class="icon-remove icon-white"></i> Eliminar proveedores</a>
                                    <div id="resetproveedores" class="modal hide">
                                        <div class="modal-header">
                                            <button data-dismiss="modal" class="close" type="button">&times;</button>
                                            <h3>Eliminar proveedores</h3>
                                        </div>
                                        <div class="modal-body">
                                            <p>Está seguro que desea eliminar todos los proveedores?</p>
                                        </div>
                                        <div class="modal-footer">
                                            <a class="btn btn-danger" href="mod/system.php?cmd=del_proveedores">Confirmar</a>
                                            <a data-dismiss="modal" class="btn" href="#">Cancelar</a>
                                        </div>
                                    </div>
                                    <!-- intentos -->
                                    <a href="#resetintentos" data-toggle="modal" class="btn btn-warning" style="margin-right:10px;margin-top:10px;"><i class="icon-remove icon-white"></i> Eliminar intentos</a>
                                    <div id="resetintentos" class="modal hide">
                                        <div class="modal-header">
                                            <button data-dismiss="modal" class="close" type="button">&times;</button>
                                            <h3>Eliminar intentos</h3>
                                        </div>
                                        <div class="modal-body">
                                            <p>Está seguro que desea eliminar todos los intentos?</p>
                                        </div>
                                        <div class="modal-footer">
                                            <a class="btn btn-danger" href="mod/system.php?cmd=del_intentos">Confirmar</a>
                                            <a data-dismiss="modal" class="btn" href="#">Cancelar</a>
                                        </div>
                                    </div>
                                    <!-- nominas -->
                                    <a href="#resetnominas" data-toggle="modal" class="btn btn-warning" style="margin-right:10px;margin-top:10px;"><i class="icon-remove icon-white"></i> Eliminar nóminas</a>
                                    <div id="resetnominas" class="modal hide">
                                        <div class="modal-header">
                                            <button data-dismiss="modal" class="close" type="button">&times;</button>
                                            <h3>Eliminar nóminas</h3>
                                        </div>
                                        <div class="modal-body">
                                            <p>Está seguro que desea eliminar todos los nóminas?</p>
                                        </div>
                                        <div class="modal-footer">
                                            <a class="btn btn-danger" href="mod/system.php?cmd=del_nominas">Confirmar</a>
                                            <a data-dismiss="modal" class="btn" href="#">Cancelar</a>
                                        </div>
                                    </div><br>
                                    <!-- Toda la base de datos -->
                                    <a href="#resetDB" data-toggle="modal" class="btn btn-danger" style="margin-right:10px;margin-top:10px;"><i class="icon-remove icon-white"></i> Limpiar Base de Datos</a>
                                    <div id="resetDB" class="modal hide">
                                        <div class="modal-header">
                                            <button data-dismiss="modal" class="close" type="button">&times;</button>
                                            <h3>Limpiar Base de Datos</h3>
                                        </div>
                                        <div class="modal-body">
                                            <p>Está seguro que desea limpiar el contenido de la base de datos?</p>
                                        </div>
                                        <div class="modal-footer">
                                            <a class="btn btn-danger" href="mod/system.php?cmd=resetdb">Confirmar</a>
                                            <a data-dismiss="modal" class="btn" href="#">Cancelar</a>
                                        </div>
                                    </div>
                                    <?php } ?>
                                
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

<?php include("inc.footer.php"); ?>