<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
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
       <link rel="stylesheet" href="recursos/adminLTE/dist/css/skins/skin-blue.min.css">

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
    
    $almacenes = $objcon->consulta_matriz("Select * from almacen where estado_fila = 1");
    ?>
    <body class="hold-transition skin-blue sidebar-mini">
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
                                    <!-- hidden-xs hides the username on small devices so only the image appears. -->
                                    <span class="hidden-xs">Hola, <?php echo $_COOKIE["nombre_usuario"]; ?> <span class="caret"></span></span>
                                </a>
                                <ul class="dropdown-menu">
                                    <!-- The user image in the menu -->
                                    <li class="user-header">
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
                  <div class="col-lg-6 col-xs-12">
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
                                <select class='form-control' id='almacen_venta' name='almacen_venta' style="margin-top: 5px;">
                                    <option value='0'>Todos los Almacenes</option>
                                    <?php 
                                    if(is_array($almacenes)){
                                        foreach($almacenes as $alv){
                                            echo "<option value='".$alv["id"]."'>".$alv["nombre"]."</option>";
                                        }
                                    }
                                    ?>                                    
                                </select>
                                </div><!-- /input-group -->
                                <div id="contenedor_bloques_productos"> 
                                
                                </div>
                                </div>
                                <div id="agregarproducto" class="row" style="display:none;">
                                    <div class="col-lg-4 col-xs-4">
                                    <input type="hidden" id="id_producto">
                                    <input type="hidden" id="taxonomia_producto">
                                    <input type="hidden" id="valor_taxonomia_producto">
                                    <input type="hidden" id="taxonomia_padre" value="0">
                                    <input type="hidden" id="taxonomia_abuelo" value="0">
                                    <img id="imgproducto" style="width:97% !important;height:150px !important;">
                                    <h3 id="nombre_producto">Nombre Producto</h3>
                                    <h3 id="stock_producto">0 en Stock</h3>
                                    <h4>S./ <span id="precio_producto">99.99</span></h4>
                                    <h1></h1>
                                    <button type="button" class="btn btn-default btn-lg btnback" onclick="regresar_lista_producto()"><i class="fa fa-reply-all" aria-hidden="true"></i></button>
                                    </div>
                                    <div class="col-lg-8 col-xs-8">
                                    <div class="input-group" style="margin-bottom: 10px;">
                                    <input type="text" class="form-control" placeholder="Cantidad" value="1" id="cantidad_producto">
                                    <span class="input-group-btn">
                                      <button class="btn btn-default" type="button" onclick="resetear_cantidad_producto()"><i class="fa fa-ban" aria-hidden="true"></i></button>
                                    </span>
                                    </div><!-- /input-group -->
                                    <button type="button" class="btn btn-default btn-lg btnadd" onclick="add1p()">1</button>
                                    <button type="button" class="btn btn-default btn-lg btnadd" onclick="add2p()">2</button>
                                    <button type="button" class="btn btn-default btn-lg btnadd" onclick="add3p()">3</button>
                                    <button type="button" class="btn btn-default btn-lg btnadd" onclick="add4p()">4</button>
                                    <button type="button" class="btn btn-default btn-lg btnadd" onclick="add5p()">5</button>
                                    <button type="button" class="btn btn-default btn-lg btnadd" onclick="add6p()">6</button>
                                    <button type="button" class="btn btn-default btn-lg btnadd" onclick="add7p()">7</button>
                                    <button type="button" class="btn btn-default btn-lg btnadd" onclick="add8p()">8</button>
                                    <button type="button" class="btn btn-default btn-lg btnadd" onclick="add9p()">9</button>
                                    <button type="button" class="btn btn-default btn-lg btnadd" onclick="addpointp()">.</button>
                                    <button type="button" class="btn btn-default btn-lg btnadd" onclick="add0p()">0</button>
                                    <button type="button" class="btn btn-default btn-lg btnadd" onclick="addcartp()"><i class="fa fa-cart-arrow-down" aria-hidden="true"></i></button>
                                    </div>  
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
                                    <div id="contenedor_bloques_servicios">
                                    </div>
                                </div>
                                <div id="agregarservicio" class="row" style="display: none;">
                                  <div class="col-lg-4 col-xs-4">
                                    <input type="hidden" id="id_servicio">
                                    <input type="hidden" id="taxonomia_servicio">
                                    <input type="hidden" id="valor_taxonomia_servicio">
                                    <input type="hidden" id="taxonomia_padre_servicio" value="0">
                                    <input type="hidden" id="taxonomia_abuelo_servicio" value="0">
                                    <img id="imgservicio" style="width:97% !important;height:150px !important;">
                                    <h3 id="nombre_servicio">Nombre Servicio</h3>
                                    <h4>S./ <span id="precio_servicio">99.99</span></h4>
                                    <h1></h1>
                                    <button type="button" class="btn btn-default btn-lg btnback" onclick="regresar_lista_servicio()"><i class="fa fa-reply-all" aria-hidden="true"></i></button>
                                    </div>
                                    <div class="col-lg-8 col-xs-8">
                                    <div class="input-group" style="margin-bottom: 10px;">
                                    <input type="text" class="form-control" placeholder="Cantidad" value="1" id="cantidad_servicio">
                                    <span class="input-group-btn">
                                      <button class="btn btn-default" type="button" onclick="resetear_cantidad_servicio()"><i class="fa fa-ban" aria-hidden="true"></i></button>
                                    </span>
                                    </div><!-- /input-group -->
                                    <button type="button" class="btn btn-default btn-lg btnadd" onclick="add1s()">1</button>
                                    <button type="button" class="btn btn-default btn-lg btnadd" onclick="add2s()">2</button>
                                    <button type="button" class="btn btn-default btn-lg btnadd" onclick="add3s()">3</button>
                                    <button type="button" class="btn btn-default btn-lg btnadd" onclick="add4s()">4</button>
                                    <button type="button" class="btn btn-default btn-lg btnadd" onclick="add5s()">5</button>
                                    <button type="button" class="btn btn-default btn-lg btnadd" onclick="add6s()">6</button>
                                    <button type="button" class="btn btn-default btn-lg btnadd" onclick="add7s()">7</button>
                                    <button type="button" class="btn btn-default btn-lg btnadd" onclick="add8s()">8</button>
                                    <button type="button" class="btn btn-default btn-lg btnadd" onclick="add9s()">9</button>
                                    <button type="button" class="btn btn-default btn-lg btnadd" onclick="addpoints()">.</button>
                                    <button type="button" class="btn btn-default btn-lg btnadd" onclick="add0s()">0</button>
                                    <button type="button" class="btn btn-default btn-lg btnadd" onclick="addcarts()"><i class="fa fa-cart-arrow-down" aria-hidden="true"></i></button>
                                    </div>  
                                </div>
                            </div>
                            </div>
                        </div>
                      <div role="tabpanel" class="tab-pane" id="mas">3...</div>
                    </div>

                    </div>
                  </div><!-- /.content-wrapper -->
                  <!-- venta actual-->
                  <div class="col-lg-6 col-xs-12">
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
                      <hr/>
                      <div class="row">
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
                      <hr/>
                      <center>
                      <button type="button" class="btn btn-primary btn-lg" onclick="nota_venta()">Nota Venta <i class="fa fa-caret-square-o-right" aria-hidden="true"></i></button>
                      <button type="button" class="btn btn-primary btn-lg" onclick="boleta()">Boleta <i class="fa fa-caret-square-o-right" aria-hidden="true"></i></button>
                      <button type="button" class="btn btn-primary btn-lg"  onclick="factura()">Factura <i class="fa fa-caret-square-o-right" aria-hidden="true"></i></button>
                      <!--<button type="button" class="btn btn-primary btn-lg">Proforma <i class="fa fa-caret-square-o-right" aria-hidden="true"></i></button>-->
                      <button type="button" class="btn btn-danger btn-lg" onclick="descartar()">Descartar <i class="fa fa-trash-o" aria-hidden="true"></i></button>
                      </center>
                      <center>
                          <div class="checkbox">
                            <label>
                                <input type="checkbox" id="generar" <?php 
                                if(isset($_COOKIE["imprimir"])){
                                    if(intval($_COOKIE["imprimir"]) === 1){
                                        echo 'checked';
                                    }  
                                }
                                ?> onchange="cambia_impresion()">
                              Generar Impresión?
                            </label>
                          </div>
                      </center>
                      
                  </div><!-- /.content-wrapper -->

                </section>

        </div><!-- ./wrapper -->
          <!--Inicio Modal-->
        <div class='modal fade' id='modal_envio_anim' tabindex='-1' role='dialog' data-keyboard="false" data-backdrop="static">
            <div class='modal-dialog'>
                <div class='modal-content'>
                    <div class='modal-header'>
                        <h4 class='modal-title' id='myModalLabel'>Procesando...</h4>
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
                                <input type="hidden" id="por_pagar_value">
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
            //generar impresion o no
            function cambia_impresion(){
                 $.post('ws/venta.php', {op: 'cambiaimpresion'}, function(data) {
                    if(data !== 0){
                        //Nel prro :v
                    }
                }, 'json');
            }
            //Ashuda
            function existeUrl(url) {
                $.post('ws/almacen.php', {op: 'verifica', ruta:url}, function(data) {
                    if(data != 0){
                        return 1;
                    }else{
                        return 0;
                    }
                }, 'json');
            }
            
            //Paginacion Productos
            var limit_producto_l1 = 9;
            var offset_producto_l1 = 0;
            
            var limit_producto_l2 = 9;
            var offset_producto_l2 = 0;
            
            var limit_producto_l3 = 9;
            var offset_producto_l3 = 0;
            
            var limit_producto_l4 = 9;
            var offset_producto_l4 = 0;
            
            var limit_producto_p0 = 9;
            var offset_producto_p0 = 0;
            
            var limit_producto_p1 = 9;
            var offset_producto_p1 = 0;
            
            var limit_producto_bus = 9;
            var offset_producto_bus = 0;
            
            //Paginacion Servicios
            
            var limit_servicio_l1 = 9;
            var offset_servicio_l1 = 0;
            
            var limit_servicio_l2 = 9;
            var offset_servicio_l2 = 0;
            
            var limit_servicio_l3 = 9;
            var offset_servicio_l3 = 0;
            
            var limit_servicio_l4 = 9;
            var offset_servicio_l4 = 0;
            
            var limit_servicio_p0 = 9;
            var offset_servicio_p0 = 0;
            
            var limit_servicio_p1 = 9;
            var offset_servicio_p1 = 0;
            
            var limit_servicio_bus = 9;
            var offset_servicio_bus = 0;
            
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


            var por_pagar_value = $("#por_pagar_value");
            
            $(document).ready(function() {
                level1();
                
                level1_servicio();
                
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
                    //calcula_vuelto();
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
                                $("#vuelto_pago").html(parseFloat(nvuelto).toFixed(2));
                        }else{
                            $("#moneda_vuelto").html("S./");
                            $("#vuelto_pago").html("0.00");
                        }                       
                    }else{
                        if(parseFloat(ingresado)>parseFloat(porpagar)){
                            $("#txtmontopago").val(parseFloat(porpagar).toFixed(2));
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
                            $("#vuelto_pago").html(parseFloat(nvuelto).toFixed(2));
                        }else{
                            $("#moneda_vuelto").html("$./");
                            $("#vuelto_pago").html("0.00");
                        }                       
                    }else{
                        if(parseFloat(ingresado)>parseFloat(porpagar)){
                            var porpagardolares = porpagar / tipo_cambio;
                            $("#txtmontopago").val(parseFloat(porpagardolares).toFixed(2));
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

            function agregar_pago() {
                var venta = $("#id_venta").val();
                var monto = $("#txtmontopago").val();
                var moneda = $("#moneda").val();
                var medio = $("#metodo").val();
                var porpagar = $("#por_pagar_pago").html();
                var vuelto = parseFloat(monto) - parseFloat(porpagar);

                var txtmoneda = "SOLES";

                if (moneda == "USD") {
                    txtmoneda = "DOLARES";
                }

                if (parseFloat(monto) < parseFloat(porpagar)) {
                    vuelto = 0.00;
                }

                $.post('ws/venta_medio_pago.php', {
                    op: 'add',
                    id_venta: venta,
                    medio: medio,
                    monto: monto,
                    vuelto: vuelto,
                    moneda: moneda
                }, function (data) {
                    if (data !== 0) {
                        if (cantidad_pagos === 0) {
                            $("#tablapago").html("");
                        }
                        var ht = '<tr id="pag' + data + '"><th scope="row">' + medio + '</th><td>' + txtmoneda + '</td><td>' + monto + '</td><td>' + vuelto + '</td><td><a href="#" onclick="del_pago(' + data + ',' + monto + ',' + vuelto + ',\'' + moneda + '\')"><i class="fa fa-trash-o" aria-hidden="true"></i></a></td></tr>';
                        $("#tablapago").append(ht);
                        porpagar = parseFloat(porpagar) - (parseFloat(monto) - vuelto);
                        $("#por_pagar_pago").html(parseFloat(porpagar).toFixed(2));
                        $("#moneda_vuelto").html("S./");
                        $("#vuelto_pago").html(vuelto);
                        cantidad_pagos = cantidad_pagos + 1;

                        por_pagar_value.val(parseFloat(por_pagar_value.val()-monto).toFixed(2));

                        $("#txtmontopago").val("");
                    }
                }, 'json');
            }

            function del_pago(id, monto, vuelto) {
                var porpagar = $("#por_pagar_pago").html();
                $.post('ws/venta_medio_pago.php', {op: 'del', id: id}, function (data) {
                    if (data !== 0) {
                        cantidad_pagos = cantidad_pagos - 1;
                        $('#pag' + id).remove();
                        if (cantidad_pagos === 0) {
                            $("#tablapago").html('<tr><td colspan="5"><center>Aquí aparecerán los medios de pagos usados</center></td></tr>');
                        }
                        porpagar = porpagar + (monto - vuelto);
                        //$("#por_pagar_pago").html(parseFloat(porpagar).toFixed(2));
                        $("#moneda_vuelto").html("S./");
                        $("#vuelto_pago").html("0.00");

                        var pp = por_pagar_value.val();
                        pp = parseFloat(pp)+ parseFloat(monto);

                        por_pagar_value.val(pp);
                        $("#por_pagar_pago").html(parseFloat(pp).toFixed(2));
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
                $("#impuesto_<?php echo $imp["nombre"];?>").val(parseFloat(<?php echo $imp["nombre"];?>).toFixed(2));
                <?php endforeach;?>
                var sub_total = total - total_impuestos;
                $("#sub_total_venta").val(parseFloat(sub_total).toFixed(2));
            }
            
            function calcula_descuento(){
                var total = parseFloat($("#total_venta").val());
                var descuento = parseFloat($("#descuento_venta").val());
                var res = total - descuento;
                $("#apagar_venta").val(parseFloat(res).toFixed(2));

                por_pagar_value.val($("#apagar_venta").val());

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
            
            function level1(){
            $.post('ws/taxonomiap.php', {op: 'level1',limit:limit_producto_l1,offset:offset_producto_l1}, function(data) {
                if(data !== 0){
                    $('#contenedor_bloques_productos').html('');
                    var ht = '';
                    var csm = 0;
                    $.each(data, function(key, value) {
                        switch(csm){
                            case 0:
                                if(offset_producto_l1 === 0){
                                    ht += render_tax(value.id,value.nombre,value.es_padre);
                                }else{
                                    ht += '<button onclick="prev_level1()" type="button" class="btn btn-default btn-lg btntax"><i class="fa fa-angle-double-left" aria-hidden="true"></i></button>';
                                }
                            break;
                            
                            case 1:
                            case 2:
                            case 3:
                            case 4:
                            case 5:
                            case 6:
                            case 7:
                                ht += render_tax(value.id,value.nombre,value.es_padre);
                            break;
                            
                            case 8:
                                ht += '<button onclick="next_level1()" type="button" class="btn btn-default btn-lg btntax"><i class="fa fa-angle-double-right" aria-hidden="true"></i></button>';
                            break;
                        }
                        csm = csm + 1;
                    });
                    $('#contenedor_bloques_productos').html(ht);
                }
                }, 'json');
            }
            
            function render_tax(id,nombre,padre){
                var htmlreturn = "";
                if(padre == "SI"){
                    htmlreturn += '<button type="button" onclick="level2('+id+')" class="btn btn-default btn-lg btntax">'+nombre+'</button>';
                }else{
                    htmlreturn += '<button type="button" onclick="level4('+id+')" class="btn btn-default btn-lg btntax">'+nombre+'</button>';
                }
                return htmlreturn;
            }
            
            function next_level1(){
                offset_producto_l1 = offset_producto_l1+7;
                level1();
            }
            
            function prev_level1(){
                offset_producto_l1 = offset_producto_l1-7;
                level1();
            }
            
            function level2(idpadre){
                if(offset_producto_l2 === -1){
                    offset_producto_l2 = 0;
                }
                $.post('ws/taxonomiap.php', {op: 'taxvals',limit:limit_producto_l2,offset:offset_producto_l2,tax:idpadre}, function(data) {
                if(data !== 0){
                    $('#contenedor_bloques_productos').html('');
                    var ht = '';
                    var csm = 0;
                    $.each(data, function(key, value) {
                        switch(csm){
                            case 0:
                                if(offset_producto_l2 === 0){
                                    ht += '<button onclick="level1()" type="button" class="btn btn-default btn-lg btntax"><i class="fa fa-level-up" aria-hidden="true"></i></button>';
                                    ht += '<button type="button" onclick="level3('+value.id+',\''+idpadre+'\')" class="btn btn-default btn-lg btntax">'+value.valor+'</button>';
                                }else{
                                    ht += '<button onclick="prev_level2('+idpadre+')" type="button" class="btn btn-default btn-lg btntax"><i class="fa fa-angle-double-left" aria-hidden="true"></i></button>';
                                }
                            break;
                            
                            case 1:
                            case 2:
                            case 3:
                            case 4:
                            case 5:
                            case 6:
                            ht += '<button type="button" onclick="level3('+value.id+',\''+idpadre+'\')" class="btn btn-default btn-lg btntax">'+value.valor+'</button>'; 
                            break;
                            
                            case 7:
                                if(offset_producto_l2 === 0){
                                    offset_producto_l2 = offset_producto_l2 - 1;
                                }else{
                                  ht += '<button type="button" onclick="level3('+value.id+',\''+idpadre+'\')" class="btn btn-default btn-lg btntax">'+value.valor+'</button>';  
                                }
                            break;
                            
                            case 8:
                                ht += '<button onclick="next_level2('+idpadre+')" type="button" class="btn btn-default btn-lg btntax"><i class="fa fa-angle-double-right" aria-hidden="true"></i></button>';
                            break;
                        }
                        csm = csm + 1;
                    });
                    $('#contenedor_bloques_productos').html(ht);
                }
                }, 'json');
            }
            
            function next_level2(idpadre){
                offset_producto_l2 = offset_producto_l2+7;
                level2(idpadre);
            }
            
            function prev_level2(idpadre){
                offset_producto_l2 = offset_producto_l2-7;
                level2(idpadre);
            }
            
            function level3(idpadre,abuelo){
                if(offset_producto_l3 === -1){
                    offset_producto_l3 = 0;
                }
                $.post('ws/taxonomiap.php', {op: 'level3',limit:limit_producto_l3,offset:offset_producto_l3,padre:idpadre}, function(data) {
                if(data !== 0){
                    $('#contenedor_bloques_productos').html('');
                    var ht = '';
                    var csm = 0;
                    $.each(data, function(key, value) {
                        switch(csm){
                            case 0:
                                if(offset_producto_l3 === 0){
                                    ht += '<button onclick="level2('+abuelo+')" type="button" class="btn btn-default btn-lg btntax"><i class="fa fa-level-up" aria-hidden="true"></i></button>';
                                    ht += '<button type="button" onclick="prodtax0('+value.id_taxonomiap+',\''+value.valor+'\','+idpadre+','+abuelo+')" class="btn btn-default btn-lg btntax">'+value.valor+'</button>';
                                }else{
                                    ht += '<button onclick="prev_level3('+idpadre+','+abuelo+')" type="button" class="btn btn-default btn-lg btntax"><i class="fa fa-angle-double-left" aria-hidden="true"></i></button>';
                                }
                            break;
                            
                            case 1:
                            case 2:
                            case 3:
                            case 4:
                            case 5:
                            case 6:
                            ht += '<button type="button" onclick="prodtax0('+value.id_taxonomiap+',\''+value.valor+'\','+idpadre+','+abuelo+')" class="btn btn-default btn-lg btntax">'+value.valor+'</button>';
                            break;
                            
                            case 7:
                                if(offset_producto_l3 === 0){
                                    offset_producto_l3 = offset_producto_l3 - 1;
                                }else{
                                  ht += '<button type="button" onclick="prodtax0('+value.id_taxonomiap+',\''+value.valor+'\','+idpadre+','+abuelo+')" class="btn btn-default btn-lg btntax">'+value.valor+'</button>';
                                }
                            break;
                            
                            case 8:
                                ht += '<button onclick="next_level3('+idpadre+','+abuelo+')" type="button" class="btn btn-default btn-lg btntax"><i class="fa fa-angle-double-right" aria-hidden="true"></i></button>';
                            break;
                        }
                        csm = csm + 1;
                    });
                    $('#contenedor_bloques_productos').html(ht);
                }
                }, 'json');
            }
            
            function next_level3(idpadre,abuelo){
                offset_producto_l3 = offset_producto_l3+7;
                level3(idpadre,abuelo);
            }
            
            function prev_level3(idpadre,abuelo){
                offset_producto_l3 = offset_producto_l3-7;
                level3(idpadre,abuelo);
            }
            
            function level4(idtax){
                if(offset_producto_l4 === -1){
                    offset_producto_l4 = 0;
                }
                $.post('ws/taxonomiap.php', {op: 'taxvals',limit:limit_producto_l4,offset:offset_producto_l4,tax:idtax}, function(data) {
                if(data !== 0){
                    $('#contenedor_bloques_productos').html('');
                    var ht = '';
                    var csm = 0;
                    $.each(data, function(key, value) {
                        switch(csm){
                            case 0:
                                if(offset_producto_l4 === 0){
                                    ht += '<button onclick="level1()" type="button" class="btn btn-default btn-lg btntax"><i class="fa fa-level-up" aria-hidden="true"></i></button>';
                                    ht += '<button type="button" onclick="prodtax1(\''+value.valor+'\','+idtax+')" class="btn btn-default btn-lg btntax">'+value.valor+'</button>';
                                }else{
                                    ht += '<button onclick="prev_level4('+idtax+')" type="button" class="btn btn-default btn-lg btntax"><i class="fa fa-angle-double-left" aria-hidden="true"></i></button>';
                                }
                            break;
                            
                            case 1:
                            case 2:
                            case 3:
                            case 4:
                            case 5:
                            case 6:
                            ht += '<button type="button" onclick="prodtax1(\''+value.valor+'\','+idtax+')" class="btn btn-default btn-lg btntax">'+value.valor+'</button>';
                            break;
                            
                            case 7:
                                if(offset_producto_l4 === 0){
                                    offset_producto_l4 = offset_producto_l4 - 1;
                                }else{
                                  ht += '<button type="button" onclick="prodtax1(\''+value.valor+'\','+idtax+')" class="btn btn-default btn-lg btntax">'+value.valor+'</button>';
                                }
                            break;
                            
                            case 8:
                                ht += '<button onclick="next_level4('+idtax+')" type="button" class="btn btn-default btn-lg btntax"><i class="fa fa-angle-double-right" aria-hidden="true"></i></button>';
                            break;
                        }
                        csm = csm + 1;
                    });
                    $('#contenedor_bloques_productos').html(ht);
                }
                }, 'json');
            }
            
            function next_level4(idtax){
                offset_producto_l4 = offset_producto_l4+7;
                level4(idtax);
            }
            
            function prev_level4(idtax){
                offset_producto_l4 = offset_producto_l4-7;
                level4(idtax);
            }
            
            function prodtax0(tax,val,padre,abuelo){
                if(offset_producto_p0 === -1){
                    offset_producto_p0 = 0;
                }
                $.post('ws/taxonomiap.php', {op: 'prodtax',limit:limit_producto_p0,offset:offset_producto_p0,tax:tax,valor:val}, function(data) {
                if(data !== 0){
                    $('#contenedor_bloques_productos').html('');
                    var ht = '';
                    var csm = 0;
                    $.each(data, function(key, value) {
                        switch(csm){
                            case 0:
                                if(offset_producto_p0 === 0){
                                    ht += '<button onclick="level3('+padre+','+abuelo+')" type="button" class="btn btn-default btn-lg btntax"><i class="fa fa-level-up" aria-hidden="true"></i></button>';
                                    ht += '<button type="button" onclick="addprod0('+value.id_producto.id+','+tax+',\''+val+'\','+padre+','+abuelo+')" class="btn btn-default btn-lg btntax">'+value.id_producto.nombre+'<br/>S./'+value.id_producto.precio_venta+'</button>';
                                }else{
                                    ht += '<button onclick="prev_prodtax0('+tax+',\''+val+'\','+padre+','+abuelo+')" type="button" class="btn btn-default btn-lg btntax"><i class="fa fa-angle-double-left" aria-hidden="true"></i></button>';
                                }
                            break;
                            
                            case 1:
                            case 2:
                            case 3:
                            case 4:
                            case 5:
                            case 6:
                            ht += '<button type="button" onclick="addprod0('+value.id_producto.id+','+tax+',\''+val+'\','+padre+','+abuelo+')" class="btn btn-default btn-lg btntax">'+value.id_producto.nombre+'<br/>S./'+value.id_producto.precio_venta+'</button>';
                            break;
                            
                            case 7:
                                if(offset_producto_p0 === 0){
                                    offset_producto_p0 = offset_producto_p0 - 1;
                                }else{
                                  ht += '<button type="button" onclick="addprod0('+value.id_producto.id+','+tax+',\''+val+'\','+padre+','+abuelo+')" class="btn btn-default btn-lg btntax">'+value.id_producto.nombre+'<br/>S./'+value.id_producto.precio_venta+'</button>';
                                }
                            break;
                            
                            case 8:
                                ht += '<button onclick="next_prodtax0('+tax+',\''+val+'\','+padre+','+abuelo+')" type="button" class="btn btn-default btn-lg btntax"><i class="fa fa-angle-double-right" aria-hidden="true"></i></button>';
                            break;
                        }
                        csm = csm + 1;
                    });
                    $('#contenedor_bloques_productos').html(ht);
                }
                }, 'json');
            }
            
            function next_prodtax0(idtax,val,padre,abuelo){
                offset_producto_p0 = offset_producto_p0+7;
                prodtax0(idtax,val,padre,abuelo);
            }
            
            function prev_prodtax0(idtax,val,padre,abuelo){
                offset_producto_p0 = offset_producto_p0-7;
                prodtax0(idtax,val,padre,abuelo);
            }
            
            function prodtax1(val,tax){
                if(offset_producto_p1 === -1){
                    offset_producto_p1 = 0;
                }
                $.post('ws/taxonomiap.php', {op: 'prodtax',limit:limit_producto_p1,offset:offset_producto_p1,tax:tax,valor:val}, function(data) {
                if(data !== 0){
                    $('#contenedor_bloques_productos').html('');
                    var ht = '';
                    var csm = 0;
                    $.each(data, function(key, value) {
                        switch(csm){
                            case 0:
                                if(offset_producto_p1 === 0){
                                    ht += '<button onclick="level4('+tax+')" type="button" class="btn btn-default btn-lg btntax"><i class="fa fa-level-up" aria-hidden="true"></i></button>';
                                    ht += '<button type="button" onclick="addprod1('+value.id_producto.id+','+tax+',\''+val+'\')" class="btn btn-default btn-lg btntax">'+value.id_producto.nombre+'<br/>S./'+value.id_producto.precio_venta+'</button>';
                                }else{
                                    ht += '<button onclick="prev_prodtax1('+tax+',\''+val+'\')" type="button" class="btn btn-default btn-lg btntax"><i class="fa fa-angle-double-left" aria-hidden="true"></i></button>';
                                }
                            break;
                            
                            case 1:
                            case 2:
                            case 3:
                            case 4:
                            case 5:
                            case 6:
                            ht += '<button type="button" onclick="addprod1('+value.id_producto.id+','+tax+',\''+val+'\')" class="btn btn-default btn-lg btntax">'+value.id_producto.nombre+'<br/>S./'+value.id_producto.precio_venta+'</button>';
                            break;
                            
                            case 7:
                                if(offset_producto_p1 === 0){
                                    offset_producto_p1 = offset_producto_p1 - 1;
                                }else{
                                  ht += '<button type="button" onclick="addprod1('+value.id_producto.id+','+tax+',\''+val+'\')" class="btn btn-default btn-lg btntax">'+value.id_producto.nombre+'<br/>S./'+value.id_producto.precio_venta+'</button>';
                                }
                            break;
                            
                            case 8:
                                ht += '<button onclick="next_prodtax1('+tax+',\''+val+'\')" type="button" class="btn btn-default btn-lg btntax"><i class="fa fa-angle-double-right" aria-hidden="true"></i></button>';
                            break;
                        }
                        csm = csm + 1;
                    });
                    $('#contenedor_bloques_productos').html(ht);
                }
                }, 'json');
            }
            
            function next_prodtax1(tax,val){
                offset_producto_p1 = offset_producto_p1+7;
                prodtax1(val,tax);
            }
            
            function prev_prodtax1(tax,val){
                offset_producto_p1 = offset_producto_p1-7;
                prodtax1(val,tax);
            }
            
            function busqueda_producto(){
                if(offset_producto_bus === -1){
                    offset_producto_bus = 0;
                }
                var value = $("#txtbusprod").val();
                $.post('ws/taxonomiap.php', {op: 'searchbytax',limit:limit_producto_bus,offset:offset_producto_bus,valor:value}, function(data) {
                if(data !== 0){
                    $('#contenedor_bloques_productos').html('');
                    var ht = '';
                    var csm = 0;
                    $.each(data, function(key, value) {
                        switch(csm){
                            case 0:
                                if(offset_producto_bus === 0){
                                    ht += '<button onclick="level1()" type="button" class="btn btn-default btn-lg btntax"><i class="fa fa-level-up" aria-hidden="true"></i></button>';
                                    ht += '<button type="button" onclick="addprod1('+value.id_producto.id+',0,\''+value+'\')" class="btn btn-default btn-lg btntax">'+value.id_producto.nombre+'<br/>S./'+value.id_producto.precio_venta+'</button>';
                                }else{
                                    ht += '<button onclick="prev_bus()" type="button" class="btn btn-default btn-lg btntax"><i class="fa fa-angle-double-left" aria-hidden="true"></i></button>';
                                }
                            break;
                            
                            case 1:
                            case 2:
                            case 3:
                            case 4:
                            case 5:
                            case 6:
                            ht += '<button type="button" onclick="addprod1('+value.id_producto.id+',0,\''+value+'\')" class="btn btn-default btn-lg btntax">'+value.id_producto.nombre+'<br/>S./'+value.id_producto.precio_venta+'</button>';
                            break;
                            
                            case 7:
                                if(offset_producto_bus === 0){
                                    offset_producto_bus = offset_producto_bus - 1;
                                }else{
                                  ht += '<button type="button" onclick="addprod1('+value.id_producto.id+',0,\''+value+'\')" class="btn btn-default btn-lg btntax">'+value.id_producto.nombre+'<br/>S./'+value.id_producto.precio_venta+'</button>';
                                }
                            break;
                            
                            case 8:
                                ht += '<button onclick="next_bus()" type="button" class="btn btn-default btn-lg btntax"><i class="fa fa-angle-double-right" aria-hidden="true"></i></button>';
                            break;
                        }
                        csm = csm + 1;
                    });
                    $('#contenedor_bloques_productos').html(ht);
                }
                }, 'json');
            }
            
            function next_bus(){
                offset_producto_bus = offset_producto_bus+7;
                busqueda_producto();
            }
            
            function prev_bus(){
                offset_producto_bus = offset_producto_bus-7;
                busqueda_producto();
            }
            
            function add1p(){
                var nuevomonto = ($("#cantidad_producto").val())+"1";
                $("#cantidad_producto").val(nuevomonto);
            }
            
            function add2p(){
                var nuevomonto = ($("#cantidad_producto").val())+"2";
                $("#cantidad_producto").val(nuevomonto);
            }
            
            function add3p(){
                var nuevomonto = ($("#cantidad_producto").val())+"3";
                $("#cantidad_producto").val(nuevomonto);
            }
            
            function add4p(){
                var nuevomonto = ($("#cantidad_producto").val())+"4";
                $("#cantidad_producto").val(nuevomonto);
            }
            
            function add5p(){
                var nuevomonto = ($("#cantidad_producto").val())+"5";
                $("#cantidad_producto").val(nuevomonto);
            }
            
            function add6p(){
                var nuevomonto = ($("#cantidad_producto").val())+"6";
                $("#cantidad_producto").val(nuevomonto);
            }
            
            function add7p(){
                var nuevomonto = ($("#cantidad_producto").val())+"7";
                $("#cantidad_producto").val(nuevomonto);
            }
            
            function add8p(){
                var nuevomonto = ($("#cantidad_producto").val())+"8";
                $("#cantidad_producto").val(nuevomonto);
            }
            
            function add9p(){
                var nuevomonto = ($("#cantidad_producto").val())+"9";
                $("#cantidad_producto").val(nuevomonto);
            }
            
            function add0p(){
                var nuevomonto = ($("#cantidad_producto").val())+"0";
                $("#cantidad_producto").val(nuevomonto);
            }
            
            function addpointp(){
                var nuevomonto = ($("#cantidad_producto").val())+".";
                $("#cantidad_producto").val(nuevomonto);
            }
            
            function resetear_cantidad_producto(){
                $("#cantidad_producto").val("");
            }
            
            function regresar_lista_producto(){
                var tax = $('#taxonomia_producto').val();
                var val = $('#valor_taxonomia_producto').val();
                var padre = $('#taxonomia_padre').val();
                var abuelo = $('#taxonomia_abuelo').val();
                if(parseInt(padre)>0){
                    prodtax0(tax,val,padre,abuelo);
                    $("#busquedaproducto").show("fast");
                    $("#agregarproducto").hide("fast");
                }else{
                    if(parseInt(tax)>0){
                        prodtax1(val,tax);
                        $("#busquedaproducto").show("fast");
                        $("#agregarproducto").hide("fast");
                    }else{
                        busqueda_producto();
                        $("#busquedaproducto").show("fast");
                        $("#agregarproducto").hide("fast");
                    }
                }
            }
                                        
            function addprod0(idproducto,idtaxonomia,valortaxonomia,idtaxpadre,idtaxabuelo){
                $("#busquedaproducto").hide("fast");
                $("#agregarproducto").show("fast");
                $('#taxonomia_producto').val(idtaxonomia);
                $('#valor_taxonomia_producto').val(valortaxonomia);
                $('#taxonomia_padre').val(idtaxpadre);
                $('#taxonomia_abuelo').val(idtaxabuelo);
                var id_almacen = $("#almacen_venta").val();
                $.post('ws/producto.php', {op: 'getventa', id: idproducto, id_almacen:id_almacen}, function(data) {
                    if(data !== 0){
                        if(existeUrl("recursos/uploads/productos/"+data.id+".png")){
                            $("#imgproducto").attr("src","recursos/uploads/productos/"+data.id+".png");
                        }else{
                            $("#imgproducto").attr("src","recursos/img/logo-mini2.png");
                        }
                        $('#id_producto').val(data.id);
                        $('#nombre_producto').html(data.nombre);
                        $('#stock_producto').html(data.stock+" En stock");
                        $('#precio_producto').html(data.precio_venta);
                    }
                }, 'json');
            }
            
            function addprod1(idproducto,idtaxonomia,valortaxonomia){
                $("#busquedaproducto").hide("fast");
                $("#agregarproducto").show("fast");
                $('#taxonomia_producto').val(idtaxonomia);
                $('#valor_taxonomia_producto').val(valortaxonomia);
                $('#taxonomia_padre').val("0");
                $('#taxonomia_abuelo').val("0");
                var id_almacen = $("#almacen_venta").val();
                $.post('ws/producto.php', {op: 'getventa', id: idproducto, id_almacen:id_almacen}, function(data) {
                    if(data !== 0){
                        if(existeUrl("recursos/uploads/productos/"+data.id+".png")){
                            $("#imgproducto").attr("src","recursos/uploads/productos/"+data.id+".png");
                        }else{
                            $("#imgproducto").attr("src","recursos/img/logo-mini2.png");
                        }
                        $('#id_producto').val(data.id);
                        $('#nombre_producto').html(data.nombre);
                        $('#stock_producto').html(data.stock+" En stock");
                        $('#precio_producto').html(data.precio_venta);
                    }
                }, 'json');
            }
            
            //Funciones Navegacion Servicio
            
            function level1_servicio(){
            $.post('ws/taxonomias.php', {op: 'level1',limit:limit_servicio_l1,offset:offset_servicio_l1}, function(data) {
                if(data !== 0){
                    $('#contenedor_bloques_servicios').html('');
                    var ht = '';
                    var csm = 0;
                    $.each(data, function(key, value) {
                        switch(csm){
                            case 0:
                                if(offset_servicio_l1 === 0){
                                    ht += render_tax_servicio(value.id,value.nombre,value.es_padre);
                                }else{
                                    ht += '<button onclick="prev_level1_servicio()" type="button" class="btn btn-default btn-lg btntax"><i class="fa fa-angle-double-left" aria-hidden="true"></i></button>';
                                }
                            break;
                            
                            case 1:
                            case 2:
                            case 3:
                            case 4:
                            case 5:
                            case 6:
                            case 7:
                                ht += render_tax_servicio(value.id,value.nombre,value.es_padre);
                            break;
                            
                            case 8:
                                ht += '<button onclick="next_level1_servicio()" type="button" class="btn btn-default btn-lg btntax"><i class="fa fa-angle-double-right" aria-hidden="true"></i></button>';
                            break;
                        }
                        csm = csm + 1;
                    });
                    $('#contenedor_bloques_servicios').html(ht);
                }
                }, 'json');
            }
            
            function render_tax_servicio(id,nombre,padre){
                var htmlreturn = "";
                if(padre == "SI"){
                    htmlreturn += '<button type="button" onclick="level2_servicio('+id+')" class="btn btn-default btn-lg btntax">'+nombre+'</button>';
                }else{
                    htmlreturn += '<button type="button" onclick="level4_servicio('+id+')" class="btn btn-default btn-lg btntax">'+nombre+'</button>';
                }
                return htmlreturn;
            }
            
            function next_level1_servicio(){
                offset_servicio_l1 = offset_servicio_l1+7;
                level1();
            }
            
            function prev_level1_servicio(){
                offset_servicio_l1 = offset_servicio_l1-7;
                level1();
            }
            
            function level2_servicio(idpadre){
                if(offset_servicio_l2 === -1){
                    offset_servicio_l2 = 0;
                }
                $.post('ws/taxonomias.php', {op: 'taxvals',limit:limit_servicio_l2,offset:offset_servicio_l2,tax:idpadre}, function(data) {
                if(data !== 0){
                    $('#contenedor_bloques_servicios').html('');
                    var ht = '';
                    var csm = 0;
                    $.each(data, function(key, value) {
                        switch(csm){
                            case 0:
                                if(offset_servicio_l2 === 0){
                                    ht += '<button onclick="level1_servicio()" type="button" class="btn btn-default btn-lg btntax"><i class="fa fa-level-up" aria-hidden="true"></i></button>';
                                    ht += '<button type="button" onclick="level3_servicio('+value.id+',\''+idpadre+'\')" class="btn btn-default btn-lg btntax">'+value.valor+'</button>';
                                }else{
                                    ht += '<button onclick="prev_level2_servicio('+idpadre+')" type="button" class="btn btn-default btn-lg btntax"><i class="fa fa-angle-double-left" aria-hidden="true"></i></button>';
                                }
                            break;
                            
                            case 1:
                            case 2:
                            case 3:
                            case 4:
                            case 5:
                            case 6:
                            ht += '<button type="button" onclick="level3_servicio('+value.id+',\''+idpadre+'\')" class="btn btn-default btn-lg btntax">'+value.valor+'</button>'; 
                            break;
                            
                            case 7:
                                if(offset_servicio_l2 === 0){
                                    offset_servicio_l2 = offset_servicio_l2 - 1;
                                }else{
                                  ht += '<button type="button" onclick="level3_servicio('+value.id+',\''+idpadre+'\')" class="btn btn-default btn-lg btntax">'+value.valor+'</button>';  
                                }
                            break;
                            
                            case 8:
                                ht += '<button onclick="next_level2_servicio('+idpadre+')" type="button" class="btn btn-default btn-lg btntax"><i class="fa fa-angle-double-right" aria-hidden="true"></i></button>';
                            break;
                        }
                        csm = csm + 1;
                    });
                    $('#contenedor_bloques_servicios').html(ht);
                }
                }, 'json');
            }
            
            function next_level2_servicio(idpadre){
                offset_servicio_l2 = offset_servicio_l2+7;
                level2_servicio(idpadre);
            }
            
            function prev_level2_servicio(idpadre){
                offset_servicio_l2 = offset_servicio_l2-7;
                level2_servicio(idpadre);
            }
            
            function level3_servicio(idpadre,abuelo){
                if(offset_servicio_l3 === -1){
                    offset_servicio_l3 = 0;
                }
                $.post('ws/taxonomias.php', {op: 'level3',limit:limit_servicio_l3,offset:offset_servicio_l3,padre:idpadre}, function(data) {
                if(data !== 0){
                    $('#contenedor_bloques_servicios').html('');
                    var ht = '';
                    var csm = 0;
                    $.each(data, function(key, value) {
                        switch(csm){
                            case 0:
                                if(offset_servicio_l3 === 0){
                                    ht += '<button onclick="level2_servicio('+abuelo+')" type="button" class="btn btn-default btn-lg btntax"><i class="fa fa-level-up" aria-hidden="true"></i></button>';
                                    ht += '<button type="button" onclick="servtax0('+value.id_taxonomias+',\''+value.valor+'\','+idpadre+','+abuelo+')" class="btn btn-default btn-lg btntax">'+value.valor+'</button>';
                                }else{
                                    ht += '<button onclick="prev_level3_servicio('+idpadre+','+abuelo+')" type="button" class="btn btn-default btn-lg btntax"><i class="fa fa-angle-double-left" aria-hidden="true"></i></button>';
                                }
                            break;
                            
                            case 1:
                            case 2:
                            case 3:
                            case 4:
                            case 5:
                            case 6:
                            ht += '<button type="button" onclick="servtax0('+value.id_taxonomias+',\''+value.valor+'\','+idpadre+','+abuelo+')" class="btn btn-default btn-lg btntax">'+value.valor+'</button>';
                            break;
                            
                            case 7:
                                if(offset_servicio_l3 === 0){
                                    offset_servicio_l3 = offset_servicio_l3 - 1;
                                }else{
                                  ht += '<button type="button" onclick="servtax0('+value.id_taxonomias+',\''+value.valor+'\','+idpadre+','+abuelo+')" class="btn btn-default btn-lg btntax">'+value.valor+'</button>';
                                }
                            break;
                            
                            case 8:
                                ht += '<button onclick="next_level3_servicio('+idpadre+','+abuelo+')" type="button" class="btn btn-default btn-lg btntax"><i class="fa fa-angle-double-right" aria-hidden="true"></i></button>';
                            break;
                        }
                        csm = csm + 1;
                    });
                    $('#contenedor_bloques_servicios').html(ht);
                }
                }, 'json');
            }
            
            function next_level3_servicio(idpadre,abuelo){
                offset_servicio_l3 = offset_servicio_l3+7;
                level3_servicio(idpadre,abuelo);
            }
            
            function prev_level3_servicio(idpadre,abuelo){
                offset_servicio_l3 = offset_servicio_l3-7;
                level3_servicio(idpadre,abuelo);
            }
            
            function level4_servicio(idtax){
                if(offset_servicio_l4 === -1){
                    offset_servicio_l4 = 0;
                }
                $.post('ws/taxonomias.php', {op: 'taxvals',limit:limit_servicio_l4,offset:offset_servicio_l4,tax:idtax}, function(data) {
                if(data !== 0){
                    $('#contenedor_bloques_servicios').html('');
                    var ht = '';
                    var csm = 0;
                    $.each(data, function(key, value) {
                        switch(csm){
                            case 0:
                                if(offset_servicio_l4 === 0){
                                    ht += '<button onclick="level1_servicio()" type="button" class="btn btn-default btn-lg btntax"><i class="fa fa-level-up" aria-hidden="true"></i></button>';
                                    ht += '<button type="button" onclick="servtax1(\''+value.valor+'\','+idtax+')" class="btn btn-default btn-lg btntax">'+value.valor+'</button>';
                                }else{
                                    ht += '<button onclick="prev_level4_servicio('+idtax+')" type="button" class="btn btn-default btn-lg btntax"><i class="fa fa-angle-double-left" aria-hidden="true"></i></button>';
                                }
                            break;
                            
                            case 1:
                            case 2:
                            case 3:
                            case 4:
                            case 5:
                            case 6:
                            ht += '<button type="button" onclick="servtax1(\''+value.valor+'\','+idtax+')" class="btn btn-default btn-lg btntax">'+value.valor+'</button>';
                            break;
                            
                            case 7:
                                if(offset_servicio_l4 === 0){
                                    offset_servicio_l4 = offset_servicio_l4 - 1;
                                }else{
                                  ht += '<button type="button" onclick="servtax1(\''+value.valor+'\','+idtax+')" class="btn btn-default btn-lg btntax">'+value.valor+'</button>';
                                }
                            break;
                            
                            case 8:
                                ht += '<button onclick="next_level4_servicio('+idtax+')" type="button" class="btn btn-default btn-lg btntax"><i class="fa fa-angle-double-right" aria-hidden="true"></i></button>';
                            break;
                        }
                        csm = csm + 1;
                    });
                    $('#contenedor_bloques_servicios').html(ht);
                }
                }, 'json');
            }
            
            function next_level4_servicio(idtax){
                offset_servicio_l4 = offset_servicio_l4+7;
                level4_servicio(idtax);
            }
            
            function prev_level4_servicio(idtax){
                offset_servicio_l4 = offset_servicio_l4-7;
                level4_servicio(idtax);
            }
            
            function servtax0(tax,val,padre,abuelo){
                if(offset_servicio_p0 === -1){
                    offset_servicio_p0 = 0;
                }
                $.post('ws/taxonomias.php', {op: 'servtax',limit:limit_servicio_p0,offset:offset_servicio_p0,tax:tax,valor:val}, function(data) {
                if(data !== 0){
                    $('#contenedor_bloques_servicios').html('');
                    var ht = '';
                    var csm = 0;
                    $.each(data, function(key, value) {
                        switch(csm){
                            case 0:
                                if(offset_servicio_p0 === 0){
                                    ht += '<button onclick="level3_servicio('+padre+','+abuelo+')" type="button" class="btn btn-default btn-lg btntax"><i class="fa fa-level-up" aria-hidden="true"></i></button>';
                                    ht += '<button type="button" onclick="addserv0('+value.id_servicio.id+','+tax+',\''+val+'\','+padre+','+abuelo+')" class="btn btn-default btn-lg btntax">'+value.id_servicio.nombre+'<br/>S./'+value.id_servicio.precio_venta+'</button>';
                                }else{
                                    ht += '<button onclick="prev_servtax0('+tax+',\''+val+'\','+padre+','+abuelo+')" type="button" class="btn btn-default btn-lg btntax"><i class="fa fa-angle-double-left" aria-hidden="true"></i></button>';
                                }
                            break;
                            
                            case 1:
                            case 2:
                            case 3:
                            case 4:
                            case 5:
                            case 6:
                            ht += '<button type="button" onclick="addserv0('+value.id_servicio.id+','+tax+',\''+val+'\','+padre+','+abuelo+')" class="btn btn-default btn-lg btntax">'+value.id_servicio.nombre+'<br/>S./'+value.id_servicio.precio_venta+'</button>';
                            break;
                            
                            case 7:
                                if(offset_servicio_p0 === 0){
                                    offset_servicio_p0 = offset_servicio_p0 - 1;
                                }else{
                                  ht += '<button type="button" onclick="addserv0('+value.id_servicio.id+','+tax+',\''+val+'\','+padre+','+abuelo+')" class="btn btn-default btn-lg btntax">'+value.id_servicio.nombre+'<br/>S./'+value.id_servicio.precio_venta+'</button>';
                                }
                            break;
                            
                            case 8:
                                ht += '<button onclick="next_servtax0('+tax+',\''+val+'\','+padre+','+abuelo+')" type="button" class="btn btn-default btn-lg btntax"><i class="fa fa-angle-double-right" aria-hidden="true"></i></button>';
                            break;
                        }
                        csm = csm + 1;
                    });
                    $('#contenedor_bloques_servicios').html(ht);
                }
                }, 'json');
            }
            
            function next_servtax0(idtax,val,padre,abuelo){
                offset_servicio_p0 = offset_servicio_p0+7;
                servtax0(idtax,val,padre,abuelo);
            }
            
            function prev_servtax0(idtax,val,padre,abuelo){
                offset_servicio_p0 = offset_servicio_p0-7;
                servtax0(idtax,val,padre,abuelo);
            }
            
            function servtax1(val,tax){
                if(offset_servicio_p1 === -1){
                    offset_servicio_p1 = 0;
                }
                $.post('ws/taxonomias.php', {op: 'servtax',limit:limit_servicio_p1,offset:offset_servicio_p1,tax:tax,valor:val}, function(data) {
                if(data !== 0){
                    $('#contenedor_bloques_servicios').html('');
                    var ht = '';
                    var csm = 0;
                    $.each(data, function(key, value) {
                        switch(csm){
                            case 0:
                                if(offset_servicio_p1 === 0){
                                    ht += '<button onclick="level4_servicio('+tax+')" type="button" class="btn btn-default btn-lg btntax"><i class="fa fa-level-up" aria-hidden="true"></i></button>';
                                    ht += '<button type="button" onclick="addserv1('+value.id_servicio.id+','+tax+',\''+val+'\')" class="btn btn-default btn-lg btntax">'+value.id_servicio.nombre+'<br/>S./'+value.id_servicio.precio_venta+'</button>';
                                }else{
                                    ht += '<button onclick="prev_servtax1('+tax+',\''+val+'\')" type="button" class="btn btn-default btn-lg btntax"><i class="fa fa-angle-double-left" aria-hidden="true"></i></button>';
                                }
                            break;
                            
                            case 1:
                            case 2:
                            case 3:
                            case 4:
                            case 5:
                            case 6:
                            ht += '<button type="button" onclick="addserv1('+value.id_servicio.id+','+tax+',\''+val+'\')" class="btn btn-default btn-lg btntax">'+value.id_servicio.nombre+'<br/>S./'+value.id_servicio.precio_venta+'</button>';
                            break;
                            
                            case 7:
                                if(offset_servicio_p1 === 0){
                                    offset_servicio_p1 = offset_servicio_p1 - 1;
                                }else{
                                  ht += '<button type="button" onclick="addserv1('+value.id_servicio.id+','+tax+',\''+val+'\')" class="btn btn-default btn-lg btntax">'+value.id_servicio.nombre+'<br/>S./'+value.id_servicio.precio_venta+'</button>';
                                }
                            break;
                            
                            case 8:
                                ht += '<button onclick="next_servtax1('+tax+',\''+val+'\')" type="button" class="btn btn-default btn-lg btntax"><i class="fa fa-angle-double-right" aria-hidden="true"></i></button>';
                            break;
                        }
                        csm = csm + 1;
                    });
                    $('#contenedor_bloques_servicios').html(ht);
                }
                }, 'json');
            }
            
            function next_servtax1(tax,val){
                offset_servicio_p1 = offset_servicio_p1+7;
                servtax1(val,tax);
            }
            
            function prev_servtax1(tax,val){
                offset_servicio_p1 = offset_servicio_p1-7;
                servtax1(val,tax);
            }
            
            function busqueda_servicio(){
                if(offset_servicio_bus === -1){
                    offset_servicio_bus = 0;
                }
                var value = $("#txtbusser").val();
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
            
            function next_bus_servicio(){
                offset_servicio_bus = offset_servicio_bus+7;
                busqueda_servicio();
            }
            
            function prev_bus_servicio(){
                offset_servicio_bus = offset_servicio_bus-7;
                busqueda_servicio();
            }
            
            function add1s(){
                var nuevomonto = ($("#cantidad_servicio").val())+"1";
                $("#cantidad_servicio").val(nuevomonto);
            }
            
            function add2s(){
                var nuevomonto = ($("#cantidad_servicio").val())+"2";
                $("#cantidad_servicio").val(nuevomonto);
            }
            
            function add3s(){
                var nuevomonto = ($("#cantidad_servicio").val())+"3";
                $("#cantidad_servicio").val(nuevomonto);
            }
            
            function add4s(){
                var nuevomonto = ($("#cantidad_servicio").val())+"4";
                $("#cantidad_servicio").val(nuevomonto);
            }
            
            function add5s(){
                var nuevomonto = ($("#cantidad_servicio").val())+"5";
                $("#cantidad_servicio").val(nuevomonto);
            }
            
            function add6s(){
                var nuevomonto = ($("#cantidad_servicio").val())+"6";
                $("#cantidad_servicio").val(nuevomonto);
            }
            
            function add7s(){
                var nuevomonto = ($("#cantidad_servicio").val())+"7";
                $("#cantidad_servicio").val(nuevomonto);
            }
            
            function add8s(){
                var nuevomonto = ($("#cantidad_servicio").val())+"8";
                $("#cantidad_servicio").val(nuevomonto);
            }
            
            function add9s(){
                var nuevomonto = ($("#cantidad_servicio").val())+"9";
                $("#cantidad_servicio").val(nuevomonto);
            }
            
            function add0s(){
                var nuevomonto = ($("#cantidad_servicio").val())+"0";
                $("#cantidad_servicio").val(nuevomonto);
            }
            
            function addpoints(){
                var nuevomonto = ($("#cantidad_servicio").val())+".";
                $("#cantidad_servicio").val(nuevomonto);
            }
            
            function resetear_cantidad_servicio(){
                $("#cantidad_servicio").val("");
            }
            
            function regresar_lista_servicio(){
                var tax = $('#taxonomia_servicio').val();
                var val = $('#valor_taxonomia_servicio').val();
                var padre = $('#taxonomia_padre_servicio').val();
                var abuelo = $('#taxonomia_abuelo_servicio').val();
                if(parseInt(padre)>0){
                    servtax0(tax,val,padre,abuelo);
                    $("#busquedaservicio").show("fast");
                    $("#agregarservicio").hide("fast");
                }else{
                    if(parseInt(tax)>0){
                        servtax1(val,tax);
                        $("#busquedaservicio").show("fast");
                        $("#agregarservicio").hide("fast");
                    }else{
                        busqueda_servicio();
                        $("#busquedaservicio").show("fast");
                        $("#agregarservicio").hide("fast");
                    }
                }
            }
                                        
            function addserv0(idservicio,idtaxonomia,valortaxonomia,idtaxpadre,idtaxabuelo){
                $("#busquedaservicio").hide("fast");
                $("#agregarservicio").show("fast");
                $('#taxonomia_servicio').val(idtaxonomia);
                $('#valor_taxonomia_servicio').val(valortaxonomia);
                $('#taxonomia_padre_servicio').val(idtaxpadre);
                $('#taxonomia_abuelo_servicio').val(idtaxabuelo);
                $.post('ws/servicio.php', {op: 'get', id: idservicio}, function(data) {
                    if(data !== 0){
                        if(existeUrl("recursos/uploads/servicios/"+data.id+".png")){
                            $("#imgservicio").attr("src","recursos/uploads/servicios/"+data.id+".png");
                        }else{
                            $("#imgservicio").attr("src","recursos/img/logo-mini2.png");
                        }
                        $('#id_servicio').val(data.id);
                        $('#nombre_servicio').html(data.nombre);
                        $('#precio_servicio').html(data.precio_venta);
                    }
                }, 'json');
            }
            
            function addserv1(idservicio,idtaxonomia,valortaxonomia){
                $("#busquedaservicio").hide("fast");
                $("#agregarservicio").show("fast");
                $('#taxonomia_servicio').val(idtaxonomia);
                $('#valor_taxonomia_servicio').val(valortaxonomia);
                $('#taxonomia_padre_servicio').val("0");
                $('#taxonomia_abuelo_servicio').val("0");
                $.post('ws/servicio.php', {op: 'get', id: idservicio}, function(data) {
                    if(data !== 0){
                        if(existeUrl("recursos/uploads/servicios/"+data.id+".png")){
                            $("#imgservicio").attr("src","recursos/uploads/servicios/"+data.id+".png");
                        }else{
                            $("#imgservicio").attr("src","recursos/img/logo-mini2.png");
                        }
                        $('#id_servicio').val(data.id);
                        $('#nombre_servicio').html(data.nombre);
                        $('#precio_servicio').html(data.precio_venta);
                    }
                }, 'json');
            }
            
            //Funciones agregar a venta
            
            function addcartp(){
                //Reseteamos indices
                limit_producto_l1 = 9;
                offset_producto_l1 = 0;

                limit_producto_l2 = 9;
                offset_producto_l2 = 0;

                limit_producto_l3 = 9;
                offset_producto_l3 = 0;

                limit_producto_l4 = 9;
                offset_producto_l4 = 0;

                limit_producto_p0 = 9;
                offset_producto_p0 = 0;

                limit_producto_p1 = 9;
                offset_producto_p1 = 0;

                limit_producto_bus = 9;
                offset_producto_bus = 0;
                
                //Obtenemos datos venta
                var venta = $("#id_venta").val();
                var usuario = $("#id_usuario").val();
                var caja = $("#id_caja").val();
                var producto = $('#id_producto').val();
                var nombre_producto = $('#nombre_producto').html();
                var cantidad = $("#cantidad_producto").val();
                var precio = $("#precio_producto").html();
                var totaru = parseFloat(precio)*parseFloat(cantidad);
                var id_almacen = $("#almacen_venta").val();
                //Total Venta 
                var totaru_ventaru = $("#total_venta").val();
                
                //agregamos a la venta csm
                //Coche rctm muere
                if(parseInt(venta) === 0){
                    $.post('ws/venta.php', {op: 'gen', id_usuario:usuario, id_caja:caja}, function(data) {
                        if(data !== 0){
                            $('#id_venta').val(data);
                            history.pushState({}, null, "dashboard_sistema.php?id="+data);
                            $.post('ws/producto_venta.php', {op: 'addventa',id_venta:data,id_producto:producto,precio:precio,cantidad:cantidad,total:totaru,id_usuario:usuario,id_almacen:id_almacen}, function(data0) {
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
                    $.post('ws/producto_venta.php', {op: 'addventa',id_venta:venta,id_producto:producto,precio:precio,cantidad:cantidad,total:totaru,id_usuario:usuario,id_almacen:id_almacen}, function(data0) {
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
                limit_servicio_l1 = 9;
                offset_servicio_l1 = 0;

                limit_servicio_l2 = 9;
                offset_servicio_l2 = 0;

                limit_servicio_l3 = 9;
                offset_servicio_l3 = 0;

                limit_servicio_l4 = 9;
                offset_servicio_l4 = 0;

                limit_servicio_p0 = 9;
                offset_servicio_p0 = 0;

                limit_servicio_p1 = 9;
                offset_servicio_p1 = 0;

                limit_servicio_bus = 9;
                offset_servicio_bus = 0;
                
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
