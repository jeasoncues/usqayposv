<?php require_once('../../globales_sistema.php');?>
 $(document).ready(function() {
	 var $table = $('#table')

  $(function() {
    $('#toolbar').find('select').change(function () {
      $table.bootstrapTable('destroy').bootstrapTable({
        exportDataType: $(this).val(),
        exportTypes: ['excel', 'pdf'],
        
      })
    }).trigger('change')
  })
		var $table = $('#table');
		$table.bootstrapTable()
			var tbl1=$('#tblKardex').dataTable({type:'pdf',
                           jspdf: {orientation: 'l',
                                   format: 'a4',
                                   margins: {left:10, right:10, top:20, bottom:20},
                                   autotable: {styles: {fillColor: 'inherit', 
                                                        textColor: 'inherit'},
                                               tableWidth: 'auto'}
                                  }
                          });

        /*var tbl = $("#tblKardex").dataTable({
		    "dom": 'T<"clear">lfrtip',
		    "bInfo": false,
		    "oTableTools": {
		            "sSwfPath": "recursos/swf/copy_csv_xls_pdf.swf",
		            "aButtons": [
		            {
		                "sExtends": "xlsx",
		            },
		            {
		                "sExtends": "pdf"
		            }
		        	]
		    }
		});*/
		//tbl.fnSort( [ [0,'desc'] ] );

		$('#boton-tabla').on('click', function(){
			$('#table').bootstrapTable('removeAll', "");
			
			var total_util=0;
			var total_pcompra=0;
			var total_pventa=0;

			$('#tcompra').html('');
			$('#tventa').html('');
			$('#tutilidad').html('');

			const id_almacen = $('#id_almacen').val();
			
			const inicio = $('#txtfechaini').val();
			if ($.fn.dataTable.isDataTable('#tbl-k'))
			{
				table = $('#tbl-k').DataTable();
				table.destroy();
			}
			
			$("#tbl-k > tbody").html("");
			$('#table').bootstrapTable('showLoading'); 
			
			$.post('ws/movimiento_producto.php',{ op: 'kardex', id_almacen: id_almacen, inicio: inicio }, function(response){
				
				for(i=0; i< response.data.length; i++){

					var t1 = Number(response.data[i]['cantidad3']);
					response.data[i]['cantidad3'] = t1.toFixed(2);
					var pv = Number(response.data[i]['pventa']);
					response.data[i]['pventa'] = pv.toFixed(2);
					var pc = Number(response.data[i]['pcompra']);
					response.data[i]['pcompra'] = pc.toFixed(2);
					var util = Number(pv-pc);
					response.data[i]['utilidad']=util.toFixed(2);

					var pc_st = Number(t1*pc);
					response.data[i]['pcompra_st']=pc_st.toFixed(2);
					var pv_st = Number(t1*pv);
					response.data[i]['pventa_st']=pv_st.toFixed(2);
					var util_st = Number(t1*util);
					response.data[i]['utilidad_st']=util_st.toFixed(2);

					total_pcompra=total_pcompra+pc_st;
					total_pventa=total_pventa+pv_st;
					total_util=total_util+util_st;
										
				}
				$('#table').bootstrapTable('hideColumn','id');
				$('#table').bootstrapTable('hideColumn','pcompra');
				$('#table').bootstrapTable('hideColumn','pventa');
				$('#table').bootstrapTable('hideColumn','utilidad');
				$('#table').bootstrapTable('hideColumn','pcompra_st');
				$('#table').bootstrapTable('hideColumn','pventa_st');
				$('#table').bootstrapTable('hideColumn','utilidad_st');
				$('#table').bootstrapTable('hideLoading');
				$('#table').bootstrapTable('load', response.data);
				console.log(response.data)

				$('#tcompra').html(parseFloat(total_pcompra.toFixed(2)));
				$('#tventa').html(parseFloat(total_pventa.toFixed(2)));
				$('#tutilidad').html(parseFloat(total_util.toFixed(2)));


			},'json')


		})

		
		
    });


    function buscar() {
        window.location.href = "reporte_kardex_productos.php?fecha_inicio=" + $('#txtfechaini').val() + 
            "&fecha_fin="+ $('#txtfechafin').val();
    }
    
	function cellStyle(value, row, index) {
        var classes = ['active', 'success', 'info', 'warning', 'danger'];
        if(value < 0){
			return {
				css: {
                    "background-color": "#D33513",
                }
			}
		}else if(value == 0){
			return {
				css: {
                    "background-color": "#fba344",
                }
			}
		}else if(value > 0){
			return {
				css: {
                    "background-color": "#75933d",
                }
			}
		}
        return {};
    } 

	function operateFormatter(value, row, index) {
        return [
            '<a class="like" href="javascript:void(0)" title="Editar">',
            '<i class="fa fa-line-chart"></i>',
            '</a>  ',
        ].join('');
    }
    window.operateEvents = {
        'click .like': function (e, value, row, index) {

			if ($.fn.dataTable.isDataTable('#tbl-k'))

			{
				table = $('#tbl-k').DataTable();
				table.destroy();
			}
			$("#tbl-k > tbody").html("");
			
			let fecha=$('#txtfechaini').val();
			let almacen=$('#id_almacen').val();

			$.post('ws/movimiento_producto.php',{op: 'search', data: row['id'], value: 'id_producto', type: 1, fecha: fecha, almacen: almacen }, function(response){
				console.log(response);
				let tfoot = ``;
				let cantidad_total = 0;
				let total_vendido = 0;
				let total_gastado = 0;

				$("#tbl-k > tbody").html("");
	
				$.each(response, (i, val) => {

					cantidad_total += Number(val['cantidad']);
					let total_costo = Number(val['cantidad']) * Number(val['costo']);
					let clase = '';

					if( val['cantidad'] < 0 ){
						const costo = Number(val['cantidad']) * Number(val['costo']);
						total_vendido += costo;
						clase = 'danger';
					}else{
						const costo = Number(val['cantidad']) * Number(val['costo']);
						total_gastado += costo;
						clase = 'primary';
					}
					
					let tbody = `<tr>`;
					
					tbody += `<td class="text-center">${ val['id_producto']['nombre'] }</td>`;
					tbody += `<td class="text-center">${ val['id_almacen']['nombre'] }</td>`;
					tbody += `<td class="text-center"><span class="label label-${clase}">${ val['cantidad'] }</span></td>`;
					tbody += `<td class="text-center">${ val['costo'] }</td>`;
					tbody += `<td class="text-center">${ Math.abs(total_costo).toFixed(2) }</td>`;
					tbody += `<td class="text-center">${ val['tipo_movimiento'] }</td>`;
					tbody += `<td class="text-center">${ val['id_usuario']['nombres_y_apellidos'] }</td>`;
					tbody += `<td class="text-center">${ val['id_turno']['nombre'] }</td>`;
					tbody += `<td class="text-center">${ val['fecha'] }</td>`;
					tbody += `<td class="text-center">${ val['fecha_cierre'] }</td>`;
					
					tbody += `</tr>`;

					$("#tbl-k > tbody:last").append(tbody);

				});
				
				
				tfoot += `<tr>`;
				tfoot += `<th class="text-center" colspan="2">Stock Actual</th>`;
				tfoot += `<th class="text-center">${cantidad_total}</th>`;
				tfoot += `</tr>`;

				tfoot += `<tr>`;
				tfoot += `<th class="text-center" colspan="4">Total Vendido</th>`;
				tfoot += `<th class="text-center">${Math.abs(total_vendido).toFixed(2)}</th>`;
				tfoot += `</tr>`;

				tfoot += `<tr>`;
				tfoot += `<th class="text-center" colspan="4">Total Comprado</th>`;
				tfoot += `<th class="text-center">${total_gastado.toFixed(2)}</th>`;
				tfoot += `</tr>`;

				tfoot += `<tr>`;
				tfoot += `<th class="text-center" colspan="4">Total Ganancia</th>`;
				tfoot += `<th class="text-center">${ (Math.abs(total_vendido) - total_gastado).toFixed(2) }</th>`;
				tfoot += `</tr>`;



				$('#tbl-k > tfoot').html(tfoot);
				
				$('#tbl-k').DataTable({
					responsive: true
				});
				
			}, 'json' );
            
				
			
        },
        
    };
	