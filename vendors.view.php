<?php

# include configuration file
include_once ("includes/inc.init.php");

# vars & filters
$vendorId = (int)aget('id');

# queries
$record = get_vendor($vendorId);
$contracts = get_contracts_vendor($vendorId);

$csf_valid = vendor_verify_doc_date($record['constancia_fecha']);
$oc_valid = vendor_verify_doc_date($record['opinionCumplimiento_fecha']);
$dom_valid = vendor_verify_doc_date($record['residencia_fecha']);
$repse_valid = vendor_verify_repse_date($record, $contracts);

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
                                <h2 style="color:#1b54a3;"><?=($record['director']==1) ? 'Director' : 'Proveedor';?> - <?=$record['razonSocial'];?></h2>
                            </div>
                        </div>
                        <!-- breadcrumb -->
                        <div class="navbar">
                            <div class="navbar-inner">
                                <ul class="breadcrumb">
                                    <i class="icon-chevron-left hide-sidebar"><a href="#" title="Hide Sidebar" rel="tooltip">&nbsp;</a></i>
                                    <i class="icon-chevron-right show-sidebar" style="display:none;"><a href="#" title="Show Sidebar" rel="tooltip">&nbsp;</a></i>
                                    <li><a href="index.php">Inicio</a> <span class="divider">/</span></li>
                                    <li><a href="vendors.php">Proveedores</a> <span class="divider">/</span></li>
                                    <li class="active">Editar</li>
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
                                <div class="muted pull-left"><?=($record['director']==1) ? 'Director' : 'Proveedor';?></div>
                            </div>
                            <div class="block-content collapse in">

                                <table class="table">
                                    <tr>
                                        <th style="width:30%;">
                                            Datos
                                            <?php if((bool)$record['extranjero']) { ?><span class="label label-Pendiente" style="font-size:14px;">Proveedor extranjero</span><?php } ?>
                                            <?php if((bool)$record['director']) { ?><span class="label label-Pendiente" style="font-size:14px;">Director</span><?php } ?>
                                        </th>
                                        <td>
                                            <strong>Razón Social</strong>: <?=$record['razonSocial'];?><br>
                                            <strong>RFC</strong>: <?=$record['rfc'];?><br>
                                            <strong>Email</strong>: <?=$record['email'];?><br>
                                            <?php if((int)$record['repseReq']==1) { ?>
                                                <strong># Repse</strong>: <?=$record['repseNumero'];?><br>
                                                <strong># Aviso Repse</strong>: <?=$record['repseAviso'];?>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Datos Bancarios</th>
                                        <td>
                                            <strong>Banco</strong>: <?=$record['banco'];?><br>
                                            <strong>Cuenta</strong>: <?=$record['cuenta'];?><br>
                                            <?php if((bool)$record['extranjero']==false) { ?>
                                                <strong>CLABE</strong>: <?=$record['clabe'];?><br>
                                            <?php } ?>
                                            <strong>SWIFT</strong>: <?=$record['swift'];?><br>
                                            <strong>ABA</strong>: <?=$record['aba'];?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th style="border-top:none;">Documentos</th>
                                        <td style="border-top:none;">&nbsp;</td>
                                    </tr>
                                    <?php if($record['extranjero']==0) { ?>
                                        <?php if(get_vendor_type($record['rfc'])=="PM") { ?>
                                            <tr>
                                                <td>Acta Constitutiva</td>
                                                <td>
                                                    <?php if( file_is_valid($record['acta']) ) { ?>
                                                        <a href="file.download.php?f=<?=base64_encode($record['acta']);?>&t=o" title="Descargar"><img src="images/icon_file_valid.png" /></a>
                                                    <?php } else { ?>
                                                        <img src="images/icon_file_missing.png" /> <span class="label label-warning" style="margin-left:20px;">Pendiente</span>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                        <tr>
                                            <td>Constancia de Situación Fiscal</td>
                                            <td>
                                                <?php if( file_is_valid($record['constancia']) ) { ?>
                                                    <?php if($csf_valid) { ?>
                                                        <a href="file.download.php?f=<?=base64_encode($record['constancia']);?>&t=o" title="Descargar"><img src="images/icon_file_valid.png" /></a>
                                                    <?php } else { ?>
                                                        <a href="file.download.php?f=<?=base64_encode($record['constancia']);?>&t=o" title="Descargar"><img src="images/icon_file_invalid.png" /></a>
                                                        <span class="label label-important" style="margin-left:20px;">Vencida</span>
                                                    <?php } ?>
                                                <?php } else { ?>
                                                    <img src="images/icon_file_missing.png" /> <span class="label label-warning" style="margin-left:20px;">Pendiente</span>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Opinión del Cumplimiento (D32)</td>
                                            <td>
                                                <?php if( file_is_valid($record['opinionCumplimiento']) ) { ?>
                                                    <?php if($oc_valid) { ?>
                                                        <a href="file.download.php?f=<?=base64_encode($record['opinionCumplimiento']);?>&t=o" title="Descargar"><img src="images/icon_file_valid.png" /></a>
                                                    <?php } else { ?>
                                                        <a href="file.download.php?f=<?=base64_encode($record['opinionCumplimiento']);?>&t=o" title="Descargar"><img src="images/icon_file_invalid.png" /></a>
                                                        <span class="label label-important" style="margin-left:20px;">Vencida</span>
                                                    <?php } ?>
                                                <?php } else { ?>
                                                    <img src="images/icon_file_missing.png" /> <span class="label label-warning" style="margin-left:20px;">Pendiente</span>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Identificación Oficial</td>
                                            <td>
                                                <?php if( file_is_valid($record['identificacion']) ) { ?>
                                                    <a href="file.download.php?f=<?=base64_encode($record['identificacion']);?>&t=o" title="Descargar"><img src="images/icon_file_valid.png" /></a>
                                                <?php } else { ?>
                                                    <img src="images/icon_file_missing.png" /> <span class="label label-warning" style="margin-left:20px;">Pendiente</span>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Acuse de Inscripción REPSE</td>
                                            <td>
                                                <?php if($repse_valid) { ?>
                                                    <a href="contracts.admin.php"><img src="images/icon_file_valid.png" /></a>
                                                <?php } else { ?>
                                                    <img src="images/icon_file_invalid.png" />
                                                    <span class="label label-important" style="margin-left:20px;">Vencida o no firmada</span>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                    <tr>
                                        <td><?=((bool)$record['extranjero']) ? 'Comprobante de residencia fiscal' : 'Comprobante de domicilio';?></td>
                                        <td>
                                            <?php if( file_is_valid($record['residencia']) ) { ?>
                                                <?php if($dom_valid) { ?>
                                                    <a href="file.download.php?f=<?=base64_encode($record['residencia']);?>&t=o" title="Descargar"><img src="images/icon_file_valid.png" /></a>
                                                <?php } else { ?>
                                                    <a href="file.download.php?f=<?=base64_encode($record['residencia']);?>&t=o" title="Descargar"><img src="images/icon_file_invalid.png" /></a>
                                                    <span class="label label-important" style="margin-left:20px;">Vencida</span>
                                                <?php } ?>
                                            <?php } else { ?>
                                                <img src="images/icon_file_missing.png" /> <span class="label label-warning" style="margin-left:20px;">Pendiente</span>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Carátula Estado de Cuenta</td>
                                        <td>
                                            <?php if( file_is_valid($record['estadoDeCuenta']) ) { ?>
                                                <a href="file.download.php?f=<?=base64_encode($record['estadoDeCuenta']);?>&t=o" title="Descargar"><img src="images/icon_file_valid.png" /></a>
                                            <?php } else { ?>
                                                <img src="images/icon_file_missing.png" /> <span class="label label-warning" style="margin-left:20px;">Pendiente</span>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>&nbsp;</td>
                                        <th>&nbsp;</th>
                                    </tr>
                                    <tr>
                                        <th style="border-top:none;">Contratos</th>
                                        <td style="border-top:none;">&nbsp;</td>
                                    </tr>
                                    <?php if($contracts) { ?>
                                        <tr>
                                            <td colspan="2">
                                                <table>
                                                    <tr>
                                                        <th style="border-top:none;">Proyecto</th>
                                                        <th style="border-top:none;">Contrato</th>
                                                        <th style="border-top:none;">Anexo</th>
                                                        <th style="border-top:none;">Carta NDA</th>
                                                        <th style="border-top:none;">Estatus</th>
                                                    </tr>
                                                    <?php for($i=0; $i<count($contracts); $i++) { ?>
                                                        <tr>
                                                            <td><?=$contracts[$i]['titulo'];?></td>
                                                            <td>
                                                                <?php if( file_is_valid($contracts[$i]['contrato']) ) { ?>
                                                                    <a href="file.download.php?f=<?=base64_encode($contracts[$i]['contrato']);?>&t=o" title="Descargar contrato"><img src="images/icon_file_valid.png" /></a>
                                                                <?php } ?>
                                                            </td>
                                                            <td>
                                                                <?php if( file_is_valid($contracts[$i]['anexo']) ) { ?>
                                                                    <a href="file.download.php?f=<?=base64_encode($contracts[$i]['anexo']);?>&t=o" title="Descargar anexo"><img src="images/icon_file_valid.png" /></a>
                                                                <?php } ?>
                                                            </td>
                                                            <td>
                                                                <?php if( file_is_valid($contracts[$i]['carta']) ) { ?>
                                                                    <a href="file.download.php?f=<?=base64_encode($contracts[$i]['carta']);?>&t=o" title="Descargar NDA"><img src="images/icon_file_valid.png" /></a>
                                                                <?php } ?>
                                                            </td>
                                                            <td><span class="label label-<?=$contracts[$i]['contratoStatus'];?>"><?=$contracts[$i]['contratoStatus'];?></span></td>
                                                        </tr>
                                                    <?php } ?>
                                                </table>
                                            </td>
                                        </tr>
                                    <?php } else { ?>
                                        <tr>
                                            <th>Este proveedor no tiene contratos</th>
                                            <td>&nbsp;</td>
                                        </tr>
                                    <?php } ?>

                                </table>

                                <div class="control-group">
                                    <label class="control-label">&nbsp;</label>
                                    <div class="controls">
                                        <a href="vendors.php" class="btn btn-inverse"><i class="icon-arrow-left icon-white"></i> Regresar</a>
                                        <?php if($global_perms['EDIT']) { ?>
                                            <a href="vendors.edit.php?id=<?=$vendorId;?>" class="btn btn-primary"><i class="icon-edit icon-white"></i> Editar</a>
                                        <?php } ?>
                                        <?php if($global_perms['DELETE']) { ?>
                                            <a href="#myAlert" data-toggle="modal" class="btn btn-danger"><i class="icon-remove icon-white"></i> Eliminar</a>
                                        <?php } ?>
                                        <div id="myAlert" class="modal hide">
                                            <div class="modal-header">
                                                <button data-dismiss="modal" class="close" type="button">&times;</button>
                                                <h3>Eliminar</h3>
                                            </div>
                                            <div class="modal-body">
                                                <p>Está seguro que desea eliminar este registro?</p>
                                            </div>
                                            <div class="modal-footer">
                                                <a class="btn btn-primary" href="mod/vendors.php?cmd=del&id=<?=$vendorId;?>">Confirmar</a>
                                                <a data-dismiss="modal" class="btn" href="#">Cancelar</a>
                                            </div>
                                        </div>
                                        <?php if((int)session_get_data("roleId")==1 && (int)session_get_data("userId")==1 && $vendorId!=1) { ?>
                                            <?php if($record['tmp']=='') { ?>
                                                <a href="#myAlertTmpPswd" data-toggle="modal" class="btn btn-info"><i class="icon-refresh icon-white"></i> Establecer Contraseña Temporal</a>
                                                <div id="myAlertTmpPswd" class="modal hide">
                                                    <div class="modal-header">
                                                        <button data-dismiss="modal" class="close" type="button">&times;</button>
                                                        <h3>Establecer Contraseña Temporal</h3>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>Está seguro que desea cambiar la contraseña a este proveedor?</p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <a class="btn btn-primary" href="mod/vendors.php?cmd=tmpset&id=<?=$vendorId;?>">Confirmar</a>
                                                        <a data-dismiss="modal" class="btn" href="#">Cancelar</a>
                                                    </div>
                                                </div>
                                            <?php } else { ?>
                                                <a href="#myAlertTmpPswd" data-toggle="modal" class="btn btn-info"><i class="icon-refresh icon-white"></i> Regresar Contraseña</a>
                                                <div id="myAlertTmpPswd" class="modal hide">
                                                    <div class="modal-header">
                                                        <button data-dismiss="modal" class="close" type="button">&times;</button>
                                                        <h3>Regresar Contraseña</h3>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>Está seguro que desea regresar a la contraseña original a este proveedor?</p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <a class="btn btn-primary" href="mod/vendors.php?cmd=tmpunset&id=<?=$vendorId;?>">Confirmar</a>
                                                        <a data-dismiss="modal" class="btn" href="#">Cancelar</a>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        <?php } ?>
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