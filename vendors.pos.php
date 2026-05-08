<?php

# include configuration file
include_once ("includes/inc.init.php");
include_once ("includes/lib.numbers.php");

# vars
$vendorId = session_get_data("userId");
$vendor = get_vendor($vendorId);

# verify datos fiscales
if($vendor['rfc']=="" || $vendor['razonSocial']=="" || (int)$vendor['repseReq']==0) {
    set_alert("error", "Es necesario que ingrese su RFC o NIF, su Razón Social y su registro al REPSE para continuar.");
    redirect("vendors.info.invoice.php");
}

# verify datos bancarios
if($vendor['banco']=="" || $vendor['cuenta']=="" || ($vendor['extranjero']==0 && $vendor['clabe']=="") ) {
    set_alert("error", "Es necesario que complete su datos bancarios para continuar.");
    redirect("vendors.info.bank.php");
}

# verify pending contracts
if(!vendor_all_contracts_signed($vendor)) {
    redirect("vendors.contracts.php");
}

# verify id & dom
$id = vendor_valid_identificacion($vendor);
$dom = vendor_valid_comprobante_domicilio($vendor);
if(!$id || !$dom) {
    redirect("vendors.info.docs.php");
}

# queries
$results = sql_select(" SELECT 	g.gastoId, g.concepto, IF(g.fechaDePago IS NULL, '-', g.fechaDePago) AS fechaDePago, g.moneda, g.total, 
                                g.pagoMetodoId, g.facturaUuid, 
                                c.razonSocial, c.color, c.bgcolor, 
                                p.proyectoId, CONCAT(p.clave, ' - ', p.titulo) AS proyecto, 
                                CONCAT('".PATH_PROJECTS."', p.uniqId, '/facturas/', g.facturaNombre, '.pdf') AS facturaNombre, 
                                CONCAT('".PATH_PROJECTS."', p.uniqId, '/transfers/', g.transfer) AS transferNombre, 
                                CONCAT('".PATH_PROJECTS."', p.uniqId, '/comprobantes/', g.comprobante, '.pdf') AS comprobanteNombre, 
                                CONCAT(pf.claveFormaPago, ' - ', pf.pagoForma) AS pagoForma, 
                                ps.pagoStatusId, ps.pagoStatus
                        FROM ".TABLE_SAT_FORMA_PAGO." pf, ".TABLE_PAYMENTS_STATUS." ps, ".TABLE_POS." g, ".TABLE_PROJECTS." p, ".TABLE_COMPANIES." c 
                        WHERE   g.proyectoId = p.proyectoId AND g.pagoFormaId = pf.pagoFormaId AND g.pagoStatusId = ps.pagoStatusId AND p.companyId = c.companyId AND 
                                g.proveedorId = $vendorId 
                     ");

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
                                <h2 style="color:#1b54a3;">Pagos - <?=session_get_data("name");?></h2>
                            </div>
                        </div>
                        <!-- breadcrumb -->
                        <div class="navbar">
                            <div class="navbar-inner">
                                <ul class="breadcrumb">
                                    <i class="icon-chevron-left hide-sidebar"><a href="#" title="Hide Sidebar" rel="tooltip">&nbsp;</a></i>
                                    <i class="icon-chevron-right show-sidebar" style="display:none;"><a href="#" title="Show Sidebar" rel="tooltip">&nbsp;</a></i>
                                    <li><a href="index.php">Inicio</a> <span class="divider">/</span></li>
                                    <li class="active">Pagos</li>
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
                                                <th>Fecha de Pago</th>
                                                <th>Empresa</th>
                                                <th>Concepto</th>
                                                <th>Forma de Pago</th>
                                                <th>Factura</th>
                                                <th>Pago</th>
                                                <th>Complemento</th>
                                                <th>Monto</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if($results) { ?>
                                                <?php for($i=0; $i<count($results); $i++) { ?>
                                                    <tr>
                                                        <td><?=$results[$i]['fechaDePago'];?></td>
                                                        <td><span style="border:2px solid #<?=$results[$i]['color'];?>;border-radius:5px;font-size:16px;color:#<?=$results[$i]['color'];?>;background-color:#<?=$results[$i]['bgcolor'];?>;padding:5px;"><?=$results[$i]['razonSocial'];?></span></td>
                                                        <td><a href="vendors.pos.edit.php?id=<?=$results[$i]['gastoId'];?>"><?=$results[$i]['proyecto'].': '.$results[$i]['concepto'];?></a></td>
                                                        <td><?=$results[$i]['pagoForma'];?></td>
                                                        <td>
                                                            <?php if(file_is_valid($results[$i]['facturaNombre'])) { ?>
                                                                <a href="file.download.php?f=<?=base64_encode($results[$i]['facturaNombre']);?>&t=o" title="Descargar Factura"><img src="images/icon_pdf.png" /></a>
                                                            <?php } else { ?>
                                                                <span class="label label-warning">Pendiente</span>
                                                            <?php } ?>
                                                        </td>
                                                        <td>
                                                            <?php if(file_is_valid($results[$i]['transferNombre'])) { ?>
                                                                <a href="file.download.php?f=<?=base64_encode($results[$i]['transferNombre']);?>&t=o" title="Descargar Transferencia"><img src="images/icon_pdf.png" /></a>
                                                            <?php } ?>
                                                        </td>
                                                        <td>
                                                            <?php if($results[$i]['pagoMetodoId']!=FACTURAS_TIPO_COMPROBACION && $results[$i]['pagoStatusId']==PAYMENT_STATUS_PAYED) { ?>
                                                                <?php if(file_is_valid($results[$i]['comprobanteNombre'])) { ?>
                                                                    <a href="file.download.php?f=<?=base64_encode($results[$i]['comprobanteNombre']);?>&t=o" title="Descargar Comprobante de Pago"><img src="images/icon_pdf.png" /></a>
                                                                <?php } else { ?>
                                                                    <span class="label label-important">Pendiente</span>
                                                                <?php } ?>
                                                            <?php } ?>
                                                        </td>
                                                        <td nowrap style="text-align:right;"><?=number_currency($results[$i]['total']);?> <span class="label label-<?=($results[$i]['moneda']=="MXN") ? 'success' : 'important';?>"><?=$results[$i]['moneda'];?></span></td>
                                                        <td><span class="label label-<?=$results[$i]['pagoStatus'];?>"><?=$results[$i]['pagoStatus'];?></span></td>
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
                    "aaSorting": [[0, 'desc']],
                } );

                $(".datepicker").datepicker();

            });

        </script>

<?php include("inc.footer.php"); ?>