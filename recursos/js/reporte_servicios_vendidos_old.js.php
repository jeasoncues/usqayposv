<?php require_once('../../globales_sistema.php');?>
 $(document).ready(function() {
	
	 
         var tbl = $("#tblVentasServicio").dataTable({
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
    function buscar() {
        window.location.href = "reporte_servicios_vendidos.php?fecha_inicio=" + $('#txtfechaini').val() + 
            "&fecha_fin="+ $('#txtfechafin').val();
            
            
    }
    
    