<?php

# include configuration file
include_once ("includes/inc.init.php");
include_once ("includes/lib.numbers.php");

# vars
$projectId = (int)aget('pId');
$vendor = aget('vendor');
$statusId = (int)aget('sId');

# queries
$results = get_contracts_vendors($projectId, $vendor, $statusId);
$projects = get_projects_visible(session_get_data("companyId"));
$status = get_contracts_status();

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

                        <!-- search -->
                        <div class="block">
                            <div class="navbar navbar-inner block-header">
                                <div class="muted pull-left">Buscar</div>
                            </div>
                            <div class="block-content collapse in">

                                <form id="form_search" method="get" action="contracts.admin.php">
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
                                                <select class="span10 m-wrap" name="ano" id="proy_add_ano" onchange="change_year(this.value);">
                                                    <option value="0">Todos</option>
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
                                                    <?=form_select_options($status, "contratoStatusId", "contratoStatus", $statusId);?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">&nbsp;</label>
                                            <div class="controls">
                                                <button type="submit" class="btn btn-primary">Buscar</button>
                                                <button type="reset" class="btn" onclick="window.location='contracts.admin.php';">Limpiar</button>
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
                <div class="span9" id="content">
                    <div class="row-fluid">
                        <!-- alerts -->
                        <?php display_alerts(); ?>
                        <!-- ./alerts -->
                        <div class="block">
                            <div class="navbar navbar-inner block-header">
                                <h2 style="color:#1b54a3;">Contratos Proveedores</h2>
                            </div>
                        </div>
                        <!-- breadcrumb -->
                        <div class="navbar">
                            <div class="navbar-inner">
                                <ul class="breadcrumb">
                                    <i class="icon-chevron-left hide-sidebar"><a href="#" title="Hide Sidebar" rel="tooltip">&nbsp;</a></i>
                                    <i class="icon-chevron-right show-sidebar" style="display:none;"><a href="#" title="Show Sidebar" rel="tooltip">&nbsp;</a></i>
                                    <li><a href="index.php">Inicio</a> <span class="divider">/</span></li>
                                    <li class="active">Contratos Proveedores</li>
                                </ul>
                            </div>
                        </div>
                        <!-- ./breadcrumb -->
                    </div>
                    <!-- row -->
                    <div class="row-fluid">

                        <!-- form mass-authorize -->
                        <form method="post" action="mod/contracts.admin.php">
                        <input type="hidden" name="cmd" value="mass_del">

                        <!-- block -->
                        <div class="block">
                            <div class="navbar navbar-inner block-header">
                                <div class="muted pull-left">Resultados</div>
                            </div>
                            <div class="block-content collapse in">
                                <div class="span12">

                                    <table cellpadding="0" cellspacing="0" class="table table-striped table-bordered" id="results">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Proveedor</th>
                                                <th>Proyecto</th>
                                                <th>Firmado</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if($results) { ?>
                                                <?php for($i=0; $i<count($results); $i++) { ?>
                                                    <tr>
                                                        <td><a href="contracts.admin.detail.php?id=<?=$results[$i]['id'];?>"><?=str_pad($results[$i]['id'], 5, "0", STR_PAD_LEFT);?></td>
                                                        <td><?=$results[$i]['razonSocial'];?></td>
                                                        <td><?=$results[$i]['titulo'];?></td>
                                                        <td><?=$results[$i]['firmaFecha'];?></td>
                                                        <td><span class="label label-<?=$results[$i]['contratoStatus'];?>"><?=$results[$i]['contratoStatus'];?></span></td>
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
                    "aaSorting": [[0, 'desc']],
                } );

                $(".datepicker").datepicker();

            });

            function change_year(year) {

                $(".proy_add_select").attr('disabled', 'disabled');
                $(".proy_add_select").hide();
                $("#proy_add_proyectoId_"+year).removeAttr('disabled');
                $("#proy_add_proyectoId_"+year).show();

            }

        </script>

<?php include("inc.footer.php"); ?>