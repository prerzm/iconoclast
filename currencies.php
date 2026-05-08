<?php

# include configuration file
include_once ("includes/inc.init.php");

# queries
$results = get_currencies("MXN");

?>
<?php include("inc.header.main.php"); ?>

        <div class="container-fluid">
            
            <!-- row top -->
            <div class="row-fluid">
                
                <!-- content span12 -->
                <div class="span12" id="content">
                    <div class="row-fluid">
                        <!-- alerts -->
                        <?php display_alerts(); ?>
                        <!-- ./alerts -->
                        <div class="block">
                            <div class="navbar navbar-inner block-header">
                                <h2 style="color:#1b54a3;">Tipo de Cambio</h2>
                            </div>
                        </div>
                    </div>
                    <!-- row -->
                    <div class="row-fluid">
                        <!-- block -->
                        <div class="block">
                            <div class="navbar navbar-inner block-header">
                                <div class="muted pull-left">Resultados</div>
                            </div>
                            <div class="block-content collapse in">
                                <div class="span12">

                                    <form name="update" method="post" action="mod/currencies.php">
                                    <input type="hidden" name="cmd" value="update">

                                        <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="results">
                                            <thead>
                                                <tr>
                                                    <th>Moneda</th>
                                                    <th>Tipo de Cambio</th>
                                                    <th>&nbsp;</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if($results) { ?>
                                                    <?php foreach($results as $code => $rate) { ?>
                                                        <tr>
                                                            <td><?=$code;?></td>
                                                            <td><input type="text" name="exchangeRate" data-required="1" class="span6 m-wrap" value="<?=$rate;?>" /></td>
                                                            <td><button type="submit" class="btn btn-secondary">Guardar</button></td>
                                                        </tr>
                                                    <?php } ?>
                                                <?php } ?>
                                            </tbody>
                                        </table>

                                    </form>

                                </div>
                            </div>
                        </div>
                        <!-- /block -->
                    </div>
                    <!-- ./row -->
                </div><!-- ./content span12 -->

            </div><!-- ./row top -->

            <hr>
            <footer>
                <p> <?=SITE_FOOTER_COPY;?></p>
            </footer>
        </div><!--/.fluid-container-->

        <!-- extra js -->
        <link href="vendors/datepicker.css" rel="stylesheet" media="screen">
        <script src="vendors/bootstrap-datepicker.js"></script>

        <script type="text/javascript" src="vendors/jquery-validation/dist/jquery.validate.min.js"></script>
        <script type="text/javascript" src="vendors/datatables/js/jquery.dataTables.min.js"></script>
        <script type="text/javascript" src="assets/DT_bootstrap.js"></script>
        <script>

            $(document).ready(function() {

                $('#form_add').validate({
                    errorClass: 'help-inline',
                    rules: {
                        name: {
                            minlength: 8,
                            required: true
                        },
                        email: {
                            required: true,
                            email: true
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

                $(".datepicker").datepicker();

            });

        </script>

<?php include("inc.footer.php"); ?>