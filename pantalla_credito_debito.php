<?php
include_once 'recursos/componentes/validador.php';

require_once 'nucleo/include/MasterConexion.php';

$objcon = new MasterConexion();
$detraccion = $objcon->consulta_arreglo("SELECT * from configuracion");
$pocentaje = $detraccion['id_detraccion'];
$porc_detraccion =$objcon->consulta_arreglo("SELECT * from porcentaje_detraccion WHERE id = $pocentaje");

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Usqay</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, minimal-ui">
    <link rel="shortcut icon" type="image/x-icon" href="usqay-icon.svg">
    <link rel="stylesheet" href="node_modules/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/css.css">
    <link rel="stylesheet" href="assets/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="node_modules/vuetify/dist/vuetify.min.css">
    <link rel="stylesheet" href="assets/font_awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="node_modules/animate.css/animate.min.css">
    <link rel="stylesheet" href="assets/css/app.css">
</head>

<body>
    <header>

        <nav class="navbar navbar-expand-lg navbar-dark">
            <a class="navbar-brand" href="inicio.php">
                <img src="assets/imagenes/usqay_logo.png" width="120" alt="" loading="lazy">
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav mr-auto">

                </ul>
                <ul class="nav justify-content-end">
                    <li class="nav-item">
                        <a class="nav-link text-white active" href="#">
                            <?php echo $_COOKIE['nombre_usuario'] ?>
                        </a>
                    </li>
                    <li class="nav-item">
                         <a href="logout_sistema.php" class="">
                                <span>
                                    <i class="fa fa-sign-out" aria-hidden="true" style="margin-top:12px; color:rgb(239, 106, 0);"></i>
                                </span>
                        </a>
                     </li>

                </ul>

            </div>
            
        </nav>
    </header>
                                 <!--Inicio Modal de carga-->
                                    <div class='modal fade' id='modal_cargando' tabindex='-1' role='dialog' data-keyboard="false" data-backdrop="static">
                                        <div class='modal-dialog'>
                                            <div class='modal-content'>
                                                <div class='modal-header'>
                                                    <h4 class='modal-title' id='myModalLabel'>Cargando</h4>
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
    <main id="app">
        <div class="container-fluid">
        <input id="id_caja" type="hidden" value="<?php echo $_COOKIE['id_caja'];?>" >
            <v-app style="background: none; height: 100vh">

                <div class="row" v-if="sale">

                    <div class="col-md-8 seleccionar" id="seleccionar">
                        
                        <div class="row">
                            <div class="col-md-3 categoria-caja grupo-materias">
                               
                                <div class="btn-group" role="group" aria-label="Basic example">
                                    <button @click="changeMateriaPS('products')" type="button" class="btn btn-size" :class="{'btn-outline-usqay': materia == 'services', 'btn-usqay': materia == 'products'}">Productos</button>
                                    <button @click="changeMateriaPS('services')" type="button" class="btn btn-size" :class="{'btn-outline-usqay': materia == 'products', 'btn-usqay': materia == 'services'}">Servicios</button>
                                </div>

                            </div>
                            <div class="col-md-5 categoria-caja">
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="inputGroup-sizing-sm">
                                            <i class="fa fa-search"></i>
                                        </span>
                                    </div>
                                    <input id="search_focus" @input="update" :value="search" placeholder="Busca por nombre o código de barras" type="text" class="form-control size-form" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                                </div>
                            </div>
                            <!--<div class="col-md-2 categoria-caja">
                                <div class="dropdown">
                                    <button class="btn btn-outline-info almacenes dropdown-toggle" type="button"
                                        id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                                        aria-expanded="false">
                                        Productos
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        <a class="dropdown-item size-form" href="#">Productos</a>
                                        <a class="dropdown-item size-form" href="#">Servicios</a>

                                    </div>
                                </div>
                            </div>-->
                            <div class="col-md-3 categoria-caja">
                                <div class="dropdown" v-if="materia=='products'">
                                    <button @click.prevent="dialog = true" class="btn btn-usqay almacenes dropdown-toggle" type="button">
                                        {{dialogm1 ? dialogm1.nombre : 'Todos los almacenes'}}
                                    </button>

                                </div>
                            </div>
                            <div class="col-md-1 categoria-caja">
                                <v-btn @click="getBack" icon class="btn-return float-right">
                                    <v-icon>mdi-keyboard-return</v-icon>
                                </v-btn>
                            </div>

                        </div>

                        <!--<div class="row">
                            <div class="col-md-12">
                                <h3 class="title-materia">
                                    {{materia == 'products' ? 'Productos' : 'Servicios'}}
                                </h3>
                            </div>
                        </div>-->

                        <div class="row margen-sup" v-if="step == 1 && materia == 'products'">

                            <div @click="myValues(taxonomy)" v-for="(taxonomy, index) in taxonomies" :key="index" class="col-lg-2 col-md-3 col-sm-4 col-6 categoria-caja animate__animated animate__fadeIn">
                                <div class="card bg-usqay">
                                    <div class="card-body">
                                        <h3 class="categoria">
                                            {{taxonomy.nombre}}
                                        </h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row margen-sup" v-if="step == 1 && materia == 'services'" >
                            <div @click="myValuesTaxonomyService(taxonomy)" v-for="(taxonomy, index) in serviceTaxonomies" :key="index" class="col-lg-2 col-md-3 col-sm-4 col-6 categoria-caja animate__animated animate__fadeIn">
                                <div class="card bg-usqay">
                                    <div class="card-body">
                                        <h3 class="categoria">
                                            {{taxonomy.nombre}}
                                        </h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row margen-sup" v-if="step == 2 && materia == 'products'" >

                            <div @click="myProducts(taxonomyp_valor)" v-for="(taxonomyp_valor, t) in taxonomyp_values" :key="t" class="col-lg-2 col-md-3 col-sm-4 col-6 categoria-caja animate__animated animate__fadeIn">
                                <div class="card bg-usqay">
                                    <div class="card-body">
                                        <h3 class="categoria">
                                            {{taxonomyp_valor.valor}}
                                        </h3>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row margen-sup" v-if="step == 2 && materia == 'services'">
                          
                            <div @click="myServices(taxonomys_valor)" v-for="(taxonomys_valor, t) in taxonomys_values" :key="t" class="col-lg-2 col-md-3 col-sm-4 col-6 categoria-caja animate__animated animate__fadeIn">
                                <div class="card bg-usqay">
                                    <div class="card-body">
                                        <h3 class="categoria">
                                            {{taxonomys_valor.valor}}
                                        </h3>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row margen-sup" v-if="step == 3 && materia == 'products'">

                            <div @click="addProduct(product)" v-for="(product, k) in productsSelected" :key="k" class="col-lg-2 col-md-3 col-sm-4 col-6 card-deck categoria-caja animate__animated animate__fadeIn">
                                <div class="card">
                                    <div class="card-body">
                                    
                                       <!-- <center>
                                        <img :src="`recursos/uploads/productos/${product.id}.png`" width="80" height="80" />
                                        </center>-->
                                        
                                        <h3 class="product">{{product.nombre}}</h3>
                                        <h3 class="product" style="font-size: 0.9rem !important; color:#000 !important;">STOCK: {{Number((product.stock))}} </h3>
                                    </div>
                                     <div class="card-footer footer-price">
                                        <h3 class="price"> S/ {{Number(product.precio_venta).toFixed(2)}}</h3>
                                    </div>
                                  
                                </div>
                            </div>
                            <div class="col-md-12 text-center" v-if="categorySelected">
                                <v-btn :disabled="productsPage == 0" @click="prevProducts" icon>
                                    <v-icon class="icon-paginate">mdi-menu-left</v-icon>
                                </v-btn>
                                <v-btn @click="nextProducts" icon>
                                    <v-icon class="icon-paginate">mdi-menu-right</v-icon>
                                </v-btn>
                            </div>
                            <div class="col-md-12 text-center" v-if="!categorySelected">
                                <v-btn :disabled="productsSearchPage == 0" @click="prevSearchProducts" icon>
                                    <v-icon class="icon-paginate">mdi-menu-left</v-icon>
                                </v-btn>
                                <v-btn @click="nextSearchProducts" icon>
                                    <v-icon class="icon-paginate">mdi-menu-right</v-icon>
                                </v-btn>
                            </div>

                        </div>

                        <div class="row margen-sup" v-if="step == 3 && materia == 'services'">

                            <div @click="addService(service)" v-for="(service, k) in servicesSelected" :key="k" class="col-lg-2 col-md-3 col-sm-4 col-6 card-deck categoria-caja animate__animated
                                animate__fadeIn">
                                <div class="card">
                                    <div class="card-body">
                                        <h3 class="product">{{service.nombre}}</h3>
                                    </div>
                                    <div class="card-footer footer-price">
                                        <h3 class="price">S/{{Number(service.precio_venta).toFixed(2)}}</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 text-center" v-if="categoryServiceSelected">
                                <v-btn :disabled="servicesPage == 0" @click="prevServices" icon>
                                    <v-icon class="icon-paginate">mdi-menu-left</v-icon>
                                </v-btn>
                                <v-btn @click="nextServices" icon>
                                    <v-icon class="icon-paginate">mdi-menu-right</v-icon>
                                </v-btn>
                            </div>
                            <div class="col-md-12 text-center" v-if="!categoryServiceSelected">
                                <v-btn :disabled="servicesSearchPage == 0" @click="prevSearchServices" icon>
                                    <v-icon class="icon-paginate">mdi-menu-left</v-icon>
                                </v-btn>
                                <v-btn @click="nextSearchServices" icon>
                                    <v-icon class="icon-paginate">mdi-menu-right</v-icon>
                                </v-btn>
                            </div>

                        </div>

                    </div>

                    <div class="col-md-4 venta">
                    <div class="row venta-cliente">
                            <div class="col-md-12 cliente">
                            <?php 
                                $nota=$_GET["nota"];
                            ?>
                                <h4 class="">
                                    Nota de 
                                    <?php if($nota==1){
                                        echo "Credito";
                                    }else{
                                        echo "Debito";
                                    }?> Electronica
                                </h4>
                                
                            </div>
                            <div class='control-group col-md-12'>
                                <label>Motivo de Emision</label>
                                <select class='form-control' id='unidad' name='unidad' >
                                <?php  
                                    $motivoEmision = $objcon->consulta_matriz("SELECT* FROM motivo_emision WHERE nota=".$nota." ORDER by id");
                                   
                                        if(is_array($motivoEmision)){
                                            foreach ($motivoEmision as $und){
                                                echo "<option value='".$und["id_nota"]."'>".convert($und["descripcion"])."</option>";
                                            }
                                        }
                                    ?>
                                </select>
                            </div>                           


                        </div>
                        <div class="row venta-cliente">
                            <div class="col-md-12 cliente">
                            <span>tipo documento:  {{this.id_venta}}</span>
                            <span>serie: {{this.id_venta}}</span>
                            <span>nuermo:  {{this.id_venta}}</span>
                            </div>
                            <div class="col-md-12 cliente">
                                <h3 class="cliente-nombre">
                                    <i class="fa fa-user"></i>                                    
                                    <span >Puertas Salazar Axel Alexandre</span>
                                </h3>
                                
                            </div>
                        </div>

                        
                        <!--  axalpusa cambiar cantidad / precios -->
                        <div class="row venta-detalle animate__animated animate__fadeIn" v-if="showVenta">
                            <div v-for="(detail, j) in details" :key="j" class="col-md-12 information" :class="{'seleccionado': detail.selected}" @click="selectDetail(j)">
                                <div class="detalle">
                                    <h3 class="detalle-nombre">
                                        {{detail.quantity}} <span>{{detail.name}}</span>
                                    </h3>
                                    <div>
                                        <span class="detalle-precio">
                                            S/{{detailSubtotal(detail)}}
                                        </span>
                                        <v-btn @click="deleteDetail(j)" icon>
                                            <v-icon class=" eliminar">mdi-delete</v-icon>
                                        </v-btn>

                                    </div>

                                </div>
                                <div v-show="detail.selected">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="lbl" for="">Cantidad</label>
                                                <div class="input-group mb-3">
                                                    <div class="input-group-prepend">
                                                        <button @click="lessDetailQuantity(j)" class="btn boton" type="button" id="button-addon2">
                                                            <i class="fa fa-minus"></i>
                                                        </button>
                                                    </div>

                                                    <input min="1" v-model="detail.quantity" type="number" class="form-control size-form text-center" aria-label="Example text with
                                                            button addon" aria-describedby="button-addon1">

                                                    <div class="input-group-append">

                                                        <button @click="addDetailQuantity(j)" class="btn boton" type="button" id="button-addon1">
                                                            <i class="fa fa-plus"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="lbl" for="">Precio</label>
                                                <div class="input-group mb-3">
                                                    <div class="input-group-prepend" v-if="detail.is_product">
                                                        <button @click="showMeMyDetailPrice(detail)" class="btn boton" type="button" id="button-addon1">
                                                                S/
                                                        </button>

                                                       <!-- <span class="input-group-text size-form" id="basic-addon1">
                                                            S/
                                                        </span>-->
                                                    </div>
                                                    <input <?php //if ($_COOKIE['tipo_usuario'] > 2) echo 'readonly' ?> type="number" class="form-control size-form" v-model="detail.price" aria-label="Username" aria-describedby="basic-addon1">
                                                    <div class="input-group-append" v-if="detail.is_product">
                                                        <button @click="updateItemSaleDB(j)" class="btn boton" type="button" id="button-addon1">
                                                            <i class="fa fa-refresh"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                        <!--<div class="col-md-6">
                                            <div class="form-group">
                                                <label class="lbl" for="">Descuento</label>
                                                <div class="input-group mb-3">
                                                    <div class="input-group-prepend">
                                                        <button class="btn size-form"
                                                            :class="{'boton-descuento': detail.discount_type === null,
                                                        'boton-descuento-seleccionado': detail.discount_type === 'fijo'}" type="button"
                                                            id="button-addon3" @click="discountType(j,'fijo')">
                                                            S/
                                                        </button>
                                                    </div>

                                                    <input v-model="detail.discount" type="number"
                                                        class="form-control size-form text-center" aria-label="Example text with
                                                            button addon" aria-describedby="button-addon3">

                                                    <div class="input-group-append">
                                                        <button class="btn size-form" :class="{'boton-descuento': detail.discount_type === null,
                                                        'boton-descuento-seleccionado': detail.discount_type ===
                                                        'porcentual'}" type="button" id="button-addon3"
                                                            @click="discountType(j,'porcentual')">
                                                            %
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>-->

                                    </div>
                                </div>

                            </div>
                        </div>
                        
                        <div class="row venta-cobro" v-if="showVenta">
                            <div class="col-md-12">

                                <div class="descuento">
                                    <h3 class="descuento-texto">SubTotal</h3>
                                    <h3 class="descuento-cantidad">S/{{subTotal}}</h3>
                                </div>
                                <div class="descuento">
                                    <h3 class="descuento-texto">IGV ({{igv_val * 100}}%)</h3>
                                    <h3 class="descuento-cantidad">S/{{igv}}</h3>
                                </div>
                                <div class="descuento">
                                    <h3 class="descuento-texto">Total</h3>
                                    <h3 class="descuento-cantidad">S/{{total}}</h3>
                                </div>
                                <div class="descuento cursor" @click="openDiscountModal(<?php echo $_COOKIE['tipo_usuario'] ?>)">
                                    <h3 class="descuento-texto text-usqay">Descuento Total</h3>
                                    <h3 class="descuento-cantidad text-usqay">-S/{{discount}}</h3>
                                </div>
                                <button @click="cobrar" class="btn bg-usqay btn-size btn-block text-white">
                                    <span>Cobrar [Ctrl+c]</span>
                                    <span>S/{{payment}}</span>
                                </button>
                            </div>
                        </div>

                    </div>

                </div>
                <div class="row finish" v-if="!sale">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-5 animate__animated animate__fadeIn finish-sale">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <h3 class="finish-sale-title text-usqay">
                                                    <i class="fa fa-user"></i> <span>
                                                        {{selected_customer ? selected_customer.nombre : ''}}
                                                    </span>
                                                    
                                                </h3>
                                            </div>
                                        </div>
                                        <ul class="finish-sale-list">
                                            <li class="finish-sale-list-item" v-for="(detail, d) in details" :key="d">
                                                <h3 class="finish-sale-list-item-text">{{detail.quantity}}
                                                    <span>{{detail.name}}</span>
                                                </h3>
                                                <h3 class="finish-sale-list-item-text">S/{{detailSubtotal(detail)}}
                                                </h3>
                                            </li>
                                        </ul>



                                        <div class="totals">
                                            <div class="totals-descuento">
                                                <h3 class="totals-descuento-texto">SubTotal</h3>
                                                <h3 class="totals-descuento-cantidad">S/{{subTotal}}</h3>
                                            </div>
                                            <div class="totals-descuento">
                                                <h3 class="totals-descuento-texto">IGV ({{igv_val * 100}}%)</h3>
                                                <h3 class="totals-descuento-cantidad">S/{{igv}}</h3>
                                            </div>
                                            <div class="totals-descuento">
                                                <h3 class="totals-descuento-texto">Total</h3>
                                                <h3 class="totals-descuento-cantidad">S/{{total}}
                                                </h3>
                                            </div>
                                            <div class="totals-descuento">
                                                <h3 class="totals-descuento-texto">Descuento Total</h3>
                                                <h3 class="totals-descuento-cantidad">S/{{discount}}</h3>
                                            </div>
                                              <!-- <div class="totals-descuento" style="display: none" >  axalpusa -->
                                            <div class="totals-descuento" style="display: none" id="Detraccion_visible">
                                              <h3 class="totals-descuento-texto">Detraccion &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; {{detraccion_por = <?php echo $porc_detraccion["porcentaje"]?>}}% &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; S/ {{detraccion = (payment*(detraccion_por/100)).toFixed(2)}}</h3>
                                            </div>
                                            <div class="totals-descuento">
                                                <h3 class="totals-descuento-texto total text-usqay">Total a pagar</h3>
                                                <h3 class="totals-descuento-cantidad total text-usqay">S/{{payment}}
                                                </h3>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="col-md-7 animate__animated animate__fadeIn finish-payment">
                                        <div class="row finish-payment-info-row">
                                            <div class="col-md-12 finish-payment-info-column">
                                                <v-btn @click="returnToSale" icon>
                                                    <v-icon>mdi-keyboard-backspace</v-icon>
                                                </v-btn>
                                                <h3 class="finish-payment-info-column-title">Registro de Pago</h3>
                                                <div></div>
                                                <!--<div class="btn-group float-right" role="group"
                                                    aria-label="Basic example">
                                                    <button @click="selectCurrency(currency)"
                                                        v-for="(currency, cu) in currencies" :key="cu" type="button"
                                                        class="btn" :class="{'btn-info': selectedCurrency.iso == currency.iso,
                                                        'active-currency': selectedCurrency.iso == currency.iso,
                                                        'btn-light': selectedCurrency.iso != currency.iso}">
                                                        {{currency.iso}}
                                                    </button>
                                                </div>-->
                                            </div>

                                        </div>
                                        <!--<div class="row finish-payment-currency-row">
                                            <div class="col-md-12 finish-payment-currency-column">
                                                <div class="btn-group float-right" role="group"
                                                    aria-label="Basic example">
                                                    <button @click="selectCurrency(currency)"
                                                        v-for="(currency, cu) in currencies" :key="cu" type="button"
                                                        class="btn" :class="{'btn-info': selectedCurrency.iso == currency.iso,
                                                        'active-currency': selectedCurrency.iso == currency.iso,
                                                        'btn-light': selectedCurrency.iso != currency.iso}">
                                                        {{currency.iso}}
                                                    </button>
                                                </div>
                                            </div>
                                        </div>-->

                                        <div class="row finish-payment-amount-row">
                                            <div class="col-md-12 finish-payment-amount-info">
                                                <h3 class="finish-payment-amount-info-title">Información de Pago</h3>
                                            </div>
                                            <div class="col-md-3 finish-payment-amount-column">
                                                <h3 class="finish-payment-amount-column-title">Monto Total</h3>
                                                <p class="finish-payment-amount-column-total">S/ {{payment}}</p>
                                            </div>
                                            <div class="col-md-3 finish-payment-amount-column">
                                                <h3 class="finish-payment-amount-column-title">Monto Ingresado</h3>
                                                <p class="finish-payment-amount-column-total">
                                                    S/ {{amount_entered.toFixed(2)}}
                                                </p>
                                            </div>
                                            <div class="col-md-3 finish-payment-amount-column">
                                                <h3 class="finish-payment-amount-column-title faltante">Monto Faltante
                                                </h3>
                                                <p class="finish-payment-amount-column-total faltante">
                                                    S/
                                                    {{montoFaltante}}
                                                </p>
                                            </div>
                                            <div class="col-md-3 finish-payment-amount-column">
                                                <h3 class="finish-payment-amount-column-title">Vuelto</h3>
                                                <p class="finish-payment-amount-column-total">
                                                    S/
                                                    {{miVuelto}}
                                                </p>
                                            </div>
                                        </div>

                                        <!--<div class="row finish-payment-details-row">
                                            <div class="col-md-12 finish-payment-details-info">
                                                <h3 class="finish-payment-details-info-title">Detalles de Pago</h3>
                                            </div>
                                            <div class="col-md-12 finish-payment-details-column">
                                                <table class="table table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th>
                                                                Método de Pago
                                                            </th>
                                                            <th>Monto</th>
                                                            <th>Acciones</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>Efectivo</td>
                                                            <td>0.00</td>
                                                            <td>
                                                                <v-btn icon>
                                                                    <v-icon>mdi-delete</v-icon>
                                                                </v-btn>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>Tarjeta</td>
                                                            <td>0.00</td>
                                                            <td>
                                                                <v-btn @click="returnToSale" icon>
                                                                    <v-icon>mdi-delete</v-icon>
                                                                </v-btn>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>

                                        </div>-->

                                        <div class="row finish-payment-method-row">
                                            <div class="col-md-12 finish-payment-method-info">
                                                <h3 class="finish-payment-method-info-title">Métodos de Pago
                                                </h3>
                                            </div>
                                            <div class="col-md-4" v-for="(medio, mp) in medio_pagos" :key="mp">
                                                <button @click="openModalMedioPagos(medio)" class="btn btn-outline-info btn-block">
                                                  {{medio.nombre}}
                                                </button>

                                            </div>

                                            <div class="col-md-12 finish-payment-details-column">
                                                <table class="table table-striped table-sm">

                                                    <tbody>
                                                        <tr v-for="(venta_medio_pago, vmp) in venta_medios_pago" :key="vmp">
                                                            <td class="text-center pad-table">
                                                                {{venta_medio_pago.medio}}
                                                            </td>

                                                            <td class="text-center pad-table">
                                                                {{venta_medio_pago.monto}}
                                                            </td>
                                                            <td class="text-center">
                                                                <v-btn icon @click="deleteVentaMedioPago(vmp)">
                                                                    <v-icon>mdi-close-circle</v-icon>
                                                                </v-btn>
                                                            </td>
                                                        </tr>

                                                    </tbody>
                                                </table>
                                            </div>
                                             <div class="col-md-12">
                                                    <div class="custom-control custom-switch">
                                                        <input @click="check_detraccion" type="checkbox" class="custom-control-input" name="isDetraccion" id="isDetraccion" value="1">
                                                        <label class="custom-control-label" for="isDetraccion">Detraccion</label>
                                                    </div>
                                                </div>
                                            <div class="col-md-12">
                                                    <div class="row justify-content-center p-3">
                                                    <div class="col-md-4">
                                                        <button @click="paymentToServerDB(0)" class="btn bg-usqay btn-block text-white">
                                                            Ticket [F2]
                                                        </button>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <button @click="paymentToServerDB(1)" class="btn bg-usqay btn-block text-white">
                                                            Boleta [F4]
                                                        </button>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <button @click="paymentToServerDB(2)" class="btn bg-usqay btn-block text-white">
                                                            Factura [F7]
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                
                                                    <div class="col-md-3">
                                                        <button @click="deleteSale" class="btn btn-danger text-white btn-block">
                                                            Anular Venta 
                                                        </button>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <button @click="newSale" class="btn btn-success text-white btn-block">
                                                            Nueva Venta 
                                                        </button>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <button @click="getPreCuenta" class="btn btn-secondary text-white btn-block">
                                                            Pre Cuenta 
                                                        </button>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <button @click="paymentToServer" class="btn bg-usqay text-white btn-block">
                                                         Notas de Venta
                                                        </button>
                                                    </div>
                                                </div>


                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <v-snackbar v-model="snackbar" :timeout="timeout" :top="top" :right="right">
                    {{ text }}

                    <template v-slot:action="{ attrs }">
                        <v-btn color="pink" text v-bind="attrs" @click="snackbar = false">
                            Cerrar
                        </v-btn>
                    </template>
                </v-snackbar>
                <v-dialog v-model="dialog" scrollable max-width="300px">

                    <v-card>
                        <v-card-title>Seleccionar Almacén</v-card-title>
                        <v-divider></v-divider>
                        <v-card-text style="height: 300px;">
                            <v-radio-group v-model="dialogm1" column>

                                <v-radio color="#18a2b8" v-for="(warehouse, w) in warehouses" :key="w" :label="warehouse.nombre" :value="warehouse"></v-radio>

                            </v-radio-group>
                        </v-card-text>
                        <v-divider></v-divider>
                        <v-card-actions>
                            <v-btn color="#444444" text @click="dialog = false">
                                Cancelar
                            </v-btn>
                            <v-btn color="#18a2b8" text @click="dialog = false">
                                Seleccionar
                            </v-btn>
                        </v-card-actions>
                    </v-card>
                </v-dialog>

                <v-bottom-sheet v-model="sheet">
                    <v-sheet class="text-center" height="200px">

                        <div class="row justify-content-center">
                            <div class="col-md-4 method">
                                <h3 class="title-method">Ingresar el monto a pagar en
                                    <span>{{selectedMedioPago.nombre}}</span></h3>
                                <form @submit.prevent="getMedioPagoFormValues">
                                    <input autofocus step="0.01" type="number" name="amount" class="form-control form-control-lg text-right" placeholder="0.00">
                                    <v-btn class="mt-6" text color="red" @click="sheet = !sheet">
                                        Cancelar
                                    </v-btn>
                                    <v-btn type="submit" class="mt-6" text color="#00395e">
                                        Aceptar
                                    </v-btn>
                                </form>
                            </div>


                        </div>
                    </v-sheet>
                </v-bottom-sheet>

                <v-bottom-sheet v-model="sheet_payment">
                    <v-sheet class="text-center">

                        <div class="row justify-content-center">
                            <div class="col-md-4 method">
                                <h3 class="title-method">Datos de credito del cliente</h3>
                            </div>
                        </div>
                        <div class="row justify-content-center">
                            <div class="col-md-12 method">
                                <h3 class="title-inf">  {{selected_customer ? selected_customer.nombre : ''}}</h3>
                            
                                <h3 class="title-inf">Credito asignado: {{monto_credito}}</h3>
                            
                                <h3 class="title-inf">Credito consumido: {{consumo_credito}}</h3>
                            
                                <h3 class="title-inf">Fecha limite: {{fechaLimite_credito}}</h3>
                            
                                <h3 class="title-inf">{{msj_credito}}</h3>
                            </div>
                        </div>
                        <div class="row justify-content-center p-3">
                            <!--<div class="col-md-2">
                                <button @click="paymentToServerDB(0)" class="btn bg-usqay btn-block text-white">
                                    Ticket [F2]
                                </button>
                            </div>
                            <div class="col-md-2">
                                <button @click="paymentToServerDB(1)" class="btn bg-usqay btn-block text-white">
                                    Boleta [F4]
                                </button>
                            </div>
                            <div class="col-md-2">
                                <button @click="paymentToServerDB(2)" class="btn bg-usqay btn-block text-white">
                                    Factura [F7]
                                </button>
                            </div>-->
                            <div class="col-md-2">
                                <button @click="paymentToServerDB(3)" class="btn bg-usqay btn-block text-white">
                                    Nota de Venta - Ticket [F8]
                                </button>

                            </div>
                            <div class="col-md-2">
                                <button @click="paymentToServerDB(4)" class="btn bg-usqay btn-block text-white">
                                    Nota de Venta - Boleta [F9]
                                </button>
                            </div>
                            <div class="col-md-2">
                                <button @click="paymentToServerDB(5)" class="btn bg-usqay btn-block text-white">
                                    Nota de Venta - Factura [F10]
                                </button>
                            </div>
                        </div>
                    </v-sheet>
                </v-bottom-sheet>

                <v-dialog v-model="dialogLoading" hide-overlay persistent width="300">
                    <v-card color="#00395e" dark>
                        <v-card-text>
                            <div class="p-3">
                                Procesando Información, espera un momento porfavor...
                                <v-progress-linear indeterminate color="white" class="mb-0"></v-progress-linear>
                            </div>
                        </v-card-text>
                    </v-card>
                </v-dialog>

                <v-dialog v-model="dialogFinish" persistent max-width="290">

                    <v-card>
                        <v-card-title class="headline">
                            {{textTitleFinish}}
                        </v-card-title>
                        <v-card-text>
                            {{textInfoFinish}}
                        </v-card-text>
                        <v-card-actions>
                            <v-spacer></v-spacer>

                            <v-btn color="green darken-1" text @click="okSale">
                                Aceptar
                            </v-btn>
                        </v-card-actions>
                    </v-card>
                </v-dialog>

                <v-dialog v-model="anularVenta" persistent max-width="290">

                    <v-card>
                        <v-card-title class="headline">
                            <h3> ¿Estás seguro de <br>anular la venta?</h3>
                        </v-card-title>
                        <v-card-text>
                            Los detalles agregados a la venta, así como los movimientos generados serán eliminados.
                        </v-card-text>
                        <v-card-actions>
                            <v-spacer></v-spacer>
                            <v-btn color="darken-1" text @click="anularVenta = !anularVenta">
                                No, continuar
                            </v-btn>
                            <v-btn color="green darken-1" text @click="okAnula">
                                Sí, anular
                            </v-btn>
                        </v-card-actions>
                    </v-card>
                </v-dialog>

                <!-- Modal -->
                <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">{{modalTitle}}</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <h3 class="seleccionar-title">
                                    Precios encontrados
                                </h3>
                                <p class="seleccionar-texto">
                                    Selecciona un precio del producto, o cancelar para seguir con el precio por
                                    defecto.
                                </p>
                                <div class="row justify-content-center">
                                    <div class="col-md-3" v-for="(product_price, pp) in productPrices" :key="pp">

                                        <button @click="selectPrice(product_price)" class="btn btn-outline-info btn-block btn-lg">

                                            {{Number(product_price.precio_venta).toFixed(2)}}

                                        </button>
                                        <p class="text-center">
                                            <small>{{product_price.descripcion}}</small>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary btn-size text-white" data-dismiss="modal">Cancelar</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal -->
                <div class="modal fade" id="exampleModal2" tabindex="-1" aria-labelledby="exampleModalLabel2" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <form id="form-discount" @submit.prevent="getFormValues">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel2">Descuento Total</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <!--<h3 class="seleccionar-title">
                                    Precios encontrados
                                </h3>-->
                                    <p class="seleccionar-texto">
                                        Aplica un descuento global al monto total de la venta.
                                    </p>

                                    <div class="form-group">
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">S/</span>
                                            </div>
                                            <input name="discount" type="text" class="form-control text-right" aria-label="Amount (to the nearest dollar)">
                                            <div class="input-group-append">
                                                <span class="input-group-text">.00</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="modal-footer">
                                    <button @click="closeDiscountModal" type="button" class="btn btn-dark btn-size text-white">Cancelar</button>
                                    <button type="submit" class="btn bg-usqay btn-size text-white">Aplicar</button>
                                </div>

                            </div>
                        </form>
                    </div>
                </div>

                <!-- Modal Venta Medio Pagos -->
                <div class="modal fade" id="exampleModal3" tabindex="-1" aria-labelledby="exampleModalLabel3" aria-hidden="true">
                    <div class="modal-dialog">
                        <form @submit.prevent="getMedioPagoFormValues">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel3">
                                        Pagar con {{selectedMedioPago.nombre}}
                                    </h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <!--<h3 class="seleccionar-title">
                                    Precios encontrados
                                </h3>-->


                                    <div class="form-group">

                                        <input placeholder="0.00" type="number" class="form-control size-form text-right" name="amount">
                                    </div>

                                </div>

                                <div class="modal-footer">
                                    <button @click="closeMedioPagoModal" type="button" class="btn btn-dark btn-size text-white">Cancelar</button>
                                    <button type="submit" class="btn bg-usqay btn-size text-white">Agregar</button>
                                </div>

                            </div>
                        </form>
                    </div>
                </div>

            </v-app>
        </div>




    </main>

    <script src=" node_modules/jquery/dist/jquery.min.js"> </script>
    <script src="node_modules/@popperjs/core/dist/umd/popper.min.js"></script>
    <script src="node_modules/bootstrap/dist/js/bootstrap.min.js"></script>
    <script src="node_modules/lodash/lodash.min.js"></script>
    <script src="node_modules/vue/dist/vue.min.js"></script>
    <script src="node_modules/vuetify/dist/vuetify.min.js"></script>
    <script src="node_modules/axios/dist/axios.min.js"></script>
    <script src="assets/js/app.js"></script>
</body>

</html>

<?php 
function convert($entrada){
	$codificacion_=mb_detect_encoding($entrada,"UTF-8,ISO-8859-1");
	$dataa= iconv($codificacion_,'UTF-8',$entrada);
	return $dataa;
}
?>