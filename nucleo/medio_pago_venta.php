<?php

require_once('include/SuperClass.php');

class medio_pago_venta extends SuperClass
{

    private $inputvars = array();
    private $inputname = 'medio_pago_venta';

    function __construct($id = NULL, $nombre = NULL, $estado_fila = NULL)
    {
        $this->inputvars["id"] = $id;
        $this->inputvars["nombre"] = $nombre;
        $this->inputvars["estado_fila"] = $estado_fila;

        parent::__construct($this->inputvars, $this->inputname);
    }

    public function setId($newval)
    {
        parent::setVar("id", $newval);
    }

    public function getId()
    {
        return parent::getVar("id");
    }

    public function setNombre($newval)
    {
        parent::setVar("nombre", $newval);
    }

    public function getNombre()
    {
        return parent::getVar("nombre");
    }

    public function setEstadoFila($newval)
    {
        parent::setVar("estado_fila", $newval);
    }

    public function getEstadoFila()
    {
        return parent::getVar("estado_fila");
    }
}
