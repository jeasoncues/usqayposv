<?php require_once('../../globales_sistema.php'); ?>
jQuery.fn.reset = function () {
$(this).each (function() { this.reset(); });
}

$(document).ready(function() {

	var oData = [];
	$("#products").select2();

	$('#products').change(function(){
		if($('#products').val() != 0){
			$.post('ws/producto_venta.php', {
                op: 'addProdFree',
                id: $('#products').val(),
            }, function(data) {
            	let mContent = JSON.parse(data);
            	$('#cant').val('1');
                $('#precio').val(mContent["precio_venta"]);
                $('#nombreProducto').val(mContent["nombre"]);
            });
		}else{
			$('#cant').val('');
			$('#precio').val('0.00');
		}
	});

	$('#add').click(function(){
		let idProducto = $('#products').val();
		let nombre = $('#nombreProducto').val();
		let cantidad = parseFloat($('#cant').val());
		let precio = parseFloat($('#precio').val());
		// cantidad > 0?alert("mayor"):alert("menor");
		let oJson = {
			'idProducto' : idProducto,
			'nombre' : nombre,
			'cantidad' : cantidad,
			'precio' : precio
		};
		$("#nombreProducto").prop("type", "hidden");
		if(idProducto != 0){
			if(cantidad != "" && cantidad > 0.00){
				if(precio != "" && precio > 0.00){
					oData.push(oJson);
					console.log(oData);
					$('#ProductoFree').html('');
					$('#SubTotal').html('');
					$('#IGV').html('');
					$('#Total').html('');
					var ht = '';
					var total = 0;
					$.each(oData, function(key, value) {
						ht += '<tr>';
						ht+= '<td>'+ value.idProducto +'</td>';
						ht+= '<td>'+ value.nombre +'</td>';
						ht+= '<td>'+ value.cantidad +'</td>';
						ht+= '<td>'+ parseFloat(parseFloat(value.cantidad) * parseFloat(value.precio)) +'</td>';
						ht+= '<td>' +
                            '<button type="button" class="btn-link text-red btnEliminaPago" id="' + key + '">' +
                            '<i class="fa fa-trash-o" aria-hidden="true"></i>' +
                            '</button>' +
                            '</td>' ;
							ht+= '<td>' +
                            '<button type="button" class="btn-link text-red btnEditPago" id="' + key + '">' +
                            '<i class="fa fa-edit" aria-hidden="true"></i>' +
                            '</button>' +
                            '</td>' ;
						ht+= '</tr>';

						// total += Number(value.cantidad) * Number(value.precio);
						total += parseFloat(parseFloat(value.cantidad) * parseFloat(value.precio));
						console.log("TOTALN",total)
					});
					$('#ProductoFree').html(ht);
					// let igv = Number(total) - (Number(total) / 1.18);
					let igv = parseFloat(parseFloat(total) - (parseFloat(total) / parseFloat(1.18)));
					console.log("IGV",igv)
					// let subt = Number(total) - igv;
					let subt = parseFloat(parseFloat(total) - igv);
					console.log("SUBT",subt)
					// $('#SubTotal').html(Number(subt).toFixed(2));
					$('#SubTotal').html(parseFloat(subt.toFixed(2)));
					$('#IGV').html(parseFloat(igv.toFixed(2)));
					$('#Total').html(parseFloat(total.toFixed(2)));
					//document.getElementById("cnt").val()="";
					//document.getElementById("precio").val()="0.00";
					$('#cant').val('');
					$('#precio').val('0.00');
					console.log(" ////// ");

					console.log("totalf",total)
					console.log("")

					
				}else{
					alert("El Precio debe ser un valor mayor a 0.00");
				}
			}else{
				alert("La Cantidad debe ser un valor mayor a 0.00");
			}
		}else{
			alert("Debe Seleccionar un Producto!");
		}
		return false;

	});

	$(document).on('click', '.btnEliminaPago', function () {
        var id = $(this).attr("id");
        oData.splice(id,1);

        $('#ProductoFree').html('');
		var ht = '';
		var total = 0;
		$.each(oData, function(key, value) {
			ht += '<tr>';
			ht+= '<td>'+ value.idProducto +'</td>';
			ht+= '<td>'+ value.nombre +'</td>';
			ht+= '<td>'+ value.cantidad +'</td>';
			ht+= '<td>'+ (value.cantidad * value.precio).toFixed(2) +'</td>';
			ht+= '<td>' +
                '<button type="button" class="btn-link text-red btnEliminaPago" id="' + key + '">' +
                '<i class="fa fa-trash-o" aria-hidden="true"></i>' +
                '</button>' +
                '</td>' ;
				ht+= '<td>' +
                '<button type="button" class="btn-link text-red btnEditPago" id="' + key + '">' +
				'<i class="fa fa-edit" aria-hidden="true"></i>' +
                '</button>' +
                '</td>' ;
			ht+= '</tr>';
			
			total += parseFloat(parseFloat(value.cantidad) * parseFloat(value.precio));
		});
		$('#ProductoFree').html(ht);
	
					
					let igv = parseFloat(parseFloat(total) - (parseFloat(total) / parseFloat(1.18)));
					let subt = parseFloat(parseFloat(total) - igv);
					$('#SubTotal').html(parseFloat(subt.toFixed(2)));
					$('#IGV').html(parseFloat(igv.toFixed(2)));
					$('#Total').html(parseFloat(total.toFixed(2)));
    });
	$(document).on('click', '.btnEditPago', function () {
        var id = $(this).attr("id");
		console.log(oData);
		let idProducto = $('#products').val();
		let nombre = $('#nombreProducto').val();
		let cantidad = parseFloat($('#cant').val());
		let precio = parseFloat($('#precio').val());

		$('#products').val(oData[id]['idProducto']);
		$('#nombreProducto').val(oData[id]['nombre']);
		parseFloat($('#cant').val(oData[id]['cantidad']));
		parseFloat($('#precio').val(oData[id]['precio']));


       oData.splice(id,1);

	   $("#nombreProducto").prop("type", "text");
	  
	   

        $('#ProductoFree').html('');
		var ht = '';
		var total = 0;
		$.each(oData, function(key, value) {
			ht += '<tr>';
			ht+= '<td>'+ value.idProducto +'</td>';
			ht+= '<td>'+ value.nombre +'</td>';
			ht+= '<td>'+ value.cantidad +'</td>';
			ht+= '<td>'+ (value.cantidad * value.precio).toFixed(2) +'</td>';
			ht+= '<td>' +
                '<button type="button" class="btn-link text-red btnEliminaPago" id="' + key + '">' +
                '<i class="fa fa-trash-o" aria-hidden="true"></i>' +
                '</button>' +
                '</td>' ;
				ht+= '<td>' +
                '<button type="button" class="btn-link text-red btnEditPago" id="' + key + '">' +
                '<i class="fa fa-edit" aria-hidden="true"></i>' +
                '</button>' +
                '</td>' ;
			ht+= '</tr>';

			total += parseFloat(parseFloat(value.cantidad) * parseFloat(value.precio));
		});
		
		$('#ProductoFree').html(ht);
					
					let igv = parseFloat(parseFloat(total) - (parseFloat(total) / parseFloat(1.18)));
					let subt = parseFloat(parseFloat(total) - igv);
					$('#SubTotal').html(parseFloat(subt.toFixed(2)));
					$('#IGV').html(parseFloat(igv.toFixed(2)));
					$('#Total').html(parseFloat(total.toFixed(2)));
					
					
    });

	$('input[name=optComp]').change(function(){
		var radioValue = $("input[name='optComp']:checked").val();
        if(radioValue == 'FAC'){
        	$("#labelDoc").html("RUC:");
        	$("#labelNombre").html("Razon Social:");
        }else{
        	$("#labelDoc").html("DNI:");
        	$("#labelNombre").html("Nombre:");

        }
	});

	$('#doc').keypress(function(e){
		var code = (e.KeyCode ? e.KeyCode : e.which);
		if(code == 13){

			var doc = $("#doc").val();
			// var radioValue = $("input[name='optComp']:checked").val()
			// if( radioValue == 'BOL' && doc.length == 8){
				// alert(radioValue+" "+doc.length);
			// }
	        $.post('ws/cliente.php', {
	            op: 'getdocumento',
	            doc: doc
	        }, function(data) {
	            console.log(data);
	            if (data !== 0) {
	            	$("#nombre").val(data["nombre"]);
	            	$("#direccion").val(data["direccion"]);
	            	$("#correo").val(data["correo"]);
	            }
	        }, 'json');
			return false;
		}
	});

	$('#Emitir').click(function(){
		var doc = $("#doc").val();
		let nombre = $("#nombre").val();
    	let direccion = $("#direccion").val();
    	let correo = $("#correo").val();
		//var radioValue = $("input[name='optComp']:checked").val();
		var idUsuario = <?php echo $_COOKIE["id_usuario"];?>;
		var idCaja = <?php echo $_COOKIE["id_caja"];?>;
		var id_cotizacion = $("#nro").val();

		if(doc == "" || nombre == "" || direccion == ""){
			alert("Debe Llenar los datos del Cliente");
		}else{
			$("#modal_envio_anim").modal("show");
			$.post('ws/venta.php', {
	            op: 'Cotizador',
	            	data: JSON.stringify(oData),
		            doc: doc,
		            nombre: nombre, 
		            direccion: direccion,
		            correo: correo,
		            tipo: 2,
		            SubTotal: $("#SubTotal").html(),
		            IGV: $("#IGV").html(),
		            Total: $("#Total").html(),
		            idUsuario: idUsuario,
		            idCaja: idCaja
		        }, function(data) {
                    console.log(data)
		        	 setTimeout(function(){ 
			        	window.open('archivos_impresion/cotizacion.php?id=' +data , '_blank');
			        		location.reload();
		        	 }, 3000);
		    	}, 

		    'json');
			////
			//imprimir(id_cotizacion,idCaja); 

		}
		return false;
	});
	$('#search_client').click(function(){
		var doc = $("#doc").val();
		console.log(doc);
		$.post('api/cliente.php', {
					op: 'busca',
					q: doc,
					busca_coti: true
				}, function(data) {
					console.log(data);
					var dat = JSON.parse(data);
					$("#nombre").val(dat[0]['nombre']);
	            	$("#direccion").val('-');
	            	$("#correo").val('-');
					
				});
	
			return false;
	});




});


  function imprimir(id,caja) {
       
            $.post('ws/venta.php', {
                op: 'imprimir',
                id: id,
                id_caja: caja,
                tipo: 'COT'
            }, function(data_imp) {
                console.log(data_imp);
            });
       
    }