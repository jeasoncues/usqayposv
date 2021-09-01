<?php require_once('../../globales_sistema.php');?>
 $(document).ready(function() {
  //        var tbl = $("#tblKardex").dataTable({
  //       	"responsive" : true,
		//     "dom" : 'T<"clear">lfrtip',
		//     "bInfo" : false,
		//     "oTableTools": {
		//             "sSwfPath": "recursos/swf/copy_csv_xls_pdf.swf",
		//             "aButtons": [
		//             {
		//                 "sExtends": "xls"
		//             },
		//             {
		//                 "sExtends": "pdf"
		//             }
		//         ]
		//     }
		// });
		// //tbl.fnSort( [ [0,'desc'] ] );
  //   });
      $('#tblKardex').DataTable({
        responsive: true,
       dom: "<'row'<'col'B>>lft<'row'<'col'i><'col'p>>",
		lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
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
});
    function buscar() {
        window.location.href = "reporte_factura.php?fecha_inicio=" + $('#txtfechaini').val() + 
            "&fecha_fin="+ $('#txtfechafin').val();
    }
