<?php

# include configuration file
include_once ("includes/inc.init.php");
include_once ("includes/lib.dates.php");
include_once ("includes/lib.numbers.php");

# vars
$poId = (int)aget('id');
$projectId = (int)aget('pId');
$vendor = urlencode(aget('vendor'));
$statusId = (int)aget('sId');

# queries
$record = get_po_info($poId);

if($record===false) {
    set_alert("warning", "Hubo un problema con la información.");
    redirect("pos.php");
}

$company_info = get_company_info($record['companyId']);
$factura_info = ($record['facturaUuid']!="") ? json_decode($record['facturaInfo'], true) : false;
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
                                <h2 style="color:#1b54a3;">Cuenta por Pagar</h2>
                            </div>
                        </div>
                        <!-- breadcrumb -->
                        <div class="navbar">
                            <div class="navbar-inner">
                                <ul class="breadcrumb">
                                    <i class="icon-chevron-left hide-sidebar"><a href="#" title="Hide Sidebar" rel="tooltip">&nbsp;</a></i>
                                    <i class="icon-chevron-right show-sidebar" style="display:none;"><a href="#" title="Show Sidebar" rel="tooltip">&nbsp;</a></i>
                                    <li><a href="index.php">Inicio</a> <span class="divider">/</span></li>
                                    <li><a href="pos.php?pId=<?=$projectId;?>&vendor=<?=$vendor;?>&sId=<?=$statusId;?>">Cuentas por Pagar</a> <span class="divider">/</span></li>
                                    <li class="active">Detalle</li>
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

                                <table class="table">
                                    <tr><th>Compañía</th><td><?=$company_info['razonSocial'];?></td></tr>
                                    <?php if($record['proyectoId']>0) { ?>
                                        <tr><th>Proyecto</th><td><?=$record['clave']." - ".$record['titulo'];?></td></tr>
                                    <?php } ?>
                                    <tr><th>Concepto</th><td><?=$record['concepto'];?></td></tr>
                                    <tr>
                                        <th>Proveedor</th>
                                        <td>
                                            <strong><?=$record['razonSocial'];?></strong><br>
                                            <?=$record['rfc'];?><br>
                                            <?=$record['email'];?><br>
                                            <em>Banco:</em> <?=$record['banco'];?><br>
                                            <em>Cuenta:</em> <?=$record['cuenta'];?><br>
                                            <em>CLABE:</em> <?=$record['clabe'];?><br>
                                            <em>SWIFT:</em> <?=$record['swift'];?><br>
                                            <em>ABA:</em> <?=$record['aba'];?><br>
                                        </td>
                                    </tr>
                                    <?php if($record['pagoStatusId']!=PAYMENT_STATUS_CANCELLED) { ?>
                                    <tr>
                                        <th>Pago<?=((bool)$record['prontoPago']) ? '<br><span class="label label-pronto-pago">Pronto Pago</span>': '';?></th>
                                        <td>
                                            <?php if($record['pagoStatusId']==PAYMENT_STATUS_PENDING) { ?><strong>Fecha Tentativa de Pago:</strong> <?php } ?>
                                            <?php if($record['pagoStatusId']==PAYMENT_STATUS_AUTHORIZED) { ?><strong>Fecha Programada de Pago:</strong> <?php } ?>
                                            <?php if($record['pagoStatusId']==PAYMENT_STATUS_PAYED) { ?><strong>Fecha de Pago:</strong> <?php } ?>
                                            <?php if(!is_null($record['fechaDePago']) && strtotime($record['fechaDePago'])!==false) { ?>
                                                <?=get_date_es("d \d\\e F \d\\e Y", $record['fechaDePago']);?>
                                            <?php } else { ?>
                                                -
                                            <?php } ?>
                                            <?php if($record['extranjero']==0) { ?>
                                                <br><strong>Uso del CFDI:</strong> <?=$record['usoCfdi'];?>
                                                <br><strong>Forma de Pago:</strong> <?=$record['pagoForma'];?>
                                                <br><strong>Método de Pago:</strong> <?=$record['pagoMetodo'];?>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                    <tr>
                                        <th>Monto</th>
                                        <td>
                                            <?php if($record['pagoStatusId']==PAYMENT_STATUS_PAYED) { ?>
                                                <strong>Pagado</strong>: <?=number_currency($record['totalMXN']);?> <span class="label label-success">MXN</span>
                                                <?php if($record['moneda']!="MXN") { ?>
                                                    (<?=number_currency($record['monto']);?> <span class="label label-important"><?=$record['moneda'];?></span> @ <?=number_currency($record['tipoDeCambio']);?>)
                                                <?php } ?>
                                            <?php } else { ?>
                                                <?php if($record['extranjero']==0) { ?>
                                                    <em>Monto</em>: <?=number_currency($record['monto'])." ".$record['moneda'];?><br>
                                                    <em>IVA</em>: <?=number_currency($record['iva'])." ".$record['moneda'];?><br>
                                                    <?php if($record['retIVA']>0 || $record['retISR']>0) { ?>
                                                        <em>Ret. IVA</em>: <?=number_currency($record['retIVA'])." ".$record['moneda'];?><br>
                                                        <em>Ret. ISR</em>: <?=number_currency($record['retISR'])." ".$record['moneda'];?><br>
                                                    <?php } ?>
                                                <?php } ?>
                                                <strong>Total</strong>: <?=number_currency($record['total']);?> <span class="label label-<?=($record['moneda']=="MXN") ? 'success' : 'important';?>"><?=$record['moneda'];?></span>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Status de Pago</th>
                                        <td>
                                            <span class="label label-<?=$record['pagoStatus'];?>"><?=$record['pagoStatus'];?></span>
                                            <?php if($record['facturaUuid']!="" && (file_is_valid($record['facturaPDF']) || file_is_valid($record['facturaXML'])) ) { ?>
                                                <span class="label label-success">Con factura</span>
                                            <?php } else { ?>
                                                <span class="label label-important">Sin factura</span>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                    <?php if( file_is_valid($record['facturaPDF']) || file_is_valid($record['facturaXML']) ) { ?>
                                        <tr>
                                            <th>Factura</th>
                                            <td>
                                                <?php if(var_is_valid_array($factura_info)) { ?>
                                                    <?php foreach($factura_info as $name => $value) { ?>
                                                        <strong><?=$name;?></strong>: <?=$value;?><br>
                                                    <?php } ?>
                                                    <br>
                                                <?php } ?>
                                                <?php if( file_is_valid($record['facturaPDF']) ) { ?>
                                                    <a href="file.download.php?f=<?=base64_encode($record['facturaPDF']);?>&t=o" title="Descargar"><img src="images/icon_pdf.png" /></a>
                                                <?php } ?>
                                                <?php if( file_is_valid($record['facturaXML']) ) { ?>
                                                    <a href="file.download.php?f=<?=base64_encode($record['facturaXML']);?>" title="Descargar"><img src="images/icon_xml.png" /></a>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                    <?php if( file_is_valid($record['transfer']) || file_is_valid($record['transfer2']) || file_is_valid($record['transfer3']) ) { ?>
                                        <tr>
                                            <th>Transferencias</th>
                                            <td>
                                                <?php if( file_is_valid($record['transfer']) ) { ?>
                                                    <a href="file.download.php?f=<?=base64_encode($record['transfer']);?>&t=o" title="Descargar"><img src="images/icon_pdf.png" /></a>
                                                <?php } ?>
                                                <?php if( file_is_valid($record['transfer2']) ) { ?>
                                                    <a href="file.download.php?f=<?=base64_encode($record['transfer2']);?>&t=o" title="Descargar"><img src="images/icon_pdf.png" /></a>
                                                <?php } ?>
                                                <?php if( file_is_valid($record['transfer3']) ) { ?>
                                                    <a href="file.download.php?f=<?=base64_encode($record['transfer3']);?>&t=o" title="Descargar"><img src="images/icon_pdf.png" /></a>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                    <?php if( file_is_valid($record['comprobantePDF']) && file_is_valid($record['comprobanteXML']) ) { ?>
                                        <tr>
                                            <th>Complemento de Pago</th>
                                            <td>
                                                <a href="file.download.php?f=<?=base64_encode($record['comprobantePDF']);?>&t=o" title="Descargar"><img src="images/icon_pdf.png" /></a>&nbsp;
                                                <a href="file.download.php?f=<?=base64_encode($record['comprobanteXML']);?>" title="Descargar"><img src="images/icon_xml.png" /></a>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                    <?php if($record['referencia']!="") { ?>
                                        <tr>
                                            <th>Referencia Bancaria</th>
                                            <td><?=$record['referencia'];?></td>
                                        </tr>
                                    <?php } ?>
                                    <?php if($record['notas']!="") { ?>
                                        <tr>
                                            <th>Notas</th>
                                            <td>
                                                <div class="alert alert-info">
                                                    <?=$record['notas'];?>
                                                </div>
                                            </td>
                                        </tr>
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
                                </table>

                                <div class="control-group">
                                    <label class="control-label">&nbsp;</label>
                                    <div class="controls">
                                        <?php if($global_perms['EDIT']) { ?>
                                            <a href="pos.edit.php?id=<?=$poId;?>&pId=<?=$projectId;?>&vendor=<?=$vendor;?>&sId=<?=$statusId;?>" class="btn btn-primary"><i class="icon-edit icon-white"></i> Editar</a>
                                        <?php } ?>
                                        <?php if($record['pagoStatusId']==PAYMENT_STATUS_PENDING && $global_perms['AUTHORIZE']) { ?>
                                            <a href="mod/pos.php?cmd=auth&id=<?=$poId;?>" class="btn btn-info"><i class="icon-star icon-white"></i> Autorizar</a>
                                        <?php } ?>
                                        <?php if($record['pagoStatusId']==PAYMENT_STATUS_AUTHORIZED && $global_perms['PAY']) { ?>
                                            <a href="pos.pay.php?id=<?=$poId;?>&pId=<?=$projectId;?>&vendor=<?=$vendor;?>&sId=<?=$statusId;?>" class="btn btn-success"><i class="icon-ok icon-white"></i> Pagar</a>
                                        <?php } ?>
                                        <a href="pos.php?pId=<?=$projectId;?>&vendor=<?=$vendor;?>&sId=<?=$statusId;?>" class="btn btn-inverse"><i class="icon-arrow-left icon-white"></i> Regresar</a>
                                    </div>
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

<?php include("inc.footer.php"); ?>