<?php

# include configuration file
include_once ("includes/inc.init.php");

# vars
$projectId = (int)aget('pId');
$projects = get_projects_visible(session_get_data("companyId"));

if($projects==false) {
    set_alert("error", "Es necesario que exista al menos 1 <a href=\"projects.php\">proyecto</a> para poder ver y agregar Archivos de Nómina");
    header("Location: projects.php");
    exit;
}

# results
$sql_project = ($projectId>0) ? " AND proyectoId = $projectId" : " AND proyectoId IN (SELECT proyectoId FROM primo_proyectos WHERE companyId = ".session_get_data("companyId").")";
$res1_good = query_select_single_value("COUNT(res1)", TABLE_POLLS_ANSWERS, "res1 = 1 $sql_project", "");
$res1_bad = query_select_single_value("COUNT(res1)", TABLE_POLLS_ANSWERS, "res1 = 0 $sql_project", "");
$res1_js = "data[0] = { label: \"Buena\", data: $res1_good }; data[1] = { label: \"Mala\", data: $res1_bad };";

$res2_good = query_select_single_value("COUNT(res2)", TABLE_POLLS_ANSWERS, "res2 = 1 $sql_project", "");
$res2_bad = query_select_single_value("COUNT(res2)", TABLE_POLLS_ANSWERS, "res2 = 0 $sql_project", "");
$res2_js = "data[0] = { label: \"Buena\", data: $res2_good }; data[1] = { label: \"Mala\", data: $res2_bad };";

$res3_good = query_select_single_value("COUNT(res3)", TABLE_POLLS_ANSWERS, "res3 = 1 $sql_project", "");
$res3_bad = query_select_single_value("COUNT(res3)", TABLE_POLLS_ANSWERS, "res3 = 0 $sql_project", "");
$res3_js = "data[0] = { label: \"Si\", data: $res3_good }; data[1] = { label: \"No\", data: $res3_bad };";

if($projectId>0) {
    $results = sql_select(" SELECT v.proveedorId, v.razonSocial, 
                                IF(e.res1 = 0, 'Mala', 'Buena') AS res1, 
                                IF(e.res2 = 0, 'Mala', 'Buena') AS res2, 
                                IF(e.res3 = 0, 'No', 'Si') AS res3, 
                                e.res4
                            FROM ".TABLE_POLLS_ANSWERS." e, ".TABLE_VENDORS." v 
                            WHERE e.proveedorId = v.proveedorId AND e.proyectoId = $projectId");
} else {
    $results = sql_select(" SELECT p.proyectoId, p.titulo, v.proveedorId, v.razonSocial, e.res1, e.res2, e.res3 
                            FROM ".TABLE_POLLS_ANSWERS." e, ".TABLE_PROJECTS." p, ".TABLE_VENDORS." v 
                            WHERE e.proyectoId = p.proyectoId AND e.proveedorId = v.proveedorId AND p.companyId = ".session_get_data("companyId")."
                            ORDER BY e.proyectoId ASC");
    $projects_arr = array();
    if($results) {
        $total = count($results);
        for($i=0; $i<$total; $i++) {
            $projects_arr[(int)$results[$i]['proyectoId']][] = $results[$i];
        }
    }

    $rows = array();
    if(count($projects_arr)>0) {
        foreach($projects_arr as $key => $p) {
            $res1_good = 0; $res1_bad = 0;
            $res2_good = 0; $res2_bad = 0;
            $res3_good = 0; $res3_bad = 0;
            foreach($p as $l) {
                $id = $l['proyectoId'];
                $titulo = $l['titulo'];
                ($l['res1']==1) ? $res1_good++ : $res1_bad++;
                ($l['res2']==1) ? $res2_good++ : $res2_bad++;
                ($l['res3']==1) ? $res3_good++ : $res3_bad++;
            }
            $rows[$key] = ["proyectoId" => $id, "titulo" => $titulo, "r1g" => $res1_good, "r1b" => $res1_bad, "r2g" => $res2_good, "r2b" => $res2_bad, "r3g" => $res3_good, "r3b" => $res3_bad];
        }
    }
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
                        <div class="block">
                            <div class="navbar navbar-inner block-header">
                                <div class="muted pull-left">Filtrar</div>
                            </div>
                            <div class="block-content collapse in">

                                <!-- search-form-->
                                <form id="form_search" method="get" action="poll.detail.php">
                                    <fieldset>
                                        <div id="div_project" class="control-group">
                                            <label class="control-label">Proyecto</label>
                                            <div class="controls">
                                                <select class="span10 m-wrap" name="ano" id="proy_add_ano" onchange="change_year(this.value);">
                                                    <?php foreach($years as $year => $values) { ?>
                                                        <option value="<?=$year;?>" <?=($yearId==$year) ? 'selected="selected"' : '';?>><?=($year==0) ? "Sin año" : $year;?></option>
                                                    <?php } ?>
                                                </select>
                                                <?php foreach($years as $year => $values) { ?>
                                                    <select class="span10 m-wrap proy_add_select" size="8" name="pId" id="proy_add_proyectoId_<?=$year;?>" <?=($year==$yearId) ? '' : 'style="display:none;" disabled';?>>
                                                        <?php foreach($values as $p) { ?>
                                                            <option value="<?=$p['proyectoId'];?>" <?=($projectId==$p['proyectoId']) ? 'selected="selected"' : '';?>><?=$p['titulo'];?></option>
                                                        <?php } ?>
                                                    </select>
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <div id="div_button" class="control-group">
                                            <label class="control-label">&nbsp;</label>
                                            <div class="controls">
                                                <button type="submit" class="btn btn-primary">Mostrar</button>
                                            </div>
                                        </div>
                                    </fieldset>
                                </form>
                                <!-- ./search-form -->
                                
                            </div>
                        </div>
                    </div>
                </div>
                <!-- ./sidebar -->

                <?php if($results) { ?>

                    <div class="span3">
                        <div class="row-fluid">
                            <div class="block">
                                <div class="navbar navbar-inner block-header">
                                    <div class="muted pull-left">Atención de Producción Ejecutiva</div>
                                </div>
                                <div class="block-content collapse in">
                                    <div id="piechart1" style="width:100%;height:300px"></div>
                                </div>
                            </div><!-- /block -->
                        </div><!-- ./row -->
                    </div><!-- ./content span3 -->

                    <div class="span3">
                        <div class="row-fluid">
                            <div class="block">
                                <div class="navbar navbar-inner block-header">
                                    <div class="muted pull-left">Atención de Personal Administrativo</div>
                                </div>
                                <div class="block-content collapse in">
                                    <div id="piechart2" style="width:100%;height:300px"></div>
                                </div>
                            </div><!-- /block -->
                        </div><!-- ./row -->
                    </div><!-- ./content span3 -->

                    <div class="span3">
                        <div class="row-fluid">
                            <div class="block">
                                <div class="navbar navbar-inner block-header">
                                    <div class="muted pull-left">Trabajaría con Nosotros Nuevamente</div>
                                </div>
                                <div class="block-content collapse in">
                                    <div id="piechart3" style="width:100%;height:300px"></div>
                                </div>
                            </div><!-- /block -->
                        </div><!-- ./row -->
                    </div><!-- ./content span3 -->

                <?php } else { ?>
                    
                    <div class="span9">
                        <div class="row-fluid">
                            <div class="block">
                                <div class="navbar navbar-inner block-header">
                                    <div class="muted pull-left">Resultados</div>
                                </div>
                                <div class="block-content collapse in">
                                    No hay resultados para mostrar
                                </div>
                            </div><!-- /block -->
                        </div><!-- ./row -->
                    </div><!-- ./content span9 -->

                <?php } ?>

            </div><!-- ./row top -->

            <?php if($results) { ?>

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
                                    <?php if($projectId>0) { ?>
                                        <thead>
                                            <tr>
                                                <th>Proveedor</th>
                                                <th style="text-align:center;">Atención de Producción Ejecutiva</th>
                                                <th style="text-align:center;">Atención de Personal Administrativo</th>
                                                <th style="text-align:center;">Volvería a Trabajar con Nosotros</th>
                                                <th>Comentarios</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if($results) { ?>
                                                <?php for($i=0; $i<count($results); $i++) { ?>
                                                    <tr>
                                                        <td><?=$results[$i]['razonSocial'];?></td>
                                                        <td style="text-align:center;"><span class="label label-<?=($results[$i]['res1']=="Buena") ? 'success' : 'warning';?>"><?=$results[$i]['res1'];?></span></td>
                                                        <td style="text-align:center;"><span class="label label-<?=($results[$i]['res2']=="Buena") ? 'success' : 'warning';?>"><?=$results[$i]['res2'];?></span></td>
                                                        <td style="text-align:center;"><span class="label label-<?=($results[$i]['res3']=="Si") ? 'success' : 'important';?>"><?=$results[$i]['res3'];?></span></td>
                                                        <td><?=$results[$i]['res4'];?></td>
                                                    </tr>
                                                <?php } ?>
                                            <?php } ?>
                                        </tbody>
                                    <?php } else { ?>
                                        <thead>
                                            <tr>
                                                <th>&nbsp;</th>
                                                <th colspan="2" style="text-align:center;">Atención de Producción Ejecutiva</th>
                                                <th colspan="2" style="text-align:center;">Atención de Personal Administrativo</th>
                                                <th colspan="2" style="text-align:center;">Volvería a Trabajar con Nosotros</th>
                                            </tr>
                                            <tr>
                                                <th>Proyecto</th>
                                                <td style="text-align:center;">Buena</td>
                                                <td style="text-align:center;">Mala</td>
                                                <td style="text-align:center;">Buena</td>
                                                <td style="text-align:center;">Mala</td>
                                                <td style="text-align:center;">Si</td>
                                                <td style="text-align:center;">No</td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if($rows) { ?>
                                                <?php foreach($rows as $r) { ?>
                                                    <tr>
                                                        <td><a href="poll.detail.php?pId=<?=$r['proyectoId'];?>"><?=$r['titulo'];?></a></td>
                                                        <td style="text-align:center;"><?=$r['r1g'];?></td>
                                                        <td style="text-align:center;"><?=$r['r1b'];?></td>
                                                        <td style="text-align:center;"><?=$r['r2g'];?></td>
                                                        <td style="text-align:center;"><?=$r['r2b'];?></td>
                                                        <td style="text-align:center;"><?=$r['r3g'];?></td>
                                                        <td style="text-align:center;"><?=$r['r3b'];?></td>
                                                    </tr>
                                                <?php } ?>
                                            <?php } ?>
                                        </tbody>
                                    <?php } ?>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- /block -->
                </div><!-- ./row -->

            <?php } ?>

            <hr>
            <footer>
                <?php /* <p> <?=SITE_FOOTER_COPY;?></p> */ ?>
            </footer>
        </div><!--/.fluid-container-->

        <?php if($results) { ?>

            <!-- extra js -->
            <link rel="stylesheet" href="vendors/morris/morris.css">
            <script src="vendors/jquery-1.9.1.min.js"></script>
            <script src="vendors/raphael-min.js"></script>
            <script src="vendors/morris/morris.min.js"></script>
            <script src="vendors/flot/jquery.flot.js"></script>
            <script src="vendors/flot/jquery.flot.categories.js"></script>
            <script src="vendors/flot/jquery.flot.pie.js"></script>
            <script src="vendors/flot/jquery.flot.time.js"></script>
            <script src="vendors/flot/jquery.flot.stack.js"></script>
            <script src="vendors/flot/jquery.flot.resize.js"></script>

            <script src="assets/scripts.js"></script>
            
            <script>

                $(function() {

                    var data = [];
                    <?=$res1_js;?>;
                    $.plot('#piechart1', data, {
                        series: {
                            pie: { 
                                show: true,
                                radius: 1,
                                label: {
                                    show: true,
                                    radius: 3/4,
                                    formatter: labelFormatter,
                                    background: { 
                                        opacity: 0.5,
                                        color: '#666'
                                    }
                                }
                            }
                        },
                        legend: {
                            show: false
                        }
                    });

                    var data = [];
                    <?=$res2_js;?>;
                    $.plot('#piechart2', data, {
                        series: {
                            pie: { 
                                show: true,
                                radius: 1,
                                label: {
                                    show: true,
                                    radius: 3/4,
                                    formatter: labelFormatter,
                                    background: { 
                                        opacity: 0.5,
                                        color: '#666'
                                    }
                                }
                            }
                        },
                        legend: {
                            show: false
                        }
                    });

                    var data = [];
                    <?=$res3_js;?>;
                    $.plot('#piechart3', data, {
                        series: {
                            pie: { 
                                show: true,
                                radius: 1,
                                label: {
                                    show: true,
                                    radius: 3/4,
                                    formatter: labelFormatter,
                                    background: { 
                                        opacity: 0.5,
                                        color: '#666'
                                    }
                                }
                            }
                        },
                        legend: {
                            show: false
                        }
                    });

                    function labelFormatter(label, series) {
                        return "<div style='font-size:10pt; text-align:center; padding:4px; color:#eaeaea; font-weight:bold; '>" + label + "<br/>" + Math.round(series.percent) + "%</div>";
                    }

                });

                function change_year(year) {

                    $(".proy_add_select").attr('disabled', 'disabled');
                    $(".proy_add_select").hide();
                    $("#proy_add_proyectoId_"+year).removeAttr('disabled');
                    $("#proy_add_proyectoId_"+year).show();

                }

            </script>

        <?php } ?>

<?php include("inc.footer.php"); ?>