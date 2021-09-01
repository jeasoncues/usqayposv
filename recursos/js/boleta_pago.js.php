<?php require_once('../../globales_sistema.php'); ?>
jQuery.fn.reset = function () {
$(this).each (function() { this.reset(); });
}

$(document).ready(function() {

    
    $("input").keypress(function (e) {
        if ((e.which && e.which == 13) || (e.keyCode && e.keyCode == 13)) {
            // $('button[type=submit] .default').click();a
            return false;
        } 
    });

    $("td input").keypress(function (e) {
        if ((e.which && e.which == 13) || (e.keyCode && e.keyCode == 13)) {
            // $('button[type=submit] .default').click();a
            return false;
        } 
    });

    var oData = [];
	var oSuspencion = [];
    var sueldo = 0;
    var totalingreso = 0;
    var totaldescuento = 0;
    var totalneto = 0;
    var totalessalud = 0;
    //***********************************//
    //variables contadoras por conceptos //
    //***********************************//
    var tb = 0; // totalBruto
    var td = 0; // total descuentos
    var tat = 0; //total_aportes_trabajador
    var tn = 0 //total_neto
    var tae = 0 //total_aportes_empleador


	$("#conceptos").select2();
	// $("#conceptos2").select2();
	$("#conceptos2").hide();
	$('#lblConcepto2').hide();
	$('#btnAdd').hide();
	$('#conceptos').change(function(){
		if($('#conceptos').val() != 0){
			$.post('ws/cliente.php', {
                op: 'getConceptos',
                id: $('#conceptos').val(),
            }, function(data) {
            	mData = JSON.parse(data);
            	console.log(mData);
            	var ht = '';
            	$.each(mData , function(key, value){
            		console.log(value.codigo +" "+value.descripcion);
            		ht += "<option value = '"+value.codigo+"'>"+value.codigo+" - "+value.descripcion+"</option> ";
            	});
            	$("#conceptos2").html(ht);
            	$("#conceptos2").select2();
				$('#lblConcepto2').show();	
				$("#conceptos2").show();
				$('#btnAdd').show();

            });
		}else{
			$("#conceptos2").hide();
			$("#conceptos2").select2('destroy');
			$('#lblConcepto2').hide();
			$('#btnAdd').hide();
		}
	});

    $.post('ws/cliente.php',{
        op: 'gettrabajador',
        id: $('#id_trabajador').val(),
    },function(data){
        mData = JSON.parse(data);
        DateI = (mData.fecha_de_ingreso).split("-");
        DateC = (mData.fecha_cese).split("-");
        // console.log(DateC);
        DiasPago = 0;
        date = new Date();
        mesActual = date.getMonth()+1;
        AnoActual = parseInt(date.getFullYear());
        // console.log(DateC);
        if(mData.fecha_cese == "0000-00-00"){
            mesIngreso = parseInt(DateI[1]);
            AnoIngreso = parseInt(DateI[0]);
            if(mesActual - mesIngreso == 0 && AnoActual - AnoIngreso == 0){
                DiasPago = 31 - parseInt(DateI[2]);
            }else{
                DiasPago = 30;
            }
        }else{
            mesCese = parseInt(DateC[1]);
            AnoCese = parseInt(DateC[0]);
            if(mesActual - mesCese == 0){
                if(parseInt(DateC[2]) == 31){
                    DiasPago = 30;
                }else{
                    console.log(parseInt(DateC[2]));
                    DiasPago = parseInt(DateC[2]);
                }
            }else{
                DiasPago = 30;
            }
            // alert(DiasPago);
        }



        let oJson = {
            'id' : 4,
            'codigo' : "0121",
            'concepto' : "REMUNERACION O JORNAL BASICO",
            'ingreso' : ((mData.sueldo_basico /30)*DiasPago),
            'descuento' : 0,
            'neto' : 0
        }
        totalingreso = parseFloat(((mData.sueldo_basico /30)*DiasPago));
        totalessalud = parseFloat(((mData.sueldo_basico /30)*DiasPago));
        $('#totalingreso').html('');
        $('#totalingreso').append(parseFloat(totalingreso).toFixed(2));
        oData.push(oJson);
                //paintDom(oData);
        let htaportes = '';
        htaportes += '<tr>';
        htaportes += "<td>"+oJson.codigo+"</td>";
        htaportes += "<td>"+oJson.concepto+"</td>";
        htaportes += "<td id='lblingresos2'>"+parseFloat(oJson.ingreso).toFixed(2)+"</td>";
        htaportes += "<td>"+parseFloat(oJson.descuento).toFixed(2)+"</td>";
        htaportes += "<td>"+parseFloat(oJson.neto).toFixed(2)+"</td>";
        htaportes += '<td>' +
                        '<button type="button" class="btn-link text-red btnEliminaPago" id="' + oJson.id + '">' +
                        '<i class="fa fa-trash-o" aria-hidden="true"></i>' +
                        '</button>' +
                        '</td>' ;
        htaportes += '</tr>';
        $('#ConceptosDiv').append(htaportes);
        htaportes = '';





        if(mData.asignacion_familiar == 1){
            let ingreso = 0;

            $.post('ws/cliente.php',{
                op: 'getconceptoing',
                codigo: 201,
            },function(data){
                mDataaux = JSON.parse(data);
                console.log(data);
                let ingreso2 = 0;  
                if(data != 0) {
                    if(mDataaux.tipo == 1){
                        ingreso2 = 930 * (mDataaux.monto/100);
                    }else{
                        ingreso2 = mDataaux.monto;
                    }
                }else{
                    ingreso2 = 0;
                }

                let oJson2 = {
                    'id' : 4,
                    'codigo' : "0201",
                    'concepto' : 'ASIGNACION FAMILIAR',
                    'ingreso' : ingreso2,
                    'descuento' : 0,
                    'neto' : 0
                }
                totalingreso = totalingreso + parseFloat(ingreso2);

                console.log(totalessalud);

                $('#totalingreso').html('');
                $('#totalingreso').append(parseFloat(totalingreso).toFixed(2));
                totalessalud = parseFloat($('#totalingreso').html())
                // alert(totalingreso);
                $('#totalneto').html('');
                $('#totalneto').append(parseFloat(totalingreso - totaldescuento).toFixed(2));
                // alert(totalingreso)
                oData.push(oJson2);
                //paintDom(oData);
                htaportes = '';
                htaportes += '<tr>';
                htaportes += "<td>"+oJson2.codigo+"</td>";
                htaportes += "<td>"+oJson2.concepto+"</td>";
                htaportes += "<td id='lblingresos2'>"+parseFloat(oJson2.ingreso).toFixed(2)+"</td>";
                htaportes += "<td>"+parseFloat(oJson2.descuento).toFixed(2)+"</td>";
                htaportes += "<td>"+parseFloat(oJson2.neto).toFixed(2)+"</td>";
                htaportes += '<td>' +
                                '<button type="button" class="btn-link text-red btnEliminaPago" id="' + oJson2.id + '">' +
                                '<i class="fa fa-trash-o" aria-hidden="true"></i>' +
                                '</button>' +
                                '</td>' ;
                htaportes += '</tr>';
                $('#ConceptosDiv').append(htaportes);
                htaportes = '';

                let esJson = {
                    'id' : 2,
                    'codigo' : "0804",
                    'concepto' : 'ESSALUD(REGULAR CBSSP AGRAR/AC)TRAB',
                    'ingreso' : 0,
                    'descuento' : 0,
                    'neto' : parseFloat(totalessalud*0.09).toFixed(2)
                }
                oData.push(esJson);



                htaportes = '';
                htaportes += '<tr>';
                htaportes += "<td>"+esJson.codigo+"</td>";
                htaportes += "<td>"+esJson.concepto+"</td>";
                htaportes += "<td>"+parseFloat(esJson.ingreso).toFixed(2)+"</td>";
                htaportes += "<td>"+parseFloat(esJson.descuento).toFixed(2)+"</td>";
                htaportes += "<td id='netoessalud'>"+parseFloat(esJson.neto).toFixed(2)+"</td>";
                htaportes += '<td>' +
                                '<button type="button" class="btn-link text-red btnEliminaPago" id="' + esJson.id + '">' +
                                '<i class="fa fa-trash-o" aria-hidden="true"></i>' +
                                '</button>' +
                                '</td>' ;
                htaportes += '</tr>';
                $('#ConceptosDiv').append(htaportes);


            });

        }else{
            let esJson = {
                'id' : 2,
                'codigo' : "0804",
                'concepto' : 'ESSALUD(REGULAR CBSSP AGRAR/AC)TRAB',
                'ingreso' : 0,
                'descuento' : 0,
                'neto' : parseFloat(totalessalud*0.09).toFixed(2)
            }
            oData.push(esJson);



            htaportes = '';
            htaportes += '<tr>';
            htaportes += "<td>"+esJson.codigo+"</td>";
            htaportes += "<td>"+esJson.concepto+"</td>";
            htaportes += "<td>"+parseFloat(esJson.ingreso).toFixed(2)+"</td>";
            htaportes += "<td>"+parseFloat(esJson.descuento).toFixed(2)+"</td>";
            htaportes += "<td id='netoessalud'>"+parseFloat(esJson.neto).toFixed(2)+"</td>";
            htaportes += '<td>' +
                            '<button type="button" class="btn-link text-red btnEliminaPago" id="' + esJson.id + '">' +
                            '<i class="fa fa-trash-o" aria-hidden="true"></i>' +
                            '</button>' +
                            '</td>' ;
            htaportes += '</tr>';
            $('#ConceptosDiv').append(htaportes);
        }

        if(mData.regimen_pensionario == 21 || mData.regimen_pensionario == 22 || mData.regimen_pensionario == 23 || mData.regimen_pensionario == 24 ){

            $.post('ws/cliente.php',{
                op: 'getRegimenPensionario',
                codigo: mData.regimen_pensionario,
            },function(data){
                mDataaux = JSON.parse(data);

                let ingreso = parseFloat($('#totalingreso').html());
                if(mData.tipo_flujo == 1){
                    cp = ingreso*(mDataaux.comision_porcentual/100); // comision porcentual
                    $('#cm_p').val(mDataaux.comision_porcentual);
                }
                else{
                    cp = ingreso*(mDataaux.comision_porcentual_sf/100); // comision porcentual
                    $('#cm_p').val(mDataaux.comision_porcentual_sf);
                }


                ps = ingreso*(mDataaux.prima_seguro/100);//prima seguro;
                $('#pri_seg').val(mDataaux.prima_seguro);
                ao = ingreso*(mDataaux.aportacion_obligatoria/100); // aportacion obligatoria
                $('#apor_ob').val(mDataaux.aportacion_obligatoria);


                let cpJson = {
                    'id' : 1,
                    'codigo' : "0601",
                    'concepto' : 'COMISION AFP PORCENTUAL',
                    'ingreso' : 0,
                    'descuento' : cp,
                    'neto' : 0
                }
                oData.push(cpJson);



                htaportes = '';
                htaportes += '<tr>';
                htaportes += "<td>"+cpJson.codigo+"</td>";
                htaportes += "<td>"+cpJson.concepto+"</td>";
                htaportes += "<td>"+parseFloat(cpJson.ingreso).toFixed(2)+"</td>";
                htaportes += "<td><input class='form-control' id='descuentos' type='text' value="+parseFloat(cp).toFixed(2)+"></td>";
                htaportes += "<td>"+parseFloat(cpJson.neto).toFixed(2)+"</td>";
                htaportes += '<td>' +
                                '<button type="button" class="btn-link text-red btnEliminaPago" id="' + cpJson.id + '">' +
                                '<i class="fa fa-trash-o" aria-hidden="true"></i>' +
                                '</button>' +
                                '</td>' ;
                htaportes += '</tr>';
                $('#ConceptosDiv').append(htaportes);

                let psJson = {
                    'id' : 1,
                    'codigo' : "0606",
                    'concepto' : 'PRIMA DE SEGURO AFP',
                    'ingreso' : 0,
                    'descuento' : ps,
                    'neto' : 0
                }
                oData.push(psJson);

                htaportes = '';
                htaportes += '<tr>';
                htaportes += "<td>"+psJson.codigo+"</td>";
                htaportes += "<td>"+psJson.concepto+"</td>";
                htaportes += "<td>"+parseFloat(psJson.ingreso).toFixed(2)+"</td>";
                htaportes += "<td><input class='form-control' id='descuentos' type='text' value="+parseFloat(ps).toFixed(2)+"></td>";
                htaportes += "<td>"+parseFloat(psJson.neto).toFixed(2)+"</td>";
                htaportes += '<td>' +
                                '<button type="button" class="btn-link text-red btnEliminaPago" id="' + psJson.id + '">' +
                                '<i class="fa fa-trash-o" aria-hidden="true"></i>' +
                                '</button>' +
                                '</td>' ;
                htaportes += '</tr>';
                $('#ConceptosDiv').append(htaportes);


                let aoJson = {
                    'id' : 1,
                    'codigo' : "0608",
                    'concepto' : 'SPP - APORTACION OBLIGATORIA',
                    'ingreso' : 0,
                    'descuento' : ao,
                    'neto' : 0
                }

                oData.push(aoJson);
                
                htaportes = '';
                htaportes += '<tr>';
                htaportes += "<td>"+aoJson.codigo+"</td>";
                htaportes += "<td>"+aoJson.concepto+"</td>";
                htaportes += "<td>"+parseFloat(aoJson.ingreso).toFixed(2)+"</td>";
                htaportes += "<td><input class='form-control' id='descuentos' type='text' value="+parseFloat(ao).toFixed(2)+"></td>";
                htaportes += "<td>"+parseFloat(aoJson.neto).toFixed(2)+"</td>";
                htaportes += '<td>' +
                                '<button type="button" class="btn-link text-red btnEliminaPago" id="' + aoJson.id + '">' +
                                '<i class="fa fa-trash-o" aria-hidden="true"></i>' +
                                '</button>' +
                                '</td>' ;
                htaportes += '</tr>';
                $('#ConceptosDiv').append(htaportes);

                totaldescuento = parseFloat(cp) + parseFloat(ps) + parseFloat(ao);
                tat = tat + totaldescuento;

                $('#totaldescuento').html('');
                $('#totaldescuento').append(parseFloat(totaldescuento).toFixed(2));

                $('#totalneto').html('');
                $('#totalneto').append(parseFloat(totalingreso - totaldescuento).toFixed(2));


            });

        }else{
            let ingreso = parseFloat($('#totalingreso').html());
            var onp_porc = ingreso*(13/100); // comision porcentual
            let oJson = {
                'id' : 1,
                'codigo' : "0607",
                'concepto' : 'SISTEMA NACIONAL DE PENSIONES DEL 19990',
                'ingreso' : 0,
                'descuento' : onp_porc,
                'neto' : 0,
                'essalud' : 0,
                'afecto' : 0
            }
            oData.push(oJson);
            console.log(oJson);

            htaportes = '';
            htaportes += '<tr>';
            htaportes += "<td>"+oJson.codigo+"</td>";
            htaportes += "<td>"+oJson.concepto+"</td>";
            htaportes += "<td>"+parseFloat(oJson.ingreso).toFixed(2)+"</td>";
            htaportes += "<td>"+
                         "<input class='form-control' id='descuentos' type='text' value="+parseFloat(oJson.descuento).toFixed(2)+"></td>";
            htaportes += "<td>"+parseFloat(oJson.neto).toFixed(2)+"</td>";
            htaportes += '<td>' +
                            '<button type="button" class="btn-link text-red btnEliminaPago" id="' + oJson.id + '">' +
                            '<i class="fa fa-trash-o" aria-hidden="true"></i>' +
                            '</button>' +
                            '</td>' ;
            htaportes += '</tr>';
            $('#ConceptosDiv').append(htaportes);

            totaldescuento = parseFloat(onp_porc);
            tat = tat + totaldescuento;

            $('#totaldescuento').html('');
            $('#totaldescuento').append(parseFloat(totaldescuento).toFixed(2));

            $('#totalneto').html('');
            $('#totalneto').append(parseFloat(totalingreso - totaldescuento).toFixed(2));
        }

        if(mData.quinta_categoria == 1){

            let qcJson = {
                'id' : 1,
                'codigo' : "0605",
                'concepto' : 'RENTA QUINTA CATEGORIA RETENCIONES',
                'ingreso' : 0,
                'descuento' : 0,
                'neto' : 0
            }

            oData.push(qcJson);
            
            htaportes = '';
            htaportes += '<tr>';
            htaportes += "<td>"+qcJson.codigo+"</td>";
            htaportes += "<td>"+qcJson.concepto+"</td>";
            htaportes += "<td>"+parseFloat(qcJson.ingreso).toFixed(2)+"</td>";
            htaportes += "<td><input class='form-control' id='descuentos' type='text' value="+parseFloat(qcJson.descuento).toFixed(2)+"></td>";
            htaportes += "<td>"+parseFloat(qcJson.neto).toFixed(2)+"</td>";
            htaportes += '<td>' +
                            '<button type="button" class="btn-link text-red btnEliminaPago" id="' + qcJson.id + '">' +
                            '<i class="fa fa-trash-o" aria-hidden="true"></i>' +
                            '</button>' +
                            '</td>' ;
            htaportes += '</tr>';
            $('#ConceptosDiv').append(htaportes);

        }


    });

	$('#btnAdd').click(function(){
		let conceptos = $('#conceptos').val();
		let conceptos2 = $('#conceptos2').val();
		console.log(conceptos);
		console.log(conceptos2);
		let sueldo = parseFloat($('#sueldo_trabajador').val()).toFixed(2);
        // alert(sueldo);

		$.post('ws/cliente.php', {
            op: 'getConceptos2',
            id: conceptos,
            id2: conceptos2
        }, function(data) {
        	mData = JSON.parse(data);
        	console.log(mData);
            let ingreso = 0, descuento = 0, neto = 0;


            if(mData.tipo == 1){
                if(conceptos == 1){
                     descuento = sueldo*(mData.monto/100);
                     tat= tat+descuento;
                }else if(conceptos == 2){
                    neto = sueldo*(mData.monto/100);
                    tae = tae + neto;
                }else if(conceptos == 3){
                    descuento = sueldo*(mData.monto/100);
                    td = td + sueldo*(mData.monto/100);
                }else if(conceptos == 4){
                    if(mData.codigo == '0201')
                        ingreso = 930*(mData.monto/100);
                    else
                        ingreso = sueldo*(mData.monto/100);
                }else{
                }
            }else{

                if(conceptos == 1){
                     descuento = mData.monto;
                     tat= tat+descuento;
                }else if(conceptos == 2){
                    neto = mData.monto;
                    tae = tae + neto;
                }else if(conceptos == 3){
                    descuento = mData.monto;
                    td = td + mData.monto;
                }else if(conceptos == 4){
                    if(mData.codigo == '0201')
                        ingreso = mData.monto;
                    else
                        ingreso = mData.monto;
                }else{
                }
            }

            

        	// console.log(oData.length);
        	if(oData.length>0){

        		let flag = 0;
	    		$.each(oData , function(key, value){
                    if(value.codigo == mData.codigo){
                        flag = 1;
                    }
                });

                if(oSuspencion.length > 0){
                    $.each(oSuspencion , function(key, value){
                        if(value.codigo == mData.codigo){
                            flag = 1;
                        }
                    });
                }

	    		if(flag == 0){
                    if(conceptos != 5 && conceptos != 3 && conceptos != 4){
                        let oJson = {
                            'id' : conceptos,
                            'codigo' : mData.codigo,
                            'concepto' : mData.descripcion,
                            'ingreso' : ingreso,
                            'descuento' : descuento,
                            'neto' : neto
                        }
    	    			oData.push(oJson);
                        console.log(oJson);

                        htaportes = '';
                        htaportes += '<tr>';
                        htaportes += "<td>"+oJson.codigo+"</td>";
                        htaportes += "<td>"+oJson.concepto+"</td>";
                        htaportes += "<td>"+parseFloat(oJson.ingreso).toFixed(2)+"</td>";
                        if(mData.codigo == '0605'){
                            htaportes += "<td><input class='form-control' id='descuentos' type='text' value="+parseFloat(oJson.descuento).toFixed(2)+"></td>";
                        }else{
                            htaportes += "<td>"+parseFloat(oJson.descuento).toFixed(2)+"</td>";
                        }
                        htaportes += "<td>"+parseFloat(oJson.neto).toFixed(2)+"</td>";
                        htaportes += '<td>' +
                                        '<button type="button" class="btn-link text-red btnEliminaPago" id="' + oJson.id + '">' +
                                        '<i class="fa fa-trash-o" aria-hidden="true"></i>' +
                                        '</button>' +
                                        '</td>' ;
                        htaportes += '</tr>';
                        $('#ConceptosDiv').append(htaportes);

                        
                        calculateSumIngreso();
                        calculateSumDescuentos();
                        calculateSumNeto();

                    }else if(conceptos == 3){

                        let oJson = {
                            'id' : conceptos,
                            'codigo' : mData.codigo,
                            'concepto' : mData.descripcion,
                            'ingreso' : ingreso,
                            'descuento' : descuento,
                            'neto' : neto,
                            'essalud' : mData.essalud,
                            'afecto' : mData.afecto
                        }
                        oData.push(oJson);
                        console.log(oJson);

                        htaportes = '';
                        htaportes += '<tr>';
                        htaportes += "<td>"+oJson.codigo+"</td>";
                        htaportes += "<td>"+oJson.concepto+"</td>";
                        htaportes += "<td>"+parseFloat(oJson.ingreso).toFixed(2)+"</td>";
                        htaportes += "<td><input class='form-control' id='essaludDes' type='hidden' value="+oJson.essalud+">"+
                                     "<input class='form-control' id='descuentos' type='text' value="+parseFloat(oJson.descuento).toFixed(2)+">"+
                                     "<input class='form-control' id='afpDes' type='hidden' value="+oJson.afecto+"></td>";
                        htaportes += "<td>"+parseFloat(oJson.neto).toFixed(2)+"</td>";
                        htaportes += '<td>' +
                                        '<button type="button" class="btn-link text-red btnEliminaPago" id="' + oJson.id + '">' +
                                        '<i class="fa fa-trash-o" aria-hidden="true"></i>' +
                                        '</button>' +
                                        '</td>' ;
                        htaportes += '</tr>';
                        $('#ConceptosDiv').append(htaportes);

                        calculateSumIngreso();
                        calculateSumDescuentos();
                        calculateSumNeto();


                    }else if(conceptos == 4){

                        let oJson = {
                            'id' : conceptos,
                            'codigo' : mData.codigo,
                            'concepto' : mData.descripcion,
                            'ingreso' : ingreso,
                            'descuento' : descuento,
                            'neto' : neto,
                            'essalud' : mData.essalud,
                            'afecto' : mData.afecto
                        }
                        oData.push(oJson);
                        console.log(oJson);

                        htaportes = '';
                        htaportes += '<tr>';
                        htaportes += "<td>"+oJson.codigo+"</td>";
                        htaportes += "<td>"+oJson.concepto+"</td>";
                        htaportes += "<td><input class='form-control' id='essalud' type='hidden' value="+oJson.essalud+">";
                        if(mData.tipo == 3){
                            htaportes += "<input class='form-control' id='lblingresos' type='text' value="+parseFloat(oJson.ingreso).toFixed(2)+">"+
                                "<input class='form-control' id='afp' type='hidden' value="+oJson.essalud+"></td>";
                        }
                        else
                            htaportes += ""+parseFloat(oJson.ingreso).toFixed(2)+"</td>"; 

                        htaportes += "<td>"+parseFloat(oJson.descuento).toFixed(2)+"</td>";
                        htaportes += "<td>"+parseFloat(oJson.neto).toFixed(2)+"</td>";
                        htaportes += '<td>' +
                                        '<button type="button" class="btn-link text-red btnEliminaPago" id="' + oJson.id + '">' +
                                        '<i class="fa fa-trash-o" aria-hidden="true"></i>' +
                                        '</button>' +
                                        '</td>' ;
                        htaportes += '</tr>';
                        $('#ConceptosDiv').append(htaportes);
                        
                        calculateSumIngreso();
                        calculateSumDescuentos();
                        calculateSumNeto();

                    }else{
                        let oJson = {
                            'id' : conceptos,
                            'codigo' : mData.codigo,
                            'concepto' : mData.descripcion,
                            'dias' : 0
                        }
                        oSuspencion.push(oJson);
                        console.log(oJson);

                        suspenciones = '';
                        suspenciones += '<tr>';
                        suspenciones += "<td>"+oJson.codigo+"</td>";
                        suspenciones += "<td>"+oJson.concepto+"</td>";
                        suspenciones += "<td colspan='2'><b>Numero de dias</b></td>";
                        suspenciones += "<td><input class='form-control' id='suspencionlaboral' type='text' value="+parseInt(0)+"></td>";
                        suspenciones += '<td>' +
                                        '<button type="button" class="btn-link text-red btnEliminaSuspecion" id="' + oJson.id + '">' +
                                        '<i class="fa fa-trash-o" aria-hidden="true"></i>' +
                                        '</button>' +
                                        '</td>' ;
                        suspenciones += '</tr>';
                        $('#suspencionDiv').append(suspenciones);
                    }

	    		}else{
	    			alert("Ya el concepto existe en la lista del trabajador");
	    		}
        	}
			
        	//console.log(oData);
        	//paintDom(oData);
        });



	});

    $(document).on('click', '.btnEliminaSuspecion', function (e) {
        var td = $(this).parent();
        var tr = td.parent();
        var trIndex = tr.index();
        $('#suspencionDiv > tr').eq(trIndex).children('td').remove();
        oSuspencion.splice(trIndex,1);
        console.log(oSuspencion);
    });

	$(document).on('click', '.btnEliminaPago', function (e) {
		let id = $(this).attr("id");
        var td = $(this).parent();
        var tr = td.parent();
        var trIndex = tr.index();
        // var currentRow = $(this).closest("tr");

        // var col0=currentRow.find("td:eq(0)").html(); 
        // var col2=parseFloat(currentRow.find("td:eq(2)").html()).toFixed(2); 
        // var col3=parseFloat(currentRow.find("td:eq(3)").html()).toFixed(2); 
        // var col4=currentRow.find("td:eq(4)").html(); 

    
        $('#ConceptosDiv > tr').eq(trIndex).children('td').remove();
        oData.splice(trIndex,1);

        calculateSumIngreso();
        calculateSumDescuentos();
        calculateSumNeto();
    	console.log(oData);
	});


    $(document).on('keydown', '#suspencionlaboral', function (e) {

        var td = $(this).parent();
        var tr = td.parent();
        var trIndex = tr.index();
        if(!isNaN(this.value) && this.value.length!=0) {
            console.log("trIndex",this.value);
            oSuspencion[trIndex]["dias"] = this.value;
            console.log( oSuspencion[trIndex])
        }
    });

    $(document).on('keyup keydown', '#descuentos', function(e){

        var td = $(this).parent();
        var tr = td.parent();
        var trIndex = tr.index();
        if(!isNaN(this.value) && this.value.length!=0) {
            console.log("trIndex",this.value);
            oData[trIndex]["descuento"] = this.value;
            console.log( oData[trIndex])
        }

        calculateSumDescuentos();

    });

     $(document).on('keyup keydown', '#lblingresos', function(e){

        var td = $(this).parent();
        var tr = td.parent();
        var trIndex = tr.index();
        if(!isNaN(this.value) && this.value.length!=0) {
            console.log("trIndex",this.value);
            oData[trIndex]["ingreso"] = this.value;
        }

        calculateSumIngreso();

    });

    $('#Emitir').on("click", function(e){
        e.preventDefault();
        var dl = $('#dl').val();
        var dnl = $('#dnl').val();
        var ds = $('#ds').val();
        var joth = $('#joth').val();
        var jom = $('#jom').val();
        var sth = $('#sth').val();
        var sm = $('#sm').val();
        var idt = $('#id_trabajador').val();
        var idu = $('#id_usuario').val();
        var mes = $('#mes').val();
        var tbruto = parseFloat($('#totalingreso').html()).toFixed(2);
        // var tbruto = parseFloat($('#totaldescuento').html()).toFixed(2);
        var totalneto = parseFloat($('#totalneto').html()).toFixed(2);
        var qc = 0;
        if(mes == 0){
            alert("Debe Seleccionar un Mes de Pago");
            return false;
        }
        console.log(oSuspencion.length);
        if(oSuspencion.length == 0){
            oSuspencion = "-";
            console.log(oSuspencion);
        }

        if(qc == undefined)
            qc = 0;

        for(i=0; i<oData.length;i++){
            if(oData[i].codigo == '0804'){
                oData[i].neto  = parseFloat($('#netoessalud').html());
            }
        }

        $('#tb tr #descuentos').each(function() {
            var currentRow = $(this).closest("tr");
            var td = $(this).parent();
            var tr = td.parent();
            var trIndex = tr.index();

            var cod = currentRow.find("td:eq(0)")[0].innerHTML;
            var num=parseFloat(currentRow.find("td:eq(0) #descuentos").prevObject[0].childNodes[3].childNodes[0].value);
            console.log("ojo2", num);

            if(cod == '0601'){
                oData[trIndex]["descuento"] = num;
            }
            if(cod == '0606'){
                oData[trIndex]["descuento"] = num;
            }
            if(cod == '0608'){
                oData[trIndex]["descuento"] = num;
            }
            if(cod == '0607'){
                oData[trIndex]["descuento"] = num;
            }
            
            
        });

        console.log(oData);
        $.ajax({
            cache: false,
            type: 'POST',
            url: 'ws/cliente.php',
            data: { 
                op: 'boletadepago',
                data: oData,
                data2: oSuspencion, 
                idt:idt,
                tbruto:tbruto,
                td:td,
                tat:tat,
                totalneto:totalneto,
                tae:tae,
                dl:dl,
                dnl:dnl,
                ds:ds,
                joth:joth,
                jom:jom,
                sth:sth,
                sm:sm, 
                idu:idu,
                mes:mes
            },
            dataType: 'json',
            success:function(response) {
                $("#id_boleta").val(response);
                $("#form_emitir").submit();
            },
            error: function (err) {
                alert(JSON.stringify(err));
            }
        });

    });

});

function calculateSumIngreso() {
    var sum = 0;
    var ingreso2 = 0;
    var essalud = 0;
    //iterate through each textboxes and add the values
    $('#tb tr #lblingresos2').each(function() {
        console.log("#lblingresos2", $(this).html())
        ingreso2 += parseFloat($(this).html());
    });

    $('#tb tr #lblingresos').each(function() {
        if(!isNaN(this.value) && this.value.length!=0) {
            sum += parseFloat(this.value);
        }
    });

    

    
    //.toFixed() method will roundoff the final sum to 2 decimal places
    // console.log(sum.toFixed(2));
    $('#totalingreso').html('');
    $('#totalingreso').append(parseFloat(ingreso2 + sum).toFixed(2));
    calculateEssalud();
    calculateAfp();
    calculateSumNeto();
}


function calculateSumDescuentos() {
    var sum = 0;
    var essalud = 0;

    $('#tb tr #descuentos').each(function() {
        if(!isNaN(this.value) && this.value.length!=0) {
            sum += parseFloat(this.value);
        }
        // console.log("#lblingresos", $(this).val())
        // ingreso2 += parseFloat($(this).html());
    });

    
    
    //.toFixed() method will roundoff the final sum to 2 decimal places
    // console.log(sum.toFixed(2));
    $('#totaldescuento').html('');
    $('#totaldescuento').append(parseFloat(sum).toFixed(2));
    calculateEssalud();
    calculateAfp();
    calculateSumNeto();
}

function calculateSumNeto() {

    var sum = 0;
    var ingreso = parseFloat($('#totalingreso').html());
    var descuento = parseFloat($('#totaldescuento').html());
    $('#totalneto').html('');
    $('#totalneto').append(parseFloat(ingreso - descuento).toFixed(2));
    

}

function calculateEssalud(){

    var essalud = 0, essalud2 = 0, ingreso2 = 0;

    $('#tb tr #lblingresos2').each(function() {
        ingreso2 += parseFloat($(this).html());
    });

    $('#tb tr #essalud').each(function() {
        var col2 = 0;
        var currentRow = $(this).closest("tr")
        if(this.value == 1){
            if(!isNaN(this.value) && this.value.length!=0) {
                console.log("#essalud", currentRow);
                if(currentRow.find("td:eq(2)")[0].childNodes[1].value!="")
                    col2=parseFloat(currentRow.find("td:eq(2)")[0].childNodes[1].value);
                
                essalud += col2;
            }
        }
    });

    $('#tb tr #essaludDes').each(function() {
        var currentRow = $(this).closest("tr");
        var col2 = 0;
        if(this.value == 1){
            if(!isNaN(this.value) && this.value.length!=0) {
                console.log("#essaludDes", currentRow);
                if(currentRow.find("td:eq(3)")[0].childNodes[1].value!="")
                    col2=parseFloat(currentRow.find("td:eq(3)")[0].childNodes[1].value);

                essalud2 += col2;
            }
        }
    });



    $('#netoessalud').html('');
    $('#netoessalud').append(parseFloat(((ingreso2 + essalud)-essalud2)*0.09).toFixed(2));

}

function calculateAfp(){

    var afp = 0, afp2 = 0, ingreso2 = 0;
    var onp = 0, onp2 = 0;
    var flag = 0;

    $('#tb tr #lblingresos2').each(function() {
        ingreso2 += parseFloat($(this).html());
    });


    $('#tb tr #afp').each(function() {
        var currentRow = $(this).closest("tr");
        var col2 = 0;
        if(this.value == 1){
            if(!isNaN(this.value) && this.value.length!=0) {
                if(currentRow.find("td:eq(2)")[0].childNodes[1].value!="")
                    col2=parseFloat(currentRow.find("td:eq(2)")[0].childNodes[1].value);
                flag = 1;
                afp += col2;
            }
        }
        if(this.value == 0){
            if(!isNaN(this.value) && this.value.length!=0) {
                if(currentRow.find("td:eq(2)")[0].childNodes[1].value!="")
                    col2=parseFloat(currentRow.find("td:eq(2)")[0].childNodes[1].value);
                flag = 1;
                // afp += col2;
            }
        }
    });

    $('#tb tr #afpDes').each(function() {
        var currentRow = $(this).closest("tr");
        var col2 = 0;
        if(this.value == 1){
            if(!isNaN(this.value) && this.value.length!=0) {
                if(currentRow.find("td:eq(3)")[0].childNodes[1].value!="")
                    col2=parseFloat(currentRow.find("td:eq(3)")[0].childNodes[1].value);
                flag = 1;
                afp2 += col2;
            }
        }
        if(this.value == 0){
            if(!isNaN(this.value) && this.value.length!=0) {
                if(currentRow.find("td:eq(3)")[0].childNodes[1].value!="")
                    col2=parseFloat(currentRow.find("td:eq(3)")[0].childNodes[1].value);
                flag = 1;
                // afp2 += col2;
            }
        }
    });

    if(flag == 1){
        afpBool(afp, afp2, ingreso2);
    }


    
}

function afpBool(afp, afp2, ingreso2){
    var sum = 0;
    $('#tb tr #descuentos').each(function() {
        var currentRow = $(this).closest("tr");

        if(!isNaN(this.value) && this.value.length!=0) {

            sum += parseFloat(this.value);

            var cod = currentRow.find("td:eq(0)")[0].innerHTML;
            var num=parseFloat(currentRow.find("td:eq(0) #descuentos").prevObject[0].childNodes[3].childNodes[0].value);
            console.log("ojo", num);
            // console.log(currentRow.find("td:eq(3)"));

            if(cod == '0601'){
                var recal = parseFloat(((ingreso2 + afp)-afp2));
                newVal = recal*(parseFloat($('#cm_p').val()/100));
                currentRow.find("td:eq(3)")[0].childNodes[0].value = newVal.toFixed(2);
            }
            if(cod == '0606'){
                var recal = parseFloat(((ingreso2 + afp)-afp2));
                newVal = recal*(parseFloat($('#pri_seg').val()/100));
                currentRow.find("td:eq(3)")[0].childNodes[0].value = newVal.toFixed(2);
            }
            if(cod == '0608'){
                var recal = parseFloat(((ingreso2 + afp)-afp2));
                newVal = recal*(parseFloat($('#apor_ob').val()/100));
                currentRow.find("td:eq(3)")[0].childNodes[0].value = newVal.toFixed(2);
            }

            if(cod == '0607'){
                var recal = parseFloat(((ingreso2 + afp)-afp2));
                console.log(recal);
                newVal = recal*(parseFloat(13/100));
                console.log(newVal);
                currentRow.find("td:eq(3)")[0].childNodes[0].value = newVal.toFixed(2);
            }

        }


        $('#totaldescuento').html('');
        $('#totaldescuento').append(parseFloat(sum).toFixed(2));
    });
}