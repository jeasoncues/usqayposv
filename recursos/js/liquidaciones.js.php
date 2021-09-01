$(function(){
    
    const empieza = $('#starts').val();
    const termina = $('#ends').val();

    $('#loading').hide();

    liquidaciones( empieza, termina );

    $('#frm-liq').on('submit', function(e){

        e.preventDefault();
        
        let starts = $('#starts').val();
        let ends = $('#ends').val();

        liquidaciones( starts, ends );
    })
    var tbl = $('#tbl-liq').DataTable({
        responsive: true,
        sort: false,
        "language": {
            "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
        },
        searching: false,
        bLengthChange: false,
        dom: 't'
    });
});

function liquidaciones(starts, ends){
    $('#loading').show();
    $('#starts_').val(starts);
    $('#ends_').val(ends);
    $.post('ws/movimiento_caja.php', { op: 'show_liq', starts: starts, ends: ends }, function(response){
        $('#loading').hide();
        console.log(response);
        let total_liquidado = 0;
        let html = "";
        let html_footer = "";

        if( response.length > 0 ){

            $.each( response, function(index, value){
                total_liquidado += parseFloat(value['monto']);
                html += "<tr>";
                html += "<td>"+value['fecha']+"</td>";
                html += "<td>"+value['caja']['nombre']+"</td>";
                html += "<td>"+value['usuario']['nombres_y_apellidos']+"</td>";
                html += "<td>"+value['monto']+"</td>";
                html += "</tr>";
            });

            html_footer += "<tr>";
            html_footer += "<th colspan='3'>Total</th>";
            html_footer += "<th>"+Intl.NumberFormat("es-ES").format(total_liquidado)+"</th>";
            html_footer += "</tr>";

        }else{

            html += "<tr><td colspan='4'>No se encontraron liquidaciones registradas.</td></tr>";
            html_footer += "<tr><th colspan='3'>Total</th><th>0.00</th></tr>";

        }

        $('#tbl-liq > tbody').html(html);
        $('#tbl-liq > tfoot').html(html_footer);
        
    }, 'json')
}