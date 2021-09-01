<?php
include_once 'recursos/componentes/validador.php';
if (!isset($_COOKIE['nombre_usuario'])) {
    header('Location: index.php');
}
// else{
//     setcookie("id_usuario", "", time() - 3600);
//     setcookie("nombre_usuario", "", time() - 3600);
//     header("Location: index.php");
// }
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <title>UsqayPOS - Venta</title>
    <!-- Bootstrap 3.3.5 -->
    <link rel="stylesheet" href="recursos/adminLTE/bootstrap/css/bootstrap.min.css">
    <link href="recursos/js/plugins/datatables/jquery-datatables.css" rel="stylesheet">
    <link href="recursos/css/bootstrap-overrides.css" rel="stylesheet">
    <link href="recursos/css/jquery-ui.css" rel="stylesheet">
    <link rel="shortcut icon" type="image/x-icon" href="usqay-icon.svg">
    <meta name="mobile-web-app-capable" content="yes"/>


    <link rel="stylesheet" href="recursos/css/select2.css">
    <link rel="stylesheet" href="recursos/css/select2-bootstrap.css">
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
<?php
require_once 'nucleo/include/MasterConexion.php';
$objcon = new MasterConexion();

$almacenes = $objcon->consulta_matriz("Select * from almacen where estado_fila = 1");
$cambio = $objcon->consulta_arreglo("Select * from tipo_cambio where moneda_origen = 'USD' AND moneda_destino = 'PEN'");
$notasCredito = $objcon->consulta_matriz("
    SELECT v.id AS Venta, serie, f.id AS Numero  
    FROM venta v, factura f 
    WHERE v.estado_fila = 3 AND f.id_venta = v.id  AND f.estado_fila <> 3
    UNION
    SELECT v.id AS Venta, serie, f.id AS Numero  
    FROM venta v, boleta f 
    WHERE v.estado_fila = 3 AND f.id_venta = v.id  AND f.estado_fila <> 3");



?>
<body class="hold-transition skin-blue sidebar-mini sidebar-collapse">
<div class="wrapper">

    <!-- Main Header -->
    <header class="main-header">

        <!-- Logo -->
        <a href="index.php" class="logo">
            <!-- mini logo for sidebar mini 50x50 pixels -->
            <span class="logo-mini"><img src='recursos/img/logo-mini2.png' width="80%"></span>
            <!-- logo for regular state and mobile devices -->
            <span class="logo-lg"><img src='recursos/img/logo-mini1.png' height="45px"></span>
        </a>

        <!-- Header Navbar -->
        <nav class="navbar navbar-static-top" role="navigation" >
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

                    <?php
                        if(isset($_GET['id'])){


                            include_once('nucleo/venta.php');
                            $obj = new venta();
                            $objs = $obj->consulta_arreglo("Select * from venta where id = ".$_GET["id"]);
                            $nc = $obj->consulta_arreglo("Select * from nota_cliente where id_venta = ".$_GET["id"]);

                            $cliente = $nc['cliente'];

                            $nro = $_GET["id"];

                            $visible = 'block';


                            $tiComp = $objs['tipo_comprobante'];
                            if($objs['tipo_comprobante'] == -1){
                                $display = 'none';
                                $Select = 'block';
                            }else{
                                $display = 'block';
                                $Select = 'none';
                            }

                        }else{
                            $nro = 0;
                            $visible = 'none';
                            $Select = 'none';
                            $tiComp =0;
                            $cliente = "";
                        }
                    ?>
                    <!-- <?php if($_COOKIE["apertura"] == 1): ?>
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <h4><i class="icon fa fa-info"></i> ADVERTENCIA!</h4>
                            La fecha actual es diferente a la fecha de cierre, se recomienda aperturar la caja. <a href="montoinicial.php"> Click para aperturar</a>
                        </div>
                    <?php endif; ?>
                    <?php if($_COOKIE["vencidos"] == 1): ?>
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <h4><i class="icon fa fa-ban"></i> ADVERTENCIA!</h4>
                            Hay Productos que estan su fecha de vencimiento se acerca <a href="productos_vencidos.php">Ver Lista de Productos Vencidos</a>
                        </div>
                    <?php endif; ?> -->
                    <input type="hidden" id="id_usuario" value="<?php echo $_COOKIE["id_usuario"];?>">
                    <input type="hidden" id="id_caja" value="<?php echo $_COOKIE["id_caja"];?>">
                    <input type="hidden" id="id_venta" value="<?php echo (isset($_GET['id']) ? $_GET['id'] : '0'); ?>">
                    <input type="hidden" id="tipo_cambio" value="<?php echo $cambio["compra"];?>">
                    <input type="hidden" id="tipo_comprobante" value="<?php echo $tiComp;?>">
                    <input type="hidden" id="idNew" value="0">
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
                                <div class="panel-heading">
                                    <div class="btn-group" data-toggle="buttons">
                                        <label class="btn btn-default btn-success active btnToggleTipo" data-cls="btn-success">
                                            <input type="radio" name="tipo_op_codigo" value="true" autocomplete="off" checked> Agregar
                                        </label>
                                        <label class="btn btn-default btnToggleTipo" data-cls="btn-danger">
                                            <input type="radio" name="tipo_op_codigo" value="false" autocomplete="off"> Quitar
                                        </label>
                                    </div>
                                    (<small>Solo para scanner de código de barra</small>)
                                </div>
                                <div class="panel-body">
                                    <div id="busquedaproducto" style="display: block;">
                                        <div class="input-group" style="margin-bottom: 10px; width:100%;">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <input type="text" class="form-control" placeholder="Escribe para buscar" id="txtbusprod">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
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
                                                </div>
                                                <div class="col-md-6">
                                                    <input type="text" class="form-control" placeholder="Código de barras" id="txtCodProd" autofocus style="margin-top: 5px;">
                                                </div>
                                            </div>
                                        </div><!-- /input-group -->
                                        <div id="contenedor_bloques_productos">

                                        </div>
                                    </div>
                                    <div id="agregarproducto" class="row" style="display:none;">
                                        <div class="col-lg-4 col-xs-4">
                                            <input type="hidden" id="id_producto">
                                            <input type="hidden" id="inc_impuesto">
                                            <input type="hidden" id="taxonomia_producto">
                                            <input type="hidden" id="valor_taxonomia_producto">
                                            <input type="hidden" id="taxonomia_padre" value="0">
                                            <input type="hidden" id="taxonomia_abuelo" value="0">
                                            <img id="imgproducto" style="width:97% !important;height:150px !important;">
                                            <h3 id="nombre_producto">Nombre Producto</h3>
                                            <h3 id="stock_producto">0 en Stock</h3>
                                            <h4>S./ <span id="precio_producto">99.99</span></h4>
                                            <div class="form-group">
                                                <select id="precio_prod" name="precio_prod" class="form-control">
                                                    <option value="16">Defecto</option>
                                                </select>
                                            </div>
                                            <h1></h1>

                                            <button type="button" class="btn btn-default btn-lg btnback" id="btnregresar" onclick="regresar_lista_producto()"><i class="fa fa-reply-all" aria-hidden="true"></i></button>
                                        </div>
                                        <div class="col-lg-8 col-xs-8">
                                            <div class="input-group" style="margin-bottom: 10px;">
                                                <input type="text" class="form-control" placeholder="Cantidad" value="1" id="cantidad_producto" data-pristine="true">
                                                <span class="input-group-btn">
                                      <button class="btn btn-default" type="button" onclick="resetear_cantidad_producto()"><i class="fa fa-ban" aria-hidden="true"></i></button>
                                    </span>
                                            </div><!-- /input-group -->
                                            <button type="button" class="btn btn-default btn-lg btnadd" onclick="addCantProd(1)">1</button>
                                            <button type="button" class="btn btn-default btn-lg btnadd" onclick="addCantProd(2)">2</button>
                                            <button type="button" class="btn btn-default btn-lg btnadd" onclick="addCantProd(3)">3</button>
                                            <button type="button" class="btn btn-default btn-lg btnadd" onclick="addCantProd(4)">4</button>
                                            <button type="button" class="btn btn-default btn-lg btnadd" onclick="addCantProd(5)">5</button>
                                            <button type="button" class="btn btn-default btn-lg btnadd" onclick="addCantProd(6)">6</button>
                                            <button type="button" class="btn btn-default btn-lg btnadd" onclick="addCantProd(7)">7</button>
                                            <button type="button" class="btn btn-default btn-lg btnadd" onclick="addCantProd(8)">8</button>
                                            <button type="button" class="btn btn-default btn-lg btnadd" onclick="addCantProd(9)">9</button>
                                            <button type="button" class="btn btn-default btn-lg btnadd" onclick="addCantProd('.')">.</button>
                                            <button type="button" class="btn btn-default btn-lg btnadd" onclick="addCantProd(0)">0</button>
                                            <button type="button" class="btn btn-default btn-lg btnadd" id="btnAgregaProducto"><i class="fa fa-cart-arrow-down" aria-hidden="true"></i></button>
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
                                                <input type="text" class="form-control" placeholder="Cantidad" value="1" id="cantidad_servicio" data-pristine="true">
                                                <span class="input-group-btn">
                                      <button class="btn btn-default" type="button" onclick="resetear_cantidad_servicio()"><i class="fa fa-ban" aria-hidden="true"></i></button>
                                    </span>
                                            </div><!-- /input-group -->
                                            <button type="button" class="btn btn-default btn-lg btnadd" onclick="addCantServ(1)">1</button>
                                            <button type="button" class="btn btn-default btn-lg btnadd" onclick="addCantServ(2)">2</button>
                                            <button type="button" class="btn btn-default btn-lg btnadd" onclick="addCantServ(3)">3</button>
                                            <button type="button" class="btn btn-default btn-lg btnadd" onclick="addCantServ(4)">4</button>
                                            <button type="button" class="btn btn-default btn-lg btnadd" onclick="addCantServ(5)">5</button>
                                            <button type="button" class="btn btn-default btn-lg btnadd" onclick="addCantServ(6)">6</button>
                                            <button type="button" class="btn btn-default btn-lg btnadd" onclick="addCantServ(7)">7</button>
                                            <button type="button" class="btn btn-default btn-lg btnadd" onclick="addCantServ(8)">8</button>
                                            <button type="button" class="btn btn-default btn-lg btnadd" onclick="addCantServ(9)">9</button>
                                            <button type="button" class="btn btn-default btn-lg btnadd" onclick="addCantServ('.')">.</button>
                                            <button type="button" class="btn btn-default btn-lg btnadd" onclick="addCantServ(0)">0</button>
                                            <button type="button" class="btn btn-default btn-lg btnadd" id="btnAgregaServicio"><i class="fa fa-cart-arrow-down" aria-hidden="true"></i></button>
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
                <div style="height: 300px; overflow-y: scroll;">
                    <table class="table table-bordered table-condensed">
                        <thead>
                        <tr>
                            <th></th>
                            <th>Elemento</th>
                            <th>Und</th>
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
                        <tfoot id="tabla_cupones">
                        </tfoot>
                    </table>
                </div>
                <ul id="list_ofertas">
                </ul>
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
                            echo '<input type="hidden" id="data_impuestos" value="'.urlencode(json_encode($datos_impuestos)).'">';
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
                                <button style="font-size: 15px; font-weight: bold;" type="button" class="btn btn-default">A Pagar S./: </button>
                            </div>
                            <input style="font-size: 20px; font-weight: bold;" class="form-control"  value='0.00' step='0.01' id="apagar_venta" readonly>
                        </div>
                    </div>
                    <?php
                        if($_COOKIE['tipo_usuario'] == '4'){
                            $ocultar = 'none';
                            $display = 'none';
                        }else{
                            $ocultar = 'inline-block';
                            if($tiComp == -1){
                                $display = 'none';
                                $Select = 'block';
                            }else{
                                $display = 'block';
                                $Select = 'none';
                            }

                        }

                    ?>

                    <div class="col-lg-6 col-xs-6" style="display:<?php echo $ocultar ?>">
                        <input type="hidden" id="documento_id" name="documento_id" >
                        <div class="input-group">
                            <div class="input-group-btn" >
                                <button type="button" class="btn btn-default">DNI/RUC: </button>
                            </div>
                            <input class="form-control" id="documento_venta" autocomplete="off">
                            <!-- <div class="input-group-btn" >
                                <button type="button" id="sunat" class="btn btn-default">
                                    <img id="imgcliente" width="30" height="20" src="recursos/img/sunat.png">
                                </button>
                            </div> -->
                        </div>
                        <div class="input-group" style="margin-top: 2px;">
                            <div class="input-group-btn">
                                <button type="button" class="btn btn-default">Cliente: </button>
                            </div>
                            <input class="form-control" id="cliente_venta" autocomplete="off">
                        </div>
                        <div class="input-group" style="margin-top: 2px;">
                            <div class="input-group-btn">
                                <button type="button" class="btn btn-default">Direccion: </button>
                            </div>
                            <input class="form-control" id="direccion_venta" autocomplete="off">
                        </div>
                        <div class="input-group" style="margin-top: 2px;">
                            <div class="input-group-btn">
                                <button type="button" class="btn btn-default">Fecha Nac.: </button>
                            </div>
                            <input class="form-control" id="fecha_venta" placeholder="(Opcional)">
                        </div>
                        <div class="input-group" style="margin-top: 2px;">
                            <div class="input-group-btn">
                                <button type="button" class="btn btn-default">Email: </button>
                            </div>
                            <input class="form-control" id="correo" autocomplete="off" placeholder="(Opcional)">
                        </div>
                    </div>
                </div>
                <hr/>
                <center>

                    <h3 id="txtVentaid" >VENTA #<b id="txtVenta"><?php echo $nro ?></b></h3>
                    <!-- <input type="text" name="txtVenta" id="txtVenta" value="<?php echo $nro ?>"> -->

                    <!-- <div class="input-group" style="margin-top: 2px;">
                        <input class="form-control" placeholder="Ingrese el cliente y presione Enter" value="<?php echo $cliente; ?>" id="nombre_cliente_venta">
                        <div class="input-group-btn">
                            <button type="button" id="nombre_cliente_ventaGO" class="btn btn-primary">Guardar</button>
                        </div>
                    </div>
                    <br> -->

                    <!-- <button type="button" class="btn btn-warning btn-lg" id="btnprecuenta" style="display:<?php echo $ocultar ?>">Pre Cuenta <i class="fa fa-caret-square-o-right" aria-hidden="true"></i></button> -->
                    <div id="content">
                    <button type="button" class="btn btn-primary btn-lg" style="display:<?php echo $ocultar ?>" id="btnNotaVenta"><b>[F2]</b>Ticket <i class="fa fa-caret-square-o-right" aria-hidden="true" ></i></button>
                    <button type="button" class="btn btn-primary btn-lg" style="display:<?php echo $ocultar ?>" id="btnBoleta"><b>[F4]</b>Boleta <i class="fa fa-caret-square-o-right" aria-hidden="true" ></i></button>
                    <button type="button" class="btn btn-primary btn-lg" style="display:<?php echo $ocultar ?>" id="btnFactura"><b>[F7]</b>Factura <i class="fa fa-caret-square-o-right" aria-hidden="true" ></i></button>
                    </div>
                    <div id="content" style="margin-top: 10px;">
                    <button type="button" class="btn btn-secondary btn-lg" style="display:<?php echo $display ?>" id="btnCredito"><b>[F10]</b>Credito <i class="fa fa-caret-square-o-right" aria-hidden="true"></i></button>
                    </div>
                    <div id="content" style="margin-top: 10px;">
                    <button type="button" class="btn btn-danger btn-lg" id="btnAnularVenta">Anular Venta <i class="fa fa-trash-o" aria-hidden="true"></i></button>
                    <button type="button" class="btn btn-success btn-lg" id="btnNewVenta">Nueva Venta <i class="fa fa-refresh" aria-hidden="true"></i></button>
                    </div>
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
                            Generar Impresión 
                        </label>
                        <!-- <label>
                            <input type="checkbox" id="descargar" <?php
                            if(isset($_COOKIE["descargar"])){
                                if(intval($_COOKIE["descargar"]) === 1){
                                    echo 'checked';
                                }
                            }
                            ?> onchange="cambia_descarga()">
                            Generar Descarga
                        </label> -->
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
                    <!-- <span><b>Pulse la tecla P para activar los metodos de pagos</b></span> -->
                    <p></p>
                    <div class="row">
                        <div class="col-lg-4 col-xs-4">
                            <select class="form-control" id="metodo">
                                <option value=''>--</option>
                                <option value='EFECTIVO'>Efectivo</option>
                                <option value='VISA'>Visa</option>
                                <option value='MASTERCARD'>MasterCard</option>
                                <option value='CREDITO' style="display:<?php echo $Select;?>">Credito</option>
                                <!-- <option value='NOTACREDITO'>Nota de Credito</option> -->
                            </select>
                        </div>
                        <div class="col-lg-4 col-xs-4" id ="DivMoneda">
                            <select class="form-control" id="moneda">
                                <option value='PEN'>Soles</option>
                                <!-- <option value='USD'>Dólares</option> -->
                            </select>
                        </div>
                        <div class="col-lg-4 col-xs-4" id ="DivNotaCredito" >
                            <select class="form-control" id="listNotaCredito">
                                <option value="0">Comprobantes</option>
                                <?php 
                                if (is_array($notasCredito)):
                                    foreach ($notasCredito as $nc):
                                        ?>
                                        <option value="<?php echo $nc["Venta"]; ?>"><?php echo $nc["serie"]."-".$nc["Numero"]?></option>
                                        <?php
                                    endforeach;
                                endif; 
                                ?>
                            </select>
                        </div>
                        <div class="col-lg-4 col-xs-4">
                            <div class="input-group">
                                <input type="hidden" class="form-control"id="id_venta_nota">
                                <input type="text" class="form-control" placeholder="Monto" id="txtmontopago">
                                <span class="input-group-btn">
                              <button class="btn btn-primary" type="button" id="btnAgregarPago">Agregar</button>
                            </span>
                            </div>
                        </div>
                    </div>
                    <p></p>
                    <div class="row">
                        <div class="col-md-12">
                            <!-- <div class="row"> -->
                                <div class="col-md-2 col-xs-2">
                                    <p id="entregadolbl"><b>¿Por Entregar?</b></p>
                                </div>
                                <div class="col-md-1 col-xs-1">
                                    <label><input type="checkbox" id="entregado"></label>
                                </div>
                                <div id="Divabonado" class="col-md-2 col-xs-2">
                                    <p id="abonadolbl"><b>Parte Abonada</b></p>
                                </div>
                                <div id="Divabonado" class="col-md-2 col-xs-2">
                                    <label><input class="form-control" type="text" id="abonado" value="0"></label>
                                </div>
                                <div class="col-md-2 col-xs-2">
                                    <p id="fecha_entregalbl"><b>Fecha Entrega</b></p>
                                </div>
                                <div class="col-md-3 col-xs-3">
                                    <label><input placeholder="YYYY/MM/DD" class="form-control" type="text" id="fecha_entrega"></label>
                                </div>
                                <!-- <input type="" name=""> -->
                            <!-- </div> -->
                        </div>

                        <div class="col-md-12">
                            <!-- <div class="row"> -->
                                <div class="col-md-5 col-xs-5">
                                    <p id="cliente_entregalbl"><b>Nombre Del Cliente</b></p>
                                </div>
                                <div class="col-md-7 col-xs-7">
                                    <label><input class="form-control" type="text" id="cliente_entrega"></label>
                                </div>
                                <input class="form-control" placeholder="Escriba el asunto de la entrega" type="text" id="comentario_entrega">
                            <!-- </div> -->
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-12 col-xs-12">
                            <table class="table table-bordered" id="tableMedio">
                                <thead>
                                <tr><th>Medio</th>
                                    <th>Moneda</th>
                                    <th>Monto</th>
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

                            <div id="MontoCredito">
                                <span style="text-align: left; font-size: 18px;font-weight: bold;">Limite de Credito: S./ </span><span id="montoCred" style="text-align: left; font-size: 18px;font-weight: bold; color:green;">88.88</span><br/>
                                <span style="text-align: left; font-size: 18px;font-weight: bold; ">Credito Consumido: S./ </span><span id="totalConsumo" style="text-align: left; font-size: 18px;font-weight: bold; color:red;">55.55</span><br/>
                                <span style="text-align: left; font-size: 18px;font-weight: bold; ">Fecha Limite de Pago: </span><span id="FechaLimite" style="text-align: left; font-size: 18px;font-weight: bold;;">YYYY/MM/DD</span><br/>
                            </div>
                        </div>
                    </div>
                    <hr/>
                    <h3>Orden De Compra / Guia Remitente / Condicion de Pago</h3>
                    <p></p>
                    <div class="row" id="detallescomprobante">
                        <div class="col-lg-6 col-xs-6">
                            <input type="text" class="form-control" placeholder="Orden De Compra" id="txtoc">
                        </div>
                        <div class="col-lg-6 col-xs-6">
                            <input type="text" class="form-control" placeholder="Guia Remitente" id="txtgm">
                        </div>
                        <div class="col-lg-8 col-xs-8" style="margin-top: 5px;">
                            <input type="text" class="form-control" placeholder="Condicion de Pago" id="txtcp">
                        </div>
                        <div class="col-lg-4 col-xs-4" style="margin-top: 5px;">
                            <select class="form-control" id="fechadevencimiento">
                                <option value='0'>FECHA DE VENC</option>
                                <option value='7'>7 DIAS</option>
                                <option value='15'>15 DIAS</option>
                                <option value='30'>30 DIAS</option>
                                <option value='45'>45 DIAS</option>
                                <option value='60'>60 DIAS</option>
                            </select>
                        </div>
                    </div>
                    <hr/>
                    <div class="row">
                        <div class="col-lg-6 col-xs-6" style="text-align: left; font-size: 18px;font-weight: bold;">
                            Por Pagar: S./<span id="por_pagar_pago">0.00</span>
                            <input type="hidden" id="por_pagar_value">
                        </div>
                        <div class="col-lg-6 col-xs-6" style="text-align: right; font-size: 18px;font-weight: bold;">
                            Vuelto: <span id="vuelto_soles" class="text-green">0</span><br> <span id="vuelto_usd" class="text-green">0</span>
                        </div>
                    </div>


                </div>
                <div class='modal-footer'>
                    <button type='button' class='btn btn-default' data-dismiss='modal'>Regresar</button>
                    <button type='button' class='btn btn-success' id="btnPagar"><b>[+]</b>Pagar</button>
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
    <script src="recursos/js/select2.min.js"></script>
    <script src="recursos/js/moments.js"></script>
    <script src="recursos/js/bootstrap-datetimepicker.min.js"></script>


    <script src="recursos/js/pantalla_teclado.js"></script>

    <script>
        //Funciones Navegacion Producto
        // Se modificó ws/taxonomia.php, ws/taxonomias.php ws/producto_venta.php

        //generar impresion o no
        function cambia_impresion(){
            $.post('ws/venta.php', {op: 'cambiaimpresion'}, function(data) {
                if(data !== 0){
                    //Nel prro :v
                }
            }, 'json');
        }

        function cambia_descarga(){
            $.post('ws/venta.php', {op: 'cambiadescarga'}, function(data) {
                if(data !== 0){
                    //Nel prro :v
                }
            }, 'json');
        }
        //Ashuda
        function existeUrl(url) {
            return $.post('ws/almacen.php', {op: 'verifica', ruta:url}, function(data) {
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
        var idEdit;
        var valor = "";

        $(document).ready(function() {

            // document.addEventListener("keydown", keyDown, false);


            $("#btnPagar").prop( "disabled", false );

            $('#abonadolbl').hide();
            $('#abonado').hide();
            $('#fecha_entregalbl').hide();
            $('#fecha_entrega').hide();
            $('#cliente_entregalbl').hide();
            $('#comentario_entrega').hide();
            $('#cliente_entrega').hide();
            $('#Divabonado').hide();


            $("#entregado").change(function() {
                if(this.checked) {
                    if($('#tipo_comprobante').val() == -1){
                        $('#abonadolbl').show();
                        $('#Divabonado').show();
                        $('#abonado').prop("enabled", true);
                        $('#abonado').show();
                    }
                    $('#fecha_entregalbl').show();
                    $('#fecha_entrega').show(); 
                    $('#cliente_entregalbl').show();
                    $('#cliente_entrega').show();
                    $('#comentario_entrega').show();
                    $('#fecha_entrega').datepicker({dateFormat: 'yy-mm-dd',
                        changeMonth: true,
                        changeYear: true
                    });


                    if($("#cliente_venta").val() != ""){
                        // $("#cliente_entrega").prop("disabled", true);
                        $("#cliente_entrega").val($("#cliente_venta").val());
                    }
                }else{
                    $('#abonadolbl').hide();
                    $('#abonado').hide();
                    $('#fecha_entregalbl').hide();
                    $('#fecha_entrega').hide();
                    $('#cliente_entregalbl').hide();
                    $('#comentario_entrega').hide();
                    $('#Divabonado').hide();
                    $('#cliente_entrega').hide();
                }
            });                 

            $("#fechadevencimiento").prop("disabled", true);
            

            $('#sunat').on('click', function(){
                window.open('http://www.sunat.gob.pe/cl-ti-itmrconsruc/FrameCriterioBusquedaMovil.jsp','popup', 'width=800px,height=600px')
            });

            $('#modal_pago').on('hidden.bs.modal', function (e) {
                
                $("#entregado").prop('checked', false); 
                // $('#abonado').prop("enabled", true);

                $('#abonadolbl').hide();
                $('#abonado').hide();
                $('#fecha_entregalbl').hide();
                $('#fecha_entrega').hide();
                $('#cliente_entregalbl').hide();
                $('#Divabonado').hide();
                $('#cliente_entrega').hide();

                if($('#montoCred').html() != ""){
                    $.post('ws/cliente.php', {
                        op: 'CancelPayCredit',
                        idcliente: $('#documento_id').val(),
                        monto: $('#total_pago').html()
                    }, function(oResponse) {
                        console.log(oResponse);
                    });
                }
            })
             $('#precio_prod').on('change', function(){
                let prod_precio = $('#precio_prod option:selected').text();
                let pre = prod_precio.split("-");
                let idNew = pre[1].replace("[", "");
                idNew = idNew.replace("]", "");
                $('#btnregresar').hide();

                $.ajax({
                    type: 'POST',
                    url: 'ws/producto.php',
                    dataType: "json",
                    data: { 
                        op: 'get_precio', 
                        id: parseInt(idNew)
                    },
                    success:function(data) {
                        $("#cantidad_producto").val(data.cantidad);
                        let precio = (data.precio_venta / data.cantidad).toFixed(5);
                        $("#precio_producto").html(precio);
                        $('#nombre_producto').html(data.descripcion);
                        $('#idNew').val(idNew);
                        $('#btnregresar').show();
                    }
                });

                // $('#precio_producto').html($(this).val());
            });



            // alert($('#id_venta').val());
            if($('#id_venta').val() == 0){
                $('#txtVentaid').hide();
            }else{
                $.post('ws/venta.php', {
                    op: 'buscarCliente',
                    id: $('#id_venta').val()
                }, function (data) {
                    if(data != 0){
                        var mdata = JSON.parse(data);
                        // console.log(mdata);

                        $("#documento_id").val(mdata.id);
                        $("#documento_venta").val(mdata.documento);
                        $("#cliente_venta").val(mdata.nombre);
                        $("#direccion_venta").val(mdata.direccion);
                        $("#fecha_venta").val(mdata.fecha_nacimiento);
                        $("#correo").val(mdata.correo);
                        // location.reload()
                    }
                });

                $.post('ws/venta.php', {
                    op: 'verificar',
                    id: $('#id_venta').val()
                }, function (data) {
                    if(data == 0){
                        var mdata = JSON.parse(data);
                        // alert(data);
                        // console.log(mdata);
                        $("#btnBoleta").hide();
                        $("#btnFactura").hide();
                        // location.reload()
                    }
                });


            }

            var pantalla = new Pantalla();

            level1();


            level1_servicio();

            $("#txtbusprod").change(function() {

                busqueda_producto();
            });

            $("#txtbusser").change(function() {
                busqueda_servicio();
            });
            $("#DivNotaCredito").hide();
            $("#listNotaCredito").select2();
            $('#metodo').change(function(){
                if($("#metodo").val() == "NOTACREDITO"){
                    $('#DivMoneda').hide();
                    $("#DivNotaCredito").show();
                    $("#txtmontopago").prop("disabled", true);
                }else{
                    $("#DivNotaCredito").hide();
                    $('#DivMoneda').show();
                    $("#txtmontopago").prop("disabled", false);
                }
                $('#txtmontopago').focus().select()
            });

            $("#listNotaCredito").change(function(){
                var id = $("#listNotaCredito").val();

                $.post('ws/venta.php', {
                    op: 'totalNotaCredito',
                    id: id
                }, function (data) {
                    var mdata = JSON.parse(data);
                    $("#txtmontopago").val(mdata.total);
                    $("#id_venta_nota").val(id);
                });
            });



            $('#fecha_venta').datepicker({dateFormat: 'yy-mm-dd',
                changeMonth: true,
                changeYear: true
            });

            <?php if(isset($_GET["id"])):?>
            pantalla.getDetalleVenta();
            <?php endif;?>

            $('#documento_venta').change(function() {
                pantalla.getCliente();
            });

            $("#btnAgregaProducto").click(function () {
                pantalla.agregarItem(Pantalla.PRODUCTO);
                $('#idNew').val("0");
                $("#btnAgregaProducto").prop("disabled", true);
            });

            $("#btnAgregaServicio").click(function () {
                pantalla.agregarItem(Pantalla.SERVICIO);
            });


            $("#btnprecuenta").click(function(){

                pantalla.preCuenta();
            });

            $("#btnNotaVenta").click(function () {
                pantalla.notaVenta();
            });

            $("#btnBoleta").click(function () {
                pantalla.boleta();
            });

            $("#btnFactura").click(function () {
                pantalla.factura();
            });

            $("#btnCredito").click(function(){
                pantalla.credito();
            });

            $("#btnAgregarPago").click(function () {
                
                $("#btnAgregarPago").prop( "disabled", true );
                pantalla.agregarPago();
            });

            $("#descuento_venta").change(function() {
                pantalla.calcularDescuento();
            });

            $(document).on('click', '.btnEliminaPago', function () {
                var id = $(this).attr("id");
                pantalla.eliminarPago(id);
            });

            $(document).on('click', '.btnEliminaPagoCheck', function () {
                var id = $(this).attr("id");
                pantalla.eliminarPagoCheck(id);
            });

            /**
             * Controles de producto
             * */
            $(document).on('click', '.btnSumarProducto', function () {
                var id = $(this).attr("id");
                pantalla.cantidadItem(id, Pantalla.PRODUCTO, +1);
            });

            $(document).on('click', '.btnRestarProducto', function () {
                var id = $(this).attr("id");
                pantalla.cantidadItem(id, Pantalla.PRODUCTO, -1);
            });

            $(document).on('click', '.btnEliminarProducto', function () {
                var id = $(this).attr("id");
                pantalla.eliminarItem(id, Pantalla.PRODUCTO);
            });

            if(!isMobile()){
                $(document).on('dblclick', '#tablaventa tr #cantidadEdit', function () {
                    var myRole = <?php echo $_COOKIE["tipo_usuario"]?>;
                    if(myRole < 4 ){
                        var celda = $(this);
                        idEdit = $(this).attr("name");
                        // alert(idEdit);
                        $(celda).replaceWith('<td><input type="text" id="cantidadEdit" ></td>');
                        // $(celda).replaceWith('<td><input type="text" id="cantidadEdit" value='+celda.html()+'></td>');
                        $("#cantidadEdit").focus();
                    }

                });

                $(document).on('dblclick', '#tablaventa tr #cantidadEditServicio', function () {
                    var myRole = <?php echo $_COOKIE["tipo_usuario"]?>;
                    if(myRole == 1){
                        var celda = $(this);
                        idEdit = $(this).attr("name");
                        // alert(idEdit);
                        // $(celda).replaceWith('<td><input type="text" id="cantidadEditServicio" value='+celda.html()+'></td>');

                        $(celda).replaceWith('<td><input type="text" id="cantidadEditServicio"></td>');


                        $("#cantidadEditServicio").focus();
                    }
                });

                $(document).on('dblclick', '#tablaventa tr #precioEdit', function () {
                    role = <?php echo $_COOKIE["tipo_usuario"]?>;

                    if(role == 1){
                        var celda = $(this);
                        idEdit = $(this).attr("name");
                        // alert(idEdit);
                        // $(celda).replaceWith('<input type="text" onkeypress="editprecio(event)" id="precio">');

                        $(celda).replaceWith('<td><input type="number" id="precio"></td>');
                        $("#precio").focus();

                    }


                });


                $(document).on('dblclick', '#tablaventa tr #totalEdit', function () {
                    role = <?php echo $_COOKIE["tipo_usuario"]?>;

                    if(role == 1){
                        var celda = $(this);
                        idEdit = $(this).attr("name");
                        // alert(idEdit);
                        // $(celda).replaceWith('<input type="text" onkeypress="editprecio(event)" id="precio">');

                        $(celda).replaceWith('<td><input type="number" id="totalItems"></td>');
                        $("#totalItems").focus();

                    }


                });
                $(document).on('dblclick', '#tablaventa tr #precioEditServicio', function () {
                    var myRole = <?php echo $_COOKIE["tipo_usuario"]?>;
                    if ( myRole == 1 ) {
                        var celda = $(this);
                        idEdit = $(this).attr("name");
                        //alert(idEdit);
                        // $(celda).replaceWith('<input type="text" onkeypress="editprecioservicio(event)" id="precioServicio">');

                        $(celda).replaceWith('<td><input type="text" id="precioServicio"></td>');
                        $("#precioServicio").focus();

                    }


                });
            }else{
                // alert(isMobile());
                var timeout;
                var lastTap = 0;
                $(document).on('touchend', "#tablaventa tr #cantidadEdit" ,function(event) {
                    var currentTime = new Date().getTime();
                    var tapLength = currentTime - lastTap;
                    clearTimeout(timeout);
                    if (tapLength < 500 && tapLength > 0) {
                        var myRole = <?php echo $_COOKIE["tipo_usuario"]?>;
                        if(myRole == 1){
                            var celda = $(this);
                            idEdit = $(this).attr("name");
                            // alert(idEdit);
                            $(celda).replaceWith('<td><input type="text" id="cantidadEdit" value='+celda.html()+'></td>');
                        }
                    }
                    lastTap = currentTime;
                });

                $(document).on('touchend', "#tablaventa tr #cantidadEditServicio" ,function(event) {
                    var currentTime = new Date().getTime();
                    var tapLength = currentTime - lastTap;
                    clearTimeout(timeout);
                    if (tapLength < 500 && tapLength > 0) {
                        var myRole = <?php echo $_COOKIE["tipo_usuario"]?>;
                        if(myRole == 1){
                            var celda = $(this);
                            idEdit = $(this).attr("name");
                            // alert(idEdit);
                            $(celda).replaceWith('<td><input type="text" id="cantidadEditServicio" value='+celda.html()+'></td>');
                        }
                    }
                    lastTap = currentTime;
                });

                $(document).on('touchend', "#tablaventa tr #precioEdit" ,function(event) {
                    var currentTime = new Date().getTime();
                    var tapLength = currentTime - lastTap;
                    clearTimeout(timeout);
                    if (tapLength < 500 && tapLength > 0) {
                        role = <?php echo $_COOKIE["tipo_usuario"]?>;

                        if(role == 1){
                            var celda = $(this);
                            idEdit = $(this).attr("name");
                            // alert(idEdit);
                            // $(celda).replaceWith('<input type="text" onkeypress="editprecio(event)" id="precio">');

                            $(celda).replaceWith('<td><input type="text" id="precio"></td>');
                        }
                    }
                    lastTap = currentTime;
                });

                $(document).on('touchend', "#tablaventa tr #precioEdit" ,function(event) {
                    var currentTime = new Date().getTime();
                    var tapLength = currentTime - lastTap;
                    clearTimeout(timeout);
                    if (tapLength < 500 && tapLength > 0) {
                        var myRole = <?php echo $_COOKIE["tipo_usuario"]?>;
                        if ( myRole == 1 ) {
                            var celda = $(this);
                            idEdit = $(this).attr("name");
                            //alert(idEdit);
                            // $(celda).replaceWith('<input type="text" onkeypress="editprecioservicio(event)" id="precioServicio">');

                            $(celda).replaceWith('<td><input type="text" id="precioServicio"></td>');
                        }
                    }
                    lastTap = currentTime;
                });
            }

            $(document).on('keydown', '#tablaventa tr td #cantidadEdit', function(e){
                tecla = (document.all) ? e.keyCode : e.which;
                //alert(tecla);
                    switch(tecla) {
                        
                        case 8:
                            break;
                        case 48:
                            valor = valor+'0';
                            break;
                        case 49:
                            valor = valor+'1';
                            break;
                        case 50:
                            valor = valor+'2';
                            break;
                        case 51:
                            valor = valor+'3';
                            break;
                        case 52:
                            valor = valor+'4';
                            break;
                        case 53:
                            valor = valor+'5';
                            break;
                        case 54:
                            valor = valor+'6';
                            break;
                        case 55:
                            valor = valor+'7';
                            break;
                        case 54:
                            valor = valor+'6';
                            break;
                        case 55:
                            valor = valor+'7';
                            break;
                        case 56:
                            valor = valor+'8';
                            break;
                        case 57:
                            valor = valor+'9';
                            break;
                        case 13:
                            break;
                        case 190:
                            valor = valor+'.';
                        break;
                        case 96:
                            valor = valor+'0';
                        break;
                        case 97:
                            valor = valor+'1';
                        break;
                        case 98:
                            valor = valor+'2';
                        break;
                        case 99:
                            valor = valor+'3';
                        break;
                        case 100:
                            valor = valor+'4';
                        break;
                        case 101:
                            valor = valor+'5';
                        break;
                        case 102:
                            valor = valor+'6';
                        break;
                        case 103:
                            valor = valor+'7';
                        break;
                        case 104:
                            valor = valor+'8';
                        break;
                        case 105:
                            valor = valor+'9';
                        break;
                        case 110:
                            valor = valor+'.';
                        break;
                        default:
                            // alert("Introduzca un numero");
                            return false;
                            break;
                    }

                    // alert($('cantidadEdit').val());
                    if (tecla == 13) {
                        if(parseFloat(valor) > 0 && valor != ""){
                            $.post('ws/venta_medio_pago.php', {
                                op: 'editarCantidad',
                                prod: idEdit,
                                cantidad: parseFloat(valor)
                            }, function (data) {
                                location.reload()
                            });
                        }else{
                            alert("La cantidad no puede estar vacia o ser cero");
                            $("#cantidadEdit").focus();
                        }
                    }

            });

            $(document).on('keydown', '#tablaventa tr td #totalItems', function (e) {
                tecla = (document.all) ? e.keyCode : e.which;
                // alert(tecla);
                    switch(tecla) {
                        case 8:
                            break;
                        case 48:
                            valor = valor+'0';
                            break;
                        case 49:
                            valor = valor+'1';
                            break;
                        case 50:
                            valor = valor+'2';
                            break;
                        case 51:
                            valor = valor+'3';
                            break;
                        case 52:
                            valor = valor+'4';
                            break;
                        case 53:
                            valor = valor+'5';
                            break;
                        case 54:
                            valor = valor+'6';
                            break;
                        case 55:
                            valor = valor+'7';
                            break;
                        case 54:
                            valor = valor+'6';
                            break;
                        case 55:
                            valor = valor+'7';
                            break;
                        case 56:
                            valor = valor+'8';
                            break;
                        case 57:
                            valor = valor+'9';
                            break;
                        case 13:
                            break;
                        case 190:
                            valor = valor+'.';
                            break;
                            case 96:
                            valor = valor+'0';
                        break;
                        case 97:
                            valor = valor+'1';
                        break;
                        case 98:
                            valor = valor+'2';
                        break;
                        case 99:
                            valor = valor+'3';
                        break;
                        case 100:
                            valor = valor+'4';
                        break;
                        case 101:
                            valor = valor+'5';
                        break;
                        case 102:
                            valor = valor+'6';
                        break;
                        case 103:
                            valor = valor+'7';
                        break;
                        case 104:
                            valor = valor+'8';
                        break;
                        case 105:
                            valor = valor+'9';
                        break;
                        case 110:
                            valor = valor+'.';
                        break;
                        default:
                            // alert("Introduzca un numero");
                            return false;
                            break;
                    }
                    if (tecla == 13) {
                        if(parseFloat(valor) > 0 && valor != ""){
                            $.post('ws/venta_medio_pago.php', {
                                op: 'totalItems',
                                prod: idEdit,
                                precio: parseFloat(Math.abs(valor))
                            }, function (data) {
                                location.reload()
                            });
                        }else{
                            alert("La cantidad no puede estar vacia o ser cero");
                            $("#totalItems").focus();
                        }
                    }

            });


            $(document).on('keydown', '#tablaventa tr td #cantidadEditServicio', function(e){
                tecla = (document.all) ? e.keyCode : e.which;
                    switch(tecla) {
                        case 8:
                            break;
                        case 48:
                            valor = valor+'0';
                            break;
                        case 49:
                            valor = valor+'1';
                            break;
                        case 50:
                            valor = valor+'2';
                            break;
                        case 51:
                            valor = valor+'3';
                            break;
                        case 52:
                            valor = valor+'4';
                            break;
                        case 53:
                            valor = valor+'5';
                            break;
                        case 54:
                            valor = valor+'6';
                            break;
                        case 55:
                            valor = valor+'7';
                            break;
                        case 54:
                            valor = valor+'6';
                            break;
                        case 55:
                            valor = valor+'7';
                            break;
                        case 56:
                            valor = valor+'8';
                            break;
                        case 57:
                            valor = valor+'9';
                            break;
                        case 13:
                            break;
                        case 190:
                            valor = valor+'.';
                            break;
                            case 96:
                            valor = valor+'0';
                            break;
                            case 97:
                                valor = valor+'1';
                            break;
                            case 98:
                                valor = valor+'2';
                            break;
                            case 99:
                                valor = valor+'3';
                            break;
                            case 100:
                                valor = valor+'4';
                            break;
                            case 101:
                                valor = valor+'5';
                            break;
                            case 102:
                                valor = valor+'6';
                            break;
                            case 103:
                                valor = valor+'7';
                            break;
                            case 104:
                                valor = valor+'8';
                            break;
                            case 105:
                                valor = valor+'9';
                            break;
                            case 110:
                                valor = valor+'.';
                            break;
                        default:
                            // alert("Introduzca un numero");
                            return false;
                            break;
                    }
                    if (tecla == 13) {
                        $.post('ws/venta_medio_pago.php', {
                            op: 'editarCantidadServicio',
                            prod: idEdit,
                            cantidad: valor
                        }, function (data) {
                            location.reload()
                        });
                    }

                // if (e.which == 1p3) {

                //     $.post('ws/venta_medio_pago.php', {
                //         op: 'editarCantidadServicio',
                //         prod: idEdit,
                //         cantidad: value
                //     }, function (data) {
                //         location.reload()
                //     });
                // }

            });


            $(document).on('click', '#nombre_cliente_ventaGO', function () {
                var value = $('#nombre_cliente_venta').val();
                if( $('#id_venta').val() == 0){
                    alert("Para ingresar el Cliente debe agregar un Producto a la Venta");
                }else{
                    $.post('ws/venta.php', {
                        op: 'cliente_nota',
                        id_venta: $('#id_venta').val(),
                        cliente: value
                    }, function (data) {
                        // location.reload()
                    });
                }
            });



            $(document).on('keydown', '#nombre_cliente_venta', function(e){
                var value = $(this).val();
                // alert( $('#id_venta').val());
                if( $('#id_venta').val() == 0){
                    alert("Para ingresar el Cliente debe agregar un Producto a la Venta");
                }else{
                    if (e.which == 13) {
                        $.post('ws/venta.php', {
                            op: 'cliente_nota',
                            id_venta: $('#id_venta').val(),
                            cliente: value
                        }, function (data) {
                            // location.reload()
                        });
                    }
                }


            });



            $(document).on('keydown', '#tablaventa tr td #precio', function (e) {
                tecla = (document.all) ? e.keyCode : e.which;
                // alert(tecla);
                    switch(tecla) {
                        case 8:
                            break;
                        case 48:
                            valor = valor+'0';
                            break;
                        case 49:
                            valor = valor+'1';
                            break;
                        case 50:
                            valor = valor+'2';
                            break;
                        case 51:
                            valor = valor+'3';
                            break;
                        case 52:
                            valor = valor+'4';
                            break;
                        case 53:
                            valor = valor+'5';
                            break;
                        case 54:
                            valor = valor+'6';
                            break;
                        case 55:
                            valor = valor+'7';
                            break;
                        case 54:
                            valor = valor+'6';
                            break;
                        case 55:
                            valor = valor+'7';
                            break;
                        case 56:
                            valor = valor+'8';
                            break;
                        case 57:
                            valor = valor+'9';
                            break;
                        case 13:
                            break;
                        case 190:
                            valor = valor+'.';
                            break;
                            case 96:
                            valor = valor+'0';
                        break;
                        case 97:
                            valor = valor+'1';
                        break;
                        case 98:
                            valor = valor+'2';
                        break;
                        case 99:
                            valor = valor+'3';
                        break;
                        case 100:
                            valor = valor+'4';
                        break;
                        case 101:
                            valor = valor+'5';
                        break;
                        case 102:
                            valor = valor+'6';
                        break;
                        case 103:
                            valor = valor+'7';
                        break;
                        case 104:
                            valor = valor+'8';
                        break;
                        case 105:
                            valor = valor+'9';
                        break;
                        case 110:
                            valor = valor+'.';
                        break;
                        default:
                            // alert("Introduzca un numero");
                            return false;
                            break;
                    }
                    if (tecla == 13) {
                        if(parseFloat(valor) > 0 && valor != ""){

                            $.post('ws/venta_medio_pago.php', {
                                op: 'editarPrecio',
                                prod: idEdit,
                                precio: parseFloat(Math.abs(valor))
                            }, function (data) {
                                location.reload()
                            });
                        }else{
                            alert("La cantidad no puede estar vacia o ser cero");
                            $("#precio").focus();
                        }
                    }

            });




            $(document).on('keydown', '#tablaventa tr td #precioServicio', function (e) {
                tecla = (document.all) ? e.keyCode : e.which;
                // alert(tecla);
                    switch(tecla) {
                        case 8:
                            break;
                        case 48:
                            valor = valor+'0';
                            break;
                        case 49:
                            valor = valor+'1';
                            break;
                        case 50:
                            valor = valor+'2';
                            break;
                        case 51:
                            valor = valor+'3';
                            break;
                        case 52:
                            valor = valor+'4';
                            break;
                        case 53:
                            valor = valor+'5';
                            break;
                        case 54:
                            valor = valor+'6';
                            break;
                        case 55:
                            valor = valor+'7';
                            break;
                        case 54:
                            valor = valor+'6';
                            break;
                        case 55:
                            valor = valor+'7';
                            break;
                        case 56:
                            valor = valor+'8';
                            break;
                        case 57:
                            valor = valor+'9';
                            break;
                        case 13:
                            break;
                        case 190:
                            valor = valor+'.';
                            break;
                            case 96:
                            valor = valor+'0';
                        break;
                        case 97:
                            valor = valor+'1';
                        break;
                        case 98:
                            valor = valor+'2';
                        break;
                        case 99:
                            valor = valor+'3';
                        break;
                        case 100:
                            valor = valor+'4';
                        break;
                        case 101:
                            valor = valor+'5';
                        break;
                        case 102:
                            valor = valor+'6';
                        break;
                        case 103:
                            valor = valor+'7';
                        break;
                        case 104:
                            valor = valor+'8';
                        break;
                        case 105:
                            valor = valor+'9';
                        break;
                        case 110:
                            valor = valor+'.';
                        break;
                        default:
                            // alert("Introduzca un numero");
                            return false;
                            break;
                    }
                    if (tecla == 13) {
                        $.post('ws/venta_medio_pago.php', {
                            op: 'editarPrecioServicio',
                            prod: idEdit,
                            precio: parseFloat(valor)
                        }, function (data) {
                            location.reload()
                        });
                    }

            });


            // $(document).on('keydown', '#tablaventa tr input', function(e){
            //     var value = $(this).val();
            //     alert(value);
            //     if (e.which == 13) {
            //     alert("ENTER", $("#precioEdit").val());
            //     //     $.post('ws/venta_medio_pago.php', {
            //     //         op: 'editarPrecio',
            //     //         prod: idEdit,
            //     //         precio: value
            //     //     }, function (data) {
            //     //         location.reload()
            //     //     });
            //     }

            // });

            // $(document).on('dblclick', '#cantidadEdit', function () {


                // var cantidadEdit = $("#cantidadEdit").val();
                // alert(cantidadEdit)
                // $('#cantidadEdit').replaceWith('<input type="text" id="cantidadEdit">');
            // });

            /**
             * Controles de servicio
             * */
            $(document).on('click', '.btnSumarServicio', function () {
                var id = $(this).attr("id");
                pantalla.cantidadItem(id, Pantalla.SERVICIO, +1);
            });

            $(document).on('click', '.btnRestarServicio', function () {
                var id = $(this).attr("id");
                pantalla.cantidadItem(id, Pantalla.SERVICIO, -1);
            });

            $(document).on('click', '.btnEliminarServicio', function () {
                var id = $(this).attr("id");
                pantalla.eliminarItem(id, Pantalla.SERVICIO);
            });

            /**
             *
             * */

            $("#btnPagar").click(function () {
                if($("#metodo").val()!= ""){
                    if(parseFloat($("#total_pago").html()) > 0)
                        pantalla.pagar();
                    else
                        alert("El total debe ser mayor a 0");
                }else{
                    alert("Debe elegir un Metodo de Pago");
                }
                
            });

            $("#btnNewVenta").click(function () {
                location.href = "index.php";
            });

            $("#btnAnularVenta").click(function () {
                pantalla.anularVenta();
            });

            /**
             *
             * */
            $("#txtCodProd").change(function () {
                pantalla.buscaBarcode($(this).val());
            });

            $(".btnToggleTipo").click(function () {
                var element = $(this);
                element.addClass(element.attr("data-cls"));
                element.siblings().each(function (key, value) {
                    $(this).removeClass($(this).attr("data-cls"));
                });

                $("#txtCodProd").val('').focus();
            });

            /**
             * */

            $(document).on('click', '.btnEliminaCupon', function () {
                var id = $(this).attr("id");
                pantalla.eliminaCupon(id);
            });
        });

        //Funciones Navegacion Producto

        //likin//
        var cont = 0;
        // function keyDown(e) {
        //     var keyCode = e.keyCode;
        //     // alert(keyCode);
        //     var esVisible = $("#agregarproducto").is(":visible");
        //     var esVisiblePago = $("#modal_pago").is(":visible");

        //     if(!esVisiblePago){
        //         if(keyCode==113) { // Ticket
        //             $('#btnNotaVenta').click();
        //         }else if(keyCode==115){
        //             $('#btnBoleta').click();
        //         }else if(keyCode==118){
        //             $('#btnFactura').click();
        //         }else if(keyCode==121){
        //             $('#btnCredito').click();
        //         }else if(keyCode==27){
        //             $('#btnNewVenta').click();
        //         }else{
        //             if(esVisible){
        //                 if(keyCode == 35){
        //                     addCantProd(1);
        //                 }else if(keyCode == 35 || keyCode == 49){
        //                     addCantProd(1);
        //                 }else if(keyCode == 40 || keyCode == 50){
        //                     addCantProd(2);
        //                 }else if(keyCode == 34 || keyCode == 51){
        //                     addCantProd(3);
        //                 }else if(keyCode == 37 || keyCode == 52){
        //                     addCantProd(4);
        //                 }else if(keyCode == 12 || keyCode == 53){
        //                     addCantProd(5);
        //                 }else if(keyCode == 39 || keyCode == 54){
        //                     addCantProd(6);
        //                 }else if(keyCode == 36 || keyCode == 55){
        //                     addCantProd(7);
        //                 }else if(keyCode == 38 || keyCode == 56){
        //                     addCantProd(8);
        //                 }else if(keyCode == 33 || keyCode == 57){
        //                     addCantProd(9);
        //                 }else if(keyCode == 45 || keyCode == 48){
        //                     addCantProd(0);
        //                 }else if(keyCode == 46 || keyCode == 190){
        //                     addCantProd('.');
        //                 }else if(keyCode == 13){
        //                     $('#btnAgregaProducto').click();
        //                 }
                        
        //             }
        //         }
        //     }


        //     if(esVisiblePago){
        //         // alert(keyCode);
        //         if(keyCode == 80){
        //             $('#metodo').focus().select()
        //         }

        //         // if(keyCode == 69){
        //         //     $('#txtmontopago').focus();
        //         //     $('#txtmontopago').focus(function(){
        //         //         $("#txtmontopago").val("");
        //         //     });
        //         // }

        //         if(keyCode==13){
        //             if($('#txtmontopago').val() > 0 || $('#txtmontopago').val() != "")
        //                 $('#btnAgregarPago').click();
        //             else
        //                 alert("El monto tiene que ser mayor a 0");
        //         }

        //         if(keyCode == 107 || keyCode == 187){
        //             cont++;

        //             console.log("fsdfasgvd",cont)
        //             if(cont == 1){

        //                 if($("#metodo").val()!= ""){
        //                     if(parseFloat($("#total_pago").html()) > 0){
        //                         $('#btnPagar').click();
        //                         // $("#btnPagar").prop( "disabled", true );
        //                     }
        //                     else
        //                         alert("El total debe ser mayor a 0");
        //                 }else{
        //                     alert("Debe elegir un Metodo de Pago");
        //                 }
        //             }
        //         }
                
        //     }
        // }

        //liikin

        function isMobile(){
                return (
                    (navigator.userAgent.match(/Android/i)) ||
                    (navigator.userAgent.match(/webOS/i)) ||
                    (navigator.userAgent.match(/iPhone/i)) ||
                    (navigator.userAgent.match(/iPod/i)) ||
                    (navigator.userAgent.match(/iPad/i)) ||
                    (navigator.userAgent.match(/BlackBerry/i))
                );
            }

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
                    console.log(data);
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
                }else{
                    alert("No se encuentra Productos o Servicios con la descripcion");
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
                    $.when(existeUrl("recursos/uploads/productos/"+data.id+".png")).done(function(res){
                        if(res == 1){
                            $("#imgproducto").attr("src","recursos/uploads/productos/"+data.id+".png");
                        }else{
                            $("#imgproducto").attr("src","recursos/img/logo-mini2.png");
                        }
                    });
                    let html = "<option value='"+data.precio_venta+"'>Defecto</option>";

                    if(data['precios'].length > 0){

                        for(var i=0; i < data['precios'].length; i++){
                            html += "<option value='"+data['precios'][i]['precio_venta']+"'>"+data['precios'][i]['descripcion']+"-["+data['precios'][i]['id']+"]</option>";
                        }
                        console.log(html);
                    }

                    $('#precio_prod').html(html);

                    $('#id_producto').val(data.id);
                    $('#inc_impuesto').val(data.ivgAux);
                    $('#nombre_producto').html(data.nombre);
                    $('#stock_producto').html(data.stock+" En stock");
                    $('#precio_producto').html(data.precio_venta);
                }
            }, 'json');
        }

        function addprod1(idproducto,idtaxonomia,valortaxonomia){

            $("#cantidad_producto").val(1);
            $("#txtbusprod").val("");
            $("#cantidad_producto").attr("data-pristine",true);

            $("#busquedaproducto").hide("fast");
            $("#agregarproducto").show("fast");
            $('#taxonomia_producto').val(idtaxonomia);
            $('#valor_taxonomia_producto').val(valortaxonomia);
            $('#taxonomia_padre').val("0");
            $('#taxonomia_abuelo').val("0");
            var id_almacen = $("#almacen_venta").val();
            $.post('ws/producto.php', {op: 'getventa', id: idproducto, id_almacen:id_almacen}, function(data) {
                if(data !== 0){
                    $.when(existeUrl("recursos/uploads/productos/"+data.id+".png")).done(function(res){
                        if(res == 1){
                            $("#imgproducto").attr("src","recursos/uploads/productos/"+data.id+".png");
                        }else{
                            $("#imgproducto").attr("src","recursos/img/logo-mini2.png");
                        }
                    });

                    let html = "<option value='"+data.precio_venta+"'>Defecto</option>";

                     if(data['precios'].length > 0){

                        for(var i=0; i < data['precios'].length; i++){
                            html += "<option value='"+data['precios'][i]['precio_venta']+"'>"+data['precios'][i]['descripcion']+"- ["+data['precios'][i]['id']+"]</option>";
                        }
                        console.log(html);
                    }

                     $('#precio_prod').html(html);

                    $('#id_producto').val(data.id);
                    $('#inc_impuesto').val(data.ivgAux);
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

        function addCantProd(cant) {
            var input = $("#cantidad_producto");
            var actual = input.val();
            var nuevomonto = "";

            if (input.attr("data-pristine") == 'true') {
                nuevomonto = cant;
            } else {
                nuevomonto = actual + "" + cant;
            }

            input.attr("data-pristine", false);
            input.val(nuevomonto);
        }

        function addCantServ(cant) {
            var input = $("#cantidad_servicio");
            var actual = input.val();
            var nuevomonto = "";

            if (input.attr("data-pristine") == 'true') {
                nuevomonto = cant;
            } else {
                nuevomonto = actual + "" + cant;
            }

            input.attr("data-pristine", false);
            input.val(nuevomonto);
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

            $("#cantidad_servicio").val(1);
            $("#cantidad_servicio").attr("data-pristine",true);

            $("#busquedaservicio").hide("fast");
            $("#agregarservicio").show("fast");
            $('#taxonomia_servicio').val(idtaxonomia);
            $('#valor_taxonomia_servicio').val(valortaxonomia);
            $('#taxonomia_padre_servicio').val(idtaxpadre);
            $('#taxonomia_abuelo_servicio').val(idtaxabuelo);
            $.post('ws/servicio.php', {op: 'get', id: idservicio}, function(data) {
                if(data !== 0){
                    $.when(existeUrl("recursos/uploads/servicios/"+data.id+".png")).done(function(res){
                        if(res == 1){
                            $("#imgservicio").attr("src","recursos/uploads/servicios/"+data.id+".png");
                        }else{
                            $("#imgservicio").attr("src","recursos/img/logo-mini2.png");
                        }
                    });
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
                    $.when(existeUrl("recursos/uploads/servicios/"+data.id+".png")).done(function(res){
                        if(res == 1){
                            $("#imgservicio").attr("src","recursos/uploads/servicios/"+data.id+".png");
                        }else{
                            $("#imgservicio").attr("src","recursos/img/logo-mini2.png");
                        }
                    });
                    $('#id_servicio').val(data.id);
                    $('#nombre_servicio').html(data.nombre);
                    $('#precio_servicio').html(data.precio_venta);
                }
            }, 'json');
        }
    </script>
</body>
</html>
