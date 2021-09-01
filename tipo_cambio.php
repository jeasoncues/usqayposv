<?php
    require_once('globales_sistema.php');
    if(!isset($_COOKIE['nombre_usuario'])){
        header('Location: index.php');
    }
    $titulo_pagina = 'Tipo Cambio';
    $titulo_sistema = 'Katsu';
    require_once('recursos/componentes/header.php');
    ?>
                <input type='hidden' id='id' name='id' value='0'/>
                
            <div class='control-group col-md-4'>
            <label>Moneda Origen</label>
                <input class='form-control' placeholder='Moneda Origen' id='moneda_origen' name='moneda_origen' />
            </div>
            <div class='control-group col-md-4'>
            <label>Moneda Destino</label>
                <input class='form-control' placeholder='Moneda Destino' id='moneda_destino' name='moneda_destino' />
            </div>
            <div class='control-group col-md-4'>
            <label>Tasa</label>
                <input class='form-control' type='number' value='0.00' step='0.01' id='tasa' name='tasa' />
            </div><input type='hidden' name='estado_fila' id='estado_fila' value='1'/>
    <div class='control-group col-md-4'>
    <p></p>
    <button type='button' class='btn btn-primary' onclick='save()'>Guardar</button>
    <button type='reset' class='btn'>Limpiar</button>
    </div>
    </form>
    <hr/>
    <?php
    include_once('nucleo/tipo_cambio.php');
    $obj = new tipo_cambio();
    $objs = $obj->listDB();
    
    ?>
    <div class='contenedor-tabla'>
    <table id='tb' class='display' cellspacing='0' width='100%'>
    <thead>
    <tr>
    <th>Id</th><th>Moneda Origen</th><th>Moneda Destino</th><th>Tasa</th>
    <th>OPC</th>
    </tr>
    </thead>
    <tbody>
    <?php
        if (is_array($objs)):
        foreach ($objs as $o):
    ?>
    <tr><td><?php echo $o['id']; ?></td><td><?php echo $o['moneda_origen']; ?></td><td><?php echo $o['moneda_destino']; ?></td><td><?php echo $o['tasa']; ?></td>
    <td><a href='#' onclick='sel(<?php echo $o['id']; ?>)'><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a><br/><a href='#' onclick='del(<?php echo $o['id']; ?>)'><i class="fa fa-trash" aria-hidden="true"></i></a></td>
    </tr>
    <?php
        endforeach;
        endif;
    ?>
    <?php
    $nombre_tabla = 'tipo_cambio';
    require_once('recursos/componentes/footer.php');
    ?>