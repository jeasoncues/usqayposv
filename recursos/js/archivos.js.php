<?php require_once('../../globales_sistema.php'); ?>
jQuery.fn.reset = function () {
$(this).each (function() { this.reset(); });
}


    $(function() {
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

        $('.submit').on('click', function() {
            var file_data = $('.image').prop('files')[0];
            if($('#descripcion').val() != ''){
                if(file_data != undefined) {
                    var form_data = new FormData(this);
                    form_data.append('file', file_data);
                    form_data.append('idCliente', $('#idCliente').val());
                    form_data.append('descripcion', $('#descripcion').val());
                    $.ajax({
                        type: 'POST',
                        url: 'ws/archivos.php',
                        contentType: false,
                        processData: false,
                        data: form_data,
                        success:function(response) {
                            if(response == 'success') {
                                alert('Archivo Cargado Correctamente.');
                                location.reload();
                            /*} else if(response == 'false') {
                                alert('Invalid file type.'); */
                            } else {
                                alert('Ha Ocurrido problema en la carga.');
                            }

                            $('.image').val('');
                        }
                    });
                }
            }else{
                alert("Asunto es Requerido");
            }
            return false;
        });
    });

    function del(id) {
    if (confirm("¿Desea eliminar esta operación?")) {
        $.post('ws/cliente.php', {op: 'delFile', id: id}, function (data) {
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
}