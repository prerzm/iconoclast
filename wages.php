<?php

# include configuration file
include_once ("includes/inc.init.php");
include_once ("includes/lib.dates.php");
include_once ("includes/lib.numbers.php");

# vars
$companyId = session_get_data("companyId");
$projectId = (int)aget('pId');

# queries
$results = get_nominas($projectId);
$projects = get_projects_visible($companyId);

if($projects==false) {
    set_alert("error", "Es necesario que exista al menos 1 <a href=\"projects.php\">proyecto</a> para poder ver y agregar Archivos de Nómina");
    header("Location: projects.php");
    exit;
}

# years/projects array
$years = array();
foreach($projects as $p) {
    $years[(int)$p['ano']][] = $p;
}

# default year
$yearId = (isset($_GET['ano'])) ? aget('ano') : array_key_first($years);

?>
<?php include("inc.header.main.php"); ?>

        <div class="container-fluid">
            
            <!-- row top -->
            <div class="row-fluid">

                <!-- sidebar -->
                <div class="span3" id="sidebar">
                    <div class="row-fluid">

                        <?php if($global_perms['ADD']) { ?>
                        <!-- add-form-->
                        <div class="block">
                            <div class="navbar navbar-inner block-header">
                                <div class="muted pull-left">Agregar</div>
                            </div>
                            <div class="block-content collapse in">
                                <form id="form_add" method="post" action="mod/wages.php" enctype="multipart/form-data">
                                    <?php if($projects) { ?>
                                    <input type="hidden" name="cmd" value="load_file">
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
                                            <label class="control-label">Proyecto</label>
                                            <div class="controls">
                                                <select class="span10 m-wrap" name="ano" id="proy_add_ano" onchange="change_year(this.value);">
                                                    <?php foreach($years as $year => $values) { ?>
                                                        <option value="<?=$year;?>" <?=($yearId==$year) ? 'selected="selected"' : '';?>><?=($year==0) ? "Sin año" : $year;?></option>
                                                    <?php } ?>
                                                </select>
                                                <?php foreach($years as $year => $values) { ?>
                                                    <select class="span10 m-wrap proy_add_select" size="8" name="proyectoId" id="proy_add_proyectoId_<?=$year;?>" <?=($year==$yearId) ? '' : 'style="display:none;" disabled';?>>
                                                        <?php foreach($values as $p) { ?>
                                                            <option value="<?=$p['proyectoId'];?>" <?=($projectId==$p['proyectoId']) ? 'selected="selected"' : '';?>><?=$p['titulo'];?></option>
                                                        <?php } ?>
                                                    </select>
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">Archivo</label>
                                            <div class="controls">
                                            <input type="file" name="nomina">
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
                                    <?php } ?>
                                </form>
                            </div>
                        </div>
                        <!-- ./add-form -->
                        <?php } ?>

                        <!-- search -->
                        <div class="block">
                            <div class="navbar navbar-inner block-header">
                                <div class="muted pull-left">Buscar</div>
                            </div>
                            <div class="block-content collapse in">

                                <form id="form_search" method="get" action="wages.php">
                                    <fieldset>
                                        <div class="alert alert-error hide">
                                            <button class="close" data-dismiss="alert"></button>
                                            Hubo un problema. Favor de revisar la información.
                                        </div>
                                        <div class="alert alert-success hide">
                                            <button class="close" data-dismiss="alert"></button>
                                            La información es válida!
                                        </div>
                                        <?php if($projects) { ?>
                                        <div class="control-group">
                                            <label class="control-label">Proyecto</label>
                                            <div class="controls">
                                                <select class="span10 m-wrap" name="ano" id="proy_search_ano" onchange="change_year_search(this.value);">
                                                    <option value="0">Todos</option>
                                                    <?php foreach($years as $year => $values) { ?>
                                                        <option value="<?=$year;?>" <?=($yearId==$year) ? 'selected="selected"' : '';?>><?=($year==0) ? "Sin año" : $year;?></option>
                                                    <?php } ?>
                                                </select>
                                                <?php foreach($years as $year => $values) { ?>
                                                    <select class="span10 m-wrap proy_search_select" size="8" name="pId" id="proy_search_proyectoId_<?=$year;?>" <?=($year==$yearId) ? '' : 'style="display:none;" disabled';?>>
                                                        <?php foreach($values as $p) { ?>
                                                            <option value="<?=$p['proyectoId'];?>" <?=($projectId==$p['proyectoId']) ? 'selected="selected"' : '';?>><?=$p['titulo'];?></option>
                                                        <?php } ?>
                                                    </select>
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <?php } ?>
                                        <div class="control-group">
                                            <label class="control-label">&nbsp;</label>
                                            <div class="controls">
                                                <button type="submit" class="btn btn-primary">Buscar</button>
                                                <button type="reset" class="btn" onclick="window.location='wages.php';">Limpiar</button>
                                            </div>
                                        </div>
                                    </fieldset>
                                </form>
                                
                            </div>
                        </div>
                        <!-- ./search -->

                    </div>
                </div>
                <!-- ./sidebar -->
                
                <!-- content span -->
                <div class="span9" id="content">
                    <div class="row-fluid">
                        <!-- alerts -->
                        <?php display_alerts(); ?>
                        <!-- ./alerts -->
                        <div class="block">
                            <div class="navbar navbar-inner block-header">
                                <h2 style="color:#1b54a3;">Archivos de Nómina</h2>
                            </div>
                        </div>
                        <!-- breadcrumb -->
                        <div class="navbar">
                            <div class="navbar-inner">
                                <ul class="breadcrumb">
                                    <i class="icon-chevron-left hide-sidebar"><a href="#" title="Hide Sidebar" rel="tooltip">&nbsp;</a></i>
                                    <i class="icon-chevron-right show-sidebar" style="display:none;"><a href="#" title="Show Sidebar" rel="tooltip">&nbsp;</a></i>
                                    <li><a href="index.php">Inicio</a> <span class="divider">/</span></li>
                                    <li><a href="projects.php">Proyectos</a> <span class="divider">/</span></li>
                                    <li class="active">Nóminas</li>
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
                                                <th>Proyecto</th>
                                                <th>Fecha</th>
                                                <th>Archivo</th>
                                                <th>Monto Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if($results) { ?>
                                                <?php for($i=0; $i<count($results); $i++) { ?>
                                                    <tr>
                                                        <td><?=$results[$i]['titulo'];?></td>
                                                        <td><?=get_date_es("Y-m-d", $results[$i]['fecha']);?></td>
                                                        <td>
                                                            <?php if(is_file($results[$i]['pathCierres'].$results[$i]['archivo'])) { ?>
                                                                <a href="mod/wages.php?cmd=download&id=<?=$results[$i]['nominaId'];?>" target="_blank"><?=$results[$i]['archivo'];?></a>
                                                            <?php } else { ?>
                                                                <?=$results[$i]['pathCierres'].$results[$i]['archivo'];?></a>
                                                            <?php } ?>
                                                        </td>
                                                        <td><?=number_currency($results[$i]['monto']);?></td>
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
                    "aaSorting": [[1, 'desc']],
                } );

                $('#form_add').validate({
                    errorClass: 'help-inline',
                    rules: {
                        titulo: {
                            minlength: 5,
                            required: true
                        },
                        rodaje: {
                            number: true,
                            required: true
                        },
                        preproduccion: {
                            number: true,
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


            function change_year(year) {

                $(".proy_add_select").attr('disabled', 'disabled');
                $(".proy_add_select").hide();
                $("#proy_add_proyectoId_"+year).removeAttr('disabled');
                $("#proy_add_proyectoId_"+year).show();

            }

            function change_year_search(year) {

                $(".proy_search_select").attr('disabled', 'disabled');
                $(".proy_search_select").hide();
                $("#proy_search_proyectoId_"+year).removeAttr('disabled');
                $("#proy_search_proyectoId_"+year).show();

            }

        </script>

<?php include("inc.footer.php"); ?>