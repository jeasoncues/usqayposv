<?php require_once('../../globales_sistema.php');?>
 $(document).ready(function() {
  //        var tbl = $("#tblKardex").dataTable({
		//     "dom": 'T<"clear">lfrtip',
		//     "bInfo": false,
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
		//tbl.fnSort( [ [0,'desc'] ] );

		/*$('#tblKardex').DataTable({
	        responsive: true,
	        dom: 'lBfrtip',
	        buttons: [
	            'copyHtml5',
	            'excelHtml5',
	            'csvHtml5',
	            'pdfHtml5'
	        ],
	        "language": {
	            "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
	        }
	    });*/
		

		var ctx = document.getElementById('myChart').getContext('2d')
	var ctxx = document.getElementById('myChart2').getContext('2d')

	var newGraficoBarra = new Chart(ctx, {type: 'bar',data:null,options: null})
	var newGraficoDonut = new Chart(ctxx, {type: 'doughnut'})
	
	cargar_grafico()

	$("#btnreporteservicios").on('click',(e)=>{
		e.preventDefault()
		cargar_grafico()
		return false
	})
	
	function generarLetra(){
		var letras = ["a","b","c","d","e","f","0","1","2","3","4","5","6","7","8","9"];
		var numero = (Math.random()*15).toFixed(0);
		return letras[numero];
	}
	
	function colorHEX(){
		/*var coolor = "";
		var prefijo = "#";
		for (var i=0;i<6; i++) {
			coolor = coolor + generarLetra()
		}
		return prefijo+coolor*/
		var letters = '0123456789ABCDEF';
		var color = '';
		for( var i=0; i<6; i++ ){
			color += letters[Math.floor(Math.random()*16)];
		}
		console.log('#E'+color+'');
		return '#E6'+color+'';
	}
	
	function cargar_grafico(){
			const fi= $("#txtfechaini").val()
			const ff= $("#txtfechafin").val()
			const op= "reportevetasxcliente";		
			newGraficoDonut.destroy()
			newGraficoBarra.destroy()

			if ($.fn.dataTable.isDataTable('#tblKardex'))
			{
				table = $('#tblKardex').DataTable();
				table.destroy();
			}

			$("#tblKardex > tbody").html("");
			
			$.post("ws/cliente.php", {'op':op,'fecha_inicio':fi,'fecha_fin' :ff}, function(response){			
				let tfoot = ``;
				let totalFinal = 0;
				let reporte1 = [];
				let reporte2 = [];

				// nuevos
				let labelreportedonut=[];
				let numberreportedonut=[];
				let color=[];
				let dataNewBarra=[];
				//console.log(response)
				if(response.length>0){
					// console.log("entro")
					$.each(response, (i, val) => {					
						totalFinal =  Number(totalFinal) + Number(val['total_vendido'])
						
						if(i < 5){
							let colornew= colorHEX()												
							labelreportedonut.push(val['nombrecliente'])						
							numberreportedonut.push( Number(val['total_vendido']))
							color.push(colornew)
						}

						let tbody = `<tr>`;			
						tbody += `<td class="text-center">${val['nombrecliente']}</td>`;
						tbody += `<td class="text-center">${val['noperaciones']}</td>`;            
						tbody += `<td class="text-center">${val['total_vendido']}</td>`;
						tbody += `</tr>`;
						$("#tblKardex").append(tbody);
					});
				}

				/* tfoot += `<tr>`;
				tfoot += `<th class="text-center" colspan="3">Totales</th>`;
				tfoot += `<th class="text-center">${totalFinal.toFixed(2)}</th>`;
				tfoot += `</tr>`;
				$('#tblKardex > tfoot').html(tfoot); */
				
				$('#tblKardex').DataTable({
					responsive:true,
					dom: "<'row'<'col'B>>lft<'row'<'col'i><'col'p>>",
					lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
					buttons: [
						'copyHtml5',
						'excelHtml5',
						'csvHtml5',
						'pdfHtml5'
					],
					lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
					language:lenguaje
				});

				var chartData = { 
					labels:labelreportedonut,
					datasets:[{
						label:labelreportedonut,
						backgroundColor: color,
						boderColor: color,
						borderWidth: 2,
						hoverBackgroundColor: color,
						haverBorderColor: color,
						data: numberreportedonut,
						display: true
					}]
				}

				newGraficoDonut = new Chart(ctxx, {
					type: 'doughnut',
					data: chartData,
					options:{
						responsive:true,
						tooltips: {
							mode: 'label',
							callbacks: {
								label: function(tooltipItem, data) {
									// console.log("data", data['datasets'][0]['data'][tooltipItem['index']] + '%')
									// console.log("label", data['datasets'][0]['label'][tooltipItem['index']] + '%')
									//  return data['datasets'][0]['data'][tooltipItem['index']] + '%';							
									var dataset = data.datasets[tooltipItem.datasetIndex];
									var total = dataset.data.reduce(function(previousValue, currentValue, currentIndex, array) {
										return parseFloat( parseFloat(previousValue) + parseFloat(currentValue));
									})
									var currentValue = dataset.data[tooltipItem.index];
									var percentage = parseFloat(((currentValue/total) * 100)).toFixed(2);   							
									return data['datasets'][0]['label'][tooltipItem['index']]+" S/ "+ data['datasets'][0]['data'][tooltipItem['index']] +" ( " +  percentage + "% )";
								}
							}
						},
						title: {
							display: true,
							text: 'Top 5 Clientes que Mas Compran'
						}
					}			
				})
				
				var chartData2 = {
						labels:labelreportedonut,
						datasets: [
							{
							label: "Top 5 clientes",
							backgroundColor: color,
							data: numberreportedonut
							}
						]
				}
				
				newGraficoBarra= new Chart(ctx, {
					type: 'bar',
					data: chartData2,
					options:{
						responsive:true,
						tooltips: {
							mode: 'label',
							callbacks: {
								label: function(tooltipItem, data) {													
									var dataset = data.datasets[tooltipItem.datasetIndex];
									var total = dataset.data.reduce(function(previousValue, currentValue, currentIndex, array) {
										return parseFloat( parseFloat(previousValue) + parseFloat(currentValue));
									})
									var currentValue = dataset.data[tooltipItem.index];
									var percentage = parseFloat(((currentValue/total) * 100)).toFixed(2);   							
									return data['datasets'][0]['label'][tooltipItem['index']]+" S/ "+ data['datasets'][0]['data'][tooltipItem['index']] +" ( " +  percentage + "% )";
								}
							}
						},
						title: {
							display: true,
							text: 'Top 5 Clientes que Mas Compran'
						}
						
				}})

			}, 'json');

		}
		

	



	});
		
    function buscar() {
        window.location.href = "reporte_ventaxcliente.php?fecha_inicio=" + $('#txtfechaini').val() + 
            "&fecha_fin="+ $('#txtfechafin').val();
    }
