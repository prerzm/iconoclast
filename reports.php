<?php

# include configuration file
include_once ("includes/inc.init.php");
include_once ("includes/lib.misc.php");
include_once ("includes/lib.numbers.php");
include_once ("includes/lib.dates.php");
include_once ("includes/lib.abp.reports.php");
include_once ("includes/class.reports.php");
require_once ("includes/PHPExcel.php");

# vars & filters
$report = false;
$filters["report"] = aget('report', 12);
$filters["projectId"] = (int)aget('projectId');
$filters["concepto"] = aget('concepto',200);
$filters["dateFrom"] = aget('dateFrom',10);
$filters["dateTo"] = aget('dateTo',10);
$filters["pagoStatusId"] = (int)aget('pagoStatusId');
$filters["proveedorId"] = (int)aget('proveedorId');
$filters["directorId"] = (int)aget('directorId');
$filters["ordenarPor"] = (int)aget('ordenarPor');
$vendor = aget('ajxProveedor');

# report
if($filters["report"]!="") {
    $type = strtolower($filters["report"]);
	$type = str_replace("_", "", ucwords($type));
	if(class_exists($type)) {
		$report = new $type($filters);
	} else {
		set_alert("error", "Hubo un error al generar el reporte, favor de intentar nuevamente.");
	}
}

# queries
$projects = get_projects_visible(session_get_data("companyId"));
$pos_status = get_payments_status();
$directores = get_directors_all();

# years/projects array
$years = array();
foreach($projects as $p) {
    $years[(int)$p['ano']][] = $p;
}

# default year
$yearId = (isset($_GET['ano'])) ? aget('ano') : array_key_first($years);

?>
<?php include("inc.header.main.php"); ?>

        <div class="container-fluid">
            
            <!-- row top -->
            <div class="row-fluid">
                <!-- sidebar -->
                <div class="span3" id="sidebar">
                    <div class="row-fluid">
                        <div class="block">
                            <div class="navbar navbar-inner block-header">
                                <div class="muted pull-left">Seleccionar Reporte</div>
                            </div>
                            <div class="block-content collapse in">

                                <!-- add-form-->
                                <form id="form_add" method="get" action="reports.php">
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
                                            <label class="control-label">Reporte</label>
                                            <div class="controls">
                                                <select class="span10 m-wrap" id="report" name="report" onchange="toggle_filters(this.value);">
                                                    <option value="0">Seleccionar Reporte</option>
                                                        <option value="REP_POS" <?=($filters['report']=="REP_POS") ? 'selected' : '';?>>Cuentas por Pagar</option>
                                                        <option value="REP_COMP" <?=($filters['report']=="REP_COMP") ? 'selected' : '';?>>Cuentas sin Complemento de Pago</option>
                                                        <option value="REP_PROY" <?=($filters['report']=="REP_PROY") ? 'selected' : '';?>>Proyectos</option>
                                                        <option value="REP_PROVS" <?=($filters['report']=="REP_PROVS") ? 'selected' : '';?>>Proveedores</option>
                                                        <option value="REP_CONCEPT" <?=($filters['report']=="REP_CONCEPT") ? 'selected' : '';?>>Por Concepto</option>
                                                        <option value="REP_DIR" <?=($filters['report']=="REP_DIR") ? 'selected' : '';?>>Directores</option>
                                                        <option value="REP_FLUJO" <?=($filters['report']=="REP_FLUJO") ? 'selected' : '';?>>Flujo de Efectivo</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div id="div_project" class="control-group" style="display:<?=($report!==false) ? 'block' : 'none';?>;">
                                            <label class="control-label">Proyecto</label>
                                            <div class="controls">
                                                <select class="span10 m-wrap" name="ano" id="proy_add_ano" onchange="change_year(this.value);">
                                                    <option value="0">Todos</option>
                                                    <?php foreach($years as $year => $values) { ?>
                                                        <option value="<?=$year;?>" <?=($yearId==$year) ? 'selected="selected"' : '';?>><?=($year==0) ? "Sin año" : $year;?></option>
                                                    <?php } ?>
                                                </select>
                                                <?php foreach($years as $year => $values) { ?>
                                                    <select class="span10 m-wrap proy_add_select" size="8" name="projectId" id="proy_add_proyectoId_<?=$year;?>" <?=($year==$yearId) ? '' : 'style="display:none;" disabled';?>>
                                                        <?php foreach($values as $p) { ?>
                                                            <option value="<?=$p['proyectoId'];?>" <?=($filters['projectId']==$p['proyectoId']) ? 'selected="selected"' : '';?>><?=$p['titulo'];?></option>
                                                        <?php } ?>
                                                    </select>
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <div id="div_concept" class="control-group" style="display:<?=($report!==false && $filters['report']=="REP_CONCEPT") ? 'block' : 'none';?>;">
                                            <label class="control-label">Concepto</label>
                                            <div class="controls">
                                                <input type="text" id="concepto" name="concepto" class="span10 m-wrap" value="<?=$filters['concepto'];?>" />
                                            </div>
                                        </div>
                                        <div id="div_period" class="control-group" style="display:<?=($report!==false && ($filters['report']=="REP_POS" || $filters['report']=="REP_DIAS" || $filters['report']=="REP_CONCEPT")) ? 'block' : 'none';?>;">
                                            <label class="control-label">Fecha</label>
                                            <div class="controls">
                                                <input type="text" name="dateFrom" class="span5 m-wrap datepicker" value="<?=$filters['dateFrom'];?>" /> - 
                                                <input type="text" name="dateTo" class="span5 m-wrap datepicker" value="<?=$filters['dateTo'];?>" />
                                            </div>
                                        </div>
                                        <div id="div_status" class="control-group" style="display:<?=($report!==false && $filters['report']=="REP_POS") ? 'block' : 'none';?>;">
                                            <label class="control-label">Estatus de Pago</label>
                                            <div class="controls">
                                                <select class="span10 m-wrap" id="pagoStatusId" name="pagoStatusId">
                                                    <option value="0">Todos</option>
                                                    <?=form_select_options($pos_status, "pagoStatusId", "pagoStatus", $filters['pagoStatusId']);?>
                                                </select>
                                            </div>
                                        </div>
                                        <div id="div_ordenar" class="control-group" style="display:<?=($report!==false && $filters['report']=="REP_POS") ? 'block' : 'none';?>;">
                                            <label class="control-label">Ordenar por</label>
                                            <div class="controls">
                                                <select class="span10 m-wrap" id="ordenarPor" name="ordenarPor">
                                                    <option value="1" <?=($filters['ordenarPor']==1) ? 'selected' : '' ; ?>>Fecha de Pago (ascendente)</option>
                                                    <option value="2" <?=($filters['ordenarPor']==2) ? 'selected' : '' ; ?>>Fecha de Pago (descendente)</option>
                                                    <option value="3" <?=($filters['ordenarPor']==3) ? 'selected' : '' ; ?>>Proveedor (agrupar)</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div id="div_vendor" class="control-group" style="display:<?=($report!==false && $filters['report']=="REP_PROVS") ? 'block' : 'none';?>;">
                                            <label class="control-label">Proveedor</label>
                                            <div class="controls">
                                                <input type="text" id ="ajxProveedor" name="ajxProveedor" class="span10 m-wrap" value="<?=$vendor;?>" />
                                                <input type="hidden" id="proveedorId" name="proveedorId" value="<?=($filters['proveedorId']>0) ? $filters['proveedorId'] : '0';?>">
                                            </div>
                                        </div>
                                        <div id="div_director" class="control-group" style="display:<?=($report!==false && $filters['report']=="REP_DIR") ? 'block' : 'none';?>;">
                                            <label class="control-label">Director</label>
                                            <div class="controls">
                                                <select class="span10 m-wrap" id="directorId" name="directorId">
                                                    <option value="0">Todos</option>
                                                    <?=form_select_options($directores, "proveedorId", "razonSocial", $filters['directorId']);?>
                                                </select>
                                            </div>
                                        </div>
                                        <div id="div_button" class="control-group" style="display:<?=($report!==false) ? 'block' : 'none';?>;">
                                            <label class="control-label">&nbsp;</label>
                                            <div class="controls">
                                                <button type="submit" class="btn btn-primary">Continuar</button>
                                            </div>
                                        </div>
                                    </fieldset>
                                </form>
                                <!-- ./add-form -->
                                
                            </div>
                        </div>
                    </div>
                </div>
                <!-- ./sidebar -->
                
                
                <!-- content span9 -->
                <div class="span9" id="content">
                    <div class="row-fluid">

                        <!-- alerts -->
                        <?php display_alerts(); ?>
                        <!-- ./alerts -->

                        <?php if(isset($report) && $report!==false) { ?>
                        <div class="block">
                            <div class="navbar navbar-inner block-header">
                                <h2 style="color:#1b54a3;"><?=$report->getName();?></h2>
                            </div>
                        </div>
                        <?php } ?>

                    </div>
                    <!-- row -->

                    <?php if(isset($report) && $report!==false) { ?>
                    <div class="row-fluid">
                        <!-- block -->
                        <div class="block">
                            <div class="navbar navbar-inner block-header">
                                <div class="muted pull-left">&nbsp;</div>
                            </div>
                            <div class="block-content collapse in">
                                <div class="span12">

                                    <div class="table-toolbar">
                                        <div class="btn-group pull-right">
                                            <a id="button_print_report" href="#" onclick="print_report();"><button class="btn"><i class="icon-print"></i> Imprimir</button></a>&nbsp;
                                            <a id="button_download_report" href="reports.excel.php?<?=http_build_query($filters);?>" target="_blank"><button class="btn"><i class="icon-download-alt"></i> Descargar</button></a>
                                        </div>
                                    </div>

                                    <table cellpadding="0" cellspacing="0" border="0" class="table table-striped" style="width:100%;">
                                        <thead>
                                            <?php $report->displayHeader(); ?>
                                        </thead>
                                        <tbody>
                                            <?php $report->displayRows(); ?>
                                        </tbody>
                                        <tfoot>
                                            <?php $report->displayTotals(); ?>
                                        </tfoot>
                                    </table>

                                </div>
                            </div>
                        </div>
                        <!-- /block -->
                    </div>
                    <!-- ./row -->

                </div><!-- ./content span9 -->
                <?php } ?>

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

        <script type="text/javascript" src="vendors/autocomplete/js/jquery.autocomplete.js"></script>
        <link rel="stylesheet" href="vendors/autocomplete/css/styles.css" media="screen">

        <script>

			$(document).ready(function() {

				$(".datepicker").datepicker();

                // autocomplete vendor
                $('#ajxProveedor').autocomplete({
                    serviceUrl: './mod/ajax.php',
                    params: {cmd: 'search_vendor'},
                    minChars: 3,
                    maxHeight: 150,
                    showNoSuggestionNotice: true,
                    noSuggestionNotice: 'No se encontraron registros',
                    noCache: true,
                    onSelect: function (suggestion) {
                        dataIdForeign = suggestion.data;
                        arrayData = dataIdForeign.split("|");
                        vendorId = parseInt(arrayData[0]);
                        vendorForeign = parseInt(arrayData[1]);
                        $("#proveedorId").val(vendorId);
                        //update_extranjero(vendorForeign);
                    }
                });

			});

            //function update_extranjero(vendorForeign) {
            //    console.log(vendorForeign);
            //}

			function toggle_filters(report) {
				$('#div_period').hide();
				$('#div_status').hide();
                $('#div_project').show();
                $('#div_concept').hide();
				$('#div_button').show();
                $('#div_vendor').hide();
                $('#div_director').hide();
                $('#div_ordenar').hide();
				if(report=="0") {
					$('#div_project').hide();
					$('#div_button').hide();
				} else if(report=="REP_POS") {
					$('#div_period').show();
					$('#div_status').show();
                    $('#div_ordenar').show();
				} else if(report=="REP_POS") {
					$('#div_period').show();
				} else if(report=="REP_CONCEPT") {
					$('#div_concept').show();
                    $('#div_period').show();
				} else if(report=="REP_PROVS") {
					$('#div_vendor').show();
				} else if(report=="REP_DIR") {
					$('#div_director').show();
				}
			}

            function print_report() {
                $("#button_print_report").hide();
                $("#button_download_report").hide();
                var printContents = $("#content").html();
                var originalContents = document.body.innerHTML;
                document.body.innerHTML = printContents;
                window.print();
                document.body.innerHTML = originalContents;
                $("#button_print_report").show();
                $("#button_download_report").show();
            }

            function change_year(year) {

                $(".proy_add_select").attr('disabled', 'disabled');
                $(".proy_add_select").hide();
                $("#proy_add_proyectoId_"+year).removeAttr('disabled');
                $("#proy_add_proyectoId_"+year).show();

            }

        </script>

<?php include("inc.footer.php"); ?>