<?php

# include configuration file
include_once ("includes/inc.init.php");

# queries
$results = sql_select("SELECT * FROM ".TABLE_VENDORS." WHERE deleted = 0");
$banks = get_banks();

?>
<?php include("inc.header.main.php"); ?>

        <div class="container-fluid">
            
            <!-- row top -->
            <div class="row-fluid">
                <!-- sidebar -->
                <div class="span2 <?=(!$global_perms['ADD']) ? 'hide' : '';?>" id="sidebar">
                    <div class="row-fluid">
                        <div class="block">
                            <div class="navbar navbar-inner block-header">
                                <div class="muted pull-left">Agregar</div>
                            </div>
                            <div class="block-content collapse in">

                                <?php if($global_perms['ADD']) { ?>
                                <!-- add-form-->
                                <form id="form_add" method="post" action="mod/vendors.php">
                                    <input type="hidden" name="cmd" value="add">
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
                                                <input type="text" name="razonSocial" data-required="1" class="span10 m-wrap"/>
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">RFC</label>
                                            <div class="controls">
                                                <input type="text" name="rfc" data-required="1" class="span10 m-wrap"/>
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">email</label>
                                            <div class="controls">
                                                <input type="text" name="email" class="span10 m-wrap"/>
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">Es Repse?</label>
                                            <div class="controls">
                                                <label for="repseReq1"><input type="radio" name="repseReq" id="repseReq1" value="1" onclick="$('.div_repse').show();" /> Si</label>
                                                <label for="repseReq0"><input type="radio" name="repseReq" id="repseReq0" value="-1" checked="checked" onclick="$('.div_repse').hide();" /> No</label>
                                            </div>
                                        </div>
                                        <div class="control-group div_repse" style="display:none;>
                                            <label class="control-label">Número Repse</label>
                                            <div class="controls">
                                                <input type="text" name="repseNumero" class="span10 m-wrap" />
                                            </div>
                                        </div>
                                        <div class="control-group div_repse" style="display:none;>
                                            <label class="control-label">Número de Aviso Repse</label>
                                            <div class="controls">
                                                <input type="text" name="repseAviso" class="span10 m-wrap" />
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">Banco</label>
                                            <div class="controls">
                                                <select class="span10 m-wrap" name="banco">
                                                    <?=form_select_options($banks, "bank", "bank");?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">Cuenta</label>
                                            <div class="controls">
                                                <input type="text" name="cuenta" class="span10 m-wrap"/>
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">CLABE</label>
                                            <div class="controls">
                                                <input type="text" name="clabe" class="span10 m-wrap"/>
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">SWIFT</label>
                                            <div class="controls">
                                                <input type="text" name="swift" class="span10 m-wrap"/>
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">ABA</label>
                                            <div class="controls">
                                                <input type="text" name="aba" class="span10 m-wrap"/>
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">Contraseña</label>
                                            <div class="controls">
                                                <input type="text" name="pswd" data-required="1" class="span10 m-wrap"/>
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">&nbsp;</label>
                                            <div class="controls">
                                                <label><input type="checkbox" name="extranjero" value="1"> &nbsp;El proveedor extranjero</label>
                                                <label><input type="checkbox" name="director" value="1"> &nbsp;El proveedor es director</label>
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">&nbsp;</label>
                                            <div class="controls">
                                                <button type="submit" class="btn btn-primary">Agregar</button>
                                                <button type="reset" class="btn">Limpiar</button>
                                            </div>
                                        </div>
                                    </fieldset>
                                </form>
                                <!-- ./add-form -->
                                <?php } ?>
                                
                            </div>
                        </div>
                    </div>
                </div>
                <!-- ./sidebar -->
                
                <!-- content span -->
                <div class="<?=($global_perms['ADD']) ? 'span10' : 'span12';?>" id="content">
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
                                    <li class="active">Proveedores</li>
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
                                <div class="muted pull-left">Resultados</div>
                            </div>
                            <div class="block-content collapse in">
                                <div class="span12">
                                    <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="results">
                                        <thead>
                                            <tr>
                                                <th>Nombre</th>
                                                <th>RFC/NIF</th>
                                                <th>Email</th>
                                                <th>Banco</th>
                                                <th>Cuenta</th>
                                                <th>CLABE</th>
                                                <th>SWIFT/ABA</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if($results) { ?>
                                                <?php for($i=0; $i<count($results); $i++) { ?>
                                                    <tr>
                                                        <td><a href="vendors.view.php?id=<?=$results[$i]['proveedorId'];?>"><?=$results[$i]['razonSocial'];?></a></td>
                                                        <td><?=$results[$i]['rfc'];?></td>
                                                        <td style="font-size:12px;"><?=$results[$i]['email'];?></td>
                                                        <td><?=$results[$i]['banco'];?></td>
                                                        <td><?=$results[$i]['cuenta'];?></td>
                                                        <td><?=$results[$i]['clabe'];?></td>
                                                        <td>
                                                            <strong>SWIFT</strong>: <?=($results[$i]['swift']!="") ? $results[$i]['swift']: "N/A";?><br>
                                                            <strong>ABA</strong>: <?=($results[$i]['aba']!="") ? $results[$i]['aba']: "N/A";?>
                                                        </td>
                                                    </tr>
                                                <?php } ?>
                                            <?php } ?>
                                        </tbody>
						            </table>
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

        <!-- extra js -->
        <link rel="stylesheet" href="vendors/datepicker.css" media="screen">
        <script type="text/javascript" src="vendors/bootstrap-datepicker.js"></script>
        <script type="text/javascript" src="vendors/jquery-validation/dist/jquery.validate.min.js"></script>
        <script type="text/javascript" src="vendors/datatables/js/jquery.dataTables.min.js"></script>
        <script type="text/javascript" src="assets/DT_bootstrap.js"></script>
        <script>

            $(document).ready(function() {

                $('#results').dataTable( {
                    "sDom": "<'row'<'span6'l><'span6'f>r>t<'row'<'span6'i><'span6'p>>",
                    "sPaginationType": "bootstrap",
                    "iDisplayLength": 50,
                } );

                $('#form_add').validate({
                    errorClass: 'help-inline',
                    rules: {
                        rzonSocial: {
                            minlength: 5,
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