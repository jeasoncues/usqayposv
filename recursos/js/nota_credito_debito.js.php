<?php require_once('../../globales_sistema.php'); ?>
jQuery.fn.reset = function () {
$(this).each (function() { this.reset(); });
}

$(document).ready(function() {
	var mostrar=0;
	var idProdEdit=0;
	$('#edit').hide();
	var oData=[];	
	inicio();
	
	operacion();

	$('#motivoEmision').click(function(){
		operacion();
	});
	function operacion(){
		var val = ($("#motivoEmision").val());
		console.log(val);

		if(val ==1 || val ==2 || val ==6 || val ==10){
			blocq();
			
		}else{
			unblocq();
		}
	}
	function blocq(){
			$('#products').attr('disabled', 'disabled');
			$('#cant').attr('disabled', 'disabled');
			$('#precio').attr('disabled', 'disabled');
			$('#add').attr('disabled', 'disabled');
			$('#edit').attr('disabled', 'disabled');
			$('#cancel').attr('disabled', 'disabled');
			$('#opciones').hide();
			mostrar=1;
			mostrarItems();
	}
	function unblocq(){
		$('#products').removeAttr('disabled');
		$('#cant').removeAttr('disabled');
		$('#precio').removeAttr('disabled');
		$('#add').removeAttr('disabled');
		$('#edit').removeAttr('disabled');
		$('#cancel').removeAttr('disabled');
		$('#opciones').show();
		mostrar=0;
		mostrarItems();
}


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
			$('#cant').val('0');
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
						mostrarItems()
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
	$('#cancel').click(function(){
			$('#edit').hide();
			$('#add').show();
			$('#products').removeAttr('disabled');
			mostrarItems()
		return false;
	});
	$('#edit').click(function(){
		
		let cantidad = parseFloat($('#cant').val());
		let precio = parseFloat($('#precio').val());
	
			if(cantidad != "" && cantidad > 0.00){
				if(precio != "" && precio > 0.00){
					oData[idProdEdit].cantidad=cantidad;
					oData[idProdEdit].precio=precio;
					$('#edit').hide();
					$('#add').show();
					$('#products').removeAttr('disabled');
					mostrarItems()
				}else{
					alert("El Precio debe ser un valor mayor a 0.00");
				}
			}else{
				alert("La Cantidad debe ser un valor mayor a 0.00");
			}
		return false;
	});

	$(document).on('click', '.btnEliminaProd', function () {
        var id = $(this).attr("id");
        oData.splice(id,1);
		mostrarItems()
    });
	$(document).on('click', '.btnModificarProd', function () {
	
		$('#edit').show();
		$('#add').hide();
		
		$('#products').attr('disabled', 'disabled');
        var id = $(this).attr("id");
		idProdEdit=id;
		$('#cant').val(oData[id].cantidad);
		$('#precio').val(oData[id].precio);
		$("#products").val(oData[id].idProducto);
	
    });
	$('#Emitir').click(function(){
		
		
		var idUsuario = <?php echo $_COOKIE["id_usuario"];?>;
		var idCaja = <?php echo $_COOKIE["id_caja"];?>;


		var idCliente = $("#idCliente").val();
		var motivoEmision = $("#motivoEmision").val();
		var idVenta = $("#idVenta").val();
		var nota = $("#nota").val();
		var tipo = $("#tipo").val();
		

	
		
			$("#modal_envio_anim").modal("show");
			$.post('ws/venta.php', {
	            op: 'notaCreditoDebito',
				itemsNotaCreditoDebito: JSON.stringify(oData),

		            idCliente: idCliente,
		            motivoEmision: motivoEmision, 
		            idVenta: idVenta,
		            nota: nota,
					tipo:tipo,
		           
		            subTotal: $("#SubTotal").html(),
		            igv: $("#IGV").html(),
		            total: $("#Total").html(),

		            idUsuario: idUsuario,
		            idCaja: idCaja
		        }, function(data) {
					alert(data.errors)
                    console.log(data)
		        	
		    	}, 

		    'json');
			end($("#idVenta").val());
			$.each(oData, function(key, value) {
				addProdVenta($("#idVenta").val(),value.idProducto,parseFloat(value.precio),parseFloat(value.cantidad));
			});

			imprimir(idVenta,idCaja,tipo,nota,motivoEmision); 
			window.location="reporte_ventas_totales.php";
		return false;
	});
	
	function inicio(){
		var items =JSON.parse($('#items').val());
	
		for (var i = 0; i < items.length; i++) {
		
			let idProducto = items[i].idProducto;
			let nombre =items[i].nombre;
			let cantidad =items[i].cantidad;
			let precio = items[i].precio;
			
			let dataItems = {
				'idProducto' : idProducto,
				'nombre' : nombre,
				'cantidad' : cantidad,
				'precio' : precio
			};
			oData.push(dataItems);
			
		}
	}

	function mostrarItems(){
					var ht = '';
					var total = 0;
		if(mostrar==0){
					
					$.each(oData, function(key, value) {
						ht += '<tr>';
					
						ht+= '<td>'+ value.nombre +'</td>';
						ht+= '<td>'+ value.cantidad +'</td>';
						ht+= '<td>'+ parseFloat(value.precio) +'</td>';
						ht+= '<td>'+ parseFloat(parseFloat(value.cantidad) * parseFloat(value.precio)) +'</td>';
						ht+= '<td>' +
                            '<button title="Eliminar" type="button" class="btn-link text-red btnEliminaProd" id="' + key + '">' +
                            '<i class="fa fa-trash-o" aria-hidden="true"></i>' +
                            '</button>' +
							'<button title="Editar" type="button" class="btn-link text-red btnModificarProd" id="' + key + '">' +
                            '<i class="fa fa-pencil" aria-hidden="true"></i>' +
                            '</button>' +
                            '</td>' ;
						ht+= '</tr>';
						total += parseFloat(parseFloat(value.cantidad) * parseFloat(value.precio));
						
					});
		}else{
			$.each(oData, function(key, value) {
						ht += '<tr>';
						ht+= '<td>'+ value.nombre +'</td>';
						ht+= '<td>'+ value.cantidad +'</td>';
						ht+= '<td>'+ parseFloat(value.precio) +'</td>';
						ht+= '<td>'+ parseFloat(parseFloat(value.cantidad) * parseFloat(value.precio)) +'</td>';
						ht+= '</tr>';
						total += parseFloat(parseFloat(value.cantidad) * parseFloat(value.precio));
			});
		}
					
					$('#itemsNota').html(ht);
					let igv = parseFloat(parseFloat(total) - (parseFloat(total) / parseFloat(1.18)));
					let subt = parseFloat(parseFloat(total) - igv);
					$('#SubTotal').html(parseFloat(subt.toFixed(2)));
					$('#IGV').html(parseFloat(igv.toFixed(2)));
					$('#Total').html(parseFloat(total.toFixed(2)));
					
					$('#cant').val('0');
					$('#precio').val('0.00');
					$("#products").val('0');
	}

	function imprimir(id,idCaja,tipoComp,nota,motivoEmision){
       
	   $.post('ws/venta.php', {
		   op: 'imprimir',
		   id: id,
		   id_caja: idCaja,
		   tipo: 'NOTCD',
		   tipoComp: tipoComp,
		   nota:nota,
		   motivoEmision:motivoEmision

	   }, function(data_imp) {
		   console.log(data_imp);
	   });
	}
	function end(id){
		$.post('ws/venta.php', {
		   op: 'anulaProdNota',
		   id: id,
	   }, function(data_imp) {
		   console.log(data_imp);
	   });
	}
	function addProdVenta(idVenta,idProd,precio,cantidad){
		$.post('ws/venta.php', {
		   op: 'addProdNota',
		   idVenta: idVenta,
		   idProd:idProd,
		   precio:precio,
		   cantidad:cantidad,
		   total:precio*cantidad
	   }, function(data_imp) {
		   console.log(data_imp);
	   });

	}
	
});

