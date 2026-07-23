<?php

# include configuration file
include_once ("includes/inc.init.php");

# vars & filters
$vendorId = (int)session_get_data("userId");

# queries
$record = get_vendor($vendorId);
$allow_update_info = vendor_allow_edit_info($vendorId);

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
                                <h2 style="color:#1b54a3;">Datos Fiscales</h2>
                            </div>
                        </div>
                        <!-- breadcrumb -->
                        <div class="navbar">
                            <div class="navbar-inner">
                                <ul class="breadcrumb">
                                    <li><a href="index.php">Inicio</a> <span class="divider">/</span></li>
                                    <li class="active">Datos Fiscales</li>
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
                                <input type="hidden" name="cmd" value="update_info_invoice">
                                
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
                                            <label class="control-label">Nombre / Razón Social <span class="required">*</span></label>
                                            <div class="controls">
                                                <input type="text" name="razonSocial" class="span10 m-wrap" value="<?=$record['razonSocial'];?>" />
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">RFC / NIF <span class="required">*</span></label>
                                            <div class="controls">
                                                <input type="text" name="rfc" class="span10 m-wrap" value="<?=$record['rfc'];?>" />
                                            </div>
                                        </div>
                                        <?php if($record['extranjero']==0) { ?>
                                            <div class="control-group">
                                                <label class="control-label"><span <?=((int)$record['repseReq']==0) ? 'class="required"' : '' ;?>>REPSE</span> <span class="required">*</span></label>
                                                <div class="controls">
                                                    <select name="repseReq" id="repseReq" class="span5 m-wrap" onchange="repseToggle(this.value);">
                                                        <option value="-1" <?=((int)$record['repseReq']==-1) ? 'selected' : '' ;?>>NO necesito estar en el REPSE</option>
                                                        <option value="1" <?=((int)$record['repseReq']==1) ? 'selected' : '' ;?>>SI necesito estar en el REPSE</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div id="repseNumero" class="control-group" <?=((int)$record['repseReq']==1) ? '' : 'style="display:none;"' ;?>>
                                                <label class="control-label">Número de REPSE</label>
                                                <div class="controls">
                                                    <input type="text" name="repseNumero" class="span10 m-wrap" value="<?=$record['repseNumero'];?>" />
                                                </div>
                                            </div>
                                            <div id="repseAviso" class="control-group" <?=((int)$record['repseReq']==1) ? '' : 'style="display:none;"' ;?>>
                                                <label class="control-label">Número de Aviso de Inscripción en el REPSE</label>
                                                <div class="controls">
                                                    <input type="text" name="repseAviso" class="span10 m-wrap" value="<?=$record['repseAviso'];?>" />
                                                </div>
                                            </div>
                                        <?php } ?>
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

            function repseToggle(val) {
                var value = parseInt(val);
                if(value==0) {
                    $("#repseNumero").hide();
                    $("#repseAviso").hide();
                }
                if(value==-1) {
                    $("#repseNumero").hide();
                    $("#repseAviso").hide();
                }
                if(value==1) {
                    $("#repseNumero").show();
                    $("#repseAviso").show();
                }
            }

        </script>

<?php include("inc.footer.php"); ?>