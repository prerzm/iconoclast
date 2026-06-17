<?php

# include configuration file
include_once ("includes/inc.init.php");

# vars & filters
$vendorId = (int)aget('id');

# queries
$record = get_vendor($vendorId);
$banks = get_banks();
$acta = new FileHandler(PATH_VENDORS, basename($record['acta']));
$csf = new FileHandler(PATH_VENDORS, basename($record['constancia']));
$oc = new FileHandler(PATH_VENDORS, basename($record['opinionCumplimiento']));
$edo = new FileHandler(PATH_VENDORS, basename($record['estadoDeCuenta']));
$ide = new FileHandler(PATH_VENDORS, basename($record['identificacion']));
$dom = new FileHandler(PATH_VENDORS, basename($record['residencia']));
$repse = new FileHandler(PATH_VENDORS, basename($record['repse']));
$csf_valid = vendor_verify_doc_date($record['constancia_fecha']);
$oc_valid = vendor_verify_doc_date($record['opinionCumplimiento_fecha']);
$dom_valid = vendor_verify_doc_date($record['residencia_fecha']);
$repse_valid = vendor_verify_doc_date($record['repse_fecha'], 1095);

?>
<?php include("inc.header.main.php"); ?>

        <div class="container-fluid">
            
            <!-- row top -->
            <div class="row-fluid">
                <!-- sidebar -->
                <div class="span3 hide" id="sidebar">
                    <div class="row-fluid">
                    </div>
                </div>
                <!-- ./sidebar -->
                
                <!-- content span12 -->
                <div class="span12" id="content">
                    <div class="row-fluid">
                        <!-- alerts -->
                        <?php display_alerts(); ?>
                        <!-- ./alerts -->
                        <div class="block">
                            <div class="navbar navbar-inner block-header">
                                <h2 style="color:#1b54a3;">Proveedores</h2>
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
                                <div class="muted pull-left">Editar</div>
                            </div>
                            <div class="block-content collapse in">

                                <!-- add-form-->
                                <form id="form_add" method="post" action="mod/vendors.php" enctype="multipart/form-data">
                                <input type="hidden" name="cmd" value="update">
                                <input type="hidden" name="id" value="<?=$vendorId;?>">
                                    <fieldset>
                                        <div class="alert alert-error hide">
                                            <button class="close" data-dismiss="alert"></button>
                                            Hubo un problema. Favor de revisar la información.
                                        </div>
                                        <div class="alert alert-success hide">
                                            <button class="close" data-dismiss="alert"></button>
                                            La información es válida!
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">Nombre / Razón Social<span class="required">*</span></label>
                                            <div class="controls">
                                                <input type="text" name="razonSocial" data-required="1" class="span10 m-wrap" value="<?=$record['razonSocial'];?>" />
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">RFC / NIF</label>
                                            <div class="controls">
                                                <input type="text" name="rfc" data-required="1" class="span10 m-wrap" value="<?=$record['rfc'];?>" />
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">email</label>
                                            <div class="controls">
                                                <input type="text" name="email" class="span10 m-wrap" value="<?=$record['email'];?>" />
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">Es Repse?</label>
                                            <div class="controls">
                                                <label for="repseReq1"><input type="radio" name="repseReq" id="repseReq1" value="1" <?=((int)$record['repseReq']==1) ? 'checked="checked"': '';?> onclick="$('.div_repse').show();" /> Si</label>
                                                <label for="repseReq0"><input type="radio" name="repseReq" id="repseReq0" value="-1" <?=((int)$record['repseReq']==-1) ? 'checked="checked"': '';?> onclick="$('.div_repse').hide();" /> No</label>
                                            </div>
                                        </div>
                                        <div class="control-group div_repse" <?=((int)$record['repseReq']==-1) ? 'style="display:none;"' : '';?>>
                                            <label class="control-label">Número Repse</label>
                                            <div class="controls">
                                                <input type="text" name="repseNumero" class="span10 m-wrap" value="<?=$record['repseNumero'];?>" />
                                            </div>
                                        </div>
                                        <div class="control-group div_repse" <?=((int)$record['repseReq']==-1) ? 'style="display:none;"' : '';?>>
                                            <label class="control-label">Número de Aviso Repse</label>
                                            <div class="controls">
                                                <input type="text" name="repseAviso" class="span10 m-wrap" value="<?=$record['repseAviso'];?>" />
                                            </div>
                                        </div>
                                        <?php if($record['extranjero']==0) { ?>
                                            <div class="control-group">
                                                <label class="control-label">Banco</label>
                                                <div class="controls">
                                                    <select class="span10 m-wrap" name="banco">
                                                        <?=form_select_options($banks, "bank", "bank", $record['banco']);?>
                                                    </select>
                                                </div>
                                            </div>
                                        <?php } else { ?>
                                            <div class="control-group">
                                                <label class="control-label">Banco</label>
                                                <div class="controls">
                                                    <input type="text" name="banco" class="span10 m-wrap" value="<?=$record['banco'];?>" />
                                                </div>
                                            </div>
                                        <?php } ?>
                                        <div class="control-group">
                                            <label class="control-label">Cuenta</label>
                                            <div class="controls">
                                                <input type="text" name="cuenta" class="span10 m-wrap" value="<?=$record['cuenta'];?>" />
                                            </div>
                                        </div>
                                        <?php if($record['extranjero']==0) { ?>
                                        <div class="control-group">
                                            <label class="control-label">CLABE</label>
                                            <div class="controls">
                                                <input type="text" name="clabe" class="span10 m-wrap" value="<?=$record['clabe'];?>" />
                                            </div>
                                        </div>
                                        <?php } ?>
                                        <div class="control-group">
                                            <label class="control-label">SWIFT</label>
                                            <div class="controls">
                                                <input type="text" name="swift" class="span10 m-wrap" value="<?=$record['swift'];?>" />
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">ABA</label>
                                            <div class="controls">
                                                <input type="text" name="aba" class="span10 m-wrap" value="<?=$record['aba'];?>" />
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">Contraseña (Solo si desea cambiar)</label>
                                            <div class="controls">
                                                <input type="text" name="pswd" class="span10 m-wrap" />
                                            </div>
                                        </div>
                                        <?php if($record['extranjero']==0) { ?>
                                            <?php if(get_vendor_type($record['rfc'])=="PM") { ?>
                                                <div class="filebox">
                                                    <div class="filebox_header">Acta Constitutiva</div>
                                                    <?php if($acta->is_valid()) { ?>
                                                        <div class="filebox_content"><a href="file.download.php?f=<?=base64_encode(PATH_VENDORS.$record['acta']);?>&t=o" title="Descargar"><img src="images/icon_file_valid.png" /></a></div>
                                                        <div class="filebox_footer"><a href="mod/vendors.php?cmd=delfile&id=<?=$vendorId;?>&f=ac" class="btn btn-small btn-danger" onclick="return confirm('Está seguro que desea eliminar este documento?');">Eliminar</a></div>
                                                    <?php } else { ?>
                                                        <div class="filebox_content"><img src="images/icon_file_missing.png" /></div>
                                                        <div class="filebox_footer"><input type="file" name="acta" /></div>
                                                    <?php } ?>
                                                </div>
                                            <?php } ?>
                                            <div class="filebox">
                                                <div class="filebox_header">Constancia de Situación Fiscal</div>
                                                <?php if($csf->is_valid()) { ?>
                                                    <?php if($csf_valid) { ?>
                                                        <div class="filebox_content"><a href="file.download.php?f=<?=base64_encode(PATH_VENDORS.$record['constancia']);?>&t=o" title="Descargar"><img src="images/icon_file_valid.png" /></a></div>
                                                        <div class="filebox_footer"><a href="mod/vendors.php?cmd=delfile&id=<?=$vendorId;?>&f=csf" class="btn btn-small btn-danger" onclick="return confirm('Está seguro que desea eliminar este documento?');">Eliminar</a></div>
                                                    <?php } else { ?>
                                                        <div class="filebox_content"><img src="images/icon_file_invalid.png" /></div>
                                                        <div class="filebox_footer"><input type="file" name="constancia" /></div>
                                                    <?php } ?>
                                                <?php } else { ?>
                                                    <div class="filebox_content"><img src="images/icon_file_missing.png" /></div>
                                                    <div class="filebox_footer"><input type="file" name="constancia" /></div>
                                                <?php } ?>
                                            </div>
                                            <div class="filebox">
                                                <div class="filebox_header">Opinión del Cumplimiento (D32)</div>
                                                <?php if($oc->is_valid()) { ?>
                                                    <?php if($oc_valid) { ?>
                                                        <div class="filebox_content"><a href="file.download.php?f=<?=base64_encode(PATH_VENDORS.$record['opinionCumplimiento']);?>&t=o" title="Descargar"><img src="images/icon_file_valid.png" /></a></div>
                                                        <div class="filebox_footer"><a href="mod/vendors.php?cmd=delfile&id=<?=$vendorId;?>&f=oc" class="btn btn-small btn-danger" onclick="return confirm('Está seguro que desea eliminar este documento?');">Eliminar</a></div>
                                                    <?php } else { ?>
                                                        <div class="filebox_content"><img src="images/icon_file_invalid.png" /></div>
                                                        <div class="filebox_footer"><input type="file" name="opinionCumplimiento" /></div>
                                                    <?php } ?>
                                                <?php } else { ?>
                                                    <div class="filebox_content"><img src="images/icon_file_missing.png" /></div>
                                                    <div class="filebox_footer"><input type="file" name="opinionCumplimiento" /></div>
                                                <?php } ?>
                                            </div>
                                            <div class="filebox">
                                                <div class="filebox_header">Identificación Oficial</div>
                                                <?php if($ide->is_valid()) { ?>
                                                    <div class="filebox_content"><a href="file.download.php?f=<?=base64_encode(PATH_VENDORS.$record['identificacion']);?>&t=o" title="Descargar"><img src="images/icon_file_valid.png" /></a></div>
                                                    <div class="filebox_footer"><a href="mod/vendors.php?cmd=delfile&id=<?=$vendorId;?>&f=ide" class="btn btn-small btn-danger" onclick="return confirm('Está seguro que desea eliminar este documento?');">Eliminar</a></div>
                                                <?php } else { ?>
                                                    <div class="filebox_content"><img src="images/icon_file_missing.png" /></div>
                                                    <div class="filebox_footer"><input type="file" name="identificacion" /></div>
                                                <?php } ?>
                                            </div>
                                            <div class="filebox">
                                                <div class="filebox_header">REPSE o Carta</div>
                                                <?php if($repse->is_valid()) { ?>
                                                    <?php if($repse_valid) { ?>
                                                        <div class="filebox_content"><a href="file.download.php?f=<?=base64_encode(PATH_VENDORS.$record['repse']);?>&t=o" title="Descargar"><img src="images/icon_file_valid.png" /></a></div>
                                                        <div class="filebox_footer"><a href="mod/vendors.php?cmd=delfile&id=<?=$vendorId;?>&f=rep" class="btn btn-small btn-danger" onclick="return confirm('Está seguro que desea eliminar este documento?');">Eliminar</a></div>
                                                    <?php } else { ?>
                                                        <div class="filebox_content"><img src="images/icon_file_invalid.png" /></div>
                                                        <div class="filebox_footer"><input type="file" name="repse" /></div>
                                                    <?php } ?>
                                                <?php } else { ?>
                                                    <div class="filebox_content"><img src="images/icon_file_missing.png" /></div>
                                                    <div class="filebox_footer"><input type="file" name="repse" /></div>
                                                <?php } ?>
                                            </div>
                                        <?php } ?>
                                        <div class="filebox">
                                            <div class="filebox_header"><?=((bool)$record['extranjero']) ? 'Comprobante de residencia fiscal' : 'Comprobante de domicilio';?></div>
                                            <?php if($dom->is_valid()) { ?>
                                                <?php if($dom_valid) { ?>
                                                    <div class="filebox_content"><a href="file.download.php?f=<?=base64_encode(PATH_VENDORS.$record['residencia']);?>&t=o" title="Descargar"><img src="images/icon_file_valid.png" /></a></div>
                                                    <div class="filebox_footer"><a href="mod/vendors.php?cmd=delfile&id=<?=$vendorId;?>&f=dom" class="btn btn-small btn-danger" onclick="return confirm('Está seguro que desea eliminar este documento?');">Eliminar</a></div>
                                                <?php } else { ?>
                                                    <div class="filebox_content"><img src="images/icon_file_invalid.png" /></div>
                                                    <div class="filebox_footer"><input type="file" name="residencia" /></div>
                                                <?php } ?>
                                            <?php } else { ?>
                                                <div class="filebox_content"><img src="images/icon_file_missing.png" /></div>
                                                <div class="filebox_footer"><input type="file" name="residencia" /></div>
                                            <?php } ?>
                                        </div>
                                        <div class="filebox">
                                            <div class="filebox_header">Carátula Estado de Cuenta</div>
                                            <?php if($edo->is_valid()) { ?>
                                                <div class="filebox_content"><a href="file.download.php?f=<?=base64_encode(PATH_VENDORS.$record['estadoDeCuenta']);?>&t=o" title="Descargar"><img src="images/icon_file_valid.png" /></a></div>
                                                <div class="filebox_footer"><a href="mod/vendors.php?cmd=delfile&id=<?=$vendorId;?>&f=edo" class="btn btn-small btn-danger" onclick="return confirm('Está seguro que desea eliminar este documento?');">Eliminar</a></div>
                                            <?php } else { ?>
                                                <div class="filebox_content"><img src="images/icon_file_missing.png" /></div>
                                                <div class="filebox_footer"><input type="file" name="estadoDeCuenta" /></div>
                                            <?php } ?>
                                        </div>
                                        <div style="clear:both;"></div>
                                        <div class="control-group">
                                            <label class="control-label">&nbsp;</label>
                                            <div class="controls">
                                                <label><input type="checkbox" name="editar" value="1" <?=($record['editar']==1) ? 'checked' : '';?>> El proveedor puede editar sus datos</label>
                                                <label><input type="checkbox" name="extranjero" value="1" <?=($record['extranjero']==1) ? 'checked' : '';?>> El proveedor es extranjero</label>
                                                <label><input type="checkbox" name="director" value="1" <?=($record['director']==1) ? 'checked' : '';?>> El proveedor es director</label>
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">&nbsp;</label>
                                            <div class="controls">
                                                <button type="reset" class="btn btn-inverse" onclick="window.location='vendors.view.php?id=<?=$vendorId;?>';"><i class="icon-arrow-left icon-white"></i> Cancelar</button>
                                                <button type="submit" class="btn btn-primary"><i class="icon-pencil icon-white"></i> Guardar</button>
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
        <script>

            $(document).ready(function() {

                $('#form_add').validate({
                    errorClass: 'help-inline',
                    rules: {
                        permisoKey: {
                            minlength: 3,
                            required: true
                        },
                        name: {
                            minlength: 4,
                            required: true
                        },
                        archivos: {
                            minlength: 6,
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

            });

        </script>

<?php include("inc.footer.php"); ?>