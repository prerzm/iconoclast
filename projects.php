<?php

# include configuration file
include_once ("includes/inc.init.php");
include_once ("includes/lib.dates.php");
include_once ("includes/lib.numbers.php");

# queries
$results = get_projects_all(session_get_data("companyId"));
$directors = get_directors_all();

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
                                <form id="form_add" method="post" action="mod/projects.php" onsubmit="return check_cuenta();">
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
                                            <label class="control-label"># de Proyecto<span class="required">*</span></label>
                                            <div class="controls">
                                                <input type="text" name="clave" data-required="1" class="span10 m-wrap"/>
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">Nombre<span class="required">*</span></label>
                                            <div class="controls">
                                                <input type="text" name="titulo" data-required="1" class="span10 m-wrap"/>
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">Cliente</label>
                                            <div class="controls">
                                                <input type="text" name="cliente" data-required="1" class="span10 m-wrap"/>
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">Fecha de Inicio</label>
                                            <div class="controls">
                                                <input type="text" name="fechaInicio" data-required="1" class="span10 m-wrap datepicker"/>
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">Fecha de Fin</label>
                                            <div class="controls">
                                                <input type="text" name="fechaFin" data-required="1" class="span10 m-wrap datepicker"/>
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">Lugar</label>
                                            <div class="controls">
                                                <input type="text" name="lugar" data-required="1" class="span10 m-wrap"/>
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">Director</label>
                                            <div class="controls">
                                                <select class="span10 m-wrap" name="directorId">
                                                    <?=form_select_options($directors, "proveedorId", "razonSocial");?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">Productor</label>
                                            <div class="controls">
                                                <input type="text" name="productor" data-required="1" class="span10 m-wrap"/>
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">Productor en línea</label>
                                            <div class="controls">
                                                <input type="text" name="productorLinea" data-required="1" class="span10 m-wrap"/>
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
                                <h2 style="color:#1b54a3;">Proyectos</h2>
                            </div>
                        </div>
                        <!-- breadcrumb -->
                        <div class="navbar">
                            <div class="navbar-inner">
                                <ul class="breadcrumb">
                                    <i class="icon-chevron-left hide-sidebar"><a href="#" title="Hide Sidebar" rel="tooltip">&nbsp;</a></i>
                                    <i class="icon-chevron-right show-sidebar" style="display:none;"><a href="#" title="Show Sidebar" rel="tooltip">&nbsp;</a></i>
                                    <li><a href="index.php">Inicio</a> <span class="divider">/</span></li>
                                    <li class="active">Proyectos</li>
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
                                                <th>Clave</th>
                                                <th>Año</th>
                                                <th>Nombre</th>
                                                <th>Cliente</th>
                                                <th>Fechas</th>
                                                <th>Lugar</th>
                                                <th>Director</th>
                                                <th>Productor</th>
                                                <th>Productor en línea</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if($results) { ?>
                                                <?php for($i=0; $i<count($results); $i++) { ?>
                                                    <tr>
                                                        <td><?=$results[$i]['clave'];?></td>
                                                        <td><?=$results[$i]['ano'];?></td>
                                                        <td><a href="projects.view.php?id=<?=$results[$i]['proyectoId'];?>"><?=$results[$i]['titulo'];?></a></td>
                                                        <td><?=$results[$i]['cliente'];?></td>
                                                        <td><?=$results[$i]['fechaInicio']." - ".$results[$i]['fechaFin'];?></td>
                                                        <td><?=$results[$i]['lugar'];?></td>
                                                        <td><?=$results[$i]['director'];?></td>
                                                        <td><?=$results[$i]['productor'];?></td>
                                                        <td><?=$results[$i]['productorLinea'];?></td>
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

        <script type="text/javascript" src="vendors/autocomplete/js/jquery.autocomplete.js"></script>
        <link rel="stylesheet" href="vendors/autocomplete/css/styles.css" media="screen">
        <script>

            $(document).ready(function() {

                $('#results').dataTable( {
                    "sDom": "<'row'<'span6'l><'span6'f>r>t<'row'<'span6'i><'span6'p>>",
                    "sPaginationType": "bootstrap",
                    "iDisplayLength": 50,
                    "aaSorting": [[1, 'desc'], [0, 'desc']],
                } );

                $('#form_add').validate({
                    errorClass: 'help-inline',
                    rules: {
                        clave: {
                            minlength: 3,
                            required: true
                        },
                        titulo: {
                            minlength: 3,
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

                $(".datepicker").datepicker();

            });

        </script>

<?php include("inc.footer.php"); ?>