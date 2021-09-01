<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title><?php echo $titulo_sistema; ?> </title>        
        <!-- Bootstrap 3.3.5 -->
        <link rel="stylesheet" href="recursos/adminLTE/bootstrap/css/bootstrap.min.css">
        <!-- Font Awesome -->
        <link href="recursos/js/plugins/datatables/jquery-datatables.css" rel="stylesheet">
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
       <link rel="stylesheet" href="recursos/adminLTE/dist/css/skins/skin-green.min.css">

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
    |               | skin-green                              |
    |---------------------------------------------------------|
    |LAYOUT OPTIONS | fixed                                   |
    |               | layout-boxed                            |
    |               | layout-top-nav                          |
    |               | sidebar-collapse                        |
    |               | sidebar-mini                            |
    |---------------------------------------------------------|
    -->
    <?php
    require_once 'nucleo/include/MasterConexion.php';
    $objcon = new MasterConexion();
    ?>
    <body class="hold-transition skin-green sidebar-mini">
        <div class="wrapper">

            <!-- Main Header -->
            <header class="main-header">

                       <!-- Logo -->
                 <a href="index.php" class="logo"  style="background: #00395e !important;">
                    <!-- mini logo for sidebar mini 50x50 pixels -->
                    <span  class="logo-mini"><img src='recursos/img/usqay-circle-icon.svg' width="80%"></span>
                    <!-- logo for regular state and mobile devices -->
                    <span  class="logo-lg"><img src='recursos/img/usqay_logo.png'  height="60px"></span>
                </a>

                <!-- Header Navbar -->
                <nav class="navbar navbar-static-top" role="navigation"  style="background: #00395e !important;">
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
                                    <span class="hidden-xs">Hola <?php echo $_COOKIE["nombre_usuario"]; ?> <span class="caret"></span></span>
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
                    <ul class="sidebar-menu">
                        <?php include_once('navbar_sistema.php'); ?>
                    </ul><!-- /.sidebar-menu -->
                </section>
                <!-- /.sidebar -->
            </aside>

            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper cntvnt">
                <section class="content row contenedorventa">
                <!-- Small boxes (Stat box) -->

                    <!--productos y servicios-->
                  <div class="col-lg-12 col-xs-12">
                    <div>
                    <!--elementos venta -->
                    <input type="hidden" id="id_usuario" value="<?php echo $_COOKIE["id_usuario"];?>">
                    <input type="hidden" id="id_caja" value="<?php echo $_COOKIE["id_caja"];?>">
                    <input type="hidden" id="id_venta" value="0">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs" role="tablist">
                      <li role="presentation" class="active"><a href="#productos" aria-controls="productos" role="tab" data-toggle="tab">Productos</a></li>
                      <li role="presentation"><a href="#servicios" aria-controls="servicios" role="tab" data-toggle="tab">Servicios</a></li>
                    <!--  
                    <li role="presentation"><a href="#mas" aria-controls="mas" role="tab" data-toggle="tab">Mas Vendidos</a></li>
                    -->
                    </ul>
                    <!-- Tab panes -->
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane active panbtn" id="productos">
                            <div class="panel panel-default">
                            <div class="panel-body">
                                <div id="busquedaproducto" style="display: block;">
                                <div class="input-group" style="margin-bottom: 10px; width:100%;">
                                <input type="text" class="form-control" placeholder="Escribe para buscar" id="txtbusprod">
                                </div><!-- /input-group -->
                                <table class="table table-bordered">
                                    <thead>
                                        <tr><th>Id</th>
                                            <th>Categoría</th>
                                            <th>Tipo</th>
                                            <th>Marca</th>
                                            <th>Producto</th>
                                            <th>Unidad</th>
                                            <th>Stock</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody id="tablaproductos">
                                        <tr><td colspan="8">
                                              <center>
                                                Aquí aparecerá el resultado de tu búsqueda
                                              </center>
                                         </td></tr>
                                    </tbody>
                                </table>
                                </div>
                            </div>
                            </div>
                        </div>
                        <div role="tabpanel" class="tab-pane panbtn" id="servicios">
                            <div class="panel panel-default">
                            <div class="panel-body">
                                <div id="busquedaservicio" style="display: block;">
                                    <div class="input-group" style="margin-bottom: 10px; width:100%;">
                                    <input type="text" class="form-control" placeholder="Escribe para buscar" id="txtbusser">
                                    </div><!-- /input-group -->
                                    <table class="table table-bordered">
                                    <thead>
                                        <tr><th>Id</th>
                                            <th>Categoría</th>
                                            <th>Tipo</th>
                                            <th>Servicio</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody id="tablaservicios">
                                        <tr><td colspan="5">
                                              <center>
                                                Aquí aparecerá el resultado de tu búsqueda
                                              </center>
                                         </td></tr>
                                    </tbody>
                                </table>
                                </div>
                            </div>
                            </div>
                        </div>
                      <div role="tabpanel" class="tab-pane" id="mas">3...</div>
                    </div>

                    </div>
                  </div><!-- /.content-wrapper -->
                </section>
                <section class="content row contenedorventa">
                  <!-- venta actual-->
                  <div class="col-lg-6 col-xs-6">
                      <table class="table table-bordered">
                          <thead>
                              <tr><th>Elemento</th>
                                  <th>Cantidad</th>
                                  <th>Precio Unitario</th>
                                  <th>Total</th>
                                  <th></th>
                              </tr>
                          </thead>
                          <tbody id="tablaventa">
                              <tr><td colspan="5">
                                    <center>
                                      Aquí aparecerán los productos y servicios para esta venta
                                    </center>
                               </td></tr>
                          </tbody>
                      </table>
                  </div>
                      <div class="row col-lg-6 col-xs-6">
                      <div class="col-lg-6 col-xs-6">
                      <div class="input-group">
                        <div class="input-group-btn">
                            <button type="button" class="btn btn-default">Sub Total S./: </button>
                        </div>
                          <input class="form-control" value='0.00' step='0.01' id="sub_total_venta" readonly>
                      </div>
                      <?php
                        $datos_impuestos = array();
                        $impuestos = $objcon->consulta_matriz("Select * from impuesto where estado_fila = 1");
                        if(is_array($impuestos)):
                        foreach($impuestos as $imp):
                        $fila_impuesto = array();
                        $fila_impuesto["id"] = $imp["id"];
                        $fila_impuesto["nombre"] = $imp["nombre"];
                        $fila_impuesto["tipo"] = $imp["tipo"];
                        $fila_impuesto["valor"] = $imp["valor"];
                        $datos_impuestos[] = $fila_impuesto;
                       ?>
                        <div class="input-group" style="margin-top: 2px;">
                          <div class="input-group-btn">
                              <button type="button" class="btn btn-default"><?php echo $imp["nombre"];?> (<?php
                              if(intval($imp["tipo"]) === 1){
                                  echo ($imp["valor"]*100)."%";
                              }else{
                                  echo $imp["valor"];
                              }
                              ?>) S./: </button>
                          </div>
                          <input class="form-control" value='0.00' step='0.01' id="impuesto_<?php echo $imp["nombre"]?>" readonly>
                        </div>
                        <?php
                        endforeach;
                        endif;
                        ?>

                        <div class="input-group" style="margin-top: 2px;">
                        <div class="input-group-btn">
                          <button type="button" class="btn btn-default">Total S./: </button>
                        </div>
                        <input class="form-control"  value='0.00' step='0.01' id="total_venta" readonly>
                        </div>
                        <div class="input-group" style="margin-top: 2px;">
                        <div class="input-group-btn">
                          <button type="button" class="btn btn-default">Descuento: </button>
                        </div>
                        <input class="form-control"  value='0.00' step='0.01' id="descuento_venta" type="number">
                        </div>
                        <div class="input-group" style="margin-top: 2px;">
                        <div class="input-group-btn">
                          <button type="button" class="btn btn-default">A Pagar S./: </button>
                        </div>
                        <input class="form-control"  value='0.00' step='0.01' id="apagar_venta" readonly>
                        </div>
                        </div>
                        <div class="col-lg-6 col-xs-6">
                        <div class="input-group">
                        <div class="input-group-btn">
                          <button type="button" class="btn btn-default">DNI/RUC: </button>
                        </div>
                        <input class="form-control" id="documento_venta">
                        </div>
                        <div class="input-group" style="margin-top: 2px;">
                        <div class="input-group-btn">
                          <button type="button" class="btn btn-default">Cliente: </button>
                        </div>
                        <input class="form-control" id="cliente_venta">
                        </div>
                        <div class="input-group" style="margin-top: 2px;">
                        <div class="input-group-btn">
                          <button type="button" class="btn btn-default">Direccion: </button>
                        </div>
                        <input class="form-control" id="direccion_venta">
                        </div>
                        <div class="input-group" style="margin-top: 2px;">
                        <div class="input-group-btn">
                          <button type="button" class="btn btn-default">Fecha Nac.: </button>
                        </div>
                          <input class="form-control" id="fecha_venta" placeholder="(Opcional)">
                        </div>
                        </div>
                        </div>
                </section>
                <section class="content row contenedorventa">
                      <center>
                      <button type="button" class="btn btn-primary btn-lg" onclick="nota_venta()">Nota Venta <i class="fa fa-caret-square-o-right" aria-hidden="true"></i></button>
                      <button type="button" class="btn btn-primary btn-lg" onclick="boleta()">Boleta <i class="fa fa-caret-square-o-right" aria-hidden="true"></i></button>
                      <button type="button" class="btn btn-primary btn-lg"  onclick="factura()">Factura <i class="fa fa-caret-square-o-right" aria-hidden="true"></i></button>
                      <!--<button type="button" class="btn btn-primary btn-lg">Proforma <i class="fa fa-caret-square-o-right" aria-hidden="true"></i></button>-->
                      <button type="button" class="btn btn-danger btn-lg" onclick="descartar()">Descartar <i class="fa fa-trash-o" aria-hidden="true"></i></button>
                      </center>
                      
                  </div><!-- /.content-wrapper -->

                </section>

        </div><!-- ./wrapper -->
          <!--Inicio Modal-->
        <div class='modal fade' id='modal_envio_anim' tabindex='-1' role='dialog' data-keyboard="false" data-backdrop="static">
            <div class='modal-dialog'>
                <div class='modal-content'>
                    <div class='modal-header'>
                        <h4 class='modal-title' id='myModalLabel'>Generando Impresión</h4>
                    </div>
                    <div class='modal-body'>
                        <center>
                            <i class="fa fa-cog fa-spin fa-5x fa-fw"></i>
                        </center>
                    </div>
                </div>
            </div>
        </div>
        <!--Fin Modal-->
        <!--Inicio Modal-->
        <div class='modal fade' id='modal_pago' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
            <div class='modal-dialog'>
                <div class='modal-content'>
                    <div class='modal-header'>
                        <button type='button' class='close' data-dismiss='modal'><span aria-hidden='true'>&times;</span><span class='sr-only'>Cerrar</span></button>
                        <h4 class='modal-title' id='myModalLabel'>Pago</h4>
                    </div>
                    <div class='modal-body'>
                        <div class="row">
                            <div class="col-lg-6 col-xs-6">
                                <span style="text-align: left; font-size: 28px;font-weight: bold;">Total: S./ </span><span id="total_pago" style="text-align: left; font-size: 28px;font-weight: bold;">88.88</span><br/>
                                <span style="text-align: left; font-size: 18px;" id="nombre_pago">Nombre Cliente</span><br/>
                                <span style="text-align: left; font-size: 18px;" id="doc_pago">12345553</span><br/>
                                <span style="text-align: left; font-size: 18px;" id="dir_pago">Calle 123 Piura</span><br/>
                            </div>
                            <div class="col-lg-6 col-xs-6" style="text-align: right;">
                                <img id="imgcliente" width="120" height="120" src="recursos/img/logo-mini2.png">
                            </div>
                        </div>
                        <hr/>
                        <h3>Medios de pago:</h3>
                        <p></p>
                        <div class="row">
                        <div class="col-lg-4 col-xs-4">
                            <select class="form-control" id="metodo">
                                <option value='EFECTIVO'>Efectivo</option>
                                <option value='VISA'>Visa</option>
                                <option value='MASTERCARD'>MasterCard</option>                           
                            </select>
                        </div>
                        <div class="col-lg-4 col-xs-4">
                            <select class="form-control" id="moneda">
                                <option value='PEN'>Soles</option>
                                <option value='USD'>Dólares</option>                          
                            </select>
                        </div>
                        <div class="col-lg-4 col-xs-4">
                            <div class="input-group">
                            <input type="text" class="form-control" placeholder="Monto" id="txtmontopago">
                            <span class="input-group-btn">
                              <button class="btn btn-primary" type="button" onclick="agregar_pago()">Agregar</button>
                            </span>
                            </div>
                        </div>
                        </div>
                        <p></p>
                        <div class="row">
                            <div class="col-lg-12 col-xs-12">
                            <table class="table table-bordered">
                                <thead>
                                    <tr><th>Medio</th>
                                        <th>Moneda</th>
                                        <th>Monto</th>
                                        <th>Vuelto</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody id="tablapago">
                                    <tr><td colspan="5">
                                    <center>
                                      Aquí aparecerán los medios de pagos usados
                                    </center>
                                    </td></tr>
                                </tbody>
                            </table>
                            </div>
                        </div>
                        <hr/>
                        <div class="row">
                            <div class="col-lg-6 col-xs-6" style="text-align: left; font-size: 18px;font-weight: bold;">
                                Por Pagar: S./<span id="por_pagar_pago">0.00</span>
                            </div>
                            <div class="col-lg-6 col-xs-6" style="text-align: right; font-size: 18px;font-weight: bold;">
                                Vuelto: <span id="moneda_vuelto">S./</span><span id="vuelto_pago">0.00</span>
                            </div>
                        </div>
                        
                        
                    </div>
                    <div class='modal-footer'>
                        <button type='button' class='btn btn-default' data-dismiss='modal'>Regresar</button>
                        <button type='button' class='btn btn-success' onclick="yuca_pa_ti()">Pagar</button>
                    </div>
                </div>
            </div>
        </div>
        <!--Fin Modal-->

        <!-- REQUIRED JS SCRIPTS -->

        <!-- jQuery 2.1.4 -->
        <script src="recursos/adminLTE/plugins/jQuery/jQuery-2.1.4.min.js"></script>
        <script src="recursos/js/plugins/datatables/jquery-datatables.js"></script>
        <script src="recursos/js/plugins/datatables/dataTables.tableTools.js"></script>




        <!-- Bootstrap 3.3.5 -->
        <script src="recursos/adminLTE/bootstrap/js/bootstrap.min.js"></script> 
        <script src="recursos/adminLTE/plugins/jQueryUI/jquery-ui.js"></script>
        <script src="recursos/adminLTE/dist/js/app.js"></script>

        <script src="recursos/js/moments.js"></script>
        <script src="recursos/js/bootstrap-datetimepicker.min.js"></script>

        <script>
            //Ashuda
            function existeUrl(url) {
                var http = new XMLHttpRequest();
                http.open('HEAD', url, false);
                http.send();
                return http.status!=404;
            }
            
            var inx_prod = 0;
            var inx_serv = 0;
            
            //Contadores
            <?php
            $cambio = $objcon->consulta_arreglo("Select * from tipo_cambio where moneda_origen = 'USD' AND moneda_destino = 'PEN'");
            ?>
            var tipo_comprobante = 0;
            var tipo_cambio = <?php echo $cambio["compra"];?>;
            var id_cliente = 0;
            var total_impuestos = 0;
            var cantidad_pagos = 0;
            var cantidad_productos = 0;
            
            
            $(document).ready(function() {                
                $("#txtbusprod").change(function() {
                    busqueda_producto();
                });
                
                $("#txtbusser").change(function() {
                    busqueda_servicio();
                });
                
                $("#descuento_venta").change(function() {
                    calcula_descuento();
                });
                
                $('#documento_venta').change(function() {
                    carga_cliente();
                });
                
                $('#fecha_venta').datepicker({dateFormat: 'yy-mm-dd',
                    changeMonth: true,
                    changeYear: true
                });
                
                $("#txtmontopago").change(function() {
                    calcula_vuelto();
                });
                
                $("#metodo").change(function() {
                    calcula_vuelto();
                });
                
                $("#moneda").change(function() {
                    calcula_vuelto();
                });
                
                <?php if(isset($_GET["id"])):?>
                carga_venta(<?php echo $_GET["id"];?>);
                <?php endif;?>
                    
            });
            
            //Funciones facturacion
            
            function calcula_vuelto(){
                var ingresado = $("#txtmontopago").val();
                var moneda = $("#moneda").val();
                var tipopago = $("#metodo").val();
                var porpagar = $("#por_pagar_pago").html();
                if(moneda == "PEN"){
                    if(tipopago == "EFECTIVO"){
                        if(parseFloat(ingresado)>parseFloat(porpagar)){
                                var nvuelto = parseFloat(ingresado) - parseFloat(porpagar);
                                $("#moneda_vuelto").html("S./");
                                $("#vuelto_pago").html(nvuelto);
                        }else{
                            $("#moneda_vuelto").html("S./");
                            $("#vuelto_pago").html("0.00");
                        }                       
                    }else{
                        if(parseFloat(ingresado)>parseFloat(porpagar)){
                            $("#txtmontopago").val(porpagar);
                            $("#moneda_vuelto").html("S./");
                            $("#vuelto_pago").html("0.00");
                        }else{
                            $("#moneda_vuelto").html("S./");
                            $("#vuelto_pago").html("0.00");
                        }
                    }
                }else{
                    var ingresado_cambio = parseFloat(ingresado) * tipo_cambio;
                    if(tipopago == "EFECTIVO"){
                        if(parseFloat(ingresado_cambio)>parseFloat(porpagar)){
                            var nvuelto = parseFloat(ingresado) - (parseFloat(porpagar)/tipo_cambio);
                            $("#moneda_vuelto").html("$./");
                            $("#vuelto_pago").html(nvuelto);
                        }else{
                            $("#moneda_vuelto").html("$./");
                            $("#vuelto_pago").html("0.00");
                        }                       
                    }else{
                        if(parseFloat(ingresado)>parseFloat(porpagar)){
                            var porpagardolares = porpagar / tipo_cambio;
                            $("#txtmontopago").val(porpagardolares);
                            $("#moneda_vuelto").html("$./");
                            $("#vuelto_pago").html("0.00");
                        }else{
                            $("#moneda_vuelto").html("$./");
                            $("#vuelto_pago").html("0.00");
                        }
                    }
                }
            }
            
            function carga_cliente(){
                var doc = $("#documento_venta").val();
                $.post('ws/cliente.php', {op: 'getdocumento', doc: doc}, function(data) {
                    if(data !== 0){
                        $("#cliente_venta").val(data.nombre);
                        $("#direccion_venta").val(data.direccion);
                        $("#fecha_venta").val(data.fecha_nacimiento);
                        id_cliente = data.id;
                    }else{
                        id_cliente = 0;
                    }
                }, 'json'); 
            }
            
            function nota_venta(){
                if(existeUrl("recursos/uploads/clientes/"+id_cliente+".png")){
                    $("#imgcliente").attr("src","recursos/uploads/clientes/"+id_cliente+".png");
                }else{
                    $("#imgcliente").attr("src","recursos/img/logo-mini2.png");
                }
                $("#total_pago").html($("#apagar_venta").val());
                $("#nombre_pago").html($("#cliente_venta").val());
                $("#doc_pago").html($("#documento_venta").val());
                $("#dir_pago").html($("#direccion_venta").val());
                $("#por_pagar_pago").html($("#apagar_venta").val());
                $("#moneda_vuelto").html("S./");
                $("#vuelto_pago").html("0.00");
                $("#modal_pago").modal("show");
                carga_pagos();
            }
            
            function boleta(){
                if(existeUrl("recursos/uploads/clientes/"+id_cliente+".png")){
                    $("#imgcliente").attr("src","recursos/uploads/clientes/"+id_cliente+".png");
                }else{
                    $("#imgcliente").attr("src","recursos/img/logo-mini2.png");
                }
                $("#total_pago").html($("#apagar_venta").val());
                $("#nombre_pago").html($("#cliente_venta").val());
                $("#doc_pago").html($("#documento_venta").val());
                $("#dir_pago").html($("#direccion_venta").val());
                $("#por_pagar_pago").html($("#apagar_venta").val());
                $("#moneda_vuelto").html("S./");
                $("#vuelto_pago").html("0.00");
                $("#modal_pago").modal("show");
                carga_pagos();
                tipo_comprobante = 1;
            }           
            
            function factura(){
                if(existeUrl("recursos/uploads/clientes/"+id_cliente+".png")){
                    $("#imgcliente").attr("src","recursos/uploads/clientes/"+id_cliente+".png");
                }else{
                    $("#imgcliente").attr("src","recursos/img/logo-mini2.png");
                }
                $("#total_pago").html($("#apagar_venta").val());
                $("#nombre_pago").html($("#cliente_venta").val());
                $("#doc_pago").html($("#documento_venta").val());
                $("#dir_pago").html($("#direccion_venta").val());
                $("#por_pagar_pago").html($("#apagar_venta").val());
                $("#moneda_vuelto").html("S./");
                $("#vuelto_pago").html("0.00");
                $("#modal_pago").modal("show");
                carga_pagos();
                tipo_comprobante = 2;
            }
            
            function agregar_pago(){
                var venta = $("#id_venta").val();
                var monto = $("#txtmontopago").val();
                var moneda = $("#moneda").val();
                var medio = $("#metodo").val();
                var porpagar = $("#por_pagar_pago").html();
                var vuelto = parseFloat(monto) - parseFloat(porpagar);
                
                var txtmoneda = "SOLES";
                
                if(moneda == "USD"){
                    txtmoneda = "DOLARES";
                }
                
                if(parseFloat(monto) < parseFloat(porpagar)){
                   vuelto = 0.00;
                }
                
                $.post('ws/venta_medio_pago.php', {op: 'add', id_venta: venta, medio:medio, monto:monto, vuelto:vuelto, moneda:moneda}, function(data) {
                    if(data !== 0){
                        if(cantidad_pagos === 0){
                            $("#tablapago").html("");
                        }
                        var ht = '<tr id="pag'+data+'"><th scope="row">'+medio+'</th><td>'+txtmoneda+'</td><td>'+monto+'</td><td>'+vuelto+'</td><td><a href="#" onclick="del_pago('+data+','+monto+','+vuelto+',\''+moneda+'\')"><i class="fa fa-trash-o" aria-hidden="true"></i></a></td></tr>';
                        $("#tablapago").append(ht);
                        porpagar = parseFloat(porpagar) - (parseFloat(monto)-vuelto);
                        $("#por_pagar_pago").html(porpagar);
                        $("#moneda_vuelto").html("S./");
                        $("#vuelto_pago").html("0.00");
                        cantidad_pagos = cantidad_pagos + 1;
                    }
                }, 'json');
            }
            
            function del_pago(id,monto,vuelto){
                var porpagar = $("#por_pagar_pago").html();
                $.post('ws/venta_medio_pago.php', {op: 'del', id:id}, function(data) {
                    if(data !== 0){
                        cantidad_pagos = cantidad_pagos - 1;
                        $('#pag'+id).remove();
                        if(cantidad_pagos === 0){
                            $("#tablapago").html('<tr><td colspan="5"><center>Aquí aparecerán los medios de pagos usados</center></td></tr>');
                        }
                        porpagar = porpagar + (monto-vuelto);
                        $("#por_pagar_pago").html(porpagar);
                        $("#moneda_vuelto").html("S./");
                        $("#vuelto_pago").html("0.00");
                    }
                }, 'json');
            }
            
            function carga_pagos(){
                var total_pagado = 0;
                var venta = $("#id_venta").val();
                var por_pagar = $("#por_pagar_pago").html();
                if(parseInt(venta) > 0){
                    $.post('ws/venta_medio_pago.php', {op: 'listventa',id:venta}, function(data) {
                    if(data !== 0){
                        $('#tablapago').html('');
                        var ht = '';
                        $.each(data, function(key, value) {
                            var monedita = "DOLARES";
                            if(value.moneda == "PEN"){
                                monedita = "SOLES";
                            }
                            ht += '<tr id="pag'+value.id+'"><th scope="row">'+value.medio+'</th><td>'+monedita+'</td><td>'+value.monto+'</td><td>'+value.vuelto+'</td><td><a href="#" onclick="del_pago('+value.id+','+value.monto+','+value.vuelto+',\''+value.moneda+'\')"><i class="fa fa-trash-o" aria-hidden="true"></i></a></td></tr>';
                            total_pagado = total_pagado + (parseFloat(value.monto)-parseFloat(value.vuelto));
                        });
                        $('#tablapago').html(ht);
                        $("#por_pagar_pago").html(parseFloat(por_pagar)-total_pagado);
                    }
                    }, 'json');  
                } 
            }
            
            function anula_venta(){
                if(confirm("¿Realmente desea anular la venta?")){
                    var venta = $("#id_venta").val();
                    if(parseInt(venta)>0){
                        $.post('ws/venta.php', {op: 'anulaventa', id: venta}, function(data) {
                            if(data !== 0){
                                location.reload();
                            }
                        }, 'json');
                    }else{
                        location.reload();
                    }
                }
            }
                        
            function calcula_impuestos(){
                total_impuestos = 0;
                var total = $("#total_venta").val();
                <?php foreach($datos_impuestos as $imp):?>
                var <?php echo $imp["nombre"];?> = <?php if(intval($imp["tipo"]) === 1){?>total<?php echo"*".$imp["valor"];}
                else{echo $imp["valor"];}?>;
                total_impuestos = total_impuestos + <?php echo $imp["nombre"];?>;
                $("#impuesto_<?php echo $imp["nombre"];?>").val(<?php echo $imp["nombre"];?>);
                <?php endforeach;?>
                var sub_total = total - total_impuestos;
                $("#sub_total_venta").val(sub_total);
            }
            
            function calcula_descuento(){
                var total = $("#total_venta").val();
                var descuento = $("#descuento_venta").val();
                $("#apagar_venta").val(total-descuento);
            }
            
            function carga_venta(ide){
                $("#id_venta").val(ide);
                var totaru_ventaru = $("#total_venta").val();
                $.post('ws/producto_venta.php', {op: 'listbyventa',id:ide}, function(data) {
                if(data !== 0){
                    $("#tablaventa").html("");
                    $.each(data, function(key, value) {
                        var ht = '<tr id="p'+value.id+'"><th scope="row">'+value.id_producto.nombre+'</th><td>'+value.cantidad+'</td><td>S./ '+value.precio+'</td><td>S./ '+value.total+'</td><td><a href="#" onclick="del_prod_venta('+value.id+','+value.total+')"><i class="fa fa-trash-o" aria-hidden="true"></i></a></td></tr>';
                        $("#tablaventa").append(ht);
                        totaru_ventaru = parseFloat(totaru_ventaru)+parseFloat(value.total);
                        $("#total_venta").val(totaru_ventaru);
                        calcula_impuestos();
                        calcula_descuento();
                        cantidad_productos = cantidad_productos + 1;
                    });
                }
                }, 'json');
                
                $.post('ws/servicio_venta.php', {op: 'listbyventa',id:ide}, function(data) {
                if(data !== 0){
                    $.each(data, function(key, value) {
                        var ht = '<tr id="p'+value.id+'"><th scope="row">'+value.id_servicio.nombre+'</th><td>'+value.cantidad+'</td><td>S./ '+value.precio+'</td><td>S./ '+value.total+'</td><td><a href="#" onclick="del_serv_venta('+value.id+','+value.total+')"><i class="fa fa-trash-o" aria-hidden="true"></i></a></td></tr>';
                        $("#tablaventa").append(ht);
                        totaru_ventaru = parseFloat(totaru_ventaru)+parseFloat(value.total);
                        $("#total_venta").val(totaru_ventaru);
                        calcula_impuestos();
                        calcula_descuento();
                        cantidad_productos = cantidad_productos + 1;
                    });
                }
                }, 'json');
            }
            
            //Funcion para pagar
            function yuca_pa_ti(){
                var dcc = $("#documento_venta").val();
                var nc = $("#cliente_venta").val();
                var dc = $("#direccion_venta").val();
                var fc = $("#fecha_venta").val();
                
                if(dcc === "" && parseInt(tipo_comprobante) === 2){
                    alert("Debes agregar los datos del cliente");
                }else{
                    $("#modal_pago").modal("hide");
                    $("#modal_envio_anim").modal("show");
                    var caja = $("#id_caja").val();
                    var venta = $("#id_venta").val();
                    var por_pagar = $("#por_pagar_pago").html();
                    var descuento = $("#descuento_venta").val();
                    if(parseInt(venta)>0){
                        if(cantidad_pagos > 0 && parseFloat(por_pagar) > 0){
                            alert("Debes cancelar totalmente la cuenta");
                        }else{
                            //Envios a esperar
                             var espera = <?php echo count($datos_impuestos);?>;
                             espera = espera + 1;
                             var completado = 0;

                            //Guardamos Impuestos

                            <?php foreach($datos_impuestos as $imp):?>
                            var <?php echo $imp["nombre"];?> = $("#impuesto_<?php echo $imp["nombre"];?>").val();
                            $.post('ws/venta_impuesto.php', {op: 'add',id_venta:venta,id_impuesto:<?php echo $imp["id"]?>,monto:<?php echo $imp["nombre"];?>}, function(data) {
                                if(data !== 0){
                                    completado = completado + 1;
                                }
                            }, 'json');
                            <?php endforeach;?>

                            if(id_cliente === 0){
                                if(dcc === ""){
                                    id_cliente = 1;
                                    completado = completado + 1;
                                }else{ 
                                    $.post('ws/cliente.php', {op: 'add', nombre:nc,documento:dcc,direccion:dc,tipo_cliente:tipo_comprobante,fecha_nacimiento:fc}, function(data) {
                                        completado = completado + 1;
                                        if(data !== 0){
                                            id_cliente = data;                                    
                                        }
                                    }, 'json');
                                }
                            }else{
                                $.post('ws/cliente.php', {op: 'mod',id:id_cliente,nombre:nc,documento:dcc,direccion:dc,tipo_cliente:tipo_comprobante,fecha_nacimiento:fc}, function(data) {
                                    if(data !== 0){
                                        completado = completado + 1;
                                    }
                                }, 'json');
                            }

                            if(cantidad_pagos === 0){
                                espera = espera + 1;
                                var monto = $("#apagar_venta").val();
                                $.post('ws/venta_medio_pago.php', {op: 'add', id_venta: venta, medio:'EFECTIVO', monto:monto, vuelto: 0.00, moneda:'PEN'}, function(data) {
                                    if(data !== 0){
                                        completado = completado + 1;
                                    }
                                }, 'json');
                            }

                            if(parseFloat(descuento)>0){
                                espera = espera + 1;
                                $.post('ws/venta_medio_pago.php', {op: 'add', id_venta: venta, medio:'DESCUENTO', monto:descuento, vuelto: 0.00, moneda:'PEN'}, function(data) {
                                    if(data !== 0){
                                        completado = completado + 1;
                                    }
                                }, 'json');    
                            }

                            var vartimer = setInterval(function(){ 
                                if(completado >= espera){
                                    clearInterval(vartimer);
                                    var subtotal = $("#sub_total_venta").val();
                                    var total = $("#total_venta").val();
                                    $.post('ws/venta.php', {op: 'end', id: venta,subtotal:subtotal,total_impuestos:total_impuestos,total:total,tipo_comprobante:tipo_comprobante,id_cliente:id_cliente,id_caja:caja}, function(data) {
                                        if(data !== 0){
                                            document.title = 'KATSU IMPRIMIENDO';
                                            setTimeout(function(){
                                                location.href = "dashboard_sistema.php";
                                            }, 4000);                                     
                                        }
                                    }, 'json');

                                }
                            },500);
                        }
                    }
                }
            }
                    
            //Funciones Navegacion Producto
            function busqueda_producto(){
                var value = $("#txtbusprod").val();
                $.post('ws/producto.php', {op: 'searchsale', bus:value}, function(data) {
                if(data !== 0){
                    $('#tablaproductos').html('');
                    var ht = '';
                    $.each(data, function(key, value) {
                        ht += "<tr><td>"+value.id+"</td><td>"+value.taxonomias[0].valor+"</td>";
                        ht += "<td>"+value.taxonomias[1].valor+"</td>";
                        ht += "<td>"+value.taxonomias[2].valor+"</td>";
                        ht += "<td>"+value.nombre+"</td>";
                        ht += "<td>"+value.precio_venta+"</td>";
                        ht += "<td>"+value.taxonomias[3].valor+"</td>";
                        ht += "<td>"+value.stock+"</td>";
                    });
                    $('#tablaproductos').html(ht);
                }
                }, 'json');
            }
             
            //Funciones Navegacion Servicio
           
            function busqueda_servicio(){
                if(offset_servicio_bus === -1){
                    offset_servicio_bus = 0;
                }
                var value = $("#txtbusserv").val();
                $.post('ws/taxonomias.php', {op: 'searchbytax',limit:limit_servicio_bus,offset:offset_servicio_bus,valor:value}, function(data) {
                if(data !== 0){
                    $('#contenedor_bloques_servicios').html('');
                    var ht = '';
                    var csm = 0;
                    $.each(data, function(key, value) {
                        switch(csm){
                            case 0:
                                if(offset_servicio_bus === 0){
                                    ht += '<button onclick="level1_servicio()" type="button" class="btn btn-default btn-lg btntax"><i class="fa fa-level-up" aria-hidden="true"></i></button>';
                                    ht += '<button type="button" onclick="addserv1('+value.id_servicio.id+',0,\''+value+'\')" class="btn btn-default btn-lg btntax">'+value.id_servicio.nombre+'<br/>S./'+value.id_servicio.precio_venta+'</button>';
                                }else{
                                    ht += '<button onclick="prev_bus_servicio()" type="button" class="btn btn-default btn-lg btntax"><i class="fa fa-angle-double-left" aria-hidden="true"></i></button>';
                                }
                            break;
                            
                            case 1:
                            case 2:
                            case 3:
                            case 4:
                            case 5:
                            case 6:
                            ht += '<button type="button" onclick="addserv1('+value.id_servicio.id+',0,\''+value+'\')" class="btn btn-default btn-lg btntax">'+value.id_servicio.nombre+'<br/>S./'+value.id_servicio.precio_venta+'</button>';
                            break;
                            
                            case 7:
                                if(offset_servicio_bus === 0){
                                    offset_servicio_bus = offset_servicio_bus - 1;
                                }else{
                                  ht += '<button type="button" onclick="addserv1('+value.id_servicio.id+',0,\''+value+'\')" class="btn btn-default btn-lg btntax">'+value.id_servicio.nombre+'<br/>S./'+value.id_servicio.precio_venta+'</button>';
                                }
                            break;
                            
                            case 8:
                                ht += '<button onclick="next_bus_servicio()" type="button" class="btn btn-default btn-lg btntax"><i class="fa fa-angle-double-right" aria-hidden="true"></i></button>';
                            break;
                        }
                        csm = csm + 1;
                    });
                    $('#contenedor_bloques_servicios').html(ht);
                }
                }, 'json');
            }
            
            //Funciones agregar a venta
            
            function addcartp(){
                //Reseteamos indices

                
                //Obtenemos datos venta
                var venta = $("#id_venta").val();
                var usuario = $("#id_usuario").val();
                var caja = $("#id_caja").val();
                var producto = $('#id_producto').val();
                var nombre_producto = $('#nombre_producto').html();
                var cantidad = $("#cantidad_producto").val();
                var precio = $("#precio_producto").html();
                var totaru = parseFloat(precio)*parseFloat(cantidad);
                
                //Total Venta 
                var totaru_ventaru = $("#total_venta").val();
                
                //agregamos a la venta csm
                //Coche rctm muere
                if(parseInt(venta) === 0){
                    $.post('ws/venta.php', {op: 'gen', id_usuario:usuario, id_caja:caja}, function(data) {
                        if(data !== 0){
                            $('#id_venta').val(data);
                            history.pushState({}, null, "dashboard_sistema.php?id="+data);
                            $.post('ws/producto_venta.php', {op: 'addventa',id_venta:data,id_producto:producto,precio:precio,cantidad:cantidad,total:totaru,id_usuario:usuario}, function(data0) {
                                if(data0 !== 0){
                                   if(cantidad_productos === 0){
                                       $("#tablaventa").html("");                                     
                                   } 
                                   cantidad_productos = cantidad_productos +1; 
                                   var ht = '<tr id="p'+data0+'"><th scope="row">'+nombre_producto+'</th><td>'+cantidad+'</td><td>S./ '+precio+'</td><td>S./ '+totaru+'</td><td><a href="#" onclick="del_prod_venta('+data0+','+totaru+')"><i class="fa fa-trash-o" aria-hidden="true"></i></a></td></tr>';
                                   $("#tablaventa").append(ht);
                                   totaru_ventaru = parseFloat(totaru_ventaru)+totaru;
                                   $("#total_venta").val(totaru_ventaru);
                                   calcula_impuestos();
                                   calcula_descuento();
                                   level1();
                                   $("#busquedaproducto").show("fast");
                                   $("#agregarproducto").hide("fast");
                                }
                            }, 'json');
                        }
                    }, 'json');
                }else{
                    $.post('ws/producto_venta.php', {op: 'addventa',id_venta:venta,id_producto:producto,precio:precio,cantidad:cantidad,total:totaru,id_usuario:usuario}, function(data0) {
                        if(data0 !== 0){
                            if(cantidad_productos === 0){
                                $("#tablaventa").html("");                                     
                            } 
                            cantidad_productos = cantidad_productos +1; 
                            var ht = '<tr id="p'+data0+'"><th scope="row">'+nombre_producto+'</th><td>'+cantidad+'</td><td>S./ '+precio+'</td><td>S./ '+totaru+'</td><td><a href="#" onclick="del_prod_venta('+data0+','+totaru+')"><i class="fa fa-trash-o" aria-hidden="true"></i></a></td></tr>';
                            $("#tablaventa").append(ht);
                            totaru_ventaru = parseFloat(totaru_ventaru)+totaru;
                            $("#total_venta").val(totaru_ventaru);
                            calcula_impuestos();
                            calcula_descuento();
                            level1();
                            $("#busquedaproducto").show("fast");
                            $("#agregarproducto").hide("fast");
                        }
                    }, 'json');
                }               
            }
            
            function addcarts(){
                //Reseteamos indices

                //Obtenemos datos venta
                var venta = $("#id_venta").val();
                var usuario = $("#id_usuario").val();
                var caja = $("#id_caja").val();
                var servicio = $('#id_servicio').val();
                var nombre_servicio = $('#nombre_servicio').html();
                var cantidad = $("#cantidad_servicio").val();
                var precio = $("#precio_servicio").html();
                var totaru = parseFloat(precio)*parseFloat(cantidad);
                
                //Total Venta 
                var totaru_ventaru = $("#total_venta").val();
                
                //agregamos a la venta csm
                //Coche rctm muere
                if(parseInt(venta) === 0){
                    $.post('ws/venta.php', {op: 'gen', id_usuario:usuario, id_caja:caja}, function(data) {
                        if(data !== 0){
                            $('#id_venta').val(data);
                            history.pushState({}, null, "dashboard_sistema.php?id="+data);
                            $.post('ws/servicio_venta.php', {op: 'addventa',id_venta:data,id_servicio:servicio,precio:precio,cantidad:cantidad,total:totaru,id_usuario:usuario}, function(data0) {
                                if(data0 !== 0){
                                    if(cantidad_productos === 0){
                                       $("#tablaventa").html("");                                     
                                   } 
                                   cantidad_productos = cantidad_productos +1; 
                                   var ht = '<tr id="s'+data0+'"><th scope="row">'+nombre_servicio+'</th><td>'+cantidad+'</td><td>S./ '+precio+'</td><td>S./ '+totaru+'</td><td><a href="#" onclick="del_serv_venta('+data0+','+totaru+')"><i class="fa fa-trash-o" aria-hidden="true"></i></a></td></tr>';
                                   $("#tablaventa").append(ht);
                                   totaru_ventaru = parseFloat(totaru_ventaru)+totaru;
                                   $("#total_venta").val(totaru_ventaru);
                                   calcula_impuestos();
                                   calcula_descuento();
                                   level1_servicio();
                                   $("#busquedaservicio").show("fast");
                                   $("#agregarservicio").hide("fast");
                                }
                            }, 'json');
                        }
                    }, 'json');
                }else{
                    $.post('ws/servicio_venta.php', {op: 'addventa',id_venta:venta,id_servicio:servicio,precio:precio,cantidad:cantidad,total:totaru,id_usuario:usuario}, function(data0) {
                        if(data0 !== 0){
                            if(cantidad_productos === 0){
                                $("#tablaventa").html("");                                     
                            } 
                            cantidad_productos = cantidad_productos +1; 
                            var ht = '<tr id="s'+data0+'"><th scope="row">'+nombre_servicio+'</th><td>'+cantidad+'</td><td>S./ '+precio+'</td><td>S./ '+totaru+'</td><td><a href="#" onclick="del_serv_venta('+data0+','+totaru+')"><i class="fa fa-trash-o" aria-hidden="true"></i></a></td></tr>';
                            $("#tablaventa").append(ht);
                            totaru_ventaru = parseFloat(totaru_ventaru)+totaru;
                            $("#total_venta").val(totaru_ventaru);
                            calcula_impuestos();
                            calcula_descuento();
                            level1_servicio();
                            $("#busquedaservicio").show("fast");
                            $("#agregarservicio").hide("fast");
                        }
                    }, 'json');
                }               
            }
            
            //Funciones Quitar de Venta
            
            function del_prod_venta(invar,total){
                //Total Venta 
                var id_usuario = $("#id_usuario").val();
                var totaru_ventaru = $("#total_venta").val();
                $.post('ws/producto_venta.php', {op: 'delventa', id: invar, id_usuario:id_usuario}, function(data) {
                    if(data !== 0){
                        cantidad_productos = cantidad_productos - 1;
                        $('#p'+invar).remove();
                        if(cantidad_productos === 0){
                            $("#tablaventa").html('<tr><td colspan="5"><center>Aquí aparecerán los productos y servicios para esta venta</center></td></tr>');                                     
                        } 
                        totaru_ventaru = parseFloat(totaru_ventaru)-total;
                        $("#total_venta").val(totaru_ventaru);
                        calcula_impuestos();
                        calcula_descuento();
                    }
                }, 'json');
            }
            
            function del_serv_venta(invar,total){
               //Total Venta
               var id_usuario = $("#id_usuario").val();
                var totaru_ventaru = $("#total_venta").val();
                $.post('ws/servicio_venta.php', {op: 'delventa', id: invar, id_usuario:id_usuario}, function(data) {
                    if(data !== 0){
                        cantidad_productos = cantidad_productos - 1;
                        $('#s'+invar).remove();
                        if(cantidad_productos === 0){
                            $("#tablaventa").html('<tr><td colspan="5"><center>Aquí aparecerán los productos y servicios para esta venta</center></td></tr>');                                     
                        } 
                        totaru_ventaru = parseFloat(totaru_ventaru)-total;
                        $("#total_venta").val(totaru_ventaru);
                        calcula_impuestos();
                        calcula_descuento();
                    }
                }, 'json'); 
            }
        </script>
    </body>
</html>
