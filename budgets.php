<?php

# include configuration file
include_once ("includes/inc.init.php");
include_once ("includes/lib.dates.php");
include_once ("includes/lib.numbers.php");

# vars
$projectId = (int)aget('pId');
$customerId = (int)aget('cId');
$directorId = (int)aget('dId');
$dateFrom = get_budget_search_date_from(aget('dateFrom',10));
$dateTo = get_budget_search_date_to(aget('dateTo',10));

# queries
$results = get_budgets($projectId, $customerId, $directorId, $dateFrom, $dateTo);
$projects = get_projects_visible(session_get_data("companyId"));
$customers = get_customers();
$directors = get_directors_visible();

if($projects==false || $customers==false || $directors==false) {
    set_alert("error", "Es necesario que exista al menos 1 <a href=\"projects.php\">proyecto</a>, 1 <a href=\"customers.php\">cuenta</a> y 1 <a href=\"directors.php\">director</a> para poder ver y agregar Presupuestos");
    header("Location: index.php");
    exit;
}

?>
<?php include("inc.header.main.php"); ?>

        <div class="container-fluid">
            
            <!-- row top -->
            <div class="row-fluid">

                <!-- sidebar -->
                <div class="span3" id="sidebar">

                    <div class="row-fluid">

                        <?php if($global_perms['ADD']) { ?>
                        <div class="block">

                            <div class="navbar navbar-inner block-header">
                                <div class="muted pull-left">Agregar presupuesto</div>
                            </div>

                            <div class="block-content collapse in">

                                <!-- add-form-->
                                <form id="form_add" method="post" action="mod/budgets.php">
                                    <?php if($projects) { ?>
                                    <input type="hidden" name="cmd" value="budget_add">
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
                                            <label class="control-label">Proyecto</label>
                                            <div class="controls">
                                                <select class="span10 m-wrap" name="proyectoId">
                                                    <?=form_select_options($projects, "proyectoId", "titulo", $projectId);?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">Director</label>
                                            <div class="controls">
                                                <?php if($directors) { ?>
                                                    <select class="span10 m-wrap" name="directorId">
                                                        <?=form_select_options($directors, "directorId", "directorNombre", $directorId);?>
                                                    </select>
                                                <?php } else { ?>
                                                    No hay directores
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">Días de filmación<span class="required">*</span></label>
                                            <div class="controls">
                                                <input type="text" name="diasFilmacion" data-required="1" class="span10 m-wrap"/>
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">Fecha de rodaje</label>
                                            <div class="controls">
                                                <input type="text" name="fechaDeRodaje" data-required="1" class="span10 m-wrap datepicker"/>
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
                                    <?php } ?>
                                </form>
                                <!-- ./add-form -->

                            </div>
                        </div>
                        <?php } ?>

                        <?php if($results) { ?>
                            <!-- search -->
                            <div class="block">

                                <div class="navbar navbar-inner block-header">
                                    <div class="muted pull-left">Buscar</div>
                                </div>

                                <div class="block-content collapse in">

                                    <form id="form_search" method="get" action="budgets.php">
                                        <?php if($projects) { ?>
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
                                                <label class="control-label">Proyecto</label>
                                                <div class="controls">
                                                    <select class="span10 m-wrap" id="pId" name="pId">
                                                        <option value="0">Todos</option>
                                                        <?=form_select_options($projects, "proyectoId", "titulo", $projectId);?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="control-group">
                                                <label class="control-label">Cuenta</label>
                                                <div class="controls">
                                                    <select class="span10 m-wrap" id="cId" name="cId">
                                                        <option value="0">Todas</option>
                                                        <?=form_select_options($customers, "cuentaId", "razonSocial", $customerId);?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="control-group">
                                                <label class="control-label">Director</label>
                                                <div class="controls">
                                                    <select class="span10 m-wrap" id="dId" name="dId">
                                                        <option value="0">Todos</option>
                                                        <?=form_select_options($directors, "directorId", "directorNombre", $directorId);?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="control-group">
                                                <label class="control-label">Fecha de Rodaje</label>
                                                <div class="controls">
                                                    <input type="text" name="dateFrom" class="span7 m-wrap datepicker" value="<?=$dateFrom;?>" /><br>
                                                    <input type="text" name="dateTo" class="span7 m-wrap datepicker" value="<?=$dateTo;?>" />
                                                </div>
                                            </div>
                                            <div class="control-group">
                                                <label class="control-label">&nbsp;</label>
                                                <div class="controls">
                                                    <button type="submit" class="btn btn-primary">Buscar</button>
                                                    <button type="reset" class="btn" onclick="window.location='budgets.php';">Limpiar</button>
                                                </div>
                                            </div>
                                        </fieldset>
                                        <?php } ?>
                                    </form>
                                    
                                </div>
                            </div>
                            <!-- ./search -->
                        <?php } ?>

                    </div>
                </div>
                <!-- ./sidebar -->
                
                <!-- content span -->
                <div class="span9" id="content">
                    <div class="row-fluid">
                        <!-- alerts -->
                        <?php display_alerts(); ?>
                        <!-- ./alerts -->
                        <div class="block">
                            <div class="navbar navbar-inner block-header">
                                <h2 style="color:#1b54a3;">Presupuestos del <?=get_date_es("d/m/Y",$dateFrom) ." al ". get_date_es("d/m/Y", $dateTo);?></h2>
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
                                    <li class="active">Presupuestos</li>
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
                                                <th>Proyecto - Presupuesto</th>
                                                <th>Cuenta</th>
                                                <th>Director</th>
                                                <th>Días</th>
                                                <th>Fecha</th>
                                                <th>Monto</th>
                                                <?php if($global_perms['EDIT']) { ?><th>Cierres</th><?php } ?>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if($results) { ?>
                                                <?php for($i=0; $i<count($results); $i++) { ?>
                                                    <tr>
                                                        <td>
                                                            <?php if($global_perms['EDIT']) { ?>
                                                                <a href="budgets.detail.php?id=<?=$results[$i]['presupuestoId'];?>"><?=$results[$i]['referencia'];?></a>
                                                            <?php } else { ?>
                                                                <?=$results[$i]['referencia'];?>
                                                            <?php } ?>
                                                        </td>
                                                        <td><?=$results[$i]['razonSocial'];?></td>
                                                        <td><?=$results[$i]['directorNombre'];?></td>
                                                        <td><?=$results[$i]['diasFilmacion'];?></td>
                                                        <td><?=get_date_es("Y-m-d", $results[$i]['fechaDeRodaje']);?></td>
                                                        <td><?=number_currency($results[$i]['total']);?></td>
                                                        <?php if($global_perms['EDIT']) { ?>
                                                            <td>
                                                                <form id="form_<?=$results[$i]['presupuestoId'];?>" method="post" action="mod/budgets.php" enctype="multipart/form-data" style="margin-bottom:0px;">
                                                                <input type="hidden" name="cmd" value="load_cierre">
                                                                <input type="hidden" name="id" value="<?=$results[$i]['presupuestoId'];?>">
                                                                    <a href="#cierre_<?=$results[$i]['presupuestoId'];?>" data-toggle="modal" class="btn btn-secondary"><i class="icon-upload"></i> Cierre</a>
                                                                    <div id="cierre_<?=$results[$i]['presupuestoId'];?>" class="modal hide">
                                                                        <div class="modal-header">
                                                                            <button data-dismiss="modal" class="close" type="button">&times;</button>
                                                                            <h3>Cargar Cierre <?=$results[$i]['referencia'];?></h3>
                                                                        </div>
                                                                        <div class="modal-body">
                                                                            <p>Cargar cierre para generar cuentas por pagar de proveedores</p>
                                                                            <p><input type="file" name="cierre"></p>
                                                                            <p>Para asegurar que el archivo se cargue correctamente descargue el archivo csv <a href="mod/budgets.php?cmd=download_cierre&id=<?=$results[$i]['presupuestoId'];?>" target="_blank">aquí</a></p>
                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            <button class="btn btn-primary" href="mod/projects.php">Guardar</button>
                                                                            <a data-dismiss="modal" class="btn" href="#">Cancelar</a>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                            </td>
                                                        <?php } ?>
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
        <link rel="stylesheet" href="vendors/datepicker.css" media="screen">
        <script type="text/javascript" src="vendors/bootstrap-datepicker.js"></script>
        <script type="text/javascript" src="vendors/jquery-validation/dist/jquery.validate.min.js"></script>
        <script type="text/javascript" src="vendors/datatables/js/jquery.dataTables.min.js"></script>
        <script type="text/javascript" src="assets/DT_bootstrap.js"></script>
        <script>

            $(document).ready(function() {

                $('#results').dataTable( {
                    "sDom": "<'row'<'span6'l><'span6'f>r>t<'row'<'span6'i><'span6'p>>",
                    "sPaginationType": "bootstrap",
                    "iDisplayLength": 50,
                    "aaSorting": [[4, 'desc']],
                } );

                $('#form_add').validate({
                    errorClass: 'help-inline',
                    rules: {
                        titulo: {
                            minlength: 5,
                            required: true
                        },
                        rodaje: {
                            number: true,
                            required: true
                        },
                        preproduccion: {
                            number: true,
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