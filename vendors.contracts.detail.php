<?php

# include configuration file
include_once ("includes/inc.init.php");

# vars
$contract_vendor_id = (int)aget("id");

if($contract_vendor_id<CONTRACTS_NEW_ID) {
    $contract = new ContractOld($contract_vendor_id);
} else {
    $contract = new ContractsAdendas($contract_vendor_id);
}

# update info in session
if($contract->get_id()==0) {
    set_alert("error", "El contrato seleccionado no existe");
    redirect("vendors.contracts.php");
}

?>
<?php include("inc.header.main.php"); ?>

    <div class="container-fluid">
        
        <?php if($contract_vendor_id<CONTRACTS_NEW_ID) { ?>

            <!-- row top -->
            <div class="row-fluid">
                
                <div class="span12" id="content">

                    <div class="row-fluid">
                        <div class="block">
                            <div class="navbar navbar-inner block-header">
                                <h2 style="color:#1b54a3;">Contrato - <?=$contract->get("titulo");?></h2>
                            </div>
                        </div>
                        <!-- breadcrumb -->
                        <div class="navbar">
                            <div class="navbar-inner">
                                <ul class="breadcrumb">
                                    <i class="icon-chevron-right show-sidebar" style="display:none;"><a href="#" title="Show Sidebar" rel="tooltip">&nbsp;</a></i>
                                    <li><a href="index.php">Inicio</a> <span class="divider">/</span></li>
                                    <li><a href="vendors.contracts.php">Contratos</a> <span class="divider">/</span></li>
                                    <li class="active">Contrato</li>
                                </ul>
                            </div>
                        </div>
                        <!-- ./breadcrumb -->
                    </div>
                    
                </div><!-- ./content span9 -->

            </div><!-- ./row top -->

            <!-- row contrato -->
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
                                <?php if(file_is_valid($contract->get("anexo"))) { ?><div style="margin-bottom:10px;"><button type="button" id="button_Anexo" class="btn btn-large btn-block" onclick="show_hide_div('Anexo');">Anexo</button></div><?php } ?>
                                <?php if(file_is_valid($contract->get("carta"))) { ?><div style="margin-bottom:10px;"><button type="button" id="button_Carta" class="btn btn-large btn-block" onclick="show_hide_div('Carta');">Carta NDA</button></div><?php } ?>
                            </div>
                        </div><!-- /block -->
                    </div><!-- ./row -->
                </div><!-- ./content span -->

                <!-- contrato -->
                <div class="span10 div_Contrato">
                    <div class="row-fluid">
                        <div class="block">
                            <div class="navbar navbar-inner block-header">
                                <div class="muted pull-left">Contrato</div>
                            </div>
                            <div class="block-content collapse in">
                                <?=$contract->get_html("contrato"); ?>
                            </div>
                        </div><!-- /block -->
                    </div><!-- ./row -->
                </div><!-- ./content span -->

                <!-- anexo -->
                <?php if(file_is_valid($contract->get("anexo"))) { ?>
                    <div class="span10 div_Anexo" style="display:none;">
                        <div class="row-fluid">
                            <div class="block">
                                <div class="navbar navbar-inner block-header">
                                    <div class="muted pull-left">Anexo subido por el proveedor</div>
                                </div>
                                <div class="block-content collapse in">
                                    <?=$contract->get_html("anexo"); ?>
                                </div>
                            </div><!-- /block -->
                        </div><!-- ./row -->
                    </div><!-- ./content span3 -->
                <?php } ?>
                <!-- /anexo -->

                <!-- carta -->
                <?php if(file_is_valid($contract->get("carta"))) { ?>
                    <div class="span10 div_Carta" style="display:none;">
                        <div class="row-fluid">
                            <div class="block">
                                <div class="navbar navbar-inner block-header">
                                    <div class="muted pull-left">Carta NDA</div>
                                </div>
                                <div class="block-content collapse in">
                                    <?=$contract->get_html("carta"); ?>
                                </div>
                            </div><!-- /block -->
                        </div><!-- ./row -->
                    </div><!-- ./content span3 -->
                <?php } ?>
                <!-- /carta -->

            </div>


        <?php } else { ?>

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
                        <div class="block">
                            <div class="navbar navbar-inner block-header">
                                <h2 style="color:#1b54a3;"><?=$contract->get("nombre");?> - <?=$contract->get("titulo");?></h2>
                            </div>
                        </div>
                        <!-- breadcrumb -->
                        <div class="navbar">
                            <div class="navbar-inner">
                                <ul class="breadcrumb">
                                    <li><a href="index.php">Inicio</a> <span class="divider">/</span></li>
                                    <li><a href="vendors.contracts.php">Mis Contratos</a> <span class="divider">/</span></li>
                                    <li class="active"><?=$contract->get("nombre");?></li>
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
                                <div class="muted pull-left">Contrato</div>
                            </div>
                            <div class="block-content collapse in" style="text-align:justify;">
                                <?=$contract->get_html(); ?>
                            </div>
                        </div>
                        <!-- /block -->
                    </div>
                    <!-- ./row -->
                </div><!-- ./content span9 -->

            </div><!-- ./row top -->

        <?php } ?>

        <hr>
        <footer>
            <p> <?=SITE_FOOTER_COPY;?></p>
        </footer>
        
    </div><!--/.fluid-container-->

    <!-- extra js -->
    <script>
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
    </script>

<?php include("inc.footer.php"); ?>