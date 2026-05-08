<?php

/** HiperMedica **/

# include configuration file
include_once ("includes/inc.init.php");
include_once ("includes/lib.numbers.php");

# vars & filters
$proyectoId = (int)aget('id');

# queries
$record = get_project($proyectoId);
$directors = get_directors_all();
$results = sql_select("SELECT DISTINCT v.proveedorId, v.rfc, v.razonSocial, v.repseReq, v.email
                        FROM ".TABLE_VENDORS." v 
                        WHERE v.deleted = 0 AND 
                                (v.proveedorId IN (SELECT DISTINCT proveedorId FROM ".TABLE_CONTRACTS_VENDORS." WHERE proyectoId = $proyectoId) OR
                                v.proveedorId IN (SELECT DISTINCT proveedorId FROM ".TABLE_POS." WHERE proyectoId = $proyectoId))
                        ORDER BY v.proveedorId ASC");

$companies = sql_select("SELECT companyId, CONCAT(nombre, ' - ', razonSocial) AS company FROM ".TABLE_COMPANIES." ORDER BY razonSocial ASC");

$contracts['PM'][0] = sql_select("SELECT contratoId, nombre FROM ".TABLE_CONTRACTS." WHERE tipo = 'Contrato' AND subtipo NOT LIKE '%PF%' ORDER BY nombre ASC");
$contracts['PM'][-1] = sql_select("SELECT contratoId, nombre FROM ".TABLE_CONTRACTS." WHERE tipo = 'Contrato' AND subtipo NOT LIKE '%PF%' AND subtipo NOT LIKE '%Repse%' ORDER BY nombre ASC");
$contracts['PM'][1] = sql_select("SELECT contratoId, nombre FROM ".TABLE_CONTRACTS." WHERE tipo = 'Contrato' AND subtipo NOT LIKE '%PF%' AND (subtipo LIKE '%Repse%' OR subtipo LIKE '%Talent%' OR subtipo LIKE '%Encargo%') ORDER BY nombre ASC");

$contracts['PF'][0] = sql_select("SELECT contratoId, nombre FROM ".TABLE_CONTRACTS." WHERE tipo = 'Contrato' AND subtipo NOT LIKE '%PM%' ORDER BY nombre ASC");
$contracts['PF'][-1] = sql_select("SELECT contratoId, nombre FROM ".TABLE_CONTRACTS." WHERE tipo = 'Contrato' AND subtipo NOT LIKE '%PM%' AND subtipo NOT LIKE '%Repse%' ORDER BY nombre ASC");
$contracts['PF'][1] = sql_select("SELECT contratoId, nombre FROM ".TABLE_CONTRACTS." WHERE tipo = 'Contrato' AND subtipo NOT LIKE '%PM%' AND (subtipo LIKE '%Repse%' OR subtipo LIKE '%Talent%' OR subtipo LIKE '%Encargo%') ORDER BY nombre ASC");

?>
<?php include("inc.header.main.php"); ?>

    <div class="container-fluid">

        <!-- row top -->
        <div class="row-fluid">
        
            <div class="span12" id="content">

                <div class="row-fluid">
                    <!-- alerts -->
                    <?php display_alerts(); ?>
                    <!-- ./alerts -->
                    <div class="block">
                        <div class="navbar navbar-inner block-header">
                            <h2 style="color:#1b54a3;">Proyecto <?=$record['titulo'];?></h2>
                        </div>
                    </div>
                    <!-- breadcrumb -->
                    <div class="navbar">
                        <div class="navbar-inner">
                            <ul class="breadcrumb">
                                <i class="icon-chevron-right show-sidebar" style="display:none;"><a href="#" title="Show Sidebar" rel="tooltip">&nbsp;</a></i>
                                <li><a href="index.php">Inicio</a> <span class="divider">/</span></li>
                                <li><a href="projects.php">Proyectos</a> <span class="divider">/</span></li>
                                <li class="active">Proyecto</li>
                            </ul>
                        </div>
                    </div>
                    <!-- ./breadcrumb -->
                </div>
                
            </div><!-- ./content span9 -->

        </div><!-- ./row top -->
        
        <!-- row top -->
        <div class="row-fluid">

            <!-- menu -->
            <div id="div_menu" class="span3">

                <div class="row-fluid">

                    <div class="block">
                        <div class="navbar navbar-inner block-header">
                            <div class="muted pull-left">Información</div>
                        </div>
                        <div class="block-content collapse in">

                            <?php if($global_perms['EDIT']) { ?>
                                <form name="info" method="post" action="mod/projects.php">
                                <input type="hidden" name="cmd" value="update">
                                <input type="hidden" name="id" value="<?=$proyectoId;?>">
                            <?php } ?>
                                <table cellpadding="0" cellspacing="0" class="table table-striped table-bordered">
                                    <tbody>
                                        <tr class="row_Info" ><td style="width:40%;"><strong>Clave</strong></td><td><?=($global_perms['EDIT']) ? '<input type="text" name="clave" id="clave" value="'.$record['clave'].'">' : $record['clave'];?></td></tr>
                                        <tr class="row_Info" ><td style="width:40%;"><strong>Año</strong></td><td><?=($global_perms['EDIT']) ? '<input type="text" name="ano" id="ano" value="'.$record['ano'].'">' : $record['ano'];?></td></tr>
                                        <?php if(session_get_data("roleId")==ROLE_WEBMASTER) { ?>
                                        <tr class="row_Info" >
                                            <td style="width:40%;"><strong>Compañía</strong></td>
                                            <td>
                                                <select class="span10 m-wrap" name="companyId">
                                                    <?=form_select_options($companies, "companyId", "company", $record['companyId']);?>
                                                </select>
                                            </td>
                                        </tr>
                                        <?php } ?>
                                        <tr class="row_Info" ><td style="width:40%;"><strong>Nombre</strong></td><td><?=($global_perms['EDIT']) ? '<input type="text" name="titulo" id="titulo" value="'.$record['titulo'].'">' : $record['titulo'];?></td></tr>
                                        <tr class="row_Info" ><td style="width:40%;"><strong>Cliente</strong></td><td><?=($global_perms['EDIT']) ? '<input type="text" name="cliente" id="cliente" value="cliente">' : $record['cliente'];?></td></tr>
                                        <tr class="row_Info" ><td style="width:40%;"><strong>Fecha inicio</strong></td><td><?=($global_perms['EDIT']) ? '<input type="text" name="fechaInicio" id="fechaInicio" class="datepicker" value="'.$record['fechaInicio'].'">' : $record['fechaInicio'];?></td></tr>
                                        <tr class="row_Info" ><td style="width:40%;"><strong>Fecha fin</strong></td><td><?=($global_perms['EDIT']) ? '<input type="text" name="fechaFin" id="fechaFin" class="datepicker" value="'.$record['fechaFin'].'">' : $record['fechaFin'];?></td></tr>
                                        <tr class="row_Info" ><td style="width:40%;"><strong>Lugar</strong></td><td><?=($global_perms['EDIT']) ? '<input type="text" name="lugar" id="lugar" value="'.$record['lugar'].'">' : $record['lugar'];?></td></tr>
                                        <tr class="row_Info" >
                                            <td style="width:40%;"><strong>Director</strong></td>
                                            <td>
                                                <?php if($global_perms['EDIT']) { ?>
                                                    <select class="span10 m-wrap" name="directorId">
                                                        <option value="<?=$record['directorId'];?>" selected><?=$record['director'];?></option>
                                                        <?=form_select_options($directors, "proveedorId", "razonSocial", $record['directorId']);?>
                                                    </select>
                                                <?php } else { ?>
                                                    <?=$record['director'];?>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                        <tr class="row_Info" ><td style="width:40%;"><strong>Productor</strong></td><td><?=($global_perms['EDIT']) ? '<input type="text" name="productor" value="'.$record['productor'].'">' : $record['productor'];?></td></tr>
                                        <tr class="row_Info" ><td style="width:40%;"><strong>Productor en línea</strong></td><td><?=($global_perms['EDIT']) ? '<input type="text" name="productorLinea" value="'.$record['productorLinea'].'">' : $record['productorLinea'];?></td></tr>
                                    </tbody>
                                </table>
                                <?php /*** Check to add adenda when dates change
                                <div id="check_adenda_add">
                                    <label for="adenda_add"><input type="checkbox" name="adenda_add" id="adenda_add" value="1"> Agregar adenda a todos los proveedores al cambiar las fechas del proyecto</label>
                                </div>
                                ****/ ?>
                                <?php if($global_perms['EDIT']) { ?>
                                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                                <?php } ?>
                            </form>
                        </div>
                    </div><!-- /block -->

                    <?php if($global_perms['EDIT']) { ?>
                        <div class="block">
                            <div class="navbar navbar-inner block-header">
                                <div class="muted pull-left">Archivos</div>
                            </div>
                            <div class="block-content collapse in">
                                <div>
                                    <form name="crew" method="post" action="mod/projects.php" enctype="multipart/form-data">
                                        <input type="hidden" name="cmd" value="load_crew_file">
                                        <input type="hidden" name="id" value="<?=$proyectoId;?>">
                                        <div class="control-group">
                                            <label class="control-label"><strong>Cargar Crew</strong> (<a href="file.download.php?f=<?=base64_encode("files/files/CrewTemplate.xlsx");?>" title="Descargar plantilla">Descargar Plantilla</a>)</label>
                                            <div class="controls">
                                                <input type="file" name="crewfile" class="span10 m-wrap" style="float:left;width:70%;" /><button type="submit" class="btn btn-primary" style="float:right;">Cargar</button>
                                            </div>
                                        </div>
                                    </form>
                                    <div style="clear:both;">&nbsp;</div>
                                </div>
                                <div style="border-top:1px solid #eaeaea;padding-top:15px;">
                                    <form name="wage" method="post" action="mod/wages.php" enctype="multipart/form-data">
                                        <input type="hidden" name="cmd" value="load_file">
                                        <input type="hidden" name="proyectoId" value="<?=$proyectoId;?>">
                                        <div class="control-group">
                                            <label class="control-label"><strong>Cargar Nómina</strong></label>
                                            <div class="controls">
                                                <input type="file" name="nomina" class="span10 m-wrap" style="float:left;width:70%;" /><button type="submit" class="btn btn-primary" style="float:right;">Cargar</button>
                                            </div>
                                        </div>
                                    </form>
                                    <div style="clear:both;"></div>
                                </div>
                            </div>
                        </div><!-- /block -->
                    <?php } ?>

                </div><!-- ./row -->

            </div><!-- ./content span -->

            <!-- table -->
            <div id="div_table" class="span9">

                <div class="row-fluid">
                    <div class="block">
                        <div class="navbar navbar-inner block-header">
                            <div class="muted pull-left">Proveedores</div>
                        </div>
                        <div class="block-content collapse in">

                            <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="results">
                                <thead>
                                    <tr>
                                        <th style="width:33%;">Proveedor</th>
                                        <th style="width:37%;">Cuentas por Pagar</th>
                                        <th style="width:30%;">Contratos</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if($results) { ?>
                                        <?php for($i=0; $i<count($results); $i++) { ?>
                                            <?php 
                                            $proveedorId = (int)$results[$i]['proveedorId'];
                                            $proveedor_tipo = get_vendor_type($results[$i]['rfc']);
                                            $proveedor_repse = (int)$results[$i]['repseReq'];
                                            $pos = sql_select("SELECT g.gastoId, g.fechaDePago, g.concepto, g.total, g.moneda, g.facturaUuid, 
                                                                    ps.pagoStatusId, ps.pagoStatus, 
                                                                    CONCAT('".$record['pathFacturas']."', g.facturaNombre, '.pdf') AS facturaPDF
                                                                FROM ".TABLE_POS." g, ".TABLE_PAYMENTS_STATUS." ps
                                                                WHERE g.pagoStatusId = ps.pagoStatusId AND g.proyectoId = $proyectoId AND proveedorId = $proveedorId");
                                            $cas = sql_select("SELECT cv.id, cv.parentId, cv.fechaCreado, cv.firmaStatusId, cv.firmaFecha, 
                                                                    c.contratoId, c.tipo, IFNULL(c.nombre, 'Contrato') AS nombre, 
                                                                    cs.contratoStatus
                                                                FROM ".TABLE_CONTRACTS_STATUS." cs, ".TABLE_CONTRACTS_VENDORS." cv LEFT JOIN ".TABLE_CONTRACTS." c ON cv.contratoId = c.contratoId 
                                                                WHERE cs.contratoStatusId = cv.firmaStatusId AND cv.proyectoId = $proyectoId AND proveedorId = $proveedorId");
                                            ?>
                                            <tr>
                                                <td>
                                                    <h3 style="color:#243c5e;line-height:25px;margin-bottom:0px;"><?=$results[$i]['razonSocial'];?></h3>
                                                    <span style="font-size:16px;color:#888888;font-weight:bold;"><?=$results[$i]['rfc'];?><br><?=$results[$i]['email'];?></span>
                                                </td>
                                                <td>
                                                    <?php if($pos) { $j=0; ?>
                                                        <?php foreach($pos as $p) { $j++; ?>
                                                            <div style="padding:10px;border-radius:4px;margin-bottom:5px;border:1px solid gray;<?=($j%2==0) ? 'background-color:#eaeaea;' : '';?>">
                                                                <div style="width:45%;float:left;white-space:wrap;">
                                                                    <a href="pos.view.php?id=<?=$p['gastoId'];?>"><?=$p['concepto'];?></a>
                                                                </div>
                                                                <div style="width:55%;float:right;text-align:right;">
                                                                    <span style="font-size:12px;padding:2px;border:1px solid #a9a9a9;border-radius:2px;"><strong><?=number_currency($p['total']);?></strong></span> <span class="label label-<?=($p['moneda']=="MXN") ? 'success' : 'important';?>"><?=$p['moneda'];?></span>
                                                                    <span class="label label-<?=$p['pagoStatus'];?>"><?=$p['pagoStatus'];?></span>&nbsp;
                                                                    <?php if($p['facturaUuid']!="") { ?>
                                                                        <span class="label label-success">Con factura</span>
                                                                    <?php } else { ?>
                                                                        <span class="label label-important">Sin factura</span>
                                                                    <?php } ?>
                                                                </div>
                                                                <div style="clear:both;">&nbsp;</div>
                                                            </div>
                                                        <?php } ?>
                                                    <?php } ?>
                                                </td>
                                                <td>
                                                    <?php if($cas) { $j=0; ?>
                                                        <?php foreach($cas as $c) { $j++; ?>
                                                            <div style="padding:10px;border-radius:4px;margin-bottom:5px;white-space:nowrap;border:1px solid gray;<?=($j%2==0) ? 'background-color:#eaeaea;' : '';?>">
                                                                <?php if((int)$c['id']<CONTRACTS_NEW_ID && $c['firmaStatusId']==CONTRACT_STATUS_PENDING) { ?>
                                                                    Este contrato no se ha firmado, pero primero debe actualizarse:<br><br>
                                                                    <form method="post" action="mod/contracts.admin.php">
                                                                        <input type="hidden" name="cmd" value="change">
                                                                        <input type="hidden" name="id" value="<?=$c['id'];?>">
                                                                        <select name="newContratoId" style="width:90%;margin-bottom:10px;">
                                                                            <?=form_select_options($contracts[$proveedor_tipo][$proveedor_repse], "contratoId", "nombre");?>
                                                                        </select><br>
                                                                        <button type="submit" class="btn btn-primary">Actualizar Contrato</button>
                                                                    </form>
                                                                <?php } else { ?>
                                                                    <a href="contracts.admin.detail.php?id=<?=$c['id'];?>"><?=$c['nombre'];?></a> 
                                                                    <span class="label label-<?=$c['contratoStatus'];?>"><?=$c['contratoStatus'];?></span><br>
                                                                    <strong>Creado:</strong> <?=$c['fechaCreado'];?>
                                                                    <?=($c['firmaStatusId']==CONTRACT_STATUS_SIGNED) ? '<strong>Firmado:</strong> '.$c['firmaFecha'] : '';?>
                                                                <?php } ?>
                                                            </div>
                                                        <?php } ?>
                                                    <?php } else { ?>

                                                        Este proveedor no tiene contrato para este proyecto, seleccione el que desea agregar:<br><br>
                                                        <a href="#alertAddContract<?=$proveedorId;?>" data-toggle="modal" class="btn btn-primary btn-block"><i class="icon-repeat icon-white"></i> Agregar Contrato</a>
                                                        <div id="alertAddContract<?=$proveedorId;?>" class="modal hide">
                                                            <form method="post" action="mod/contracts.admin.php">
                                                                <input type="hidden" name="cmd" value="addctov">
                                                                <input type="hidden" name="pid" value="<?=$proyectoId;?>">
                                                                <input type="hidden" name="vid" value="<?=$proveedorId;?>">
                                                                <div class="modal-header">
                                                                    <button data-dismiss="modal" class="close" type="button">&times;</button>
                                                                    <h3>Agregar Contrato</h3>
                                                                </div>
                                                                <div class="modal-body">

                                                                    <div class="control-group">
                                                                        <label class="control-label">Contrato<span class="required">*</span></label>
                                                                        <div class="controls">
                                                                            <select name="contratoId" style="width:75%;">
                                                                                <?=form_select_options($contracts[$proveedor_tipo][$proveedor_repse], "contratoId", "nombre");?>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="control-group">
                                                                        <label class="control-label">Servicios Proporcionados o Personaje</label>
                                                                        <div class="controls">
                                                                            <input type="text" name="Servicios_Proporcionados_o_Personaje" style="width:75%;" />
                                                                        </div>
                                                                    </div>
                                                                    <div class="control-group">
                                                                        <label class="control-label">Monto de Pago</label>
                                                                        <div class="controls">
                                                                            <input type="text" name="Monto_de_Pago" style="width:75%;" />
                                                                        </div>
                                                                    </div>
                                                                    <div class="control-group">
                                                                        <div class="controls">
                                                                            <label for="notify_vendor"><input type="checkbox" name="notify_vendor" id="notify_vendor" value="1" /> Notificar al proveedor sobre el nuevo contrato</label>
                                                                        </div>
                                                                    </div>

                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="submit" class="btn btn-primary">Agregar Contrato</button>
                                                                    <a data-dismiss="modal" class="btn" href="#">Cancelar</a>
                                                                </div>
                                                            </form>
                                                        </div>

                                                    <?php } ?>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    <?php } ?>
                                </tbody>
                            </table>

                        </div>
                    </div><!-- /block -->
                </div><!-- ./row -->

            </div>

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

            $('#form_add').validate({
                errorClass: 'help-inline',
                rules: {
                    clave: {
                        minlength: 3,
                        required: true
                    },
                    titulo: {
                        minlength: 5,
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

        });

    </script>

<?php include("inc.footer.php"); ?>