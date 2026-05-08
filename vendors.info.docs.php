<?php

# include configuration file
include_once ("includes/inc.init.php");

# vars & filters
$vendorId = (int)session_get_data("userId");

# queries
$record = get_vendor($vendorId);
$allow_update_info = vendor_allow_edit_info($vendorId);

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
                                <h2 style="color:#1b54a3;">Documentos</h2>
                            </div>
                        </div>
                        <!-- breadcrumb -->
                        <div class="navbar">
                            <div class="navbar-inner">
                                <ul class="breadcrumb">
                                    <li><a href="index.php">Inicio</a> <span class="divider">/</span></li>
                                    <li class="active">Documentos</li>
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
                                <form id="form_add" method="post" action="mod/vendors.info.php" enctype="multipart/form-data">
                                <input type="hidden" name="cmd" value="update_info_docs">
                                
                                    <fieldset>
                                        <div class="alert alert-error hide">
                                            <button class="close" data-dismiss="alert"></button>
                                            Hubo un problema. Favor de revisar la información.
                                        </div>
                                        <div class="alert alert-success hide">
                                            <button class="close" data-dismiss="alert"></button>
                                            La información es válida!
                                        </div>
                                        <?php if($record['extranjero']==0) { ?>
                                            <?php if(get_vendor_type($record['rfc'])=="PM") { ?>
                                                <div class="filebox">
                                                    <div class="filebox_header">Acta Constitutiva</div>
                                                    <?php if( file_is_valid($record['acta']) ) { ?>
                                                        <div class="filebox_content"><a href="file.download.php?f=<?=base64_encode($record['acta']);?>&t=o" title="Descargar"><img src="images/icon_file_valid.png" /></a></div>
                                                        <div class="filebox_footer"><a href="mod/vendors.info.php?cmd=delacta" class="btn btn-small btn-danger" onclick="return confirm('Está seguro que desea eliminar este documento?');">Eliminar</a></div>
                                                    <?php } else { ?>
                                                        <div class="filebox_content"><img src="images/icon_file_missing.png" /></div>
                                                        <div class="filebox_footer"><input type="file" name="acta" /></div>
                                                    <?php } ?>
                                                </div>
                                            <?php } ?>
                                            <div class="filebox">
                                                <div class="filebox_header">Constancia de Situación Fiscal</div>
                                                <?php if(file_is_valid($record['constancia'])) { ?>
                                                    <?php if($csf_valid) { ?>
                                                        <div class="filebox_content"><a href="file.download.php?f=<?=base64_encode($record['constancia']);?>&t=o" title="Descargar"><img src="images/icon_file_valid.png" /></a></div>
                                                        <div class="filebox_footer"><a href="mod/vendors.info.php?cmd=delcon" class="btn btn-small btn-danger" onclick="return confirm('Está seguro que desea eliminar este documento?');">Eliminar</a></div>
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
                                                <div class="filebox_header">Opinión de Cumplimiento</div>
                                                <?php if(file_is_valid($record['opinionCumplimiento'])) { ?>
                                                    <?php if($oc_valid) { ?>
                                                        <div class="filebox_content"><a href="file.download.php?f=<?=base64_encode($record['opinionCumplimiento']);?>&t=o" title="Descargar"><img src="images/icon_file_valid.png" /></a></div>
                                                        <div class="filebox_footer"><a href="mod/vendors.info.php?cmd=delopn" class="btn btn-small btn-danger" onclick="return confirm('Está seguro que desea eliminar este documento?');">Eliminar</a></div>
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
                                                <div class="filebox_header">Identificación con Firma</div>
                                                <?php if( file_is_valid($record['identificacion']) ) { ?>
                                                    <div class="filebox_content"><a href="file.download.php?f=<?=base64_encode($record['identificacion']);?>&t=o" title="Descargar"><img src="images/icon_file_valid.png" /></a></div>
                                                    <div class="filebox_footer"><a href="mod/vendors.info.php?cmd=delid" class="btn btn-small btn-danger" onclick="return confirm('Está seguro que desea eliminar este documento?');">Eliminar</a></div>
                                                <?php } else { ?>
                                                    <div class="filebox_content"><img src="images/icon_file_missing.png" /></div>
                                                    <div class="filebox_footer"><input type="file" name="identificacion" /></div>
                                                <?php } ?>
                                            </div>
                                            <div class="filebox">
                                                <div class="filebox_header"><a href="file.download.php?f=<?=base64_encode(PATH_ROOT."files/files/FormatoBajoProtestaDeDecirVerdad.doc");?>" title="Descargar">Formato bajo protesta</a></div>
                                                <?php if(file_is_valid($record['repse'])) { ?>
                                                    <?php if($repse_valid) { ?>
                                                        <div class="filebox_content"><a href="file.download.php?f=<?=base64_encode($record['repse']);?>&t=o" title="Descargar"><img src="images/icon_file_valid.png" /></a></div>
                                                        <div class="filebox_footer"><a href="mod/vendors.info.php?cmd=delrep" class="btn btn-small btn-danger" onclick="return confirm('Está seguro que desea eliminar este documento?');">Eliminar</a></div>
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
                                            <div class="filebox_header"><?=((bool)$record['extranjero']) ? 'Comprobante de Residencia Fiscal' : 'Comprobante de Domicilio';?></div>
                                            <?php if(file_is_valid($record['residencia'])) { ?>
                                                <?php if($dom_valid) { ?>
                                                    <div class="filebox_content"><a href="file.download.php?f=<?=base64_encode($record['residencia']);?>&t=o" title="Descargar"><img src="images/icon_file_valid.png" /></a></div>
                                                    <div class="filebox_footer"><a href="mod/vendors.info.php?cmd=delres" class="btn btn-small btn-danger" onclick="return confirm('Está seguro que desea eliminar este documento?');">Eliminar</a></div>
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
                                            <?php if( file_is_valid($record['estadoDeCuenta']) ) { ?>
                                                <div class="filebox_content"><a href="file.download.php?f=<?=base64_encode($record['estadoDeCuenta']);?>&t=o" title="Descargar"><img src="images/icon_file_valid.png" /></a></div>
                                                <div class="filebox_footer"><a href="mod/vendors.info.php?cmd=deledo" class="btn btn-small btn-danger" onclick="return confirm('Está seguro que desea eliminar este documento?');">Eliminar</a></div>
                                            <?php } else { ?>
                                                <div class="filebox_content"><img src="images/icon_file_missing.png" /></div>
                                                <div class="filebox_footer"><input type="file" name="estadoDeCuenta" /></div>
                                            <?php } ?>
                                        </div>
                                        <div style="clear:both;"></div>
                                        <div class="control-group">
                                            <label class="control-label">&nbsp;</label>
                                            <div class="controls">
                                                <button type="submit" class="btn btn-primary"><i class="icon-pencil icon-white"></i> Guardar</button>
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