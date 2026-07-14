<?php

# include configuration file
include_once ("includes/inc.init.php");
include_once ("includes/lib.numbers.php");

# vars
$projectId = (int)aget('pId');
$vendor = aget('vendor');
$statusId = (int)aget('sId');
$factura = aget('inv');
$dateFrom = (isset($_GET['dateFrom']) && strtotime($_GET['dateFrom'])!==false) ? get_pos_search_date_from(aget('dateFrom',10)) : "";
$dateTo = (isset($_GET['dateTo']) && strtotime($_GET['dateTo'])!==false) ? get_pos_search_date_to(aget('dateTo',10)) : "";

# queries
$results = get_pos($projectId, $vendor, $statusId, $factura, $dateFrom, $dateTo);
$projects = get_projects_visible(session_get_data("companyId"));
$status = get_payments_status();
$vendors_exist = get_vendors_exist();

if(!$projects) {
    set_alert("error", "Es necesario que exista al menos 1 <a href=\"projects.php\">proyecto</a> para poder ver y agregar Cuentas por Pagar");
    header("Location: projects.php");
    exit;
}
if(!$vendors_exist) {
    set_alert("error", "Es necesario que exista al menos 1 <a href=\"vendors.php\">proveedor</a> para poder ver y agregar Cuentas por Pagar");
    header("Location: vendors.php");
    exit;
}

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
                <div class="span2" id="sidebar">
                    <div class="row-fluid">

                        <?php if($projects && $global_perms['ADD']) { ?>
                        <!-- add -->
                        <div class="block">
                            <div class="navbar navbar-inner block-header">
                                <div class="muted pull-left">Agregar</div>
                            </div>
                            <div class="block-content collapse in">

                                <form id="form_add" method="get" action="pos.add.php">
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
                                                <select class="span10 m-wrap" name="ano" id="proy_add_ano" onchange="change_year(this.value);">
                                                    <?php foreach($years as $year => $values) { ?>
                                                        <option value="<?=$year;?>" <?=($yearId==$year) ? 'selected="selected"' : '';?>><?=($year==0) ? "Sin año" : $year;?></option>
                                                    <?php } ?>
                                                </select>
                                                <?php foreach($years as $year => $values) { ?>
                                                    <select class="span10 m-wrap proy_add_select" size="8" name="pId" id="proy_add_proyectoId_<?=$year;?>" <?=($year==$yearId) ? '' : 'style="display:none;" disabled';?>>
                                                        <?php foreach($values as $p) { ?>
                                                            <option value="<?=$p['proyectoId'];?>" <?=($projectId==$p['proyectoId']) ? 'selected="selected"' : '';?>><?=$p['titulo'];?></option>
                                                        <?php } ?>
                                                    </select>
                                                <?php } ?>
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

                        <!-- search -->
                        <div class="block">
                            <div class="navbar navbar-inner block-header">
                                <div class="muted pull-left">Buscar</div>
                            </div>
                            <div class="block-content collapse in">

                                <form id="form_search" method="get" action="pos.php">
                                    <fieldset>
                                        <div class="alert alert-error hide">
                                            <button class="close" data-dismiss="alert"></button>
                                            Hubo un problema. Favor de revisar la información.
                                        </div>
                                        <div class="alert alert-success hide">
                                            <button class="close" data-dismiss="alert"></button>
                                            La información es válida!
                                        </div>
                                        <?php if($projects) { ?>
                                        <div id="div_project" class="control-group">
                                            <label class="control-label">Proyecto</label>
                                            <div class="controls">
                                                <select class="span10 m-wrap" name="ano" id="proy_search_ano" onchange="change_year_search(this.value);">
                                                    <option value="0">Todos</option>
                                                    <?php foreach($years as $year => $values) { ?>
                                                        <option value="<?=$year;?>" <?=($yearId==$year) ? 'selected="selected"' : '';?>><?=($year==0) ? "Sin año" : $year;?></option>
                                                    <?php } ?>
                                                </select>
                                                <?php foreach($years as $year => $values) { ?>
                                                    <select class="span10 m-wrap proy_search_select" size="8" name="pId" id="proy_search_proyectoId_<?=$year;?>" <?=($year==$yearId) ? '' : 'style="display:none;" disabled';?>>
                                                        <?php foreach($values as $p) { ?>
                                                            <option value="<?=$p['proyectoId'];?>" <?=($projectId==$p['proyectoId']) ? 'selected="selected"' : '';?>><?=$p['titulo'];?></option>
                                                        <?php } ?>
                                                    </select>
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <?php } ?>
                                        <div class="control-group">
                                            <label class="control-label">Proveedor</label>
                                            <div class="controls">
                                                <input type="text" name="vendor" class="span10 m-wrap" value="<?=$vendor;?>" />
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">Status</label>
                                            <div class="controls">
                                                <select class="span10 m-wrap" id="sId" name="sId">
                                                    <option value="0">Todos</option>
                                                    <?=form_select_options($status, "pagoStatusId", "pagoStatus", $statusId);?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">Factura</label>
                                            <div class="controls">
                                                <select class="span10 m-wrap" id="inv" name="inv">
                                                    <option value="" <?=($factura=="") ? 'selected' : '';?>>Todos</option>
                                                    <option value="si" <?=($factura=="si") ? 'selected' : '';?>>Con factura</option>
                                                    <option value="no" <?=($factura=="no") ? 'selected' : '';?>>Sin factura</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">Fecha de Pago</label>
                                            <div class="controls">
                                                <input type="text" name="dateFrom" class="span10 m-wrap datepicker" value="<?=$dateFrom;?>" /><br>
                                                <input type="text" name="dateTo" class="span10 m-wrap datepicker" value="<?=$dateTo;?>" />
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">&nbsp;</label>
                                            <div class="controls">
                                                <button type="submit" class="btn btn-primary">Buscar</button>
                                                <button type="reset" class="btn" onclick="window.location='pos.php';">Limpiar</button>
                                            </div>
                                        </div>
                                    </fieldset>
                                </form>
                                
                            </div>
                        </div>
                        <!-- ./search -->

                    </div>
                </div>
                <!-- ./sidebar -->
                
                <!-- content span -->
                <div class="span10" id="content">
                    <div class="row-fluid">
                        <!-- alerts -->
                        <?php display_alerts(); ?>
                        <!-- ./alerts -->
                        <div class="block">
                            <div class="navbar navbar-inner block-header">
                                <h2 style="color:#1b54a3;">Cuentas por Pagar y Gastos</h2>
                            </div>
                        </div>
                        <!-- breadcrumb -->
                        <div class="navbar">
                            <div class="navbar-inner">
                                <ul class="breadcrumb">
                                    <i class="icon-chevron-left hide-sidebar"><a href="#" title="Hide Sidebar" rel="tooltip">&nbsp;</a></i>
                                    <i class="icon-chevron-right show-sidebar" style="display:none;"><a href="#" title="Show Sidebar" rel="tooltip">&nbsp;</a></i>
                                    <li><a href="index.php">Inicio</a> <span class="divider">/</span></li>
                                    <li class="active">Cuentas por Pagar y Gastos</li>
                                </ul>
                            </div>
                        </div>
                        <!-- ./breadcrumb -->
                    </div>
                    <!-- row -->
                    <div class="row-fluid">

                        <!-- form mass-authorize -->
                        <form method="post" action="mod/pos.php">
                        <input type="hidden" name="cmd" value="mass_auth">

                        <!-- block -->
                        <div class="block">
                            <div class="navbar navbar-inner block-header">
                                <div class="muted pull-left">Resultados</div>
                            </div>
                            <div class="block-content collapse in">
                                <div class="span12">

                                    <?php if($global_perms['AUTHORIZE']) { ?>
                                    <div class="table-toolbar">
                                        <div class="btn-group">
                                            <?php if($global_perms['AUTHORIZE']) { ?><button type="submit" class="btn btn-info" onclick="return confirm('Está seguro que desea Autorizar todos los pagos seleccionados?');">Autorizar <i class="icon-star icon-white"></i></button><?php } ?>
                                        </div>
                                    </div>
                                    <?php } ?>

                                    <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="results">
                                        <thead>
                                            <tr>
                                                <?php if($global_perms['AUTHORIZE']) { ?><th>&nbsp;</th><?php } ?>
                                                <th>ID</th>
                                                <th>Proyecto</th>
                                                <th>Concepto</th>
                                                <th>Proveedor</th>
                                                <th>Datos Bancarios</th>
                                                <th>Fecha de<br>Pago</th>
                                                <th nowrap>Total</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if($results) { ?>
                                                <?php for($i=0; $i<count($results); $i++) { ?>
                                                    <tr>
                                                        <?php if($global_perms['AUTHORIZE']) { ?><td><input type="checkbox" class="checkbox" name="pos[<?=$results[$i]['gastoId'];?>]" <?=($results[$i]['pagoStatusId']!=PAYMENT_STATUS_PENDING) ? 'disabled' :'';?>></td><?php } ?>
                                                        <td><a href="pos.view.php?id=<?=$results[$i]['gastoId'];?>&pId=<?=$projectId;?>&vendor=<?=urlencode($vendor);?>&sId=<?=$statusId;?>"><?=$results[$i]['gastoId'];?></a></td>
                                                        <td><?=$results[$i]['titulo'];?><?=((bool)$results[$i]['prontoPago']) ? ' <span class="label label-pronto-pago">Pronto Pago</span>': '';?></td>
                                                        <td><?=$results[$i]['concepto'];?></td>
                                                        <td><?=$results[$i]['razonSocial'];?></td>
                                                        <td>
                                                            <strong>Banco: </strong><?=$results[$i]['banco'];?><br>
                                                            <strong>CLABE: </strong><?=$results[$i]['clabe'];?><br>
                                                            <strong>SWIFT</strong>: <?=($results[$i]['swift']!="") ? $results[$i]['swift']: "N/A";?><br>
                                                            <strong>ABA</strong>: <?=($results[$i]['aba']!="") ? $results[$i]['aba']: "N/A";?>
                                                        </td>
                                                        <td><?=$results[$i]['fechaDePago'];?></td>
                                                        <td nowrap style="text-align:right;"><?=number_currency($results[$i]['total']);?> <span class="label label-<?=($results[$i]['moneda']=="MXN") ? 'success' : 'important';?>"><?=$results[$i]['moneda'];?></span></td>
                                                        <td style="text-align:center;">
                                                            <span class="label label-<?=$results[$i]['pagoStatus'];?>"><?=$results[$i]['pagoStatus'];?></span><br>
                                                            <?php if($results[$i]['facturaUuid']!="") { ?>
                                                                <span class="label label-success">Con factura</span>
                                                            <?php } else { ?>
                                                                <span class="label label-important">Sin factura</span>
                                                            <?php } ?>
                                                            <?php if($results[$i]['extranjero']==0) { ?>
                                                                <?php if($results[$i]['pagoStatusId']==PAYMENT_STATUS_PAYED) { ?>
                                                                    <?php if($results[$i]['comprobante']!="") { ?>
                                                                        <br><span class="label label-success">Con complemento</span>
                                                                    <?php } else { ?>
                                                                        <br><span class="label label-important">Sin complemento</span>
                                                                    <?php } ?>
                                                                <?php } ?>
                                                            <?php } ?>
                                                        </td>
                                                    </tr>
                                                <?php } ?>
                                            <?php } ?>
                                        </tbody>
						            </table>
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
                    "aaSorting": [[7, 'desc']],
                } );

                $(".datepicker").datepicker();

            });

            function change_year(year) {

                $(".proy_add_select").attr('disabled', 'disabled');
                $(".proy_add_select").hide();
                $("#proy_add_proyectoId_"+year).removeAttr('disabled');
                $("#proy_add_proyectoId_"+year).show();

            }

            function change_year_search(year) {

                $(".proy_search_select").attr('disabled', 'disabled');
                $(".proy_search_select").hide();
                $("#proy_search_proyectoId_"+year).removeAttr('disabled');
                $("#proy_search_proyectoId_"+year).show();

            }

        </script>

<?php include("inc.footer.php"); ?>