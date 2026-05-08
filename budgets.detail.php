<?php

# include configuration file
include_once ("includes/inc.init.php");
include_once ("includes/lib.numbers.php");

# vars
$parentId = (int)aget('parentId');
$budgetId = (int)aget('id');

# queries
$budget = get_budget($budgetId);
$global_project = get_project($budget['proyectoId']);
$monedas_js = "";
foreach($global_currencies as $moneda => $tc) {
    $monedas_js .= "monedas['$moneda'] = $tc;\n";
}
$cats_options = get_budget_options($budgetId, $parentId);
$cats_rows = get_budget_table_rows($budgetId, $global_currencies);
$directors = get_directors_visible();

?>
<?php include("inc.header.main.php"); ?>

        <div class="container-fluid">
            
            <!-- row top -->
            <div class="row-fluid">
                <!-- sidebar -->
                <div class="span3 " id="sidebar">
                    <div class="row-fluid">

                    <div class="block">
                            <div class="navbar navbar-inner block-header">
                                <div class="muted pull-left">Editar Presupuesto</div>
                            </div>
                            <div class="block-content collapse in">

                                <!-- add-form-->
                                <form id="form_add" method="post" action="mod/budgets.php">
                                <input type="hidden" name="cmd" value="info_update">
                                <input type="hidden" name="id" value="<?=$budgetId;?>">
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
                                            <label class="control-label">Director</label>
                                            <div class="controls">
                                                <select class="span10 m-wrap" name="directorId">
                                                    <?=form_select_options($directors, "directorId", "directorNombre", $budget['directorId']);?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">Días de filmación<span class="required">*</span></label>
                                            <div class="controls">
                                                <input type="text" name="diasFilmacion" data-required="1" class="span10 m-wrap" value="<?=$budget['diasFilmacion'];?>" />
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">Fecha de rodaje</label>
                                            <div class="controls">
                                                <input type="text" name="fechaDeRodaje" data-required="1" class="span10 m-wrap datepicker" value="<?=$budget['fechaDeRodaje'];?>" />
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">&nbsp;</label>
                                            <div class="controls">
                                                <button type="submit" class="btn btn-primary">Guardar</button>
                                                <?php if($global_perms['DELETE']) { ?>
                                                    <a href="#modalDelete" data-toggle="modal" class="btn btn-danger"><i class="icon-remove icon-white"></i> Eliminar presupuesto</a>
                                                    <div id="modalDelete" class="modal hide">
                                                        <div class="modal-header">
                                                            <button data-dismiss="modal" class="close" type="button">&times;</button>
                                                            <h3>Eliminar</h3>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p>Está seguro que desea eliminar este registro?</p>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <a class="btn btn-primary" href="mod/budgets.php?cmd=budget_delete&id=<?=$budgetId;?>">Confirmar</a>
                                                            <a data-dismiss="modal" class="btn" href="#">Cancelar</a>
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </fieldset>
                                </form>
                                <!-- ./add-form -->
                                
                            </div>
                        </div>

                        <?php if($global_perms['ADD']) { ?>
                        <div class="block">
                            <div class="navbar navbar-inner block-header">
                                <div class="muted pull-left">Agregar concepto</div>
                            </div>
                            <div class="block-content collapse in">

                                <!-- add-form-->
                                <form id="form_add" method="post" action="mod/budgets.php">
                                <input type="hidden" name="cmd" value="item_add">
                                <input type="hidden" name="id" value="<?=$budgetId;?>">
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
                                            <label class="control-label">Cuenta Padre</label>
                                            <div class="controls">
                                                <select class="span10 m-wrap" id="parentId" name="parentId">
                                                    <option value="0">- Nivel Superior -</option>
                                                    <?=$cats_options;?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label"># de Cuenta<span class="required">*</span></label>
                                            <div class="controls">
                                                <input type="text" name="cuenta" data-required="1" class="span10 m-wrap"/>
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">Nombre<span class="required">*</span></label>
                                            <div class="controls">
                                                <input type="text" name="nombre" data-required="1" class="span10 m-wrap"/>
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">Monto</label>
                                            <div class="controls">
                                                <input type="text" name="monto" data-required="1" class="span10 m-wrap"/>
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">Moneda</label>
                                            <div class="controls">
                                                <select name="moneda" class="span10 m-wrap">
                                                    <?php foreach($global_currencies as $key => $tc) { ?>
                                                        <option value="<?=$key;?>"><?=$key;?></<option>
                                                    <?php } ?>
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
                                
                            </div>
                        </div>
                        <?php } ?>

                    </div>
                </div>
                <!-- ./sidebar -->
                
                <!-- content span9 -->
                <div class="span9" id="content">
                    <div class="row-fluid">
                        <!-- alerts -->
                        <?php display_alerts(); ?>
                        <!-- ./alerts -->
                        <div class="block">
                            <div class="navbar navbar-inner block-header">
                                <h2 style="color:#1b54a3;">Presupuesto <?=$budget['referencia']." - ".$global_project['titulo'];?></h2>
                            </div>
                        </div>
                        <!-- breadcrumb -->
                        <div class="navbar">
                            <div class="navbar-inner">
                                <ul class="breadcrumb">
                                    <i class="icon-chevron-left hide-sidebar"><a href="#" title="Hide Sidebar" rel="tooltip">&nbsp;</a></i>
                                    <i class="icon-chevron-right show-sidebar" style="display:none;"><a href="#" title="Show Sidebar" rel="tooltip">&nbsp;</a></i>
                                    <li><a href="index.php">Inicio</a> <span class="divider">/</span></li>
                                    <li><a href="budgets.php">Presupuestos</a> <span class="divider">/</span></li>
                                    <li class="active"><?=$global_project['titulo'];?></li>
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

                                    <form id="form_edit" method="post" action="mod/budgets.php">
                                    <input type="hidden" name="cmd" value="budget_update">
                                    <input type="hidden" name="id" value="<?=$budgetId;?>">

                                        <div class="table-toolbar">
                                            <div class="btn-group pull-right">
                                                <button type="submit" class="btn btn-primary">Guardar</button>
                                            </div>
                                            <div class="btn-group">
                                            </div>
                                        </div>

                                        <table cellpadding="0" cellspacing="0" border="0" class="table table-bordered" id="results">
                                            <thead>
                                                <tr>
                                                    <th>Concepto</th>
                                                    <th>Moneda</th>
                                                    <th>Monto</th>
                                                    <th>Total</th>
                                                    <th>&nbsp;</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?=$cats_rows;?>
                                            </tbody>
                                        </table>

                                    </form>

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

                <?php if(isset($cats_rows) && $cats_rows!="") { ?>
                $('#results').dataTable( {
                    "sDom": "<'row'<'span6'l><'span6'f>r>t<'row'<'span6'i><'span6'p>>",
                    "sPaginationType": "bootstrap",
                    "iDisplayLength": 100,
                    "bSort": false,
                } );
                <?php } ?>

                $('#form_add').validate({
                    errorClass: 'help-inline',
                    rules: {
                        nombre: {
                            minlength: 3,
                            required: true
                        },
                        cuenta: {
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

            });

            // global vars
            var monedas = new Array;
            <?=$monedas_js;?>

            function calc_totals(rowId) {

                var current = document.getElementById('row_td_total_'+rowId).innerHTML;
                current = current.replace("$", "");
                current = current.split(" ").join("");
                current = current.split(",").join("");
                
                var total = document.getElementById('budget_total').innerHTML;
                total = total.replace("$", "");
                total = total.split(" ").join("");
                total = total.split(",").join("");

                var tc = monedas[document.getElementById('moneda_'+rowId).value];
                var monto = parseFloat(document.getElementById('monto_'+rowId).value) * tc;
                if(monto==undefined || isNaN(monto)) {
                    monto = 0;
                    document.getElementById('monto_'+rowId).value = 0;
                }
                document.getElementById('row_td_total_'+rowId).innerHTML = "$ "+monto.toLocaleString('es-MX', {minimumFractionDigits: 2, maximumFractionDigits: 2});

                var new_total = parseFloat(total - current + monto);
                document.getElementById('budget_total').innerHTML = "$ "+new_total.toLocaleString('es-MX', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                
            }

        </script>

<?php include("inc.footer.php"); ?>