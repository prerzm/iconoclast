<?php

# include configuration file
include_once ("includes/inc.init.php");

# vars
$poId = (int)aget('id');
$projectId = (int)aget('pId');
$vendorId = (int)aget('vId');
$statusId = (int)aget('sId');

# queries
$record = get_po_info($poId);

if($record===false) {
    set_alert("error", "Hubo un problema con la información.");
    redirect("pos.php");
}

$cfdi = ($record['facturaUuid']!="") ? json_decode($record['facturaInfo'], true) : false;
$monedas = get_currencies();
$cats_monedas_js = "";

$project = get_project($record['proyectoId']);
$vendor = get_vendor($record['proveedorId']);
$customers = get_customers();
$status = get_payments_status_role(session_get_data("roleId"), $record['pagoStatusId']);

$cfdi_forma_pago = get_sat_forma_pago();
$cfdi_metodo_pago = get_sat_metodo_pago();
$cfdi_uso = get_sat_uso_cfdi();

# years & projects
$companies = array();
if($global_companies) {
    foreach($global_companies as $g) {
        $projects = get_projects_visible($g['companyId']);
        $years = array();
        foreach($projects as $p) {
            $index = (int)$p['ano'];
            $years[$index][] = $p;
        }
        $companies[] = array_merge($g, array("years" => $years));
    }
}

?>
<?php include("inc.header.main.php"); ?>

        <div class="container-fluid">
            
            <!-- row top -->
            <div class="row-fluid">

                <!-- sidebar -->
                <div class="span3 hide" id="sidebar">

                </div>
                <!-- ./sidebar -->
                
                <!-- content span -->
                <div class="span12" id="content">
                    <div class="row-fluid">
                        <!-- alerts -->
                        <?php display_alerts(); ?>
                        <!-- ./alerts -->
                        <div class="block">
                            <div class="navbar navbar-inner block-header">
                                <h2 style="color:#1b54a3;"><?=($record['proyectoId']>0) ? $record['clave'].' - '.$record['titulo'].' - ' : '';?><?=$record['concepto'];?></h2>
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
                                <div class="muted pull-left">Editar</div>
                            </div>
                            <div class="block-content collapse in">

                                <?php if($global_perms['EDIT']) { ?>
                                <!-- edit-form-->
                                <form id="form_add" method="post" action="mod/pos.php" enctype="multipart/form-data">
                                    <input type="hidden" name="cmd" value="update">
                                    <input type="hidden" name="id" value="<?=$poId;?>">
                                    <fieldset>
                                        <div class="alert alert-error hide">
                                            <button class="close" data-dismiss="alert"></button>
                                            Hubo un problema. Favor de revisar la información.
                                        </div>
                                        <div class="alert alert-success hide">
                                            <button class="close" data-dismiss="alert"></button>
                                            La información es válida!
                                        </div>
                                        <?php if(count($global_companies)>1) { ?>
                                            <div class="control-group">
                                                <label class="control-label">Compañía</label>
                                                <div class="controls">
                                                    <select class="span10 m-wrap" id="companyId" name="companyId" onchange="change_company(this.value);">
                                                        <?=form_select_options($global_companies, "companyId", "razonSocial", $record['companyId']);?>
                                                    </select>
                                                </div>
                                            </div>
                                        <?php } ?>
                                        <?php if($companies) { ?>
                                            <div class="control-group">
                                                <label class="control-label">Proyecto</label>
                                                <div class="controls">
                                                    <!-- projects -->
                                                    <?php foreach($companies as $g) { ?>
                                                        <?php $years = $g['years']; ?>
                                                        <select class="span10 m-wrap ano_select" name="ano" id="proy_ano_<?=$g['companyId'];?>" <?=($g['companyId']!=$project['companyId']) ? 'style="display:none;" disabled': '';?> onchange="change_year(this.value);">
                                                            <?php foreach($years as $year => $values) { ?>
                                                                <?php if($g['companyId']==$project['companyId']) { ?>
                                                                    <option value="<?=$year;?>" <?=((int)$project['ano']==(int)$year) ? 'selected="selected"' : '';?>><?=($year==0) ? "Sin año" : $year;?></option>
                                                                <?php } else { ?>
                                                                    <option value="<?=$year;?>"><?=($year==0) ? "Sin año" : $year;?></option>
                                                                <?php } ?>
                                                            <?php } ?>
                                                        </select>
                                                        <?php foreach($years as $year => $values) { ?>
                                                            <select class="span10 m-wrap proy_select" size="8" name="proyectoId" id="proy_proyectoId_<?=$g['companyId'];?>_<?=$year;?>" <?=($g['companyId']==$project['companyId'] && (int)$project['ano']==(int)$year) ? '' : 'style="display:none;" disabled';?>>
                                                                <?php foreach($values as $p) { ?>
                                                                    <?php if($g['companyId']==$project['companyId']) { ?>
                                                                        <option value="<?=$p['proyectoId'];?>" <?=($record['proyectoId']==$p['proyectoId']) ? 'selected="selected"' : '';?>><?=$p['titulo'];?></option>
                                                                    <?php } else { ?>
                                                                        <option value="<?=$p['proyectoId'];?>"><?=$p['titulo'];?></option>
                                                                    <?php } ?>
                                                                <?php } ?>
                                                            </select>
                                                        <?php } ?>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        <?php } ?>
                                        <div class="control-group">
                                            <label class="control-label">Concepto<span class="required">*</span></label>
                                            <div class="controls">
                                                <input type="text" name="concepto" class="span10 m-wrap" value="<?=$record['concepto'];?>" />
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">Proveedor<span class="required">*</span></label>
                                            <div class="controls">
                                                <input type="text" id ="ajxProveedor" name="ajxProveedor" class="span10 m-wrap" value="<?=$vendor['razonSocial'];?>" />
                                                <input type="hidden" id="proveedorId" name="proveedorId" value="<?=$vendor['proveedorId'];?>">
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">Fecha tentativa de pago (<a onclick="quitar_fecha();">quitar</a>)</label>
                                            <div class="controls">
                                                <input type="text" id="fechaDePago" name="fechaDePago" class="span10 m-wrap datepicker" value="<?=$record['fechaDePago'];?>"/>
                                            </div>
                                        </div>
                                        <?php if($record['extranjero']==0) { ?>
                                            <div class="control-group">
                                                <label class="control-label">Uso del CFDI</label>
                                                <div class="controls">
                                                    <select class="span10 m-wrap" id="usoCfdiId" name="usoCfdiId">
                                                        <?=form_select_options($cfdi_uso, "usoCfdiId", "usoFull", $record['usoCfdiId']);?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="control-group">
                                                <label class="control-label">Forma de pago</label>
                                                <div class="controls">
                                                    <select class="span10 m-wrap" id="pagoFormaId" name="pagoFormaId">
                                                        <?=form_select_options($cfdi_forma_pago, "pagoFormaId", "pagoFormaFull", $record['pagoFormaId']);?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="control-group">
                                                <label class="control-label">Método de pago</label>
                                                <div class="controls">
                                                    <select class="span10 m-wrap" id="pagoMetodoId" name="pagoMetodoId">
                                                        <?=form_select_options($cfdi_metodo_pago, "pagoMetodoId", "pagoMetodoFull", $record['pagoMetodoId']);?>
                                                    </select>
                                                </div>
                                            </div>
                                        <?php } ?>
                                        <div class="control-group">
                                            <label class="control-label">Monto</label>
                                            <div class="controls">
                                                <input type="text" id="monto" name="monto" class="span5 m-wrap" value="<?=$record['monto'];?>" onchange="update_taxes();"/>
                                                <select id="moneda" name="moneda" class="span5 m-wrap">
                                                    <?php foreach($monedas as $key => $tc) { ?>
                                                        <option value="<?=$key;?>" <?=($record['moneda']==$key) ? 'selected="selected"' : '';?>><?=$key;?></<option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <?php if($record['extranjero']==0) { ?>
                                            <div class="control-group">
                                                <label class="control-label">IVA</label>
                                                <div class="controls">
                                                    <input type="text" id="iva" name="iva" class="span10 m-wrap" value="<?=$record['iva'];?>" onchange="update_total();"/>
                                                </div>
                                            </div>
                                            <div class="control-group">
                                                <label class="control-label">Retención IVA</label>
                                                <div class="controls">
                                                    <input type="text" id="retIVA" name="retIVA" class="span10 m-wrap" value="<?=$record['retIVA'];?>" onchange="update_total();"/>
                                                </div>
                                            </div>
                                            <div class="control-group">
                                                <label class="control-label">Retención ISR</label>
                                                <div class="controls">
                                                    <input type="text" id="retISR" name="retISR" class="span10 m-wrap" value="<?=$record['retISR'];?>" onchange="update_total();"/>
                                                </div>
                                            </div>
                                            <div class="control-group">
                                                <label class="control-label">Total</label>
                                                <div class="controls">
                                                    <input type="text" id="total" name="total" class="span10 m-wrap" value="<?=$record['total'];?>"/>
                                                </div>
                                            </div>
                                        <?php } ?>
                                        <div class="control-group">
                                            <label class="control-label">Factura o Recibo del Proveedor</label>
                                            <div class="controls">
                                                <?php if(var_is_valid_array($cfdi) && file_is_valid($record['facturaPDF'])) { ?>
                                                    <div class="alert alert-warning">Si desea sustituir el Recibo/Factura primero debe eliminarlo</div>
                                                <?php } else { ?>
                                                    Archivo PDF&nbsp;&nbsp;<input type="file" name="facturaPDF" class="span10 m-wrap"/>
                                                    <?php if($record['extranjero']==0) { ?>
                                                        <br>Archivo XML&nbsp;&nbsp;<input type="file" name="facturaXML" class="span10 m-wrap"/>
                                                    <?php } ?>
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">Transferencias</label>
                                            <div class="controls">
                                                <?php if(file_is_valid($record['transfer'])) { ?>
                                                    <a href="#delTransfer1" data-toggle="modal" class="btn"><i class="icon-file"></i> Eliminar Transferencia 1</a><br>
                                                    <div id="delTransfer1" class="modal hide">
                                                        <div class="modal-header">
                                                            <button data-dismiss="modal" class="close" type="button">&times;</button>
                                                            <h3>Eliminar Transferencia 1</h3>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p>Está seguro que desea eliminar la transferencia 1?</p>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <a class="btn btn-primary" href="mod/pos.php?cmd=deltransfer&id=<?=$poId;?>">Confirmar</a>
                                                            <a data-dismiss="modal" class="btn" href="#">Cancelar</a>
                                                        </div>
                                                    </div>
                                                <?php } else { ?>
                                                    <input type="file" name="transfer" class="span10 m-wrap"/><br>
                                                <?php } ?>
                                                <?php if(file_is_valid($record['transfer2'])) { ?>
                                                    <a href="#delTransfer2" data-toggle="modal" class="btn"><i class="icon-file"></i> Eliminar Transferencia 2</a><br>
                                                    <div id="delTransfer2" class="modal hide">
                                                        <div class="modal-header">
                                                            <button data-dismiss="modal" class="close" type="button">&times;</button>
                                                            <h3>Eliminar Transferencia 2</h3>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p>Está seguro que desea eliminar la transferencia 3?</p>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <a class="btn btn-primary" href="mod/pos.php?cmd=deltransfer&t=2&id=<?=$poId;?>">Confirmar</a>
                                                            <a data-dismiss="modal" class="btn" href="#">Cancelar</a>
                                                        </div>
                                                    </div>
                                                <?php } else { ?>
                                                    <input type="file" name="transfer2" class="span10 m-wrap"/><br>
                                                <?php } ?>
                                                <?php if(file_is_valid($record['transfer3'])) { ?>
                                                    <a href="#delTransfer3" data-toggle="modal" class="btn"><i class="icon-file"></i> Eliminar Transferencia 3</a><br>
                                                    <div id="delTransfer3" class="modal hide">
                                                        <div class="modal-header">
                                                            <button data-dismiss="modal" class="close" type="button">&times;</button>
                                                            <h3>Eliminar Transferencia 3</h3>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p>Está seguro que desea eliminar la transferencia 3?</p>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <a class="btn btn-primary" href="mod/pos.php?cmd=deltransfer&t=3&id=<?=$poId;?>">Confirmar</a>
                                                            <a data-dismiss="modal" class="btn" href="#">Cancelar</a>
                                                        </div>
                                                    </div>
                                                <?php } else { ?>
                                                    <input type="file" name="transfer3" class="span10 m-wrap"/><br>
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <?php if( $record['extranjero']==0 && $record['pagoMetodoId']!=FACTURAS_TIPO_COMPROBACION ) { ?>
                                            <div class="control-group">
                                                <label class="control-label">Complemento de Pago</label>
                                                <div class="controls">
                                                    <?php if(file_is_valid($record['comprobantePDF']) && file_is_valid($record['comprobanteXML'])) { ?>
                                                        <div class="alert alert-warning">Si desea sustituir el complemento de pago primero debe eliminarlo</div>
                                                    <?php } else { ?>
                                                        Archivo PDF&nbsp;&nbsp;<input type="file" name="comprobantePDF" class="span10 m-wrap"/><br>
                                                        Archivo XML&nbsp;&nbsp;<input type="file" name="comprobanteXML" class="span10 m-wrap"/>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        <?php } ?>
                                        <div class="control-group">
                                            <label class="control-label">Estatus de pago</label>
                                            <div class="controls">
                                                <select class="span10 m-wrap" id="pagoStatusId" name="pagoStatusId">
                                                    <?=form_select_options($status, "pagoStatusId", "pagoStatus", $record['pagoStatusId']);?>
                                                </select>
                                            </div>
                                        </div>
                                        <?php if($record['pagoStatusId']==PAYMENT_STATUS_PAYED) { ?>
                                        <div class="control-group">
                                            <label class="control-label">Referencia Bancaria</label>
                                            <div class="controls">
                                                <input type="text" name="referencia" class="span10 m-wrap" value="<?=$record['referencia'];?>"/>
                                            </div>
                                        </div>
                                        <?php } ?>
                                        <div class="control-group">
                                            <label class="control-label">Notas</label>
                                            <div class="controls">
                                                <input type="text" name="notas" class="span10 m-wrap" value="<?=$record['notas'];?>"/>
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">&nbsp;</label>
                                            <div class="controls">
                                                <button type="submit" class="btn btn-primary"><i class="icon-pencil icon-white"></i> Guardar</button>
                                                <button type="reset" class="btn btn-inverse" onclick="window.location='pos.view.php?id=<?=$poId;?>&pId=<?=$projectId;?>&vId=<?=$vendorId;?>&sId=<?=$statusId;?>';"><i class="icon-arrow-left icon-white"></i> Cancelar</button>
                                                <?php if($global_perms['DELETE']) { ?>
                                                    <a href="#myAlert" data-toggle="modal" class="btn btn-danger"><i class="icon-remove icon-white"></i> Eliminar</a>
                                                    <div id="myAlert" class="modal hide">
                                                        <div class="modal-header">
                                                            <button data-dismiss="modal" class="close" type="button">&times;</button>
                                                            <h3>Eliminar</h3>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p>Está seguro que desea eliminar este registro?</p>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <a class="btn btn-primary" href="mod/pos.php?cmd=del&id=<?=$poId;?>">Confirmar</a>
                                                            <a data-dismiss="modal" class="btn" href="#">Cancelar</a>
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                                <?php if( var_is_valid_array($cfdi) && file_is_valid($record['facturaPDF']) ) { ?>
                                                    <a href="#delInvoice" data-toggle="modal" class="btn"><i class="icon-file"></i> Eliminar Factura</a>
                                                    <div id="delInvoice" class="modal hide">
                                                        <div class="modal-header">
                                                            <button data-dismiss="modal" class="close" type="button">&times;</button>
                                                            <h3>Eliminar Factura</h3>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p>Está seguro que desea eliminar la factura?</p>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <a class="btn btn-primary" href="mod/pos.php?cmd=delinv&id=<?=$poId;?>">Confirmar</a>
                                                            <a data-dismiss="modal" class="btn" href="#">Cancelar</a>
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                                <?php if(file_is_valid($record['comprobantePDF']) && file_is_valid($record['comprobanteXML'])) { ?>
                                                    <a href="#delComprobante" data-toggle="modal" class="btn"><i class="icon-file"></i> Eliminar Comprobante</a>
                                                    <div id="delComprobante" class="modal hide">
                                                        <div class="modal-header">
                                                            <button data-dismiss="modal" class="close" type="button">&times;</button>
                                                            <h3>Eliminar Comprobante</h3>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p>Está seguro que desea eliminar el Comprobante de Pago?</p>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <a class="btn btn-primary" href="mod/pos.php?cmd=delcomp&id=<?=$poId;?>">Confirmar</a>
                                                            <a data-dismiss="modal" class="btn" href="#">Cancelar</a>
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </fieldset>
                                </form>
                                <!-- ./edit-form -->
                                <?php } ?>
                                
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
        <script type="text/javascript" src="vendors/autocomplete/js/jquery.autocomplete.js"></script>
        <link rel="stylesheet" href="vendors/autocomplete/css/styles.css" media="screen">
        <link rel="stylesheet" href="vendors/datepicker.css" media="screen">
        <script type="text/javascript" src="vendors/bootstrap-datepicker.js"></script>
        <script type="text/javascript" src="vendors/jquery-validation/dist/jquery.validate.min.js"></script>
        <script type="text/javascript" src="vendors/datatables/js/jquery.dataTables.min.js"></script>
        <script type="text/javascript" src="assets/DT_bootstrap.js"></script>
        <script>

            $(document).ready(function() {

                var date_delete = false;

                $('#results').dataTable( {
                    "sDom": "<'row'<'span6'l><'span6'f>r>t<'row'<'span6'i><'span6'p>>",
                    "sPaginationType": "bootstrap",
                    "iDisplayLength": 50,
                } );

                $('#form_add').validate({
                    errorClass: 'help-inline',
                    rules: {
                        ajxProveedor: {
                            required: true,
                        },
                        proveedorId: {
                            required: true,
                            min: 1
                        },
                        monto: {
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

                // autocomplete suggestion box
                $('#ajxProveedor').autocomplete({
                    serviceUrl: './mod/ajax.php',
                    params: {cmd: 'search_vendor'},
                    minChars: 3,
                    maxHeight: 150,
                    showNoSuggestionNotice: true,
                    noSuggestionNotice: 'No se encontraron registros',
                    noCache: true,
                    onSelect: function (suggestion) {
                        $("#proveedorId").val(suggestion.data);
                    }
                });

            });

            // company / project
            function change_company(company_id) {

                $(".sel_company_proys").hide();
                $(".sel_company_proys").prop('disabled', true);
                $("#proyectoId_"+company_id).show();
                $("#proyectoId_"+company_id).prop('disabled', false);

                var year = parseInt($("#proy_ano_"+company_id).val());
                change_year(year);

            }

            function change_year(year) {

                var company_id = parseInt($("#companyId").val());

                $(".ano_select").attr('disabled', 'disabled');
                $(".proy_select").attr('disabled', 'disabled');
                $(".ano_select").hide();
                $(".proy_select").hide();

                $("#proy_ano_"+company_id).removeAttr('disabled');
                $("#proy_ano_"+company_id).show();
                
                $("#proy_proyectoId_"+company_id+"_"+year).removeAttr('disabled');
                $("#proy_proyectoId_"+company_id+"_"+year).show();

            }

            // taxes
            function update_taxes() {

                var monto = parseFloat($("#monto").val());
                var iva = 0;
                var retiva = 0;
                var retisr = 0;

                if(monto!=undefined && monto>0) {
                    iva = monto * <?=TAX_IVA;?>;
                    retiva = iva * 3 / 2;
                    retisr = monto * <?=TAX_ISR;?>;
                }

                $("#iva").val(iva.toLocaleString('es-MX', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                $("#retIVA").val(retiva.toLocaleString('es-MX', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                $("#retISR").val(retisr.toLocaleString('es-MX', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                $("#iva").val(iva);
                $("#retIVA").val(retiva);
                $("#retISR").val(retisr);

                update_total();

            }

            function update_total() {

                if( $("#monto").val()=="" || isNaN($("#monto").val())) { var monto = 0; } else { var monto = parseFloat($("#monto").val()); }
                if( $("#iva").val()=="" || isNaN($("#iva").val())) { var iva = 0; } else { var iva = parseFloat($("#iva").val()); }
                if( $("#retIVA").val()=="" || isNaN($("#retIVA").val())) { var retIVA = 0; } else { var retIVA = parseFloat($("#retIVA").val()); }
                if( $("#retISR").val()=="" || isNaN($("#retISR").val())) { var retISR = 0; } else { var retISR = parseFloat($("#retISR").val()); }
                var total = monto + iva - retIVA - retISR;

                $("#total").val(total.toLocaleString('es-MX', {minimumFractionDigits: 2, maximumFractionDigits: 2}));

            }

            function quitar_fecha() {
                $("#fechaDePago").val("");
            }

        </script>

<?php include("inc.footer.php"); ?>