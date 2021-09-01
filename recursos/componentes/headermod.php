<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title><?php echo $titulo_sistema; ?> - <?php echo $titulo_pagina; ?></title>
        <!-- Tell the browser to be responsive to screen width -->
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
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
        <!-- Start Theme style To Only Buttons -->
        <link rel="stylesheet" href="recursos/css/button.css">
        <!-- Finish Theme style To Only Buttons -->
        <link rel="stylesheet" href="recursos/adminLTE/dist/css/skins/skin-red.min.css">

    </head>

    <body style="padding-top:0px !important;">


        <div class="container" style="width:100%;">
            <div class="alert alert-danger alert-dismissable" style="display:none;" id="merror">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                Hubo un error, reintenta
            </div>
            <div class="alert alert-success alert-dismissable" style="display:none;" id="msuccess">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                Operación Completada con Éxito
            </div>
           <form role="form" id="frmall" class="form-horizontal row">