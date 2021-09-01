<?php require_once('../../globales_sistema.php'); ?>
jQuery.fn.reset = function () {
$(this).each (function() { this.reset(); });
}

$(document).ready(function() {

	var tbl = $('#tb').DataTable({
	    responsive: true,
	    "order": [[ 0, "desc" ]],
	    dom: 'Bfrtip',
	    buttons: [
	    ],
	    "language": {
	        "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
	    }
	});

	$('#fecha_pago').datepicker({dateFormat: 'yy-mm-dd',
	    changeMonth: true,
	    changeYear: true
    });  

});


function save(){
	var vid = $('#id').val();
	if(vid === '0')
		insert();
	else
		update();
}

function Editar(id){
	$.post('ws/cliente.php', {
    	op: 'EditCredit', 
    	id: id
    }, function (data) {
    	oData = JSON.parse(data);
    	$('#id').val(oData.Id);
    	$('#monto').val(oData.Monto);
    	$('#fecha_pago').val(oData.FechaLimite);
    	$('#cliente').val(oData.IdCliente);
    });
}


function Cerrar(id){
	if (confirm("¿Está seguro de cerrar este Credito?")){
		$.post('ws/cliente.php', {
	    	op: 'CloseCredit', 
	    	id: id
	    }, function (data) {
	    	if (data === 0) {
	            $('body,html').animate({
	                scrollTop: 0
	            }, 800);
	            $('#merror').show('fast').delay(4000).hide('fast');
	        }
	        else {
	            $('body,html').animate({
	                scrollTop: 0
	            }, 800);
	            $('#msuccess').show('fast').delay(4000).hide('fast');
	            location.reload();
	        }
	    });
	}
}


function insert() {
	var monto = $('#monto').val();
	var fecha_pago = $('#fecha_pago').val();
	var cliente = $('#cliente').val();
	if(monto != "" || monto > 0){
		if (confirm("¿Desea Asignar el Monto de "+monto+" S/ como Credito?")) {
	        $.post('ws/cliente.php', {
	        	op: 'AddCredito', 
	        	idcliente: cliente,
	        	monto: monto,
	        	fecha_pago: fecha_pago
	        }, function (data) {
	            if (data === 0) {
	                $('body,html').animate({
	                    scrollTop: 0
	                }, 800);
	                $('#merror').show('fast').delay(4000).hide('fast');
	            }
	            else if(data === 2){
	            	alert("El cliente tiene un credito Abierto si desea agregar un Credito nuevo por favor cerrar el Anterior");
	            }
	            else {
	                $('body,html').animate({
	                    scrollTop: 0
	                }, 800);
	                $('#msuccess').show('fast').delay(4000).hide('fast');
	                location.reload();
	            }
	        }, 'json');
    	}
    }else{
    	alert("Para Generar un Credito su monto debe ser Mayor Cero.")
    }
}


function update() {
	var id = $('#id').val();
	var monto = $('#monto').val();
	var fecha_pago = $('#fecha_pago').val();
	var cliente = $('#cliente').val();
	if(monto != "" || monto > 0){
		if (confirm("¿Desea Asignar el Monto de "+monto+" S/ como Credito?")) {
	        $.post('ws/cliente.php', {
	        	op: 'ModCredito', 
	        	id: id,
	        	monto: monto,
	        	fecha_pago: fecha_pago
	        }, function (data) {
	            if (data === 0) {
	                $('body,html').animate({
	                    scrollTop: 0
	                }, 800);
	                $('#merror').show('fast').delay(4000).hide('fast');
	            }
	            else {
	                $('body,html').animate({
	                    scrollTop: 0
	                }, 800);
	                $('#msuccess').show('fast').delay(4000).hide('fast');
	                location.reload();
	            }
	        }, 'json');
    	}
    }else{
    	alert("Para Generar un Credito su monto debe ser Mayor Cero.")
    }
}

