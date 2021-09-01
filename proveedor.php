<?php
    require_once('globales_sistema.php');
    if(!isset($_COOKIE['nombre_usuario'])){
        header('Location: index.php');
    }
    $titulo_pagina = 'Proveedor';
    $titulo_sistema = 'Katsu';
    require_once('recursos/componentes/header.php');
    ?>
                <input type='hidden' id='id' name='id' value='0'/>
                
            <div class='control-group col-md-4'>
            <label>Razon Social</label>
                <input class='form-control' placeholder='Razon Social' id='razon_social' name='razon_social' />
            </div>
            <div class='control-group col-md-4'>
            <label>Ruc</label>
                <input class='form-control' placeholder='Ruc' id='ruc' name='ruc' />
            </div>
            <div class='control-group col-md-4'>
            <label>Direccion</label>
                <textarea class='form-control' rows='3' id='direccion' name='direccion' required></textarea>   
            </div>
            <div class='control-group col-md-4'>
            <label>Telefono</label>
                <input class='form-control' placeholder='Telefono' id='telefono' name='telefono' required/>
            </div><input type='hidden' name='estado_fila' id='estado_fila' value='1'/>
    <div class='control-group col-md-4'>
    <p></p>
    <button type='button' class='btn btn-primary' onclick='save()'>Guardar</button>
    <button type='reset' class='btn'>Limpiar</button>
    </div>
    </form>
    <hr/>
    <?php
    include_once('nucleo/proveedor.php');
    $obj = new proveedor();
    $objs = $obj->listDB();
    
    ?>
    <div class='contenedor-tabla'>
    <table id='tb' class='display' cellspacing='0' width='100%'>
    <thead>
    <tr>
    <th>Id</th><th>Razon Social</th><th>Ruc</th><th>Direccion</th><th>Telefono</th>
    <th>OPC</th>
    </tr>
    </thead>
    <tbody>
    <?php
        if (is_array($objs)):
        foreach ($objs as $o):
    ?>
    <tr><td><?php echo $o['id']; ?></td><td><?php echo $o['razon_social']; ?></td><td><?php echo $o['ruc']; ?></td><td><?php echo $o['direccion']; ?></td><td><?php echo $o['telefono']; ?></td>
    <td><a href='#' onclick='sel(<?php echo $o['id']; ?>)'><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a><br/><a href='#' onclick='del(<?php echo $o['id']; ?>)'><i class="fa fa-trash" aria-hidden="true"></i></a></td>
    </tr>
    <?php
        endforeach;
        endif;
    ?>
    <?php
    $nombre_tabla = 'proveedor';
    require_once('recursos/componentes/footer.php');
    ?>