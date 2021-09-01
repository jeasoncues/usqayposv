$(function(){

    const empieza = $('#starts').val();
    const termina = $('#ends').val();

    $('#loading').hide();

    cuadre( empieza, termina );

    $('#frm-liq').on('submit', function(e){

        e.preventDefault();
        
        let starts = $('#starts').val();
        let ends = $('#ends').val();

        cuadre( starts, ends );        
    })
    
    var tbl = $('#tbl-liq').DataTable({
        responsive: true,
        searching: false,
        bLengthChange: false,
        dom: 't',
        sort:false
    });
})

function cuadre(starts, ends){
    $('#loading').show();
    $('#starts_').val(starts);
    $('#ends_').val(ends);
    $.post('ws/movimiento_caja.php', { op: 'show_cuadre', starts: starts, ends: ends }, function(response){
        $('#loading').hide();
        let total_liquidado = 0;
        let html = "";
        let html_footer = "";
        let total_ingresos = 0;
        let total_salidas = 0;

        if( response.length > 0 ){
            $.each( response, function(index, value){
                total_liquidado += Number(value['monto']);
                html += "<tr>";
                html += "<td>"+value['fecha']+"</td>";
                html += "<td>"+value['caja']['nombre']+"</td>";
                html += "<td>"+value['usuario']['nombres_y_apellidos']+"</td>";
                html += "<td>"+value['descripcion']['descripcion']+"</td>";
                html += "<td><span class='label label-"+value['tipo']['class']+"'>"+value['tipo']['tipo']+"</span></td>";
                html += "<td>"+Math.abs(value['monto'])+"</td>";
                html += "</tr>";
                if(value['tipo']['class'] == 'danger'){
                    total_salidas += Math.abs(value['monto']);
                }else{
                    total_ingresos += Math.abs(value['monto']);
                }
            });

            html_footer += "<tr>";
            html_footer += "<th colspan='5'>Total</th>";
            html_footer += "<th>"+total_liquidado.toFixed(2)+"</th>";
            html_footer += "</tr>";

            $("#saldo_anterior").html(response[0]['saldo_anterior']);
            $("#total_ingresos").html(total_ingresos);
            $("#total_salidas").html(total_salidas);
            let saldo_final = (parseFloat(response[0]['saldo_anterior'] + total_ingresos) - total_salidas);
            $("#saldo_final").html(saldo_final.toFixed(2));
        }else{

            html += "<tr><td colspan='6'>No se encontraron movimientos registrados.</td></tr>";
            html_footer += "<tr><th colspan='5'>Total</th><th>0.00</th></tr>";

        }

        $('#tbl-liq > tbody').html(html);
        $('#tbl-liq > tfoot').html(html_footer);
    
    }, 'json')
}