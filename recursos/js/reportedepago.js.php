
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
            'copyHtml5',
            'excelHtml5',
            'csvHtml5',
            'pdfHtml5'
        ],
        "language": {
            "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
        }
    });

     $('#finicio').datepicker({dateFormat: 'yy-mm-dd',
        changeMonth: true,
        changeYear: true
    }); 
    $('#ffin').datepicker({dateFormat: 'yy-mm-dd',
        changeMonth: true,
        changeYear: true
    }); 
    $('#mes').change(function(){
        $.post('ws/cliente.php',{
            op: 'filtroxMes',
            mes: $('#mes').val(),
        },function(data){
            let mData = JSON.parse(data);
            // console.log(mData);
            if(data != 0){
                $('#tbody').html('');
                var ht = '';
                $.each(mData, function(key, value) {
                    let mes = 0;

                    if(value.mes == 1)
                        mes = "ENERO";
                    else if(value.mes == 2)
                        mes = "FEBRERO";
                    else if(value.mes == 3)
                        mes = "MARZO";
                    else if(value.mes == 4)
                        mes = "ABRIL";
                    else if(value.mes == 5)
                        mes = "MAYO";
                    else if(value.mes == 6)
                        mes = "JUNIO";
                    else if(value.mes == 7)
                        mes = "JULIO";
                    else if(value.mes == 8)
                        mes = "AGOSTO";
                    else if(value.mes == 9)
                        mes = "SEPTIEMBRE";
                    else if(value.mes == 10)
                        mes = "OCTUBRE";
                    else if(value.mes == 11)
                        mes = "NOVIEMBRE";
                    else
                        mes = "DICIEMBRE";

                    ht += '<tr>'+
                        '<td>'+value.id+'</td>'+
                        '<td>'+value.nombres_y_apellidos+'</td>'+
                        '<td>'+mes+'</td>'+
                        '<td>'+value.ano+'</td>'+
                        '<td>'+value.fecha_generada+'</td>'+
                        '<td>'+value.total_bruto+'</td>'+
                        '<td>'+value.total_descuentos+'</td>'+
                        '<td>'+value.total_neto+'</td>'+
                        '<td>'+value.total_aportes_empleador+'</td>'+
                        '<td>'+value.dias_laborados+'</td>'+
                        '<td>'+value.dias_no_laborados+'</td>'+
                        '<td>'+value.dias_subsidiados+'</td>'+
                        '<td>'+value.horas_ordinarias+'</td>'+
                        '<td>'+value.minutos_ordinarios+'</td>'+
                        '<td>'+value.horas_extra+'</td>'+
                        '<td>'+value.minutos_extra+'</td>'+
                        '<td>'+value.usuario+'</td>'+
                        '<td>'+
                            '<div class="btn-group" role="group">'+
                                '<a title="Boleta de Pago" class="btn btn-sm btn-primary" onclick="imprimirBoleta('+value.id_trabajador+','+value.id+')"><i class="fa fa-file-excel-o" aria-hidden="true"></i></a>'+
                            '</div>'+
                        '</td>'+
                    '</tr>';
                });
                $('#tbody').html(ht);  
            }
        });
    });

});

function buscar() {
    window.location.href = "reportedepago.php?fecha_inicio=" + $('#finicio').val() + 
        "&fecha_fin="+ $('#ffin').val();
}
