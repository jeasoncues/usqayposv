//const axios = require('axios');
/*jshint esversion: 6 */
var app = new Vue({
    el: '#app',
    vuetify: new Vuetify(),
    data() {
        return {
            showPrint: false,
            anularVenta: false,
            top: false,
            right: false,
            dialogLoading: false,
            dialogFinish: false,
            textTitleFinish: "",
            textInfoFinish: "",
            redirect_url: "/pos/pantalla_teclado.php",
            customer_route: '/pos/ws/cliente.php',
            amount_entered: 0,
            turned: 0,
            missing: 0,
            venta_medios_pago: [],
            sheet: false,
            sheet_payment: false,
            selectedMedioPago: {},
            igv_val: 0.18,
            div_igv: 1.18,
            discount: 0.00,
            detraccion:0.00,
            detraccion_por:0.00,
            venta_id: null,
            loading: false,
            element: null,
            endPoint: '/pos/api/',
            materia: 'products',
            categorySelected: null,
            categoryServiceSelected: null,
            search: '',
            modalTitle: '',
            selectedCurrency: { id: 1, name: 'Soles', iso: 'PEN' },
            currencies: [
                { id: 1, name: 'Soles', iso: 'PEN' },
                { id: 2, name: 'Dólares', iso: 'USD' },
            ],
            dialogm1: {id: 0, nombre: 'Todos los Almacenes'},
            dialog: false,
            snackbar: false,
            text: "",
            timeout: 2000,
            step: 1,
            sale: true,
            showAll: false,
            showVenta: true,
            showCustomer: false,
            showCustomerList: true,
            showAddPerson: false,
            showAddCompany: false,
            selected_customer: null,
            search_customer: "",
            nCustomer: { id: null, name: null, lastname: null, document: null, phone: null, email: null, type: 1, birthday: null, address: null },
            nCompany: { id: null, name: null, document: null, phone: null, email: null ,type: 2, birthday: null, address: null },
            medio_pagos: [],
            taxonomies: [],
            serviceTaxonomies: [],
            warehouses: [],
            taxonomyp_values: [],
            taxonomys_values: [],
            customersDB: [],
            details: [],
            productsPage: 0,
            productsSearchPage: 0,
            productsSelected: [],
            productPrices: [],
            servicesPage: 0,
            servicesSearchPage: 0,
            servicesSelected: [],
            finished : false,
            id_venta : null,
            id_caja: null,
            monto_credito:0,
            consumo_credito:0,
            fechaLimite_credito:0,
            msj_credito:"",
            
        };
    },
    mounted() {
        this.getProductTaxonomies();
        this.getServiceTaxonomies();
        this.getWarehouses();
        this.getCustomers(this.search_customer);
        this.element = document.getElementById("seleccionar");
        this.getUrlParams();
        this.getMedioPagos();
        this.listenKeysPressed();
       // document.getElementById("search_focus").focus();
 	document.getElementById("search_manual").focus();
        this.id_caja = document.getElementById("id_caja").value;
        //console.log(document.getElementById("id_caja").value);
    },
    methods: {        
        listenKeysPressed() {
            
            document.onkeydown = () => {
                this.atajos();
            };
        },
        atajos(){
            if ((window.event.ctrlKey && window.event.keyCode === 65)) {
                this.deleteSale();//anular venta ctrl+a
            }   
            if ((window.event.ctrlKey && window.event.keyCode === 67)) {
               
                this.cobrar(); //cobrar venta ctrl+c
            }   
           // if ((window.event.ctrlKey && window.event.keyCode === 86)) {//ctrl+v
            if ((window.event.ctrlKey && window.event.keyCode === 88)) {
               // this.paymentToServer();//pagar venta ctrl+x
            }  
            

            if (window.event && window.event.keyCode == 113) {//f2  TICKET
                this.paymentToServerDB(0);
            }

            if (window.event && window.event.keyCode == 115) {//f4  BOLETA
                this.paymentToServerDB(1);
            }

            if (window.event && window.event.keyCode == 118) {//f7  FACTURA
                this.paymentToServerDB(2);
            }

            // PARA CREDITOS
            if (window.event && window.event.keyCode == 119) {//f8  CREDITO - NOTA VENTA
                console.log("para credito nota venta")
                this.paymentToServerDB(3);
            }
            
            if (window.event && window.event.keyCode == 120) {//f9  CREDITO - BOLETA
                console.log("para credito boleta")
                this.paymentToServerDB(4);
            }

            if (window.event && window.event.keyCode == 121) {//f10 CREDITO - FACTURA
                console.log("para credito factura")
                this.paymentToServerDB(5);
            }
        },
        cutomerFind(){                                
            /*if(this.search_customer.length==8 || this.search_customer.length==11){
                console.log("listo para buscar")
                this.getCustomers(this.search_customer);
            }else{
                this.presentToast("Ingrese un DNI / RUC valido");
                console.log("wno es, es dieferente ctmr")
            }*/            
            document.onkeydown = () => {

                if (window.event.keyCode == 13) {
                    this.dialogLoading = true;
                    this.getCustomers(this.search_customer);
                } 
              
            };
            this.dialogLoading = true;
            this.getCustomers(this.search_customer);
        },
        changeMateriaPS(materia) {
            this.materia = materia;  
            this.step = 1;
        },
        newSale() {
            location.href = this.redirect_url;  
        },
        okAnula() {
            const url = `${this.endPoint}venta.php`;
            const formData = new FormData();
            formData.append("op", "delete_sale");
            formData.append("id", this.id_venta);
            axios.post(url, formData)
                .then(response => {
                    console.log(response);
                    if (response.data.ok == true) {
                       // this.presentToast(response.data.msg);
                        setInterval(() => {
                            location.href = this.redirect_url;
                        }, 1000);
                    } else {
                        this.presentToast(response.data.msg);
                    }
                })
                .catch(error => {
                    console.log(error);
                });
        },
        deleteSale() {
            
            this.anularVenta = true;
           
        },
        getPreCuenta() {
            /*
            const url = "/pos/archivos_impresion/precuenta.php?id=" + this.id_venta;
            var win = window.open(url, '_blank');
            win.focus();
            */
            const url = `${this.endPoint}/precuenta.php`;
            const formData = new FormData();
            formData.append("op", "precuenta");
            formData.append("id", this.id_venta);

            axios.post(url, formData)
                .then(response => {
                    console.log(response);
                });
        },
        okSale() {
            this.dialogFinish = false;
            this.newSale();
        
           /* if(!this.finished){
                if(this.showPrint){
                    location.href = "/pos/print.php?id_venta=" + this.id_venta;
                }else{
                    this.newSale();
                }
            }else{
                this.newSale();
            }
            */
        },
        topRightSnackBar(message) {
            this.top = true;
            this.right = true;
            this.presentToast(message);
            setTimeout(() => {
                this.top = false;
                this.right = false;
            }, 3000);
        },
        paymentToServer() {
            this.msj_credito="";
            ///cargar datos de creditos para visualizar 147
            const url = this.customer_route;
            const customer_id = this.selected_customer ? this.selected_customer.id : 0;

            const formData = new FormData();
            formData.append('op', 'searchCredLimit');
            formData.append('idcliente', customer_id);

            axios.post(url, formData)
                .then(response => {
                    console.log(response);
                    this.dialogLoading = false;
                    const dataResponse = response.data;

                    this.monto_credito = dataResponse[0].Monto;
                    this.consumo_credito = dataResponse[0].Consumo;
                    this.fechaLimite_credito = dataResponse[0].FechaLimite;
                    console.log("monto_credito: "+this.monto_credito);
                    console.log("consumo_credito: " + this.consumo_credito);
                    console.log("fechaLimite_credito: "+this.fechaLimite_credito);
                    console.log("monto faltante : " + this.montoFaltante);
                    let total_credito = parseInt(this.consumo_credito) + parseInt(this.montoFaltante);
                    console.log("total credito : " + total_credito );
                    var f = new Date();
                    let hoy = f.getFullYear() + "-" + (f.getMonth() + 1) + "-" + f.getDate();
                    console.log(hoy);
                    if (Date.parse(this.fechaLimite_credito)<Date.parse(hoy)) {
                        this.msj_credito += " Excedio la fecha limite del credito "+'\n' ;
                        console.log("Se paso la fecha limite ");
                    } 
                    if (total_credito> this.monto_credito) {
                            this.msj_credito +=" Monto limite de credito excedido ";
                    }

                })
                .catch(err => {
                    console.log(err);
                    this.dialogLoading = false;
                });

            this.sheet_payment = true;
        },
        check_detraccion(){
        var isChecked = document.getElementById('isDetraccion').checked;
        if(isChecked) {
            document.getElementById('Detraccion_visible').style.display = 'inline';
        }else{
            document.getElementById('Detraccion_visible').style.display = 'none';
        }
     },

        paymentToServerDB(tipo_documento) {

        /** Validaciones */
           
            if (this.details.length === 0) {
                this.presentToast("No se han agregado productos a la venta");
                return;
            }

            const venta_medios_pago_v = this.venta_medios_pago.map(vmp => vmp);

            /*
             if (tipo_documento < 3 && this.montoFaltante > 0) {
                
                this.presentToast("La venta aún tiene pendiente un monto faltante");
                return;
            }
            */
            if(document.getElementById('isDetraccion')==null){
                var isChecked = false;
                console.log("pasa directo");
            }else{ 
                var isChecked = document.getElementById('isDetraccion').checked;
                console.log("cambio de vista al pasar ");
            }
          
           
           
            if (tipo_documento < 3) {
                
                if (venta_medios_pago_v.length === 0) {

                    // detraccion axalpusa 
                    //if (tipo_documento == 0) {
                        //validar solo para boletas y facturas 
                    if (tipo_documento == 2 || tipo_documento==1) {
                        if (isChecked) {
                            console.log(this.detraccion);
                            const pago_efectivo = {
                                id_venta: this.id_venta,
                                medio: 'EFECTIVO_DETRACCION',
                                monto:this.detraccion,
                                vuelto: 0.00,
                                moneda: 'PEN'
                            };
                            venta_medios_pago_v.push(pago_efectivo);
                        }

                    }
                        const pago_efectivo = {
                            id_venta: this.id_venta,
                            medio: 'EFECTIVO',
                            monto: this.total - this.discount,
                            vuelto: 0.00,
                            moneda: 'PEN'
                        };
                        venta_medios_pago_v.push(pago_efectivo);
                }else{
                   // if (tipo_documento == 0) {
                        //validar solo para boletas y facturas 
                    if (tipo_documento == 2 || tipo_documento==1) {

                        if (isChecked) {
                            let detraccion_venta_medios_pago = this.venta_medios_pago;
                            this.venta_medios_pago = [];
                            detraccion_venta_medios_pago.forEach(vmpag => {
                                this.turned = this.miVuelto;
                                vuelto = this.miVuelto;

                                var medior_ret = vmpag.medio + "_DETRACCION"
                                if (medior_ret=="EFECTIVO") {
                                    medior_ret="EFECTIVO_DETRACCION"
                                }
                                const venta_medio_pago_detraccion = {
                                    id_venta: vmpag.id_venta,
                                    medio: medior_ret,
                                    monto: this.detraccion_por*vmpag.monto,
                                    vuelto: vuelto,
                                    moneda: 'PEN'
                                };
                                venta_medios_pago_v.push(venta_medio_pago_detraccion);


                            });
                        }
                    }
                }
            }
            
            
            if (this.discount > 0) {
                console.log("DESCUENTOO");
                const descuento_pago = {
                    id_venta: this.id_venta,
                    medio: 'DESCUENTO',
                    monto: this.discount,
                    vuelto: 0.00,
                    moneda: 'PEN'
                };
                venta_medios_pago_v.push(descuento_pago);
            }


            
            if (tipo_documento == 1) {
               
                if (Number(this.payment) >= 700 && !this.selected_customer) {
                    this.topRightSnackBar("Se requiere un cliente para montos mayor o igual a 700");
                    return;
                }
            }

            if (tipo_documento == 2) {
                if (!this.selected_customer) {
                    this.topRightSnackBar("El cliente es requerido para generar una factura");
                    return;
                }
            }

            if (tipo_documento >= 3) {


                if (!this.selected_customer) {
                    this.topRightSnackBar("El cliente es requerido para generar un crédito");
                    return;
                }

                if (this.montoFaltante == 0) {
                    this.topRightSnackBar("No se puede generar créditos al no tener montos faltantes");
                    return;
                }
           

                ////peguntar limite de creditos y siaunhay  limite asignar  789

                
            /*const url = this.customer_route;
            const customer_id = this.selected_customer ? this.selected_customer.id : 0;
            
            const formData = new FormData();
                formData.append('op', 'searchCredLimit');
                formData.append('idcliente', customer_id);
            
            axios.post(url, formData)
                .then(response => {
                    console.log(response);
                    this.dialogLoading = false;
                    const dataResponse = response.data;
                    this.monto_credito = dataResponse[0].Monto;
                    this.consumo_credito = dataResponse[0].Consumo;
                    this.fechaLimite_credito = dataResponse[0].FechaLimite;*/
                    
                        const url = this.customer_route;
                        const customer_id = this.selected_customer ? this.selected_customer.id : 0;
                        const formDatas = new FormData();
                        formDatas.append('op', 'insertPayCredit');
                        formDatas.append('idcliente', customer_id);
                        formDatas.append('monto', this.montoFaltante);
                        axios.post(url, formDatas)
                            .then(oResponse => {
                                console.log(oResponse);
                                this.dialogLoading = true;
                                const dataResponse = oResponse.data;

                            })
                            .catch(err => {
                                console.log(err);

                                this.dialogLoading = false;
                            });

                   

               /* })
                .catch(err => {
                    console.log(err);
                    this.dialogLoading = false;
                });*/
            


               const venta_credito_pago = {
                    id_venta: this.id_venta,
                    medio: 'CREDITO',
                    monto: this.montoFaltante,
                    vuelto: 0.00,
                    moneda: 'PEN'
                };
                
                venta_medios_pago_v.unshift(venta_credito_pago);
            }
           
            this.dialogLoading = true;
            this.sheet_payment = false;
            const url = this.customer_route;
            const customer_id = this.selected_customer ? this.selected_customer.id : 0;
            
            const formData = new FormData();
            formData.append('op', 'pruebafactura');
            formData.append('cliente', customer_id);
            formData.append('id', this.id_venta);
            formData.append('tipo_documento', tipo_documento);
            formData.append('descuento_global', this.discount);
            formData.append('medio_pago', JSON.stringify(venta_medios_pago_v));
            formData.append('id_caja', this.id_caja);
           // console.log(" ----------------- ");
            //console.log(this.id_caja);
            axios.post(url, formData)
                .then(response => {
                    console.log(response);
                    this.dialogLoading = false;
                    const dataResponse = response.data;
                    
                    if (dataResponse.success) {
                        this.textTitleFinish = dataResponse.message;
                        this.showPrint = dataResponse.showPrint;
                        this.textInfoFinish = "Podrás encontrar la venta generada en tu resumen de ventas";
                        this.dialogFinish = true;    
                        this.okSale();
                       
                                          
                    } else {
                        this.presentToast(dataResponse.message.errors);
                        console.log(this.venta_medios_pago);
                    }
                    
                })
                .catch(err => {
                    console.log(err);
                    this.dialogLoading = false;
                });
                
        },
        pulsar() {
            document.onkeydown = () => {
                if (window.event.keyCode == 13) {
                    console.log("se precionso enter ");
                    var valor = document.getElementById("search_manual").value;
                    let url = '';
                    if(valor!=""){
                        url = `${this.endPoint}producto_taxonomiap.php?q=${valor}&page=${this.productsSearchPage}`;
                        this.searchProductFromDB(url);
                        document.getElementById("search_manual").value="";
                    }
                   /* console.log(this.dialogm1.id);
                   if (this.dialogm1.id === 0) {
                              url = `${this.endPoint}producto_taxonomiap.php?q=${valor}&page=${this.productsSearchPage}`;
                    } else {
                        console.log("bbb");
                        url = `${this.endPoint}producto_taxonomiap.php?q=${valor}&almacen=${this.dialogm1.id}&page=${this.productsSearchPage}`;
                    }
                    console.log(url);
                    this.searchProductFromDB(url);
                    document.getElementById("search_manual").value="";*/
                } 
                this.atajos();

            };
           
           
        },
       
        openModalMedioPagos(medio_pago) {
            this.selectedMedioPago = medio_pago;
            this.sheet = true;
        },
        getMedioPagos() {
            const url = `${this.endPoint}medio_pago_venta.php`;
            axios.get(url)
                .then(response => {
                    this.medio_pagos = response.data;
                   // console.log(this.medio_pagos);
                });
        },
        getMedioPagoFormValues(submitEvent) {

            if (this.montoFaltante == 0) {
                this.presentToast("No existen montos faltantes");
                return;
            }

            let val = submitEvent.target.elements.amount.value;
            val = Number(val);
            const parseAmount = Number(this.amount_entered);
            let vuelto = 0;

            /** Validaciones */
            if (val <= 0) {
                this.presentToast("Ingresar Montos mayores a 0");
                return;
            }

            // Que no exista el metodo de pago dos veces

            const existe = this.venta_medios_pago.filter(vmp =>vmp.medio == this.selectedMedioPago.nombre);

            if (existe.length > 0) {
                this.presentToast("Ya existe un detalle ingresado coneste método de pago");
                return;
            }


            if (this.selectedMedioPago.nombre != 'EFECTIVO') {
                if (this.payment < (val + parseAmount)) {
                    this.presentToast("El monto ingresado excede eltotal de la venta");
                    return;
                }
            }

            if (Number(this.payment) < val &&this.selectedMedioPago.nombre != 'EFECTIVO') {
                this.presentToast("No se pueden ingresar montos mayores que el total al pagar con tarjeta");
                return;
            }

            /*
            if (this.payment < val && this.selectedMedioPago.nombre =='EFECTIVO') {
                this.turned = val - this.payment;
                vuelto = val - this.payment;
            }
            */
            this.amount_entered = parseAmount + val;
             if (this.selectedMedioPago.nombre == 'EFECTIVO') {
                this.turned = this.miVuelto;
                vuelto = this.miVuelto;
            }
            
        

            const venta_medio_pago = {
                id_venta: this.id_venta,
                medio: this.selectedMedioPago.nombre,
                monto: val,
                vuelto: vuelto,
                moneda: 'PEN'
            };

            this.venta_medios_pago.push(venta_medio_pago);
            submitEvent.target.elements.amount.value = "";
            this.sheet = false;
        },
        deleteVentaMedioPago(index) {
            const venta_medio_pago = this.venta_medios_pago[index];
            this.venta_medios_pago.splice(index, 1);
            const parseAmount = Number(this.amount_entered) - Number(venta_medio_pago.monto);
           
            this.amount_entered = parseAmount;
        },
        getFormValues(submitEvent) {
            const val = submitEvent.target.elements.discount.value;
            const parseVal = Number(val).toFixed(2);
            console.log(this.total, parseVal);
            if (parseVal > Number(this.total)) {
                this.closeDiscountModal();
                submitEvent.target.elements.discount.value = "";
                this.presentToast("El descuento no debe de ser mayor al monto total de la venta");
                return;
            }
            this.discount = parseVal;
            this.closeDiscountModal();
        },
        openDiscountModal(profile) {

           /* if (Number(profile) > 2) {
                this.presentToast("No tienes los permisos necesarios para realizar esta acción.");
                return;
            }*/

            if (this.details.length === 0) {
                this.presentToast("No se han ingresado productos o servicios");
                return;
            }


            $('#exampleModal2').modal({
                backdrop: 'static',
                keyboard: false
            });
        },
        closeDiscountModal() {
            $('#exampleModal2').modal('hide');
        },
         closeMedioPagoModal() {
            $('#exampleModal3').modal('hide');
        },
        setDiscount() {
            console.log(this.discount);
        },
        getUrlParams() {
            const queryString = window.location.search;
            const urlParams = new URLSearchParams(queryString);
            const id_venta = urlParams.get('id');
            this.id_venta = id_venta;
            if (this.id_venta) {
                this.getMyProductSaleDetails(this.id_venta);
            }
        },
        getMyProductSaleDetails(id_venta) {
            const url = `${this.endPoint}venta.php?id=${id_venta}`;
            axios.get(url)
                .then(response => {
                    const data = response.data;
                    console.log("data",data);
                    const data_details = data.producto_venta;
                    if(!data.finished){
                        if (data.cliente) {
                            if (data.cliente.id != 0) {
                                this.selected_customer = data.cliente;
                            } 
                        } else {
                            this.selected_customer = data.cliente;
                        }
                        if (data_details.length > 0) {
                            this.details = data_details.map((detail, index) => {
                                const parseData =  {
                                    id: detail.id_producto,
                                    quantity: detail.cantidad,
                                    name: detail.nombre,
                                    price: detail.precio,
                                    selected: index == 0 ? true : false,
                                    discount: 0.00,
                                    discount_type: null,
                                    is_product: detail.is_product,
                                    incluye_impuesto: detail.incluye_impuesto
                                };
                                return parseData;
                            });
                            console.log(this.details);
                        }
                    }else{
                        this.finished=true;
                        this.textTitleFinish = "ESTA VENTA YA A SIDO FINALIZADA";
                        this.showPrint = false;
                        this.textInfoFinish = "Puede visualizarla en su reporte de ventas";
                        this.dialogFinish = true;
                        console.log("antes de new sale");                        
                        this.newSale();                        
                    }
                });
        },
        getCustomers(q) {
            const route = `${this.endPoint}cliente.php?q=${q}`;
            axios.get(route)
                .then(response => {
                    console.log("getCustomers",response.data);
                    if(response.data==null){
                        this.presentToast("Documento ingresado es incorrecto");
                    }
                    const customers = response.data;
                    this.customersDB = customers;
                    this.dialogLoading = false;
                });
        },
        //axalpusa tiempo de busqueda
        update: _.debounce(function (e) {
                 this.search = e.target.value;
        }, 1000),
        updateCustomer: _.debounce(function (e) {
            console.log(" updateCustomer: _.debounce",e.target.value)
            if(e.target.value.length>=8){
                console.log("listo para buscar")
                this.search_customer = e.target.value;
            }else{
                console.log("wno es, es dieferente ctmr")
            }
        }, 1000),
        changeMateria() {
            this.materia == 'products' ? this.materiaAsService() : this.materiaAsProduct();
            this.step = 1;

        },
        materiaAsService() {
            this.materia = 'services';
        },
        materiaAsProduct() {
            this.materia = 'products';
            this.getProductTaxonomies();
        },
        getProductTaxonomies() {
            const route = `${this.endPoint}taxonomiap.php`;
            axios.get(route)
                .then(response => {
                    this.taxonomies = response.data;
                });
        },
        getServiceTaxonomies() {
           const route = `${this.endPoint}taxonomias.php`;
            axios.get(route)
                .then(response => {
                    this.serviceTaxonomies = response.data;
                });
        },
        getWarehouses() {
            const route = `${this.endPoint}almacen.php`;
            axios.get(route)
                .then(response => {
                    const data = response.data;
                    const newData = [...data, { id: 0, nombre: 'Todos los Almacenes' }];
                    this.warehouses = newData;
                });
            
        },
        selectCurrency(currency) {
            this.selectedCurrency = currency;
        },
        returnToSale() {
            this.sale = !this.sale;
        },
        //axalpusa 
        recalculaMediosPago() {
            let new_venta_medios_pago = this.venta_medios_pago;
            this.venta_medios_pago = [];
            let vuelto = 0;
            new_venta_medios_pago.forEach(vmpag => {
                if (this.selectedMedioPago.nombre == 'EFECTIVO') {
                    this.turned = this.miVuelto;
                    vuelto = this.miVuelto;
                }
                const venta_medio_pago = {
                    id_venta: vmpag.id_venta,
                    medio: vmpag.medio,
                    monto: vmpag.monto,
                    vuelto: vuelto,
                    moneda: 'PEN'
                };
                this.venta_medios_pago.push(venta_medio_pago);
            });
        },
        cobrar() {
           
           this.recalculaMediosPago()
           
            if (this.details.length === 0) {
                this.presentToast("No se registraron productos a la venta");
                return;
            } else {
                $("#modal_cargando").modal("show");
                var vartimer = setInterval(function () {
                    $("#modal_cargando").modal("hide");
                    clearInterval(vartimer);
                }, 1000);
            }
            //valida que se el total del producto no sea 0   
            if (Number(this.payment) === 0) {
              
                this.presentToast("No se ingreso precioss");
                document.getElementById("precio_prod").focus();
                return;
            }
            
            this.details.forEach((detail, index) => {
                const url = `${this.endPoint}venta.php`;
                const product = detail;
                const id_cliente = this.selected_customer ? this.selected_customer.id : 0;
                const total_producto = product.price * product.quantity
                const formData = new FormData();

                formData.append("op", "update_item");
                formData.append("id", this.id_venta);
                formData.append("subtotal", this.subTotal);
                formData.append("total_impuestos", this.igv);
                formData.append("total", this.total);
                formData.append("tipo_comprobante", null);
                formData.append("id_cliente", id_cliente);
                formData.append("id_producto", product.id);
                formData.append("precio", product.price);
                formData.append("cantidad", product.quantity);
                formData.append("total_producto", total_producto);
                formData.append("id_almacen", this.dialogm1.id);
                formData.append("is_product", product.is_product);
                //axalpusa venta exitosa
                axios.post(url, formData)
                    .then(response => {
                       // console.log("cobrar",response);
                        if (response.data.ok == true) {
                            if(response.data.finished){
                                this.finished=true;
                                this.textTitleFinish = "ESTA VENTA YA A SIDO FINALIZADA";
                                this.showPrint = false;
                                this.textInfoFinish = "Puede visualizarla en su reporte de ventas";
                                this.dialogFinish = true;                            
                            }
                        }else {
                           // this.presentToast(response.data.msg);
                        }
                        
                    })
                    .catch(error => {
                        console.log(error);
                    })
            })
            this.sale = !this.sale;
        },
        newPerson() {
            this.loading = true;
            const data = new FormData();
            data.append('op', 'add');
            data.append('nombre', `${this.nCustomer.name} ${this.nCustomer.lastname}`);
            data.append('documento', this.nCustomer.document);
            data.append('tipo_cliente', this.nCustomer.type);
            data.append('direccion', this.nCustomer.address);
            data.append('correo', this.nCustomer.email);
            data.append('fecha_nacimiento', this.nCustomer.birthday);
           
            const url = `${this.endPoint}add_cliente.php`;
            
            axios.post(url, data)
                .then(response => {
                    const responseData = response.data;
                    if (responseData.ok == false) {
                        this.presentToast(responseData.msg);
                        this.loading = false;
                        return;
                    }
                    this.loading = false;
                    this.selected_customer = responseData;
                    this.cancelRegisterForm();
                    this.viewCustomer();
                    this.presentToast("Cliente Registrado correctamente");
                })
                .catch(err => {
                    console.log(err);
                    this.loading = false;
                });

        },
        newCompany() {

            this.loading = true;
            const data = new FormData();
            data.append('op', 'add');
            data.append('nombre', this.nCompany.name);
            data.append('documento', this.nCompany.document);
            data.append('tipo_cliente', this.nCompany.type);
            data.append('direccion', this.nCompany.address);
            data.append('correo', this.nCompany.email);
            data.append('fecha_nacimiento', this.nCompany.birthday);
           
            const url = `${this.endPoint}add_cliente.php`;
            
            axios.post(url, data)
                .then(response => {
                    const responseData = response.data;
                    if (responseData.ok == false) {
                        this.presentToast(responseData.msg);
                        this.loading = false;
                        return;
                    }
                    this.loading = false;
                    this.selected_customer = responseData;
                    this.cancelRegisterCompanyForm();
                    this.viewCustomer();
                    this.presentToast("Cliente Registrado correctamente");
                })
                .catch(err => {
                    console.log(err);
                    this.loading = false;
                });
        },
        cancelRegisterForm() {
            this.nCustomer = { id: null, name: null, lastname: null, document: null, phone: null, email: null, type: 1 };
            this.showAddPersonSection();
        },
        cancelRegisterCompanyForm() {
            this.nCompany = { id: null, name: null, document: null, phone: null, email: null ,type: 2 },
            this.showAddCompanySection();
        },
        showAddPersonSection() {
            this.showAddPerson = !this.showAddPerson;  
            this.showCustomerList = !this.showCustomerList;
        },
        showAddCompanySection() {
            this.showAddCompany = !this.showAddCompany;  
            this.showCustomerList = !this.showCustomerList;
        },
        clearCustomer() {
            this.selected_customer = null;  
        },
        markAsSelected(customer) {
            this.selected_customer = customer;
            this.viewCustomer();
        },
        viewCustomer() {
            this.showVenta = !this.showVenta;
            this.showCustomer = !this.showCustomer;
        },
        detailSubtotal(detail) {
            // return (detail.quantity * detail.price) - detail.discount;
            let discountTotal = 0;
            switch (detail.discount_type) {
                case 'fijo':
                    discountTotal = Number(detail.discount);
                    break;
                    
                case 'porcentual':
                    let d = detail.price * detail.quantity;
                    discountTotal = (Number(d) * Number(detail.discount)) / 100;
                    break;
                
                default:
                    discountTotal += 0;
            }

            return Number((detail.quantity * detail.price) - discountTotal).toFixed(2);
        },
        myValues(taxonomy) {
            const url = `${this.endPoint}taxonomiap_valor.php?padre=${taxonomy.id}&q=valores`;
            axios.get(url)
                .then(response => {
                    this.taxonomyp_values = response.data;
                    this.step++;
                })
        },
        myValuesTaxonomyService(taxonomy) {
            const url = `${this.endPoint}taxonomias_valor.php?padre=${taxonomy.id}&q=valores`;
            axios.get(url)
                .then(response => {
                    this.taxonomys_values = response.data;
                    this.step++;
                })
        },
        myProducts(category) {
            
            this.categorySelected = category;
            this.productsPage=0;
            let url = '';
            if (this.dialogm1.id === 0 ) {
                url = `${this.endPoint}taxonomiap_valor.php?id=${category.id_taxonomiap}&valor=${category.valor}&q=products&page=${this.productsPage}`;
            } else {
                url = `${this.endPoint}taxonomiap_valor.php?id=${category.id_taxonomiap}&valor=${category.valor}&almacen=${this.dialogm1.id}&q=products&page=${this.productsPage}`;
            }
           
            this.getMyProductsFromDB(url, true);
    
        },
        myServices(category) {
            this.categoryServiceSelected = category;
            this.servicesPage=0;
            const url = `${this.endPoint}taxonomias_valor.php?id=${category.id_taxonomias}&valor=${category.valor}&q=services&page=${this.servicesPage}`;
            this.getMyServicesFromDB(url, true);
        },
        nextProducts() {
            let url = '';
            this.productsPage++;
            if (this.dialogm1.id === 0 ) {
                url = `${this.endPoint}taxonomiap_valor.php?id=${this.categorySelected.id_taxonomiap}&valor=${this.categorySelected.valor}&q=products&page=${this.productsPage}`;
            } else {
                url = `${this.endPoint}taxonomiap_valor.php?id=${this.categorySelected.id_taxonomiap}&valor=${this.categorySelected.valor}&almacen=${this.dialogm1.id}&q=products&page=${this.productsPage}`;
            }
            this.nextOrPrevProductsDB(url);
        },
        nextOrPrevProductsDB(url) {
            this.getMyProductsFromDB(url, false);
        },
        prevProducts() {
            let url = '';
            this.productsPage--;
            if (this.dialogm1.id === 0 ) {
                url = `${this.endPoint}taxonomiap_valor.php?id=${this.categorySelected.id_taxonomiap}&valor=${this.categorySelected.valor}&q=products&page=${this.productsPage}`;
            } else {
                url = `${this.endPoint}taxonomiap_valor.php?id=${this.categorySelected.id_taxonomiap}&valor=${this.categorySelected.valor}&almacen=${this.dialogm1.id}&q=products&page=${this.productsPage}`;
            }
            this.nextOrPrevProductsDB(url);
            
        },
        nextServices() {
            this.servicesPage++;
            const url = `${this.endPoint}taxonomias_valor.php?id=${this.categoryServiceSelected.id_taxonomias}&valor=${this.categoryServiceSelected.valor}&q=services&page=${this.servicesPage}`;
            this.nextOrPrevServicesDB(url);
        },
        nextOrPrevServicesDB(url) {
            this.getMyServicesFromDB(url, false);
        },
        prevServices() {
            this.servicesPage--;
            const url = `${this.endPoint}taxonomias_valor.php?id=${this.categoryServiceSelected.id_taxonomias}&valor=${this.categoryServiceSelected.valor}&q=services&page=${this.servicesPage}`;
            
            this.nextOrPrevServicesDB(url);
            
        },
        getMyProductsFromDB(route, upperStep = false) {
            //console.log(route);
            axios.get(route)
                .then(response => {
                    this.productsSelected = response.data;
                    
                    if (upperStep) {
                        this.step++;
                    } else {
                        this.element.scrollTop = 0;
                    }
                });
        },
        getMyServicesFromDB(route, upperStep = false) {
            console.log(route);
            axios.get(route)
                .then(response => {
                    console.log(response);
                    this.servicesSelected = response.data;
                    
                    if (upperStep) {
                        this.step++;
                    } else {
                        this.element.scrollTop = 0;
                    }
                });
        },
        selectDetail(index) {
            this.details.map((detail, i) => index == i ? detail.selected = true : detail.selected = false);
        },
        //axalpusa eliminar
        deleteDetail(index) {
            const detail = this.details[index];
            
            this.details.splice(index, 1);  
            
            const url = `${this.endPoint}venta.php`;
            const id_cliente = this.selected_customer ? this.selected_customer.id : 0;
            const formData = new FormData();
            formData.append("op", "delete_item");
            formData.append("id", this.id_venta);
            formData.append("subtotal", this.subTotal);
            formData.append("total_impuestos", this.igv);
            formData.append("total", this.total);
            formData.append("tipo_comprobante", null);
            formData.append("id_cliente", id_cliente);
            formData.append("id_producto", detail.id);
            formData.append("is_product", detail.is_product);

            axios.post(url, formData)
                .then(response => {
                    console.log(response);
                    if (response.data.ok == true) {
                        if(response.data.finished){
                            this.finished=true;
                            this.textTitleFinish = "ESTA VENTA YA A SIDO FINALIZADA";
                            this.showPrint = false;
                            this.textInfoFinish = "Puede visualizarla en su reporte de ventas";
                            this.dialogFinish = true;
                        }else{
                            //this.presentToast(response.data.msg);
                            this.id_venta = response.data.id_venta;
                            this.getMyProductSaleDetails(this.id_venta);
                        }
                    } else {
                        this.presentToast(response.data.msg);
                    }
                })
                .catch(error => {
                    console.log(error);
                })
           
           
        },
        addProduct(product) {   

            // Verificar que producto no exista
            const exists = this.details.filter(detail => detail.id === product.id && detail.is_product == true);
           if (exists.length > 0) {
                exists[0].quantity++  // si el producto ya existe se aumenta la cantidad
               // this.presentToast("El producto ya ha sido seleccionado");
               console.log(this.details.findIndex(idx => idx.id === exists[0].id));
                this.updateItemSaleDB(this.details.findIndex(idx => idx.id === exists[0].id));
                this.backToFirstStep();
                return;
            }

            this.details.map(detail => detail.selected = false);


            /** Precios */
            const url_prices = `${this.endPoint}producto_precios.php?id=${product.id}`;
            axios.get(url_prices)
                .then(response => {
                    //onsole.log(response);
                    const prices = response.data;
                    
                    if (prices.length > 0) {
                       // console.log("existen otros precios")
                        console.log(prices);
                        this.showMeMyPrices(product, prices);
                    } else {
                        this.addProductToDetails(product);
                    }
                })
        },

        addService(service) {
            // Verificar que servicio no exista
            const exists = this.details.filter(detail => detail.id === service.id && detail.is_product == false);
            
            if (exists.length > 0) {
                this.presentToast("El servicio ya ha sido seleccionado");
                return;
            }

            this.details.map(detail => detail.selected = false);
            this.addServiceToDetails(service);

        },

        showMeMyDetailPrice(detail) {
           // console.log("otros precios");
            const url_prices = `${this.endPoint}producto_precios.php?id=${detail.id}`;
            axios.get(url_prices)
                .then(response => {
                    //console.log(response);
                    const prices = response.data;
                    if (prices.length > 0) {
                        this.showMeMyPrices(detail, prices);
                    } else {
                        this.presentToast("El producto seleccionado no cuenta con precios registrados");
                    }
                })
        },


        showMeMyPrices(product, prices) {
           
            this.modalTitle = product.nombre || product.name;
            
            this.productPrices = prices;
            $('#exampleModal').modal({
                backdrop: 'static',
                keyboard: false
            });
        },

        addProductToDetails(product) {
           //axalpusa cambio cantidad a otros precios 
            var cant = 0;
            var pre=0;
            if (product.quantity === undefined) {
                cant =1;
                pre = product.precio_venta;
            }else{
                if (product.quantity===null){
                    cant =1;
                    pre = product.precio_venta / cant;
                }else{
                cant = product.quantity;
                pre = product.precio_venta / cant;
                }
            }
            const detail = {
                id: product.id,
                quantity: cant,
                name: product.nombre,
                price: pre,
                selected: true,
                discount: 0.00,
                discount_type: null,
                is_product: true,
                incluye_impuesto: product.incluye_impuesto
            };

            // Crear en venta en caso no exista
            this.details.unshift(detail);
            if (!this.id_venta) {
                this.addFirstItemToSale(detail);
            } else {
                this.addNextItemToSale(detail);
            }

            this.backToFirstStep();
        },

        addServiceToDetails(service) {
            console.log(service);
            const detail = {
                id: service.id,
                quantity: 1,
                name: service.nombre,
                price: service.precio_venta,
                selected: true,
                discount: 0.00,
                discount_type: null,
                is_product: false,
                incluye_impuesto: service.incluye_impuesto
            };

            // Crear en venta en caso no exista
            this.details.unshift(detail);

         
            if (!this.id_venta) {
                this.addFirstItemToSale(detail);
            } else {
                this.addNextItemToSale(detail);
            }

            this.step = 1;
            this.servicesSearchPage = 0;
            this.servicesPage = 0;
        },


        addNextItemToSale(product) {
            const id_cliente = this.selected_customer ? this.selected_customer.id : 0;
            const total_producto = product.price * product.quantity
            const formData = new FormData();
            formData.append("op", "update");
            formData.append("id", this.id_venta);
            formData.append("subtotal", this.subTotal);
            formData.append("total_impuestos", this.igv);
            formData.append("total", this.total);
            formData.append("tipo_comprobante", null);
            formData.append("id_cliente", id_cliente);
            formData.append("id_producto", product.id);
            formData.append("precio", product.price);
            formData.append("cantidad", product.quantity);
            formData.append("total_producto", total_producto);
            formData.append("id_almacen", this.dialogm1.id);
            formData.append("is_product", product.is_product);
            this.createSaleDB(formData);
        },

        addFirstItemToSale(product) {
            const id_cliente = this.selected_customer ? this.selected_customer.id : 0;
            const total_producto = product.price * product.quantity
            const formData = new FormData();
            formData.append("op", "add");
            formData.append("id", this.id_venta);
            formData.append("subtotal", this.subTotal);
            formData.append("total_impuestos", this.igv);
            formData.append("total", this.total);
            formData.append("tipo_comprobante", null);
            formData.append("id_cliente", id_cliente);
            formData.append("id_producto", product.id);
            formData.append("precio", product.price);
            formData.append("cantidad", product.quantity);
            formData.append("total_producto", total_producto);
            formData.append("id_almacen", this.dialogm1.id);
            formData.append("is_product", product.is_product);
            this.createSaleDB(formData);
        },
        createSaleDB(data) {
            const url = `${this.endPoint}venta.php`;
            axios.post(url, data)
                .then(response => {
                    //console.log("createSaleDB",response);
                    if (response.data.ok == true) {
                        if(response.data.finished){
                            this.finished=true;
                            this.textTitleFinish = "ESTA VENTA YA A SIDO FINALIZADA";
                            this.showPrint = false;
                            this.textInfoFinish = "Puede visualizarla en su reporte de ventas";
                            this.dialogFinish = true;
                        }else{
                            //this.presentToast(response.data.msg);
                            this.id_venta = response.data.id_venta;
                            document.getElementById("search_manual").focus();
			//document.getElementById("search_focus").focus();
                        }                        
                    }else {
                        this.presentToast(response.data.msg);
                    }                                      
                })
                .catch(error => {
                    console.log(error);
                })
        },
        backToFirstStep() {
            this.step = 1;
            this.productsSearchPage = 0;
            this.productsPage = 0;
            this.search = "";
            document.getElementById("search_manual").focus();
	//document.getElementById("search_focus").focus();
        },
        selectPrice(product_price) {
            console.log(product_price.cantidad);
            const product = {
                id: product_price.id_producto,
                nombre: product_price.producto,
                quantity:product_price.cantidad,    //axalpusa cambio cantidad
                precio_venta: product_price.precio_venta,
                incluye_impuesto: product_price.incluye_impuesto
            };

            // Verificar que producto no exista
            
            const exists = this.details.filter(detail => detail.id === product.id && detail.is_product == true);
            
            if (exists.length > 0) {
                // this.presentToast("El producto ya ha sido seleccionado");
                /** Actualizar precio de producto */
                for (let index = 0; index < this.details.length; index++) {
                    const element = this.details[index];
                    if (element.id == product.id) {
                        var cant = 0;
                        var pre = 0;
                        if (product.quantity === undefined) {
                            cant = 1;
                            pre = product.precio_venta;
                        } else {
                            if (product.quantity === null) {
                                cant = 1;
                                pre = product.precio_venta / cant;
                            } else {
                                cant = product.quantity;
                                pre = product.precio_venta / cant;
                            }
                        }

                        element.price = pre;
                        element.quantity=cant;
                    /** Actualizamos precio DB */
                        this.updateItemSaleDB(index);
                        break;
                    }
                }
                
            } else {
                this.addProductToDetails(product);
            }
            


            
            
            this.closeModalPrices();
        },

        closeModalPrices() {
            $('#exampleModal').modal('hide');
            this.modalTitle = '';
            this.productPrices = [];
        },

        presentToast(text) {
            this.text = text;
            this.snackbar = true;  
        },
        getBack() {
            if (this.step > 1) {
                 this.step -= 1;
            } else {
                this.step = 1;
            }
            this.productsPage = 0;
            this.productsSearchPage = 0;
           
        },
        discountType(index, type) {
            if (this.details[index].discount_type === type) {
                this.details[index].discount_type = null;
                this.details[index].discount = 0;
            } else {
                this.details[index].discount_type = type;
            }
        },
        addDetailQuantity(index) {
            console.log(this.details[index]);
            this.details[index].quantity++;
            this.updateItemSaleDB(index);
        },
        lessDetailQuantity(index) {

            this.details[index].quantity--;

            if (this.details[index].quantity <= 0) {
                this.presentToast("Solo cantidades mayor a 0");
                this.details[index].quantity = 1;
            } else {
                this.updateItemSaleDB(index);
            }
            
        },
        updateItemSaleDB(index) {
            const url = `${this.endPoint}venta.php`;
            const product = this.details[index];
            
            const id_cliente = this.selected_customer ? this.selected_customer.id : 0;
            const total_producto = product.price * product.quantity
            const formData = new FormData();

            formData.append("op", "update_item");
            formData.append("id", this.id_venta);
            formData.append("subtotal", this.subTotal);
            formData.append("total_impuestos", this.igv);
            formData.append("total", this.total);
            formData.append("tipo_comprobante", null);
            formData.append("id_cliente", id_cliente);
            formData.append("id_producto", product.id);
            formData.append("precio", product.price);
            formData.append("cantidad", product.quantity);
            formData.append("total_producto", total_producto);
            formData.append("id_almacen", this.dialogm1.id);
            formData.append("is_product", product.is_product);
            
            axios.post(url, formData)
                .then(response => {
                   // console.log("updateItemSaleDB",response);                    
                        if (response.data.ok == true) {
                            if(response.data.finished){
                                this.finished=true;
                                this.textTitleFinish = "ESTA VENTA YA A SIDO FINALIZADA";
                                this.showPrint = false;
                                this.textInfoFinish = "Puede visualizarla en su reporte de ventas";
                                this.dialogFinish = true;
                                console.log("antes de new sale");
                            }else{
                                //this.presentToast(response.data.msg);
                                this.id_venta = response.data.id_venta;
                            }                            
                        } else {
                            this.presentToast(response.data.msg);
                        }                                    
                })
                .catch(error => {
                    console.log(error);
                })
        },
//axalpusa busqueda 
        searchProductFromDB(url) {
      
         // console.log(url);
                axios.get(url)
                    .then(response => {

                        this.productsSelected = response.data;
                       // console.log(this.productsSelected);
                       /* this.step = 3;
                        this.categorySelected = null;*/
                       if (this.productsSelected.length == 1) {
                           console.log(this.productsSelected[0]);
                            this.addProduct(this.productsSelected[0]);
                         console.log("solo devuleve un producto");
                        } else if (this.productsSelected.length == 0) {
                            console.log("no hubo coincidencias");
                        } else if (this.productsSelected.length >= 2) {
                            console.log(this.productsSelected);
                            this.step = 3;
                            this.categorySelected = null;
                            console.log("hay mas de 1 resultados");
                        } else {
                            console.log("nadaaa");
                           this.presentToast("Producto no encontrado");
                        }
                        
                    });  
        },
        nextSearchProducts() {
            let url = '';
            this.productsSearchPage++;
            console.log(this.search);
            if (this.dialogm1.id === 0 ) {
                url = `${this.endPoint}producto_taxonomiap.php?q=${this.search}&page=${this.productsSearchPage}`;
            } else {
                url = `${this.endPoint}producto_taxonomiap.php?q=${this.search}&almacen=${this.dialogm1.id}&page=${this.productsSearchPage}`;
            }
            
           this.searchProductFromDB(url);
        },
        prevSearchProducts() {
            let url = '';
            this.productsSearchPage--;
            if (this.dialogm1.id === 0 ) {
                url = `${this.endPoint}producto_taxonomiap.php?q=${this.search}&page=${this.productsSearchPage}`;
            } else {
                url = `${this.endPoint}producto_taxonomiap.php?q=${this.search}&almacen=${this.dialogm1.id}&page=${this.productsSearchPage}`;
            }
            
            this.searchProductFromDB(url);
        },
        searchServiceFromDB(url) {
          console.log(url);
                axios.get(url)
                    .then(response => {
                        this.servicesSelected = response.data;
                        this.step = 3;
                        this.categoryServiceSelected = null;
                    });  
        },
        nextSearchServices() {
            this.servicesSearchPage++;
            const url = `${this.endPoint}servicio_taxonomias.php?q=${this.search}&page=${this.servicesSearchPage}`; 
            this.searchServiceFromDB(url);
        },
        prevSearchServices() {
            this.servicesSearchPage--;
            const url = `${this.endPoint}servicio_taxonomias.php?q=${this.search}&page=${this.servicesSearchPage}`;
            
            this.searchServiceFromDB(url);
        }
    },
    computed: {
        subTotal() {
            let subtotal = 0;
            this.details.forEach(detail => {
                if (detail.incluye_impuesto == 1) {
                    subtotal += (detail.price * detail.quantity) / this.div_igv;
                   
                }  else if (detail.incluye_impuesto == 3) {
                    subtotal += 0;
                }
                else {
                    subtotal += detail.price * detail.quantity;
                }
            });
            return Number(subtotal).toFixed(2);
        },
        discountTotal() {
            let discountTotal = this.discount;
            /*
            this.details.forEach(detail => {
                switch (detail.discount_type) {
                    case 'fijo':
                        discountTotal += Number(detail.discount);
                        break;
                    
                    case 'porcentual':
                        let d = detail.price * detail.quantity;
                        discountTotal += (Number(d) * Number(detail.discount)) / 100;
                        break;
                
                    default:
                        discountTotal += 0;
                }
            });
            */
            

            return discountTotal.toFixed(2);
        },
        igv() {

            let subtotal = 0;

            let total = 0;

            this.details.forEach(detail => {
                
                if (detail.incluye_impuesto == 1) {
                    subtotal += (detail.price * detail.quantity) / this.div_igv;
                }
                else if (detail.incluye_impuesto == 3) {
                    subtotal += 0;
                }
                else {
                    subtotal += detail.price * detail.quantity;
                }

                if (detail.incluye_impuesto == 3) {
                    total += 0;
                }
                else {
                    total += detail.price * detail.quantity;
                }

                /*
                switch (detail.discount_type) {
                    case 'fijo':
                        discountTotal += Number(detail.discount);
                        break;
                    
                    case 'porcentual':
                        let d = detail.price * detail.quantity;
                        discountTotal += (Number(d) * Number(detail.discount)) / 100;
                        break;
                
                    default:
                        discountTotal += 0;
                }
                */
            });

            //const importe = (subtotal - discountTotal);
            const importe = total - subtotal;

            return importe.toFixed(2);

        },
        total() {
            let subtotal = 0;

            this.details.forEach(detail => {

                if (detail.incluye_impuesto == 3) {
                    subtotal += 0;
                }
                else {
                    subtotal += detail.price * detail.quantity;
                }

                /*
                switch (detail.discount_type) {
                    case 'fijo':
                        discountTotal += Number(detail.discount);
                        break;
                    
                    case 'porcentual':
                        let d = detail.price * detail.quantity;
                        discountTotal += (Number(d) * Number(detail.discount)) / 100;
                        break;
                
                    default:
                        discountTotal += 0;
                }
                */
            });

            //const importe = (subtotal - discountTotal) * 0.18;

            //const total = subtotal - discountTotal + importe;
            const total = subtotal;

            return total.toFixed(2);
        },
        payment() {
            let subtotal = 0;

            let discountTotal = this.discount;
            

            this.details.forEach(detail => {
               
                if (detail.incluye_impuesto == 3) {
                    subtotal += 0;
                }
                else {
                    subtotal += detail.price * detail.quantity;
                }

            });

            const total = subtotal - discountTotal;

            return total.toFixed(2);
        },
        filteredCustomers() {
            console.log("filteredCustomers")
            return this.customers.filter(customer => customer.name.toLowerCase().includes(this.search_customer.toLowerCase()) || customer.document.includes(this.search_customer));
        },
        montoFaltante() {
            return this.payment - this.amount_entered.toFixed(2) >= 0 ? Number(this.payment - this.amount_entered.toFixed(2)).toFixed(2) : 0.00;
        },
        miVuelto() {
            return Number(this.amount_entered).toFixed(2) - this.payment < 0 ? 0.00 : Number(Number(this.amount_entered).toFixed(2) - this.payment).toFixed(2);
        }
    },
    watch: {
        search: function (val) {
            if (val.length > 0) {
                if (this.materia == 'products') {
                    let url = '';
                    if (this.dialogm1.id === 0 ) {
                        url = `${this.endPoint}producto_taxonomiap.php?q=${val}&page=${this.productsSearchPage}`;
                    } else {
                        url = `${this.endPoint}producto_taxonomiap.php?q=${val}&almacen=${this.dialogm1.id}&page=${this.productsSearchPage}`;
                    }
                
                    this.searchProductFromDB(url);
                }
                else {

                    const url = `${this.endPoint}servicio_taxonomias.php?q=${val}&page=${this.servicesSearchPage}`;
                    this.searchServiceFromDB(url);
                }
                
            } else {
                if (this.materia == 'products') {
                    this.step = 1;
                    this.productsSelected = [];
                    this.categorySelected = null;
                } else {
                    this.step = 1;
                    this.servicesSelected = [];
                    this.servicesSelected = null;
                }
               
            }
        },        
        dialogm1: function (val) {
            console.log(val);
            if (this.step === 3) {
                console.log(this.categorySelected);
                if (this.categorySelected != null) {
                    this.productsPage = 0;
                    let url = '';
                    if (this.dialogm1.id === 0 ) {
                        url = `${this.endPoint}taxonomiap_valor.php?id=${this.categorySelected.id_taxonomiap}&valor=${this.categorySelected.valor}&q=products&page=${this.productsPage}`;
                    } else {
                        url = `${this.endPoint}taxonomiap_valor.php?id=${this.categorySelected.id_taxonomiap}&valor=${this.categorySelected.valor}&almacen=${val.id}&q=products&page=${this.productsPage}`;
                    }
                
                    this.getMyProductsFromDB(url, false);
                    this.dialog = false;
                    
                } else {
                    this.productsSearchPage = 0;
                    if (this.search.length > 0) {
                        let url = '';
                        if (this.dialogm1.id === 0 ) {
                            url = `${this.endPoint}producto_taxonomiap.php?q=${this.search}&page=${this.productsSearchPage}`;
                        } else {
                            url = `${this.endPoint}producto_taxonomiap.php?q=${this.search}&almacen=${this.dialogm1.id}&page=${this.productsSearchPage}`;
                        }
                    
                        this.searchProductFromDB(url);
                        
                    } else {
                        this.step = 1;
                        this.productsSelected = [];
                        this.categorySelected = null;
                    }
                    this.dialog = false;
                }
            }
        }
    },
    beforeDestroy () {
        document.removeEventListener("backbutton", this.yourCallBackFunction);
    }
});
