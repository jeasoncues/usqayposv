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
						ht+= '<td>'+ (value.cantidad * value.precio).toFixed(2) +'</td>';
						ht+= '<td>' +
                            '<button type="button" class="btn-link text-red btnEliminaPago" id="' + key + '">' +
                            '<i class="fa fa-trash-o" aria-hidden="true"></i>' +
                            '</button>' +
                            '</td>' ;
						ht+= '</tr>';

						total += Number(value.cantidad) * Number(value.precio);
					});
					$('#ProductoFree').html(ht);
					let igv = Number(total) - (Number(total) / 1.18);
					let subt = Number(total) - igv;
					$('#SubTotal').html(Number(subt).toFixed(2));
					$('#IGV').html(Number(igv).toFixed(2));
					$('#Total').html(Number(total).toFixed(2));

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
			ht+= '</tr>';
		});
		$('#ProductoFree').html(ht);
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
		var radioValue = $("input[name='optComp']:checked").val();
		var idUsuario = <?php echo $_COOKIE["id_usuario"];?>;
		var idCaja = <?php echo $_COOKIE["id_caja"];?>;

		if( radioValue == 'FAC' && doc.length == 11){
			if(nombre != "" && direccion != "" ){
				if(oData.length > 0){
					$("#modal_envio_anim").modal("show");
					$.post('ws/venta.php', {
			            op: 'Free',
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
				            if(data["errors"]){
				            	alert(data["errors"]);
				            	$("#modal_envio_anim").modal("hide");
				            	return false;
				            }else{
				            	location.reload();
				            }
				        }, 'json');
				}else{
					alert("Debe tener al menos un detalle para generar el comprobante");
				}
			}else{
				alert("Para emitir la factura debe llenar los campos de Razon Social, Direccion, Correo Electronico (Opcional)")
			}
		}else if(radioValue == 'BOL'){
			if(doc.length == ""){
				if(Number($("#Total").html()) < 700 ){
					if(oData.length > 0){
						$("#modal_envio_anim").modal("show");
						$.post('ws/venta.php', {
				            op: 'Free',
				            	data: JSON.stringify(oData),
					            doc: "",
					            nombre: "",
					            direccion: "",
					            correo: "",
					            tipo: 1,
					            SubTotal: $("#SubTotal").html(),
					            IGV: $("#IGV").html(),
					            Total: $("#Total").html(),
					            idUsuario: idUsuario,
					            idCaja: idCaja
					        }, function(data) {
					            if(data["errors"]){
					            	alert(data["errors"]);
				            		$("#modal_envio_anim").modal("hide");
					            	return false;
					            }else{
					            	location.reload();
					            }
					        }, 'json');
					}else{
						alert("Debe tener al menos un detalle para generar el comprobante");
					}
				}else{
					alert("La venta Excede los 700 Soles, debe colocar el DNI, Nombre, Direccion y Correo (Opcional)");
				}
			}else{
				if(doc.length == 8){
					if(oData.length > 0){
						$("#modal_envio_anim").modal("show");
						$.post('ws/venta.php', {
			            op: 'Free',
			            	data: JSON.stringify(oData),
				            doc: doc,
				            nombre: nombre,
				            direccion: direccion,
				            correo: correo,
				            tipo: 1,
				            SubTotal: $("#SubTotal").html(),
				            IGV: $("#IGV").html(),
				            Total: $("#Total").html(),
				            idUsuario: idUsuario,
				            idCaja: idCaja
				        }, function(data) {
				            console.log(data["errors"]);
				            if(data["errors"]){
				            	alert(data["errors"]);
				            	$("#modal_envio_anim").modal("hide");
				            	return false;
				            }else{
				            	location.reload();
				            }
				        }, 'json');
						// return false;
					}else{
						alert("Debe tener al menos un detalle para generar el comprobante");
					}
				}else{
					alert("Para Emitir la Boleta el DNI debe estar Vacio o Contener 8 caracteres");
				}
			}
		}else{
			alert("Para generar la factura el RUC debe contener 11 caracteres");
		}
		return false;
	});


});