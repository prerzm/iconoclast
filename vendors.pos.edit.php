<?php

# include configuration file
include_once ("includes/inc.init.php");
include_once ("includes/lib.dates.php");
include_once ("includes/lib.numbers.php");

# vars
$poId = (int)aget('id');
$record = get_vendor_po_info($poId);
$vendor = get_vendor($record['proveedorId']);

# verify id & dom
$id = vendor_valid_identificacion($vendor);
$dom = vendor_valid_comprobante_domicilio($vendor);
if(!$id || !$dom) {
    redirect("vendors.info.docs.php");
}

# verify pending contracts
if(!vendor_all_contracts_signed($vendor)) {
    redirect("vendors.contracts.php");
}

# verify bank info
if(!vendor_valid_bank_info($vendor)) {
    redirect("vendors.info.bank.php");
}

# verify all other docs
$acta = vendor_valid_acta($vendor);
$csf = vendor_valid_constancia($vendor);
$oc = vendor_valid_opinion_cumplimiento($vendor);
$repse = vendor_valid_repse($vendor);
$edo = vendor_valid_estado_cuenta($vendor);
if(!$acta || !$csf || !$oc || !$repse || !$edo) {
    redirect("vendors.info.docs.php");
}

# verify complementos
if(!vendor_all_comps_uploaded($vendor, $record)) {
    redirect("vendors.pos.php");
}

# check poll
$poll_submitted = vendor_verify_poll_submitted(session_get_data("userId"), $record['proyectoId']);

# queries
$factura_xml = ($record['facturaUuid']!="") ? json_decode($record['facturaInfo'], true) : false;
$history = get_po_log($poId);
$company_info = get_company_info($record['companyId']);
$allow_edit = get_vendor_po_allow_edit($record['pagoMetodoId'], $record['facturaUuid'], $record['pagoStatusId'], $record['comprobanteXML']);

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
                                <h2 style="color:#1b54a3;"><?=$record['concepto'];?></h2>
                            </div>
                        </div>
                        <!-- breadcrumb -->
                        <div class="navbar">
                            <div class="navbar-inner">
                                <ul class="breadcrumb">
                                    <i class="icon-chevron-left hide-sidebar"><a href="#" title="Hide Sidebar" rel="tooltip">&nbsp;</a></i>
                                    <i class="icon-chevron-right show-sidebar" style="display:none;"><a href="#" title="Show Sidebar" rel="tooltip">&nbsp;</a></i>
                                    <li><a href="index.php">Inicio</a> <span class="divider">/</span></li>
                                    <li><a href="vendors.pos.php">Pagos</a> <span class="divider">/</span></li>
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

                                <form id="form_add" method="post" action="mod/vendors.pos.php" enctype="multipart/form-data">
                                <?php if( !file_is_valid($record['facturaPDF']) ) { ?>
                                    <input type="hidden" name="cmd" value="invoice">
                                <?php } else { ?>
                                    <input type="hidden" name="cmd" value="comprobante">
                                <?php } ?>
                                <input type="hidden" name="id" value="<?=$poId;?>">

                                    <table class="table">
                                        <tr>
                                            <th>Empresa</th>
                                            <td><span style="border:2px solid #<?=$record['color'];?>;border-radius:5px;font-size:16px;color:#<?=$record['color'];?>;background-color:#<?=$record['bgcolor'];?>;padding:5px;"><?=$record['empresa'];?></span></td>
                                        </tr>
                                        <tr><th>Proyecto</th><td><?=$record['proyecto'];?></td></tr>
                                        <tr><th>Concepto</th><td><?=$record['concepto'];?></td></tr>
                                        <tr>
                                            <th>Proveedor</th>
                                            <td>
                                                <strong><?=$record['razonSocial'];?></strong><br>
                                                <?=$record['rfc'];?><br>
                                                <?=$record['email'];?><br><br>

                                                <strong>Datos bancarios:</strong><br>
                                                <em>Banco:</em> <?=$record['banco'];?><br>
                                                <em>Cuenta:</em> <?=$record['cuenta'];?><br>
                                                <em>CLABE:</em> <?=$record['clabe'];?><br>
                                                <em>SWIFT:</em> <?=$record['swift'];?><br>
                                                <em>ABA:</em> <?=$record['aba'];?><br>
                                            </td>
                                        </tr>
                                        <?php if($record['pagoStatusId']!=PAYMENT_STATUS_CANCELLED) { ?>
                                        <tr>
                                            <th>Pago</th>
                                            <td>
                                                <?php if($record['pagoStatusId']==PAYMENT_STATUS_PENDING) { ?><strong>Fecha Tentativa de Pago:</strong> <?php } ?>
                                                <?php if($record['pagoStatusId']==PAYMENT_STATUS_AUTHORIZED) { ?><strong>Fecha Programada de Pago:</strong> <?php } ?>
                                                <?php if($record['pagoStatusId']==PAYMENT_STATUS_PAYED) { ?><strong>Fecha de Pago:</strong> <?php } ?>
                                                <?php if(strtotime($record['fechaDePago'])==false) { ?>
                                                    -
                                                <?php } else { ?>
                                                    <?=get_date_es("d \d\\e F \d\\e Y", $record['fechaDePago']);?>
                                                <?php } ?>
                                                <br><strong>Forma de Pago:</strong> <?=$record['pagoForma'];?><br>
                                            </td>
                                        </tr>
                                        <?php } ?>
                                        <tr>
                                            <th>Monto</th>
                                            <td>
                                                <?php if($record['pagoStatusId']==PAYMENT_STATUS_PAYED) { ?>
                                                    <strong>Pagado</strong>: <?=number_currency($record['totalMXN']);?> <span class="label label-success">MXN</span>
                                                <?php } else { ?>
                                                    <?php if($record['extranjero']==0) { ?>
                                                        <em>Subtotal</em>: <?=number_currency($record['monto'])." ".$record['moneda'];?><br>
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
                                        <tr><th>Status de Pago</th><td><span class="label label-<?=$record['pagoStatus'];?>"><?=$record['pagoStatus'];?></span></td></tr>
                                        
                                        <?php if(!$poll_submitted) { ?>
                                            <tr>
                                                <th>Encuesta de satisfacción</th>
                                                <td>
                                                    <div class="alert alert-success">
                                                        <h4>Favor de contestar las siguientes preguntas antes de subir la factura.</h4>
                                                    </div>

                                                    <div id="question1" class="">
                                                        
                                                        <div class="btn-group">
                                                            <button data-toggle="dropdown" class="btn dropdown-toggle">¿Cómo fue la atención de Producción Ejecutiva antes y después de tu llamado?&nbsp; <span class="caret"></span></button>
                                                            <ul class="dropdown-menu">
                                                                <li><a href="#" onclick="check_answer(1, 1, 'Buena');">Buena</a></li>
                                                                <li><a href="#" onclick="check_answer(1, 0, 'Mala')">Mala</a></li>
                                                            </ul>
                                                        </div><!-- /btn-group -->
                                                        <span id="span_res1"></span>
                                                        <input type="hidden" name="res1" id="res1" value="-1">

                                                        <br><br>
                                                        <div class="btn-group">
                                                            <button data-toggle="dropdown" class="btn dropdown-toggle">¿Cómo fue la atención del personal administrativo de <?=$record['empresa'];?>?&nbsp; <span class="caret"></span></button>
                                                            <ul class="dropdown-menu">
                                                                <li><a href="#" onclick="check_answer(2, 1, 'Buena');">Buena</a></li>
                                                                <li><a href="#" onclick="check_answer(2, 0, 'Mala')">Mala</a></li>
                                                            </ul>
                                                        </div><!-- /btn-group -->
                                                        <span id="span_res2"></span>
                                                        <input type="hidden" name="res2" id="res2" value="-1">

                                                        <br><br>
                                                        <div class="btn-group">
                                                            <button data-toggle="dropdown" class="btn dropdown-toggle">¿Volverías a trabajar para <?=$record['empresa'];?>?&nbsp; <span class="caret"></span></button>
                                                            <ul class="dropdown-menu">
                                                                <li><a href="#" onclick="check_answer(3, 1, 'Si');">Si</a></li>
                                                                <li><a href="#" onclick="check_answer(3, 0, 'No')">No</a></li>
                                                            </ul>
                                                        </div><!-- /btn-group -->
                                                        <span id="span_res3"></span>
                                                        <input type="hidden" name="res3" id="res3" value="-1">

                                                        <br><br>
                                                        <textarea id="res4" name="res4" placeholder="¿Qué podemos mejorar para brindarte un mejor servicio?" style="width:500px;height:60px;"></textarea>

                                                    </div>
                                                </td>
                                            </tr>
                                        <?php } ?>

                                        <tr id="tr_invoice" <?=(!$poll_submitted) ? 'style="display:none;"' : '';?>>
                                            <th>
                                                <?php if($record['retIVA']>0 || $record['retISR']>0) { ?>
                                                    Recibo
                                                <?php } else { ?>
                                                    Factura
                                                <?php } ?>
                                            </th>
                                            <td>
                                                <?php if(var_is_valid_array($factura_xml)) { ?>
                                                    <?php foreach($factura_xml as $name => $value) { ?>
                                                        <strong><?=$name;?></strong>: <?=$value;?><br>
                                                    <?php } ?>
                                                    <br>
                                                <?php } ?>

                                                <?php if($record['extranjero']==0) { ?>
                                                    <?php if( file_is_valid($record['facturaPDF']) && file_is_valid($record['facturaXML']) ) { ?>
                                                        <a href="file.download.php?f=<?=base64_encode($record['facturaPDF']);?>&t=o" title="Descargar"><img src="images/icon_pdf.png" /></a>&nbsp;
                                                        <a href="file.download.php?f=<?=base64_encode($record['facturaXML']);?>" title="Descargar"><img src="images/icon_xml.png" /></a>
                                                    <?php } else { ?>
                                                        <div class="alert alert-warning">
                                                            <h4>Le recordamos que el cfdi debe llevar los siguientes datos, de lo contrario será rechazado.</h4>
                                                        </div>
                                                        <?php if((bool)$company_info['extranjera']) { ?>
                                                            <div class="alert alert-danger">
                                                                <h4>Consulte como realizar una factura al extranjero <a href="<?=SITE_URL;?>files/companies/331223299/ExportarServicios.pdf" target="_blank">aquí</a></h4>
                                                            </div>
                                                        <?php } ?>
                                                        <strong>Datos del receptor:</strong><br>
                                                        <em>RFC:</em> <?=$company_info['rfc'];?><br>
                                                        <em>Razón Social:</em> <?=$company_info['razonSocial'];?><br>
                                                        <em>Régimen Fiscal:</em> <?=$company_info['regimenFiscal'];?><br>
                                                        <?php if(!(bool)$company_info['extranjera']) { ?>
                                                            <em>CP:</em> <?=$company_info['cp'];?><br>
                                                        <?php } else { ?>
                                                            <em>País:</em> <?=$company_info['pais'];?><br>
                                                            <em>Clave de identificación fiscal:</em> <?=$company_info['taxid'];?><br>
                                                        <?php } ?>
                                                        <br>

                                                        <strong>Datos del CFDI:</strong><br>
                                                        <em>Uso del CFDI:</em> <?=$record['usoCfdi'];?><br>
                                                        <em>Forma de Pago:</em> <?=$record['pagoForma'];?><br>
                                                        <em>Método de Pago:</em> <?=$record['pagoMetodo'];?><br><br>

                                                        Archivo PDF&nbsp;&nbsp;<input type="file" name="facturaPDF" class="span10 m-wrap"/><br>
                                                        Archivo XML&nbsp;&nbsp;<input type="file" name="facturaXML" class="span10 m-wrap"/>
                                                    <?php } ?>
                                                <?php } else { ?>
                                                    <?php if( file_is_valid($record['facturaPDF']) ) { ?>
                                                        <a href="file.download.php?f=<?=base64_encode($record['facturaPDF']);?>&t=o" title="Descargar"><img src="images/icon_pdf.png" /></a>&nbsp;
                                                    <?php } else { ?>
                                                        <strong>Datos del receptor:</strong><br>
                                                        <em>RFC:</em> <?=$company_info['rfc'];?><br>
                                                        <em>Razón Social:</em> <?=$company_info['razonSocial'];?><br><br>

                                                        Archivo PDF&nbsp;&nbsp;<input type="file" name="facturaPDF" class="span10 m-wrap"/><br>
                                                    <?php } ?>
                                                <?php } ?>

                                            </td>
                                        </tr>
                                        <?php if( file_is_valid($record['transfer']) ) { ?>
                                            <tr>
                                                <th>Transferencia</th>
                                                <td><a href="file.download.php?f=<?=base64_encode($record['transfer']);?>&t=o" title="Descargar"><img src="images/icon_pdf.png" /></a></td>
                                            </tr>
                                        <?php } ?>
                                        <?php if( $record['extranjero']==0 && $record['pagoStatusId']==PAYMENT_STATUS_PAYED && $record['pagoMetodoId']!=FACTURAS_TIPO_COMPROBACION && strtotime($record['fechaDePago'])!=false ) { ?>
                                            <tr>
                                                <th>Complemento de Pago</th>
                                                <td>
                                                    <?php if( file_is_valid($record['comprobantePDF']) && file_is_valid($record['comprobanteXML']) ) { ?>
                                                        <a href="file.download.php?f=<?=base64_encode($record['comprobantePDF']);?>&t=o" title="Descargar"><img src="images/icon_pdf.png" /></a>&nbsp;
                                                        <a href="file.download.php?f=<?=base64_encode($record['comprobanteXML']);?>" title="Descargar"><img src="images/icon_xml.png" /></a>&nbsp;
                                                    <?php } else { ?>
                                                        <div class="alert alert-danger">
                                                            <h4>Es necesario que suba su complemento de pago.</h4>
                                                        </div>

                                                        <strong>Datos del complemento:</strong><br>
                                                        <em>Fecha de pago:</em> <?=get_date_es("d \d\\e F \d\\e Y", $record['fechaDePago']);?><br>
                                                        <em>Forma de pago:</em> <?=$record['pagoForma'];?><br>
                                                        <em>Monto:</em> <?=number_currency($record['total']);?><br><br>

                                                        Archivo PDF&nbsp;&nbsp;<input type="file" name="comprobantePDF" class="span10 m-wrap"/><br>
                                                        Archivo XML&nbsp;&nbsp;<input type="file" name="comprobanteXML" class="span10 m-wrap"/>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                        <?php }  ?>
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
                                            <?php if($allow_edit) { ?>
                                                <button type="submit" id="btn_submit" class="btn btn-primary" <?=(!file_is_valid($record['facturaPDF']) && !$poll_submitted) ? 'style="display:none;"' : '';?>><i class="icon-pencil icon-white"></i> Guardar</button>
                                            <?php } ?>
                                            <a href="vendors.pos.php" class="btn btn-inverse"><i class="icon-arrow-left icon-white"></i> Regresar</a>
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
        <script>

            $(document).ready(function() {

            });

            // check poll
            function check_answer(answer, val, txt) {
                
                var span_res = $("#span_res"+answer);
                var input_res = $("#res"+answer);

                input_res.val(val);
                span_res.html('<span class="label label-success"> '+txt+'</span>');

                check_all();

            }

            function check_all() {
                var res1 = parseInt($("#res1").val());
                var res2 = parseInt($("#res2").val());
                var res3 = parseInt($("#res3").val());

                console.log(res1, res2, res3);

                if(res1>=0 && res2>=0 && res3>=0) {
                    $("#tr_invoice").show();
                    $("#btn_submit").show();
                }

            }

        </script>

<?php include("inc.footer.php"); ?>