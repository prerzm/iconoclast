<?php

/** HiperMedica **/

# include configuration file
include_once ("includes/inc.init.php");

# vars & filters
$conceptoId = (int)aget('id');

# queries
$record = sql_select_row("SELECT * FROM ".TABLE_MASTER." WHERE conceptoId = $conceptoId");

if($record['parentId']>0) {
    $parent = query_select_single_value("nombre ", TABLE_MASTER, "conceptoId = ".$record['parentId'])." / ";
} else {
    $parent = "";
}

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
                                <h2 style="color:#1b54a3;">Presupuesto Maestro</h2>
                            </div>
                        </div>
                        <!-- breadcrumb -->
                        <div class="navbar">
                            <div class="navbar-inner">
                                <ul class="breadcrumb">
                                    <i class="icon-chevron-left hide-sidebar"><a href="#" title="Hide Sidebar" rel="tooltip">&nbsp;</a></i>
                                    <i class="icon-chevron-right show-sidebar" style="display:none;"><a href="#" title="Show Sidebar" rel="tooltip">&nbsp;</a></i>
                                    <li><a href="index.php">Inicio</a> <span class="divider">/</span></li>
                                    <li><a href="master.php">Presupuesto Maestro</a> <span class="divider">/</span></li>
                                    <li class="active"><?=$parent;?><?=$record['nombre'];?></li>
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

                                <!-- form-->
                                <form id="form_add" method="post" action="mod/master.php">
                                <input type="hidden" name="cmd" value="update">
                                <input type="hidden" name="id" value="<?=$conceptoId;?>">
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
                                            <label class="control-label">Cuenta<span class="required">*</span></label>
                                            <div class="controls">
                                                <input type="text" name="cuenta" data-required="1" class="span10 m-wrap" value="<?=$record['cuenta'];?>" />
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">Nombre<span class="required">*</span></label>
                                            <div class="controls">
                                                <input type="text" name="nombre" data-required="1" class="span10 m-wrap" value="<?=$record['nombre'];?>" />
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">&nbsp;</label>
                                            <div class="controls">
                                                <button type="submit" class="btn btn-primary"><i class="icon-pencil icon-white"></i> Guardar</button>
                                                <button type="reset" class="btn btn-inverse" onclick="window.location='master.php';"><i class="icon-arrow-left icon-white"></i> Cancelar</button>
                                                <?php if($global_perms['DELETE'] && $record['categoria']==0) { ?>
                                                    <a href="#myAlert" data-toggle="modal" class="btn btn-danger"><i class="icon-remove icon-white"></i> Eliminar</a>
                                                <?php } ?>
                                                <div id="myAlert" class="modal hide">
                                                    <div class="modal-header">
                                                        <button data-dismiss="modal" class="close" type="button">&times;</button>
                                                        <h3>Eliminar</h3>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>Está seguro que desea eliminar este registro?</p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <a class="btn btn-primary" href="mod/master.php?cmd=del&id=<?=$conceptoId;?>">Confirmar</a>
                                                        <a data-dismiss="modal" class="btn" href="#">Cancelar</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </fieldset>
                                </form>
                                <!-- ./form -->
                                
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

<?php include("inc.footer.php"); ?>