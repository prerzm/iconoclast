<?php

# include configuration file
include_once ("includes/inc.init.php");
include_once ("includes/lib.dates.php");
include_once ("includes/lib.numbers.php");

# vars
$poId = (int)aget('id');
$projectId = (int)aget('pId');
$vendorId = (int)aget('vId');
$statusId = (int)aget('sId');

# queries
$record = get_po_info($poId);
$history = get_po_log($poId);

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
                                <h2 style="color:#1b54a3;">Cuenta por Pagar - <?=$record['concepto'];?></h2>
                            </div>
                        </div>
                        <!-- breadcrumb -->
                        <div class="navbar">
                            <div class="navbar-inner">
                                <ul class="breadcrumb">
                                    <i class="icon-chevron-left hide-sidebar"><a href="#" title="Hide Sidebar" rel="tooltip">&nbsp;</a></i>
                                    <i class="icon-chevron-right show-sidebar" style="display:none;"><a href="#" title="Show Sidebar" rel="tooltip">&nbsp;</a></i>
                                    <li><a href="index.php">Inicio</a> <span class="divider">/</span></li>
                                    <li><a href="pos.php">Cuentas por Pagar</a> <span class="divider">/</span></li>
                                    <li class="active"><?=$record['concepto'];?></li>
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
                                <div class="muted pull-left">Cuenta por Pagar</div>
                            </div>
                            <div class="block-content collapse in">

                                <form id="form_add" method="post" action="mod/pos.php" enctype="multipart/form-data">
                                <input type="hidden" name="cmd" value="pay">
                                <input type="hidden" name="id" value="<?=$poId;?>">

                                    <table class="table">
                                        <tr><th>Proyecto</th><td><?=$record['titulo'];?></td></tr>
                                        <tr><th>Concepto</th><td><?=$record['concepto'];?></td></tr>
                                        <tr>
                                            <th>Proveedor</th>
                                            <td>
                                                <?=$record['razonSocial'];?><br>
                                                <?=$record['rfc'];?><br>
                                                <?=$record['email'];?><br>
                                                <em>Banco:</em> <?=$record['banco'];?><br>
                                                <em>Cuenta:</em> <?=$record['cuenta'];?><br>
                                                <em>CLABE:</em> <?=$record['clabe'];?><br>
                                            </td>
                                        </tr>
                                        <?php if($record['pagoStatusId']!=PAYMENT_STATUS_CANCELLED) { ?>
                                        <tr>
                                            <th>Pago</th>
                                            <td>
                                                <?php if($record['pagoStatusId']==PAYMENT_STATUS_PENDING) { ?><strong>Fecha Tentativa de Pago:</strong> <?php } ?>
                                                <?php if($record['pagoStatusId']==PAYMENT_STATUS_AUTHORIZED) { ?><strong>Fecha Programada de Pago:</strong> <?php } ?>
                                                <?php if($record['pagoStatusId']==PAYMENT_STATUS_PAYED) { ?><strong>Fecha de Pago:</strong> <?php } ?>
                                                <?php if(is_null($record['fechaDePago']) || strtotime($record['fechaDePago'])==false) { ?>
                                                    -
                                                <?php } else { ?>
                                                    <?=get_date_es("d \d\\e F \d\\e Y", $record['fechaDePago']);?>
                                                <?php } ?>
                                                <br><strong>Forma de Pago:</strong> <?=$record['pagoForma'];?><br>
                                            </td>
                                        </tr>
                                        <?php } ?>
                                        <tr><th>Status de Pago</th><td><span class="label label-<?=$record['pagoStatus'];?>"><?=$record['pagoStatus'];?></span></td></tr>
                                        <?php if( file_is_valid($record['facturaPDF']) || file_is_valid($record['facturaXML']) ) { ?>
                                            <tr>
                                                <th>Factura</th>
                                                <td>
                                                    <?php if( file_is_valid($record['facturaPDF']) ) { ?>
                                                        <a href="file.download.php?f=<?=base64_encode($record['facturaPDF']);?>&t=o" title="Descargar"><img src="images/icon_pdf.png" alt="<?=$record['facturaPDF'];?>" /></a>
                                                    <?php } ?>
                                                    <?php if( file_is_valid($record['facturaXML']) ) { ?>
                                                        <a href="file.download.php?f=<?=base64_encode($record['facturaXML']);?>" title="Descargar"><img src="images/icon_xml.png" alt="<?=$record['facturaXML'];?>" /></a>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                        <?php if($record['notas']!="") { ?>
                                            <tr><th>Notas</th><td><?=$record['notas'];?></td></tr>
                                        <?php } ?>
                                        <?php if($history) { ?>
                                        <tr>
                                            <th>Historial</th>
                                            <td style="font-size:12px;">
                                                <?php for($i=0; $i<count($history); $i++) { ?>
                                                    <?=get_date_es("d/m/y H:i", $history[$i]['fecha'])." - ".$history[$i]['info'];?><br>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                        <?php } ?>
                                        <tr><td colspan="2" style="border:none;">&nbsp;</td></tr>
                                        <?php if($record['moneda']=="MXN") { ?>
                                            <tr>
                                                <th>Monto</th>
                                                <td><?=number_currency($record['total']);?></td>
                                            </tr>
                                        <?php } else { ?>
                                            <tr>
                                                <th>Monto Original</th>
                                                <td><?=number_currency($record['monto']);?> <span class="label label-important"><?=$record['moneda'];?></span></td>
                                            </tr>
                                            <tr>
                                                <th>Monto a pagar en MXN</th>
                                                <td>
                                                    <input type="text" name="monto" class="span10 m-wrap" value="<?=number_float($record['monto']*$global_currencies[$record['moneda']],2);?>"/>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                        <tr>
                                            <th>Referencia Bancaria</th>
                                            <td><input type="text" name="referencia" class="span10 m-wrap" /></td>
                                        <tr>
                                            <th>Comprobante de Transferencia</th>
                                            <td><input type="file" name="transfer" class="span10 m-wrap"/></td>
                                        </tr>
                                        <tr>
                                            <th>Fecha de pago</th>
                                            <td>
                                                <label><input type="radio" name="update_fecha_pago" value="0" checked="checked" onclick="$('#fechaDePago').hide();"> Conservar la fecha original</label>
                                                <label><input type="radio" name="update_fecha_pago" value="1" onclick="$('#fechaDePago').hide();"> Actualizarla al día de hoy</label>
                                                <label><input type="radio" name="update_fecha_pago" value="2" onclick="$('#fechaDePago').show();"> Ingresar otra fecha</label>
                                                <input type="text" name="fechaDePago" id="fechaDePago" class="span2 m-wrap datepicker" style="display:none;" value="<?=date("Y-m-d");?>"/>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Notificar al proveedor del pago</th>
                                            <td>
                                                <label><input type="checkbox" name="notify_vendor" value="1" checked> Sí, enviar correo</label>
                                            </td>
                                        </tr>
                                    </table>

                                    <div class="control-group">
                                        <label class="control-label">&nbsp;</label>
                                        <div class="controls">
                                            <button type="submit" class="btn btn-primary"><i class="icon-pencil icon-white"></i> Guardar</button>
                                            <a href="pos.view.php?id=<?=$poId;?>&pId=<?=$projectId;?>&vId=<?=$vendorId;?>&sId=<?=$statusId;?>" class="btn btn-inverse"><i class="icon-arrow-left icon-white"></i> Regresar</a>
                                        </div>
                                    </div>

                                </form>
                                
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
        <script>

            $(document).ready(function() {

                $(".datepicker").datepicker();

            });

        </script>

<?php include("inc.footer.php"); ?>