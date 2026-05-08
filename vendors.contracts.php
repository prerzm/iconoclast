<?php

# include configuration file
include_once ("includes/inc.init.php");
include_once ("includes/lib.numbers.php");

# vars
$vendorId = session_get_data("userId");

# queries
$results = get_contracts_vendor($vendorId);

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
                                <h2 style="color:#1b54a3;">Contratos - <?=session_get_data("name");?></h2>
                            </div>
                        </div>
                        <!-- breadcrumb -->
                        <div class="navbar">
                            <div class="navbar-inner">
                                <ul class="breadcrumb">
                                    <i class="icon-chevron-left hide-sidebar"><a href="#" title="Hide Sidebar" rel="tooltip">&nbsp;</a></i>
                                    <i class="icon-chevron-right show-sidebar" style="display:none;"><a href="#" title="Show Sidebar" rel="tooltip">&nbsp;</a></i>
                                    <li><a href="index.php">Inicio</a> <span class="divider">/</span></li>
                                    <li class="active">Contratos</li>
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
                                                <th>Empresa</th>
                                                <th>Proyecto</th>
                                                <th>Tipo</th>
                                                <th>Status</th>
                                                <th>Fecha Firma</th>
                                                <th>&nbsp;</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if($results) { ?>
                                                <?php for($i=0; $i<count($results); $i++) { ?>
                                                    <tr>
                                                        <td><?=$results[$i]['razonSocial'];?></td>
                                                        <td><?=$results[$i]['titulo'];?></td>
                                                        <td><?=$results[$i]['tipo'];?><?=($results[$i]['tipo']=="Adenda") ? '&nbsp;&nbsp;<span class="label label-warning">Vigencia/Remuneración</span>': '';?></td>
                                                        <td><span class="label label-<?=$results[$i]['contratoStatus'];?>"><?=$results[$i]['contratoStatus'];?></span></td>
                                                        <td><?=$results[$i]['firmaFecha'];?></td>
                                                        <td>
                                                            <?php if( $results[$i]['firmaStatusId']==CONTRACT_STATUS_PENDING ) { ?>
                                                                <a href="mod/vendors.contracts.php?cmd=set&id=<?=$results[$i]['id'];?>" class="btn btn-warning"><i class="icon-pencil icon-white"></i> Firmar</a>
                                                            <?php } else { ?>
                                                                <a href="vendors.contracts.detail.php?id=<?=$results[$i]['id'];?>" class="btn"><i class="icon-eye-open"></i> Ver</a>
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
                    "aaSorting": [[0, 'asc']],
                } );

                $(".datepicker").datepicker();

            });

        </script>

<?php include("inc.footer.php"); ?>