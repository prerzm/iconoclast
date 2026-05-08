<?php

# include configuration file
include_once ("includes/inc.init.php");
include_once ("includes/lib.numbers.php");

# vars
$contract_vendor_id = (int)session_get_data("contract_id");
$contract = new ContractsAdendas($contract_vendor_id);

if($contract->get_id()==0) {
    set_alert("error", "El contrato seleccionado no existe");
    redirect("vendors.contracts.php");
}

$contract_html = $contract->get_html();

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
                                <h2 style="color:#1b54a3;">Firma</h2>
                            </div>
                        </div>
                        <!-- breadcrumb -->
                        <div class="navbar">
                            <div class="navbar-inner">
                                <ul class="breadcrumb">
                                    <i class="icon-chevron-left hide-sidebar"><a href="#" title="Hide Sidebar" rel="tooltip">&nbsp;</a></i>
                                    <i class="icon-chevron-right show-sidebar" style="display:none;"><a href="#" title="Show Sidebar" rel="tooltip">&nbsp;</a></i>
                                    <li><a href="index.php">Inicio</a> <span class="divider">/</span></li>
                                    <li><a href="vendors.contracts.php">Contratos</a> <span class="divider">/</span></li>
                                    <li class="active">Firma</li>
                                </ul>
                            </div>
                        </div>
                        <!-- ./breadcrumb -->
                    </div>

                    <!-- row -->
                    <div class="row-fluid">
                        <div class="span12">
                            <!-- block -->
                            <div class="block">
                                <div class="navbar navbar-inner block-header">
                                    <div class="muted pull-left">Firmar</div>
                                </div>
                                <div class="block-content collapse in">

                                    <form id="form-sign" method="post" action="mod/vendors.contracts.php">
                                    <input type="hidden" name="cmd" value="sign">

                                        <div><?=$contract_html;?></div>

                                        <div class="alert alert-error" id="alertAccept" style="margin-top:10px;">
                                            <h4><input name="signed" type="checkbox" style="margin-bottom:7px;" value="1" onclick="$('#buttonSign').toggle();">&nbsp;&nbsp;Bajo protesta de decir verdad, declaro que toda la información aquí proporcionada es correcta y verídica al momento de la firma de este contrato y acepto los términos y condiciones del mismo.</h4>
                                        </div>

                                        <div class="control-group">
                                            <label class="control-label">&nbsp;</label>
                                            <div class="controls">
                                                <button type="submit" id="buttonSign" class="btn btn-primary" style="display:none;"><i class="icon-edit icon-white"></i> Firmar y guardar el presente Contrato</button>
                                                <a href="#" class="btn btn-inverse" onclick="history.back();"><i class="icon-arrow-left icon-white"></i> Regresar</a>
                                            </div>
                                        </div>

                                    </form>
                                    
                                </div>
                            </div>
                            <!-- /block -->
                        </div>
                        <!-- ./span -->

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