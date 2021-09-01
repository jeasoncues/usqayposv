<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title><?php echo $titulo_sistema; ?> - <?php echo $titulo_pagina; ?></title>
        <!-- Tell the browser to be responsive to screen width -->
        <!-- Bootstrap 3.3.5 -->
        <link rel="stylesheet" href="recursos/adminLTE/bootstrap/css/bootstrap.min.css">
        <!-- Font Awesome -->
        <link href="recursos/js/plugins/datatables/jquery-datatables.css" rel="stylesheet">
        <link href="recursos/js/plugins/datatables/dataTables.tableTools.css" rel="stylesheet">
        <link href="recursos/css/bootstrap-overrides.css" rel="stylesheet">
        <link href="recursos/css/jquery-ui.css" rel="stylesheet">
        <!-- Ionicons -->
        <link rel="stylesheet" href="recursos/ionicons/css/ionicons.min.css">
        <link rel="stylesheet" href="recursos/fa/css/font-awesome.min.css">
        <!-- Theme style -->
        <link rel="stylesheet" href="recursos/adminLTE/dist/css/AdminLTE.css">
        <!-- AdminLTE Skins. We have chosen the skin-blue for this starter
              page. However, you can choose any other skin. Make sure you
              apply the skin class to the body tag so the changes take effect.
        -->
        <link rel="stylesheet" href="recursos/adminLTE/dist/css/skins/skin-blue.min.css">
        <link rel="shortcut icon" type="image/x-icon" href="usqay-icon.svg">

    </head>
    <!--
    BODY TAG OPTIONS:
    =================
    Apply one or more of the following classes to get the
    desired effect
    |---------------------------------------------------------|
    | SKINS         | skin-blue                               |
    |               | skin-black                              |
    |               | skin-purple                             |
    |               | skin-yellow                             |
    |               | skin-red                                |
    |               | skin-blue                              |
    |---------------------------------------------------------|
    |LAYOUT OPTIONS | fixed                                   |
    |               | layout-boxed                            |
    |               | layout-top-nav                          |
    |               | sidebar-collapse                        |
    |               | sidebar-mini                            |
    |---------------------------------------------------------|
    -->
    <body class="hold-transition skin-blue sidebar-mini sidebar-collapse">
        <div class="wrapper">

            <!-- Main Header -->
            <header class="main-header">

                     <!-- Logo -->
                <a href="index.php" class="logo"  style="background: #0F4B81 !important;">
                    <!-- mini logo for sidebar mini 50x50 pixels -->
                    <span  class="logo-mini"><img src='recursos/img/usqay-circle-icon.svg' width="80%"></span>
                    <!-- logo for regular state and mobile devices -->
                     <span  class="logo-lg"><img src='recursos/img/usqay_logo.png'  height="60px"></span>
                </a>

                <!-- Header Navbar -->
                <nav class="navbar navbar-static-top" role="navigation" style="background: #00395e !important">
                    <!-- Sidebar toggle button-->
                    <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                        <span class="sr-only">Toggle navigation</span>
                    </a>
                    <!-- Navbar Right Menu -->
                    <div class="navbar-custom-menu">
                        <ul class="nav navbar-nav">
                            <!-- User Account Menu -->
                            <li class="dropdown user user-menu">
                                <!-- Menu Toggle Button -->
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                    <!-- The user image in the navbar-->
                                    <img src="recursos/adminLTE/dist/img/user2-160x160.jpg" class="user-image" alt="User Image">
                                    <!-- hidden-xs hides the username on small devices so only the image appears. -->
                                    <span class="hidden-xs">Hola, <?php echo $_COOKIE["nombre_usuario"]; ?> </span><span class="caret"></span>
                                </a>
                                <ul class="dropdown-menu">
                                    <!-- The user image in the menu -->
                                    <li class="user-header">
                                        <img src="recursos/adminLTE/dist/img/user2-160x160.jpg" class="img-circle" alt="User Image">
                                        <p>
                                            <?php echo $_COOKIE["nombre_usuario"]; ?>
                                            <small>Usuario del Sistema</small>
                                        </p>
                                    </li>

                                    <!-- Menu Footer-->
                                    <li class="user-footer">
                                        <div class="pull-left">
                                            <a href="manual/index.html" target="_blank" class="btn btn-warning btn-flat">Manual</a>
                                        </div>
                                        <div class="pull-right">
                                            <a href="logout_sistema.php" class="btn btn-default btn-flat">Salir del Sistema</a>
                                        </div>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </nav>
            </header>
            <!-- Left side column. contains the logo and sidebar -->
            <aside class="main-sidebar">

                <!-- sidebar: style can be found in sidebar.less -->
                <section class="sidebar">

                    <!-- Sidebar Menu -->
                    
                </section>
                <!-- /.sidebar -->
            </aside>

            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <h1>
                        <?php echo $titulo_pagina; ?>
                    </h1>          
                </section>

                <!-- Main content -->
                <section class="content">
                    <div class="alert alert-danger alert-dismissable" style="display:none;" id="merror">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        Hubo un error, reintenta
                    </div>
                    <div class="alert alert-success alert-dismissable" style="display:none;" id="msuccess">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        Operación Completada con Éxito
                    </div>
                    <form role="form" id="frmall" class="form-horizontal row">