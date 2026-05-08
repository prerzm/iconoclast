<!DOCTYPE html>
<html class="no-js" lang="es-MX">
    
    <head>
        <meta charset="utf-8">
        <title>Servicios ABP</title>
        <!-- Bootstrap -->
        <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
        <link href="bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet" media="screen">
        <link href="assets/styles.css" rel="stylesheet" media="screen">
        <link href="assets/DT_bootstrap.css" rel="stylesheet" media="screen">
        <link href="css/gabm.css" rel="stylesheet" media="screen">
        <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
        <!--[if lt IE 9]>
            <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->
        <script src="vendors/modernizr-2.6.2-respond-1.1.0.min.js"></script>
        <script src="vendors/jquery-1.9.1.min.js"></script>
        <script src="bootstrap/js/bootstrap.min.js"></script>
        <script src="assets/scripts.js"></script>
    </head>
    
    <body>
    
        <div class="navbar navbar-fixed-top">
            <div class="navbar-inner">
                <div class="container-fluid">
                    <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse"> <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </a>
                    <a class="brand" href="index.php"><span style="color:#1b54a3;">Servicios ABP</span><?=(session_get_data('roleId')!=ROLE_VENDOR) ? ' - <span style="color:#1b54ea;">'.$global_company['nombre'].'</span>' : '';?></a>
                    <div class="nav-collapse collapse">
                        <!-- account -->
                        <ul class="nav pull-right">
                            <li class="dropdown">
                                <a href="#" role="button" class="dropdown-toggle" data-toggle="dropdown"> 
                                    <i class="icon-user"></i> <?=session_get_data("name");?> <i class="caret"></i>
                                </a>
                                <ul class="dropdown-menu">
                                    <?php if(session_get_data('roleId')!=ROLE_VENDOR && $global_companies!==false) { ?>
                                        <?php foreach($global_companies as $c) {  ?>
                                            <li><a href="mod/companies.php?cmd=set&id=<?=$c['companyId'];?>"><?=($global_company['companyId']==$c['companyId']) ? '&#10004; ' : '';?><?=$c['nombre'];?></a></li>
                                        <?php } ?>
                                        <li class="divider"></li>
                                    <?php } ?>
                                    <li><a tabindex="-1" href="mod/login.php?cmd=logout">Salir</a></li>
                                </ul>
                            </li>
                        </ul>
                        <!-- ./account -->

                        <!-- menu -->
                        <ul class="nav">
                            <?php foreach($global_menu as $menu) { ?>
                                <li class="dropdown <?=($global_active_menu==$menu['menuParentKey']) ? 'active' : '';?>">
                                <a href="#" role="button" class="dropdown-toggle" data-toggle="dropdown"><?=$menu['menuParentName'];?> <i class="caret"></i></a>
                                <ul class="dropdown-menu">
                                    <?php foreach($menu['items'] as $item) { ?>
                                        <li><a href="<?=$item['url'];?>"><?=$item['item'];?></a></li>
                                    <?php } ?>
                                </ul>
                                </li>
                            <?php } ?>
                        </ul>
                        <!-- ./menu -->

                    </div>
                    <!--/.nav-collapse -->
                    
                </div>
            </div>
        </div>

