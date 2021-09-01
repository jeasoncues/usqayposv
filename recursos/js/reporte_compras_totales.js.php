<?php require_once('../../globales_sistema.php');?>
$(document).ready(function () {
    var tbl = $("#tblKardex").dataTable({
        "dom": 'T<"clear">lfrtip',
        "bInfo": false,
        "bSort": false,
        "oTableTools": {
            "sSwfPath": "recursos/swf/copy_csv_xls_pdf.swf",
            "aButtons": [
                {
                    "sExtends": "xls"
                },
                {
                    "sExtends": "pdf"
                }
            ]
        }
    });
    //tbl.fnSort( [ [0,'desc'] ] );
});

function tipoDocumento(tipo)
{
    const tipos = [null, "BOLETA", "FACTURA", "NOTA DE VENTA"];
    return tipos[tipo];
}

function detalles(id)
{
    $('#myModal2').modal();
    $.post('ws/proveedor.php', { op:'compras', id } ,function(response){
        console.log(response);
        let monto_total_final = 0;
        let monto_total_pendiente = 0;
        let monto_pagado = 0;

        let tbody = "";
        let tfoot = "";

        $.each(response, function(i, val){
            
            monto_total_final += Number(val['monto_total']);
            monto_total_pendiente += Number(val['monto_pendiente']);
            monto_pagado = monto_total_final - monto_total_pendiente;

            tbody += '<tr>';
            tbody += `<td>${val['fecha']}</td>`;
            tbody += `<td>${val['monto_total']}</td>`;
            tbody += `<td>${val['monto_pendiente']}</td>`;
            tbody += `<td>${tipoDocumento(val['categoria'])}</td>`;
            tbody += `<td>${val['numero_documento']}</td>`;
            tbody += `<td>${val['proximo_pago']}</td>`;
            tbody += '</tr>';
        });

        tfoot += '<tr>';
        tfoot += '<th>Total</th>';
        tfoot += `<th>${monto_total_final.toFixed(2)}</th>`;
        tfoot += '</tr>';

        $('#tbl-detalles > tbody').html(tbody);
        $('#tbl-detalles > tfoot').html(tfoot);

        const dp = [
            { y: monto_pagado, label: "Monto Pagado" },
            { y: monto_total_pendiente, label: "Monto Pendiente" }
        ];

        console.log(monto_total_final, monto_total_pendiente, monto_pagado);
        
        var chart = new CanvasJS.Chart("chartContainer", {
            animationEnabled: true,
            title: {
                text: "Reporte Deuda / Haber"
            },
            data: [{
                type: "pie",
                startAngle: 240,
                yValueFormatString: "##0.00\"\"",
                indexLabel: "{label} {y}",
                dataPoints: dp
            }]
        });
        chart.render();

    }, 'json' ).done( function(){
        $('#myModal2').modal('hide');
    } )
}

