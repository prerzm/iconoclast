<?php

# include configuration file
include_once ("includes/inc.init.php");

# vars
$companyId = session_get_data("companyId");
$projectId = (int)aget('pId');

# queries
$years = get_years_projects($companyId);
$projects = get_projects_visible($companyId);
$monedas = get_currencies();

$cfdi_forma_pago = get_sat_forma_pago();
$cfdi_metodo_pago = get_sat_metodo_pago();
$cfdi_uso = get_sat_uso_cfdi();

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
                                <h2 style="color:#1b54a3;">Agregar Cuenta por Pagar</h2>
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
                                    <li class="active">Agregar</li>
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
                                <div class="muted pull-left">Agregar</div>
                            </div>
                            <div class="block-content collapse in">

                                <!-- add-form-->
                                <form id="form_add" method="post" action="mod/pos.php" enctype="multipart/form-data">
                                    <input type="hidden" name="cmd" value="add">
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
                                            <div class="control-group">
                                                <label class="control-label">Proyecto</label>
                                                <div class="controls">
                                                    <select class="span10 m-wrap" name="pId">
                                                        <?=form_select_options_groups($projects, "anio", "proyectoId", "titulo", $projectId);?>
                                                    </select>
                                                </div>
                                            </div>
                                        <?php } ?>
                                        <div class="control-group">
                                            <label class="control-label">Concepto<span class="required">*</span></label>
                                            <div class="controls">
                                                <input type="text" name="concepto" class="span10 m-wrap"/>
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">Proveedor<span class="required">*</span></label>
                                            <div class="controls">
                                                <input type="text" id ="ajxProveedor" name="ajxProveedor" class="span10 m-wrap"/>
                                                <input type="hidden" id="proveedorId" name="proveedorId" value="0">
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">Fecha de pago</label>
                                            <div class="controls">
                                                <input type="text" name="fechaDePago" class="span10 m-wrap datepicker"/>
                                            </div>
                                        </div>
                                        <div id="div_cfdi_uso" class="control-group">
                                            <label class="control-label">Uso del CFDI</label>
                                            <div class="controls">
                                                <select class="span10 m-wrap" id="usoCfdiId" name="usoCfdiId">
                                                    <?=form_select_options($cfdi_uso, "usoCfdiId", "usoFull", $global_company['comprobacionUsoCfdiId']);?>
                                                </select>
                                            </div>
                                        </div>
                                        <div id="div_cfdi_forma" class="control-group">
                                            <label class="control-label">Forma de pago</label>
                                            <div class="controls">
                                                <select class="span10 m-wrap" id="pagoFormaId" name="pagoFormaId">
                                                    <?=form_select_options($cfdi_forma_pago, "pagoFormaId", "pagoFormaFull", $global_company['comprobacionPagoFormaId']);?>
                                                </select>
                                            </div>
                                        </div>
                                        <div id="div_cfdi_metodo" class="control-group">
                                            <label class="control-label">Método de pago</label>
                                            <div class="controls">
                                                <select class="span10 m-wrap" id="pagoMetodoId" name="pagoMetodoId">
                                                    <?=form_select_options($cfdi_metodo_pago, "pagoMetodoId", "pagoMetodoFull", $global_company['comprobacionPagoMetodoId']);?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">Monto</label>
                                            <div class="controls">
                                                <input type="text" id="monto" name="monto" class="span5 m-wrap" onchange="update_taxes();" />
                                                <select id="moneda" name="moneda" class="span5 m-wrap">
                                                    <?php foreach($monedas as $key => $tc) { ?>
                                                        <option value="<?=$key;?>"><?=$key;?></<option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div id="div_iva" class="control-group" <?=((bool)$global_company['extranjera']) ? 'style="display:none;': '';?>>
                                            <label class="control-label">IVA</label>
                                            <div class="controls">
                                                <input type="text" id="iva" name="iva" class="span10 m-wrap" onchange="update_total();" />
                                            </div>
                                        </div>
                                        <div id="div_ret_iva" class="control-group" <?=((bool)$global_company['extranjera']) ? 'style="display:none;': '';?>>
                                            <label class="control-label">Retención IVA</label>
                                            <div class="controls">
                                                <input type="text" id="retIVA" name="retIVA" class="span10 m-wrap" onchange="update_total();" />
                                            </div>
                                        </div>
                                        <div id="div_ret_isr" class="control-group" <?=((bool)$global_company['extranjera']) ? 'style="display:none;': '';?>>
                                            <label class="control-label">Retención ISR</label>
                                            <div class="controls">
                                                <input type="text" id="retISR" name="retISR" class="span10 m-wrap" onchange="update_total();" />
                                            </div>
                                        </div>
                                        <div id="div_total" class="control-group" <?=((bool)$global_company['extranjera']) ? 'style="display:none;': '';?>>
                                            <label class="control-label">Total a Pagar</label>
                                            <div class="controls">
                                                <input type="text" id="total" name="total" class="span10 m-wrap"/>
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">Notas</label>
                                            <div class="controls">
                                                <input type="text" name="notas" class="span10 m-wrap"/>
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">Agregar contrato al proveedor para este proyecto si no existe?</label>
                                            <div class="controls">
                                                <label><input type="checkbox" name="add_contract" value="1" checked> Sí, agregar contrato</label>
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">Notificar al proveedor sobre el nuevo pago</label>
                                            <div class="controls">
                                                <label><input type="checkbox" name="notify_vendor" value="1" checked> Sí, enviar correo</label>
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">&nbsp;</label>
                                            <div class="controls">
                                                <button type="submit" class="btn btn-primary"><i class="icon-pencil icon-white"></i> Guardar</button>
                                                <button type="reset" class="btn btn-inverse" onclick="window.location='pos.php';"><i class="icon-arrow-left icon-white"></i> Cancelar</button>
                                            </div>
                                        </div>
                                    </fieldset>
                                </form>
                                <!-- ./add-form -->
                                
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

        <script type="text/javascript" src="vendors/bootstrap-datepicker.js"></script>
        <link rel="stylesheet" href="vendors/datepicker.css" media="screen">

        <script type="text/javascript" src="vendors/autocomplete/js/jquery.autocomplete.js"></script>
        <link rel="stylesheet" href="vendors/autocomplete/css/styles.css" media="screen">

        <script>

            $(document).ready(function() {

                $('#form_add').validate({
                    errorClass: 'help-inline',
                    rules: {
                        fechaDePago: {
                            date: true,
                            required: true
                        },
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
                        dataIdForeign = suggestion.data;
                        arrayData = dataIdForeign.split("|");
                        vendorId = parseInt(arrayData[0]);
                        vendorForeign = parseInt(arrayData[1]);
                        $("#proveedorId").val(vendorId);
                        update_extranjero(vendorForeign);
                    }
                });

            });

            // taxes
            function update_taxes() {

                var monto = parseFloat($("#monto").val());
                var iva = 0;
                var retiva = 0;
                var retisr = 0;

                if(monto!=undefined && monto>0) {
                    iva = monto * <?=TAX_IVA;?>;
                    retiva = (iva / 3) * 2;
                    retisr = monto * <?=TAX_ISR;?>;
                }

                $("#iva").val(iva.toLocaleString('es-MX', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                $("#retIVA").val(retiva.toLocaleString('es-MX', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                $("#retISR").val(retisr.toLocaleString('es-MX', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                $("#iva").val(iva.toFixed(2));
                $("#retIVA").val(retiva.toFixed(2));
                $("#retISR").val(retisr.toFixed(2));

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

            // destino - show/hide fields
            function update_extranjero(val) {
                if(val==0) {
                    $("#div_cfdi_uso").show();
                    $("#div_cfdi_forma").show();
                    $("#div_cfdi_metodo").show();
                    $("#div_iva").show();
                    $("#div_ret_iva").show();
                    $("#div_ret_isr").show();
                    $("#div_total").show();
                } else {
                    $("#div_cfdi_uso").hide();
                    $("#div_cfdi_forma").hide();
                    $("#div_cfdi_metodo").hide();
                    $("#div_iva").hide();
                    $("#div_ret_iva").hide();
                    $("#div_ret_isr").hide();
                    $("#div_total").hide();
                }
            }

        </script>

<?php include("inc.footer.php"); ?>