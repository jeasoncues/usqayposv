<?php require_once('../../globales_sistema.php');?>
 $(document).ready(function() {
    /*var ctx = document.getElementById('myChart').getContext('2d')
	var ctxx = document.getElementById('myChart2').getContext('2d')

	var newGraficoBarra = new Chart(ctx, {type: 'bar',data:null,options: null})
	var newGraficoDonut = new Chart(ctxx, {
		type: 'doughnut'} 
    )*/
    
      //    var tbl = $("#tblKardex").dataTable({
		    // "dom": 'T<"clear">lfrtip',
		    // "bInfo": false,
		    // "oTableTools": {
		    //         "sSwfPath": "recursos/swf/copy_csv_xls_pdf.swf",
		    //         "aButtons": [
		    //         {
		    //             "sExtends": "xls"
		    //         },
		    //         {
		    //             "sExtends": "pdf"
		    //         }
		//         ]
		//     }
		// });

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


		
		//tbl.fnSort( [ [0,'desc'] ] );
    });
    function buscar() {
        window.location.href = "reporte_ventasxtrabajador.php?fecha_inicio=" + $('#txtfechaini').val() + 
            "&fecha_fin="+ $('#txtfechafin').val();
    }
