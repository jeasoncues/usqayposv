<?php
require_once('globales_sistema.php');
if (!isset($_COOKIE['nombre_usuario'])) {
    header('Location: index.php');
}
$titulo_pagina = 'Mis Tickets';
$titulo_sistema = 'POS';
require_once('recursos/componentes/header_ticket.php');

require_once 'nucleo/include/MasterConexion.php';

$objcon = new MasterConexion();
$configuracion = $objcon->consulta_arreglo("SELECT * FROM configuracion");

?>
<section class="col-md-12">
    <h3>Listado de Tickets</h3>
    <p>Todos los campos con <span class="text-danger"> * </span> son obligatorios</p>
    <form id="form_soporte" name="form_soporte">
        <input type="hidden" name="op" value="add">
        <input type="hidden" name="fecha_cierre" value="<?php echo $configuracion['fecha_cierre'] ?>">
        <div class="col-md-12 alert alert-danger" role="alert" style="display: none;" id="idalert">

        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="">Correo Electronico <span class="text-danger"> * </span> </label>
                <input type="email" name="email" id="email" class="form-control" value="<?php echo $configuracion['correoEmisor'] ?>" placeholder="Correo Electronico" aria-describedby="helpId" required>
            </div>
            <div class="form-group ">
                <label for=""> Nombre Completo <span class="text-danger"> * </span> </label>
                <input type="text" name="name" id="name" class="form-control" placeholder="Nombre Completo"  value="<?php echo $configuracion['razon_social'] ?>" aria-describedby="helpId" required>
            </div>
            <div class="form-group ">
                <label for=""> Telefono <span class="text-danger"> * </span> </label>
                <input type="text" name="phone" id="phone" class="form-control" value="<?php echo $configuracion['telefono'] ?>" placeholder="Telefono" aria-describedby="helpId" required>
            </div>

        </div>
        <div class="col-md-4">
            <div class="form-group ">
                <label for=""> Temas de Ayuda <span class="text-danger"> * </span> </label>
                <select name="topicId" id="topicId" class="form-control selectpicker" title=" -- Seleccione un Tema de Ayuda --" required>
                    <option value="1">PRUEBA 1</option>
                    <option value="2">PRUEBA 2</option>
                    <option value="3">PRUEBA 3</option>
                    <option value="4">PRUEBA 4</option>
                </select>
            </div>

            <div class="form-group" style="display: none;" id="div_subject">
                <label for=""> Resumen del Problema <span class="text-danger"> * </span> </label>
                <input type="text" name="subject" id="subject" class="form-control" placeholder="Resumen del Problema" aria-describedby="helpId">
            </div>
            <div class="form-group" style="display: none;" id="div_message">
                <label for=""> Redacta tu Problema </label>
                <textarea rows="3" cols="30" type="text" name="message" id="message" class="form-control" placeholder="Redacta Tu Problema" aria-describedby="helpId"></textarea>
            </div>
            <div class="form-group" style="display: none;" id="div_image">
                <label for="exampleInputFile">Archivo</label>
		        <input type="file" name="image" class="image" multiple="multiple" id="file" accept="audio/aiff,audio/mpeg,audio/mp4,audio/ogg,0,audio/vnd.wav,audio/wav,audio/x-midi,text/css,text/html,text/javascript,text/plain,text/xml,application/json,application/javascript,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.ms-word.document.macroEnabled.12,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel.sheet.macroEnabled.12,application/vnd.ms-excel.sheet.binary.macroEnabled.12,application/vnd.ms-powerpoint,application/vnd.openxmlformats-officedocument.presentationml.presentation,application/vnd.openxmlformats-officedocument.presentationml.slideshow,application/vnd.ms-powerpoint.presentation.macroEnabled.12,application/vnd.ms-powerpoint.slideshow.macroEnabled.12,application/vnd.ms-access,application/vnd.ms-project,application/msonenote,application/vnd.ms-publisher,application/rtf,application/vnd.ms-works,application/vnd.apple.keynote,application/vnd.apple.pages,application/vnd.apple.numbers,application/vnd.oasis.opendocument.text,application/vnd.oasis.opendocument.text-web,application/vnd.oasis.opendocument.text-master,application/vnd.oasis.opendocument.graphics,application/vnd.oasis.opendocument.presentation,application/vnd.oasis.opendocument.spreadsheet,application/vnd.oasis.opendocument.chart,application/vnd.oasis.opendocument.formula,application/vnd.oasis.opendocument.database,application/vnd.oasis.opendocument.image,application/vnd.openofficeorg.extension,application/wordperfect,application/vnd.kde.karbon,application/vnd.kde.kchart,application/vnd.kde.kformula,application/vnd.kde.kivio,application/vnd.kde.kontour,application/vnd.kde.kpresenter,application/vnd.kde.kspread,application/vnd.kde.kword,application/pdf,.csv,application/illustrator,application/x-director,application/x-indesign,text/vcard,image/x-dwg,image/vnd.dwg,image/vnd.dxf,application/x-autocad,application/x-mathcad,application/x-msmoney,application/x-latex,video/avi,video/mpeg,video/mp4,video/ogg,video/quicktime,video/webm,video/x-ms-asf,video/x-ms-wmv,application/x-dvi,application/x-shockwave-flash">
            </div>

        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for=""> Texto  Captcha  </label>
                <input type="text" name="" id="" class="form-control" placeholder="Texto Captcha" aria-describedby="helpId">
            </div>
        </div>
        <div class="col-md-12 text-right">
            <button class="btn btn-default" id="btnCancelar" type="button"> <i class="far fa-window-close"></i> Cancelar </button>
            <button class="btn btn-info" id="btnReestablecer" type="button"> <i class="fas fa-redo-alt"></i> Reestablecer </button>
            <button class="btn btn-success" type="submit" > <i class="fas fa-save"></i> Crear Ticket </button>
        </div>
    </form>
</section>
<?php
$nombre_tabla = 'soporte_ticket';
require_once('recursos/componentes/footer.php');
?>
 <script src="recursos/js/notify.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>