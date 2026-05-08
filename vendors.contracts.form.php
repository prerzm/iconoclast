<?php

# include configuration file
include_once ("includes/inc.init.php");

# vars
$contract_vendor_id = (int)session_get_data("contract_id");
$contract = new ContractsAdendas($contract_vendor_id);

# update info in session
if($contract->get_id()==0) {
    set_alert("error", "El contrato seleccionado no existe");
    redirect("vendors.contracts.php");
}

$fields = $contract->get_fields();

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
                                <h2 style="color:#1b54a3;">Información</h2>
                            </div>
                        </div>
                        <!-- breadcrumb -->
                        <div class="navbar">
                            <div class="navbar-inner">
                                <ul class="breadcrumb">
                                    <li><a href="index.php">Inicio</a> <span class="divider">/</span></li>
                                    <li class="active">Llenar información</li>
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
                                <div class="muted pull-left">información</div>
                            </div>
                            <div class="block-content collapse in">

                                <!-- add-form-->
                                <form id="form_add" method="post" action="mod/vendors.contracts.php" enctype="multipart/form-data">
                                    <input type="hidden" name="cmd" value="fill">
                                
                                    <fieldset>
                                        <div class="alert alert-error hide">
                                            <button class="close" data-dismiss="alert"></button>
                                            Hubo un problema. Favor de revisar la información.
                                        </div>
                                        <div class="alert alert-success hide">
                                            <button class="close" data-dismiss="alert"></button>
                                            La información es válida!
                                        </div>

                                        <?php
                                        if(is_array($fields)) {
                                            foreach($fields as $f) {
                                                if($f['type']=="vendor") { ?>
                                                    <div class="control-group">
                                                        <label class="control-label"><?=$f['text'];?><?=(!$f['req']) ? ' (Deje en blanco si no aplica)': '';?></label>
                                                        <div class="controls">
                                                            <input type="text" name="<?=$f['field'];?>" class="span10 m-wrap" value="<?=$f['value'];?>" />
                                                        </div>
                                                    </div>
                                                <?php 
                                                }
                                            }
                                        }
                                        ?>

                                        <?php if($contract->get("tipo")=="Contrato" && $contract->get("tipo")!="talento") { ?>
                                            <div class="control-group">
                                                <label class="control-label">Anexar cotización del trabajo para la filmación (formato PDF)</label>
                                                <div class="controls">
                                                    <input type="file" name="anexo" class="span10 m-wrap"/><br>
                                                </div>
                                            </div>
                                        <?php } ?>

                                        <div class="control-group">
                                            <label class="control-label">&nbsp;</label>
                                            <div class="controls">
                                                <button type="submit" class="btn btn-primary"><i class="icon-pencil icon-white"></i> Continuar</button>
                                                <button type="reset" class="btn btn-inverse" onclick="window.location='vendors.contracts.php';"><i class="icon-arrow-left icon-white"></i> Cancelar</button>
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
        <link rel="stylesheet" href="vendors/datepicker.css" media="screen">
        <script type="text/javascript" src="vendors/bootstrap-datepicker.js"></script>
        <script type="text/javascript" src="vendors/jquery-validation/dist/jquery.validate.min.js"></script>
        <script>

            $(document).ready(function() {

                $('#form_add').validate({
                    errorClass: 'help-inline',

                    <?php if(is_array($fields)) { ?>
                    rules: {
		                <?php foreach($fields as $f) { ?>
			                <?php if($f['req']) { ?>
				                <?=$f['field'];?>: { required: true },
			                <?php } ?>
		                <?php } ?>
		            },
                    <?php } ?>

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