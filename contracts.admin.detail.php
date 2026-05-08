<?php

# include configuration file
include_once ("includes/inc.init.php");

# vars
$id = (int)aget('id');

if($id<CONTRACTS_NEW_ID) {

    $contract = new ContractOld($id);
    if(get_vendor_type($contract->get("rfc"))=="PM") {
        $no_persona = "PF";
    } else {
        $no_persona = "PM";
    }
    if((int)$contract->get("repseReq")==-1) {
        $sql = "SELECT contratoId, nombre FROM ".TABLE_CONTRACTS." WHERE tipo = 'Contrato' AND subtipo NOT LIKE '%$no_persona%' AND subtipo NOT LIKE '%Repse%' ORDER BY nombre ASC";
    } elseif((int)$contract->get("repseReq")==1) {
        $sql = "SELECT contratoId, nombre FROM ".TABLE_CONTRACTS." WHERE tipo = 'Contrato' AND subtipo NOT LIKE '%$no_persona%' AND (subtipo LIKE '%Repse%' OR subtipo LIKE '%Talent%') ORDER BY nombre ASC";
    } else {
        $sql = "SELECT contratoId, nombre FROM ".TABLE_CONTRACTS." WHERE tipo = 'Contrato' AND subtipo NOT LIKE '%$no_persona%' ORDER BY nombre ASC";
    }
    $contracts = sql_select($sql);
    
} else {

    $contract = new ContractsAdendas($id);
    $fields = $contract->get_fields();
    $attachment = ($contract->get("anexo")!="") ? true : false;
    $ads = sql_select("SELECT * FROM ".TABLE_CONTRACTS_VENDORS." cv WHERE cv.parentId = $id ORDER BY cv.id ASC");
    
    $adendas = array();
    if($ads) {
        foreach($ads as $a) {
            $adendas[] = new ContractsAdendas($a['id']);
        }
    }

}

if($contract->get_id()==0) {
    set_alert("error", "Hubo un error al consultar el contrato.");
    redirect("contracts.admin.php");
}

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
                        <h2 style="color:#1b54a3;">Contrato de <?=$contract->get("razonSocial")." para ".$contract->get("titulo");?></h2>
                    </div>
                </div>
                <!-- breadcrumb -->
                <div class="navbar">
                    <div class="navbar-inner">
                        <ul class="breadcrumb">
                            <i class="icon-chevron-right show-sidebar" style="display:none;"><a href="#" title="Show Sidebar" rel="tooltip">&nbsp;</a></i>
                            <li><a href="index.php">Inicio</a> <span class="divider">/</span></li>
                            <li><a href="contracts.admin.php">Contratos</a> <span class="divider">/</span></li>
                            <li class="active">Contrato</li>
                        </ul>
                    </div>
                </div>
                <!-- ./breadcrumb -->
            </div>
            
        </div><!-- ./content span9 -->

    </div><!-- ./row top -->

    <?php /*********************************************** OLD Contracts ****/ ?>

    <?php if($id<CONTRACTS_NEW_ID) { ?>

        <!-- row-fluid old id -->
        <div class="row-fluid">

            <!-- menu -->
            <div id="div_menu" class="span3">
                <div class="row-fluid">
                    <div class="block">
                        <div class="navbar navbar-inner block-header">
                            <div class="muted pull-left">Documentos</div>
                        </div>
                        <div class="block-content collapse in">
                            <div style="margin-bottom:10px;"><button type="button" id="button_Contrato" class="btn btn-primary btn-large btn-block" onclick="show_hide_div('Contrato');">Contrato</button></div>
                            <?php if(file_is_valid($contract->get("anexo"))) { ?><div style="margin-bottom:10px;"><button type="button" id="button_Anexo" class="btn btn-large btn-block" onclick="show_hide_div('Anexo');">Anexo</button></div><?php } ?>
                            <?php if(file_is_valid($contract->get("carta"))) { ?><div style="margin-bottom:10px;"><button type="button" id="button_Carta" class="btn btn-large btn-block" onclick="show_hide_div('Carta');">Carta NDA</button></div><?php } ?>
                        </div>
                    </div><!-- /block -->
                    <div class="block">
                        <div class="navbar navbar-inner block-header">
                            <div class="muted pull-left">Detalles</div>
                        </div>
                        <div class="block-content collapse in">
                            <div style="margin-bottom:10px;"><div class="label label-<?=$contract->get("contratoStatus");?>" style="width:97%;height:35px;font-size:20px;border-radius:5px;padding-top:20px;text-align:center;"><?=$contract->get("contratoStatus");?></div></div>
                            <div style="margin-bottom:10px;">
                                <strong>Proveedor</strong>: <?=$contract->get("razonSocial");?><br>
                                <strong>Proyecto</strong>: <?=$contract->get("titulo");?><br>
                                <strong>Tipo</strong>: <?=$contract->get("nombre");?><br>
                                <strong>Fecha de contrato</strong>: <?=$contract->get("fechaCreado");?><br>
                                <?php if($contract->get("firmaStatusId")==CONTRACT_STATUS_SIGNED) { ?>
                                    <strong>Fecha de firma</strong>: <?=$contract->get("firmaFecha");?><br>
                                <?php } ?>
                            </div>

                                <div style="margin-top:10px;">
                                    <!-- actualizar contrato -->
                                    <?php if($contract->get("firmaStatusId")==CONTRACT_STATUS_PENDING) { ?>
                                        <form name="contract" method="post" action="mod/contracts.admin.php">
                                            <input type="hidden" name="cmd" value="change">
                                            <input type="hidden" name="id" value="<?=$id;?>">
                                            <select name="newContratoId" style="width:100%;">
                                                <?=form_select_options($contracts, "contratoId", "nombre");?>
                                            </select>
                                            <button type="submit" class="btn btn-primary btn-block"><i class="icon-edit icon-white"></i> Actualizar Contrato</button>
                                        </form>
                                    <?php } ?>
                                </div>

                                <div style="margin-top:10px;">
                                    <!-- rechazar contrato -->
                                    <?php if($contract->get("firmaStatusId")==CONTRACT_STATUS_SIGNED) { ?>
                                        <a href="#alertReject" data-toggle="modal" class="btn btn-warning btn-block"><i class="icon-repeat icon-white"></i> Rechazar Contrato</a>
                                        <div id="alertReject" class="modal hide">
                                            <div class="modal-header">
                                                <button data-dismiss="modal" class="close" type="button">&times;</button>
                                                <h3>Rechazar Contrato</h3>
                                            </div>
                                            <div class="modal-body">
                                                Rechazar y notificar al proveedor que debe llenar y firmar el contrato nuevamente.
                                            </div>
                                            <div class="modal-footer">
                                                <a href="mod/contracts.admin.php?cmd=reject&id=<?=$id;?>" class="btn btn-primary">Rechazar</a>
                                                <a data-dismiss="modal" class="btn" href="#">Cancelar</a>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>

                                <div style="margin-top:10px;">
                                    <!-- eliminar contrato -->
                                    <a href="#alertDelete" data-toggle="modal" class="btn btn-danger btn-block"><i class="icon-remove icon-white"></i> Eliminar Contrato</a>
                                    <div id="alertDelete" class="modal hide">
                                        <div class="modal-header">
                                            <button data-dismiss="modal" class="close" type="button">&times;</button>
                                            <h3>Eliminar Contrato</h3>
                                        </div>
                                        <div class="modal-body">
                                            Está seguro que desea eliminar este contrato y todos los archivos asociados al mismo?
                                        </div>
                                        <div class="modal-footer">
                                            <a href="mod/contracts.admin.php?cmd=del&id=<?=$id;?>" class="btn btn-primary">Eliminar</button>
                                            <a data-dismiss="modal" class="btn" href="#">Cancelar</a>
                                        </div>
                                    </div>
                                </div>

                                    
                        </div>
                    </div><!-- /block -->
                </div><!-- ./row -->
            </div><!-- ./content span -->

            <!-- contrato -->
            <div class="span9 div_Contrato">
                <div class="row-fluid">
                    <div class="block">
                        <div class="navbar navbar-inner block-header">
                            <div class="muted pull-left">Contrato</div>
                        </div>
                        <div class="block-content collapse in" style="text-align:justify;">
                            <?php if($contract->get("firmaStatusId")==CONTRACT_STATUS_PENDING) { ?>
                                <div class="alert alert-warning">
                                    <h4>Este contrato no ha sido firmado todavía. Puede actualizarse a un contrato nuevo.</h4>
                                </div>
                            <?php } elseif($contract->get("firmaStatusId")==CONTRACT_STATUS_SIGNED) { ?>
                                <?=$contract->get_html("contrato"); ?>
                            <?php } ?>
                        </div>
                    </div><!-- /block -->
                </div><!-- ./row -->
            </div><!-- ./content span -->
            <!-- /contrato -->

            <!-- anexo -->
            <?php if(file_is_valid($contract->get("anexo"))) { ?>
            <div class="span9 div_Anexo" style="display:none;">
                <div class="row-fluid">
                    <div class="block">
                        <div class="navbar navbar-inner block-header">
                            <div class="muted pull-left">Anexo subido por el Proveedor</div>
                        </div>
                        <div class="block-content collapse in" style="text-align:justify;">
                            <?=$contract->get_html("anexo"); ?>
                            <div style="margin-top:20px;text-align:center;">
                                <a href="#deleteAttach" data-toggle="modal" class="btn btn-danger"><i class="icon-remove icon-white"></i> Eliminar Anexo</a>
                                <div id="deleteAttach" class="modal hide" style="text-align:left;">
                                    <div class="modal-header">
                                        <button data-dismiss="modal" class="close" type="button">&times;</button>
                                        <h3>Eliminar Anexo</h3>
                                    </div>
                                    <div class="modal-body">
                                        <p>Está seguro que desea eliminar el anexo subido por el proveedor?</p>
                                    </div>
                                    <div class="modal-footer">
                                        <a class="btn btn-primary" href="mod/contracts.admin.php?cmd=delattach&id=<?=$id;?>">Confirmar</a>
                                        <a data-dismiss="modal" class="btn" href="#">Cancelar</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div><!-- /block -->
                </div><!-- ./row -->
            </div><!-- ./content span -->
            <?php } ?>
            <!-- /anexo -->

            <!-- carta -->
            <?php if(file_is_valid($contract->get("carta"))) { ?>
            <div class="span9 div_Carta" style="display:none;">
                <div class="row-fluid">
                    <div class="block">
                        <div class="navbar navbar-inner block-header">
                            <div class="muted pull-left">Carta NDA</div>
                        </div>
                        <div class="block-content collapse in" style="text-align:justify;">
                            <?=$contract->get_html("carta"); ?>
                            <div style="margin-top:20px;text-align:center;">
                                <a href="#deleteNDA" data-toggle="modal" class="btn btn-danger"><i class="icon-remove icon-white"></i> Eliminar Carta NDA</a>
                                <div id="deleteNDA" class="modal hide" style="text-align:left;">
                                    <div class="modal-header">
                                        <button data-dismiss="modal" class="close" type="button">&times;</button>
                                        <h3>Eliminar Carta NDA</h3>
                                    </div>
                                    <div class="modal-body">
                                        <p>Está seguro que desea eliminar la carta NDA firmada por el proveedor?</p>
                                    </div>
                                    <div class="modal-footer">
                                        <a class="btn btn-primary" href="mod/contracts.admin.php?cmd=delnda&id=<?=$id;?>">Confirmar</a>
                                        <a data-dismiss="modal" class="btn" href="#">Cancelar</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div><!-- /block -->
                </div><!-- ./row -->
            </div><!-- ./content span -->
            <?php } ?>
            <!-- /carta -->

        </div>
        <!-- /row-fluid old id -->

    <?php /*********************************************** NEW Contracts *****/ ?>

    <?php } else { ?>

        <!-- row-fluid new id -->
        <div class="row-fluid">

            <!-- menu -->
            <div id="div_menu" class="span2">
                <div class="row-fluid">
                    <div class="block">
                        <div class="navbar navbar-inner block-header">
                            <div class="muted pull-left">Documentos</div>
                        </div>
                        <div class="block-content collapse in">
                            <div style="margin-bottom:10px;"><button type="button" id="button_Contrato" class="btn btn-primary btn-large btn-block" onclick="show_hide_div('Contrato');">Contrato</button></div>
                            <?php if($attachment) { ?><div style="margin-bottom:10px;"><button type="button" id="button_Anexo" class="btn btn-large btn-block" onclick="show_hide_div('Anexo');">Anexo</button></div><?php } ?>
                            <?php if(count($adendas)>0) { ?><div style="margin-bottom:10px;"><button type="button" id="button_Adendas" class="btn btn-large btn-block" onclick="show_hide_div('Adendas');">Adendas</button></div><?php } ?>
                        </div>
                    </div><!-- /block -->
                    <div class="block">
                        <div class="navbar navbar-inner block-header">
                            <div class="muted pull-left">Detalles</div>
                        </div>
                        <div class="block-content collapse in">
                            <div style="margin-bottom:10px;"><div class="label label-<?=$contract->get("contratoStatus");?>" style="width:97%;height:35px;font-size:20px;border-radius:5px;padding-top:20px;text-align:center;"><?=$contract->get("contratoStatus");?></div></div>
                            <div style="margin-bottom:10px;">
                                <strong>Proveedor</strong>: <?=$contract->get("razonSocial");?><br>
                                <strong>Proyecto</strong>: <?=$contract->get("titulo");?><br>
                                <strong>Tipo</strong>: <?=$contract->get("nombre");?><br>
                                <strong>Fecha de contrato</strong>: <?=$contract->get("fechaCreado");?><br>
                                <?php if($contract->get("firmaStatusId")==CONTRACT_STATUS_SIGNED) { ?>
                                    <strong>Fecha de firma</strong>: <?=$contract->get("firmaFecha");?><br>
                                <?php } ?>
                            </div>

                                <div style="margin-top:10px;">
                                    <!-- rechazar contrato -->
                                    <?php if($contract->get("firmaStatusId")==CONTRACT_STATUS_SIGNED) { ?>
                                        <div style="margin-bottom:10px;"><a href="mod/contracts.admin.php?cmd=pdf&id=<?=$id;?>" class="btn btn-primary btn-block" target="_blank"><i class="icon-file icon-white"></i> Exportar Contrato</a></div>
                                        <a href="#alertReject" data-toggle="modal" class="btn btn-warning btn-block"><i class="icon-repeat icon-white"></i> Rechazar Contrato</a>
                                        <div id="alertReject" class="modal hide">
                                            <div class="modal-header">
                                                <button data-dismiss="modal" class="close" type="button">&times;</button>
                                                <h3>Rechazar Contrato</h3>
                                            </div>
                                            <div class="modal-body">
                                                Rechazar y notificar al proveedor que debe llenar y firmar el contrato nuevamente.
                                            </div>
                                            <div class="modal-footer">
                                                <a href="mod/contracts.admin.php?cmd=reject&id=<?=$id;?>" class="btn btn-primary">Rechazar</a>
                                                <a data-dismiss="modal" class="btn" href="#">Cancelar</a>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>

                                <div style="margin-top:10px;">
                                    <!-- eliminar contrato -->
                                    <a href="#alertDelete" data-toggle="modal" class="btn btn-danger btn-block"><i class="icon-remove icon-white"></i> Eliminar Contrato</a>
                                    <div id="alertDelete" class="modal hide">
                                        <div class="modal-header">
                                            <button data-dismiss="modal" class="close" type="button">&times;</button>
                                            <h3>Eliminar Contrato</h3>
                                        </div>
                                        <div class="modal-body">
                                            Está seguro que desea eliminar este contrato, sus adendas y archivos asociados?
                                        </div>
                                        <div class="modal-footer">
                                            <a href="mod/contracts.admin.php?cmd=del&id=<?=$id;?>" class="btn btn-primary">Eliminar</button>
                                            <a data-dismiss="modal" class="btn" href="#">Cancelar</a>
                                        </div>
                                    </div>
                                </div>

                                    
                        </div>
                    </div><!-- /block -->
                </div><!-- ./row -->
            </div><!-- ./content span -->

            <!-- contrato -->
            <div class="span4 div_Contrato">
                <div class="row-fluid">

                <!-- información -->
                    <div class="block">
                        <div class="navbar navbar-inner block-header">
                            <div class="muted pull-left">Información del Contrato</div>
                        </div>
                        <div class="block-content collapse in">
                            <form name="contract" method="post" action="mod/contracts.admin.php">
                                <input type="hidden" name="cmd" value="update">
                                <input type="hidden" name="id" value="<?=$id;?>">
                                <?php if(count($fields)>0) { ?>
                                    <table cellpadding="0" cellspacing="0" class="table table-striped table-bordered" id="results">
                                        <thead>
                                            <tr>
                                                <th>Dato</th>
                                                <th>Información</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Info -->
                                            <?php foreach($fields as $f) { ?>
                                                <tr class="row_Info" >
                                                    <td style="width:40%;"><?=$f['text'];?></td>
                                                    <td>
                                                        <?php if($contract->get("firmaStatusId")==CONTRACT_STATUS_SIGNED) { ?>
                                                            <?=$f['value'];?>
                                                        <?php } else { ?>
                                                            <input type="text" name="<?=$f['field'];?>" id="<?=$f['field'];?>" class="span10 m-wrap" value="<?=$f['value'];?>">
                                                        <?php } ?>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                    <?php if($contract->get("firmaStatusId")==CONTRACT_STATUS_PENDING) { ?>
                                        <button type="submit" id="button_submit" class="btn btn-primary">Guardar Cambios</button>
                                    <?php } ?>
                                <?php } else { ?>
                                    Este contrato todavía no tiene información.
                                <?php } ?>
                            </form>
                        </div>
                    </div><!-- /block -->

                    <!-- generar adenda -->
                    <?php if($contract->get("firmaStatusId")==CONTRACT_STATUS_SIGNED && substr($contract->get("subtipo"), 0, strlen($contract->get("subtipo"))-2)!="ContratoObraEncargo") { ?>
                    <div class="block">
                        <div class="navbar navbar-inner block-header">
                            <div class="muted pull-left">Generar Adenda</div>
                        </div>
                        <div class="block-content collapse in">
                            <form name="contract" method="post" action="mod/contracts.admin.php">
                                <input type="hidden" name="cmd" value="adenda">
                                <input type="hidden" name="id" value="<?=$id;?>">
                                <table cellpadding="0" cellspacing="0" class="table table-striped table-bordered" id="results">
                                    <thead>
                                        <tr>
                                            <th>Campo</th>
                                            <th>Valor</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="row_Info" >
                                            <td>Fecha de Inicio</td>
                                            <td><input type="text" name="PROYECTO_FECHA_INICIO" id="Proyecto_Fecha_Inicio" class="span10 m-wrap datepicker" value="<?=$contract->get('fechaInicio');?>" onchange="activate_adenda();"></td>
                                        </tr>
                                        <tr class="row_Info" >
                                            <td>Fecha de Fin</td>
                                            <td><input type="text" name="PROYECTO_FECHA_FIN" id="Proyecto_Fecha_Fin" class="span10 m-wrap datepicker" value="<?=$contract->get('fechaFin');?>" onchange="activate_adenda();"></td>
                                        </tr>
                                        <tr class="row_Info" >
                                            <td>Remuneración</td>
                                            <td><input type="text" name="Monto_de_Pago" id="Monto_de_Pago" class="span10 m-wrap" value="0" onchange="activate_adenda();"></td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div style="margin-top:10px;">
                                    <button type="submit" id="button_adenda" class="btn btn-primary" style="display:none;" onclick="return check_adenda();">Generar Adenda</button>
                                </div>
                            </form>
                        </div>
                    </div><!-- /block -->
                    <?php } ?>

                </div><!-- ./row -->
            </div><!-- ./content span3 -->

            <div class="span6 div_Contrato">
                <div class="row-fluid">
                    <div class="block">
                        <div class="navbar navbar-inner block-header">
                            <div class="muted pull-left">Contrato</div>
                        </div>
                        <div class="block-content collapse in" style="text-align:justify;">
                            <?=$contract->get_html(); ?>
                        </div>
                    </div><!-- /block -->
                </div><!-- ./row -->
            </div><!-- ./content span -->

            <!-- anexo -->
            <?php if($attachment) { ?>
            <div class="span10 div_Anexo" style="display:none;">
                <div class="row-fluid">
                    <div class="block">
                        <div class="navbar navbar-inner block-header">
                            <div class="muted pull-left">Anexo subido por el proveedor</div>
                        </div>
                        <div class="block-content collapse in">
                            <div style="text-align:center;">
                                <?php if($contract->get("anexo")!=="") { ?>
                                    <?php if(file_is_valid($contract->get("pathContratos").$contract->get("anexo"))) { ?>
                                        <object data="<?=substr($contract->get("pathContratos").$contract->get("anexo"), strpos($contract->get("pathContratos"), "files"));?>" type="application/pdf" width="90%" height="800">
                                            <div class="alert alert-error">
                                                <h4>Hubo un problema al cargar el archivo anexo.</h4>
                                            </div>
                                        </object>
                                        <div style="margin-top:20px;">
                                            <a href="#deleteAttach" data-toggle="modal" class="btn btn-danger"><i class="icon-remove icon-white"></i> Eliminar Anexo</a>
                                            <div id="deleteAttach" class="modal hide" style="text-align:left;">
                                                <div class="modal-header">
                                                    <button data-dismiss="modal" class="close" type="button">&times;</button>
                                                    <h3>Eliminar Anexo</h3>
                                                </div>
                                                <div class="modal-body">
                                                    <p>Está seguro que desea eliminar el anexo subido por el proveedor?</p>
                                                </div>
                                                <div class="modal-footer">
                                                    <a class="btn btn-primary" href="mod/contracts.admin.php?cmd=delattach&id=<?=$id;?>">Confirmar</a>
                                                    <a data-dismiss="modal" class="btn" href="#">Cancelar</a>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                <?php } else { ?>
                                    <div class="alert alert-warning">
                                        <h4>El proveedor no ha subido ningún anexo para este contrato.</h4>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div><!-- /block -->
                </div><!-- ./row -->
            </div><!-- ./content span3 -->
            <?php } ?>

            <!-- adendas -->
            <?php if(count($adendas)>0) { ?>
            <div class="span4 div_Adendas" style="display:none;">
                <div class="row-fluid">
                    <div class="block">
                        <div class="navbar navbar-inner block-header">
                            <div class="muted pull-left">Adendas Generadas</div>
                        </div>
                        <div class="block-content collapse in">
                            <table cellpadding="0" cellspacing="0" class="table table-striped table-bordered" id="results">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Agregada</th>
                                        <th>Firmada</th>
                                        <th>Estatus</th>
                                        <th style="width:90px;">&nbsp;</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($adendas as $a) { ?>
                                        <tr class="row_Info" >
                                            <td><?=$a->get_id();?></td>
                                            <td><?=$a->get("fechaCreado");?></td>
                                            <td><?=$a->get("firmaFecha");?></td>
                                            <td><span class="label label-<?=$a->get("contratoStatus");?>"><?=$a->get("contratoStatus");?></span></td>
                                            <td style="width:90px;">
                                                <a href="#" class="btn" title="Ver Adenda" onclick="adenda_display(<?=$a->get_id();?>);"><i class="icon-eye-open"></i></a>
                                                <a href="#adendaDelete<?=$a->get_id();?>" data-toggle="modal" class="btn btn-danger" title="Eliminar Adenda" onclick="adenda_display(<?=$a->get_id();?>);"><i class="icon-remove icon-white"></i></a>
                                                <div id="adendaDelete<?=$a->get_id();?>" class="modal hide">
                                                    <div class="modal-header">
                                                        <button data-dismiss="modal" class="close" type="button">&times;</button>
                                                        <h3>Eliminar Adenda</h3>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>Está seguro que desea eliminar esta adenda?</p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <a class="btn btn-primary" href="mod/contracts.admin.php?cmd=delad&id=<?=$id;?>&aId=<?=$a->get_id();?>">Confirmar</a>
                                                        <a data-dismiss="modal" class="btn" href="#">Cancelar</a>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div><!-- /block -->
                </div><!-- ./row -->
            </div><!-- ./content span3 -->

            <div class="span6 div_Adendas" style="display:none;">
                <div class="row-fluid">
                    <div class="block">
                        <div class="navbar navbar-inner block-header">
                            <div class="muted pull-left">Adenda</div>
                        </div>
                        <div class="block-content collapse in">

                            <?php for($i=0; $i<count($adendas); $i++) { $a = $adendas[$i]; ?>
                                <div class="div_adenda" id="div_adenda_<?=$a->get_id();?>" style="<?=($i==0) ? 'display:block;' : 'display:none;';?>">
                                    <div id="adenda_<?=$a->get_id();?>"><?=$a->get_html();?></div>
                                </div>
                            <?php } ?>

                        </div>
                    </div><!-- /block -->
                </div><!-- ./row -->
            </div><!-- ./content span3 -->
            <?php } ?>

        </div>
        <!-- /row-fluid new id -->

    <?php } ?>

    <hr>
    <footer>
        <p> <?=SITE_FOOTER_COPY;?></p>
    </footer>
</div><!--/.fluid-container-->

<!-- extra js -->
<link rel="stylesheet" href="vendors/datepicker.css" media="screen">
<script type="text/javascript" src="vendors/bootstrap-datepicker.js"></script>
<script>

    <?php if($id<CONTRACTS_NEW_ID) { ?>

        function show_hide_div(cat) {
            $(".div_Contrato").hide();
            $(".div_Anexo").hide();
            $(".div_Carta").hide();
            $("#button_Contrato").removeClass('btn-primary');
            $("#button_Anexo").removeClass('btn-primary');
            $("#button_Carta").removeClass('btn-primary');

            $("#button_"+cat).addClass('btn-primary')
            $(".div_"+cat).show();
        }

    <?php } else { ?>

        $(document).ready(function() {
            $(".datepicker").datepicker();
        });

        function show_hide_div(cat) {
            $(".div_Contrato").hide();
            $(".div_Anexo").hide();
            $(".div_Adendas").hide();
            $("#button_Contrato").removeClass('btn-primary');
            $("#button_Anexo").removeClass('btn-primary');
            $("#button_Adendas").removeClass('btn-primary');

            $("#button_"+cat).addClass('btn-primary')
            $(".div_"+cat).show();
        }

        function activate_adenda() {
            var fecha_ini = $("#Proyecto_Fecha_Inicio").val();
            var fecha_fin = $("#Proyecto_Fecha_Fin").val();
            var monto_pag = $("#Monto_de_Pago").val();
            if(fecha_ini!="" && fecha_fin !="" && monto_pag!="") {
                $("#button_adenda").show();
            } else {
                $("#button_adenda").hide();
            }
        }

        function check_adenda() {
            var plazo = $("#adenda_plazo").val();
            var pago = $("#adenda_pago").val();
            var calculo = $("#adenda_calculo").val();
            if( (plazo=="" || pago=="" || calculo=="" ) ) {
                alert("Es necesario modificar toda la información de los servicios para generar la adenda.");
                return false;
            }
            return true;
        }

        function adenda_display(id) {
            $(".div_adenda").hide();
            $("#div_adenda_"+id).show();
        }

    <?php } ?>

</script>

<?php include("inc.footer.php"); ?>