
<?php require_once('../../globales_sistema.php');?>
$(document).ready(function () {

    $('#loading').hide();
    
    $('#tblKardex').DataTable({
        responsive: true,
        dom: 'Bfrtip',
        order: [3, 'desc'],
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


$.ajaxblock    = function(){
      $("body").prepend("<div id='ajax-overlay'><div id='ajax-overlay-body' class='center'><i class='fa fa-spinner fa-pulse fa-3x fa-fw'></i><span class='sr-only'>Loading...</span></div></div>");
      $("#ajax-overlay").css({
         position: 'absolute',
         color: '#FFFFFF',
         top: '0',
         left: '0',
         width: '100%',
         height: '100%',
         position: 'fixed',
         background: 'rgba(39, 38, 46, 0.67)',
         'text-align': 'center',
         'z-index': '9999'
      });
      $("#ajax-overlay-body").css({
         position: 'absolute',
         top: '40%',
         left: '50%',
         width: '120px',
         height: '48px',
         'margin-top': '-12px',
         'margin-left': '-60px',
      
         '-webkit-border-radius':   '10px',
         '-moz-border-radius':      '10px',
         'border-radius':        '10px'
      });
      $("#ajax-overlay").fadeIn(50);
   };
   $.ajaxunblock  = function(){
      $("#ajax-overlay").fadeOut(100, function()
      {
         $("#ajax-overlay").remove();
      });
   };


function buscar() {
    if($("#opc").val() == ""){
        window.location.href = "reporte_venta_utilidades.php?fecha_inicio=" + $('#txtfechaini').val() +
        "&fecha_fin=" + $('#txtfechafin').val();
    }else{
        window.location.href = "reporte_venta_utilidades.php?fecha_inicio=" + $('#txtfechaini').val() +
        "&fecha_fin=" + $('#txtfechafin').val();
    }
}

