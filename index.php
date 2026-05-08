<?php

# include configuration file
include_once ("includes/inc.init.php");

?>
<?php include("inc.header.main.php"); ?>

        <div class="container-fluid">
            
            <!-- row top -->
            <div class="row-fluid">
                <!-- sidebar -->
                <div class="span3 hide" id="sidebar">
                    <div class="row-fluid">
                        <div class="block">
                            <div class="navbar navbar-inner block-header">
                                <div class="muted pull-left">Agregar</div>
                            </div>
                            <div class="block-content collapse in">

                            </div>
                        </div>
                    </div>
                </div>
                <!-- ./sidebar -->
                
                <!-- content span -->
                <div class="span12" id="content">
                    <div class="row-fluid">
                        <!-- alerts -->
                        <?php display_alerts(); ?>
                        <!-- ./alerts -->
                    </div>
                    <!-- row -->
                    <div class="row-fluid">
                    </div>
                    <!-- ./row -->
                </div><!-- ./content span9 -->

            </div><!-- ./row top -->

            <hr>
            <footer>
                <?php /* <p> <?=SITE_FOOTER_COPY;?></p> */ ?>
            </footer>
        </div><!--/.fluid-container-->

<?php include("inc.footer.php"); ?>