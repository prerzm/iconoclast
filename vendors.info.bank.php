<?php

# include configuration file
include_once ("includes/inc.init.php");

# vars & filters
$vendorId = (int)session_get_data("userId");

# queries
$record = get_vendor($vendorId);
$banks = get_banks();
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
                                <h2 style="color:#1b54a3;">Datos Bancarios</h2>
                            </div>
                        </div>
                        <!-- breadcrumb -->
                        <div class="navbar">
                            <div class="navbar-inner">
                                <ul class="breadcrumb">
                                    <li><a href="index.php">Inicio</a> <span class="divider">/</span></li>
                                    <li class="active">Datos Bancarios</li>
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
                                <input type="hidden" name="cmd" value="update_info_bank">
                                
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
                                            <div class="control-group">
                                                <label class="control-label">Banco</label>
                                                <div class="controls">
                                                    <select class="span10 m-wrap" name="banco">
                                                        <?php if($record['banco']=="") { ?>
                                                            <option value="" selected="selected"></option>
                                                        <?php } ?>
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
                                                <input type="text" name="cuenta" class="span10 m-wrap" value="<?=$record['cuenta'];?>" <?=($allow_update_info) ? '' : 'disabled';?> />
                                            </div>
                                        </div>
                                        <?php if($record['extranjero']==0) { ?>
                                        <div class="control-group">
                                            <label class="control-label">CLABE</label>
                                            <div class="controls">
                                                <input type="text" name="clabe" class="span10 m-wrap" value="<?=$record['clabe'];?>" <?=($allow_update_info) ? '' : 'disabled';?> />
                                            </div>
                                        </div>
                                        <?php } ?>
                                        <div class="control-group">
                                            <label class="control-label">SWIFT</label>
                                            <div class="controls">
                                                <input type="text" name="swift" class="span10 m-wrap" value="<?=$record['swift'];?>" <?=($allow_update_info) ? '' : 'disabled';?> />
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">ABA</label>
                                            <div class="controls">
                                                <input type="text" name="aba" class="span10 m-wrap" value="<?=$record['aba'];?>" <?=($allow_update_info) ? '' : 'disabled';?> />
                                            </div>
                                        </div>
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