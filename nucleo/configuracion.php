<?php

require_once('include/SuperClass.php');

class configuracion extends SuperClass {

    private $inputvars = array();
    private $inputname = 'configuracion';

    function __construct($id = NULL, $fecha_cierre = NULL, $nombre_negocio = NULL, $ruc = NULL, $direccion = NULL, $tipo_negocio = NULL, $telefono = NULL, $pagina_web = NULL, $razon_social = NULL, $moneda = NULL, $serie_boleta = NULL, $serie_factura = NULL, $almacen_principal = NULL,$estado_fila = NULL,
    $url_os_ticket = NULL,
    $key_os_ticket = NULL,
    $ip_publica_cliente_os_ticket = NULL,
    $logo_ticket = NULL,
    $logo_boleta = NULL,
    $logo_factura = NULL
    ) {
        $this->inputvars["id"] = $id;
        $this->inputvars["fecha_cierre"] = $fecha_cierre;
        $this->inputvars["nombre_negocio"] = $nombre_negocio;
        $this->inputvars["ruc"] = $ruc;
        $this->inputvars["direccion"] = $direccion;
        $this->inputvars["tipo_negocio"] = $tipo_negocio;
        $this->inputvars["telefono"] = $telefono;
        $this->inputvars["pagina_web"] = $pagina_web;
        $this->inputvars["razon_social"] = $razon_social;
        $this->inputvars["moneda"] = $moneda;
        $this->inputvars["serie_boleta"] = $serie_boleta;
        $this->inputvars["serie_factura"] = $serie_factura;
        $this->inputvars["almacen_principal"] = $almacen_principal;

        $this->inputvars["url_os_ticket"] = $url_os_ticket;
        $this->inputvars["key_os_ticket"] = $key_os_ticket;
        $this->inputvars["ip_publica_cliente_os_ticket"] = $ip_publica_cliente_os_ticket;

        $this->inputvars["logo_ticket"] = $logo_ticket;
        $this->inputvars["logo_boleta"] = $logo_boleta;
        $this->inputvars["logo_factura"] = $logo_factura;

        $this->inputvars["estado_fila"] = $estado_fila;

        parent::__construct($this->inputvars, $this->inputname);
    }

    public function setIpPublicaOsTicket($newval) {
        parent::setVar("ip_publica_cliente_os_ticket", $newval);
    }

    public function getIpPublicaOsTicket() {
        return parent::getVar("ip_publica_cliente_os_ticket");
    }


    public function setKeyOsTicket($newval) {
        parent::setVar("key_os_ticket", $newval);
    }

    public function getKeyOsTicket() {
        return parent::getVar("key_os_ticket");
    }


    public function setUrlOsTicket($newval) {
        parent::setVar("url_os_ticket", $newval);
    }

    public function getUrlOsTicket() {
        return parent::getVar("url_os_ticket");
    }

    public function setId($newval) {
        parent::setVar("id", $newval);
    }

    public function getId() {
        return parent::getVar("id");
    }

    public function setFechaCierre($newval) {
        parent::setVar("fecha_cierre", $newval);
    }

    public function getFechaCierre() {
        return parent::getVar("fecha_cierre");
    }

    public function setNombreNegocio($newval) {
        parent::setVar("nombre_negocio", $newval);
    }

    public function getNombreNegocio() {
        return parent::getVar("nombre_negocio");
    }

    public function setRuc($newval) {
        parent::setVar("ruc", $newval);
    }

    public function getRuc() {
        return parent::getVar("ruc");
    }

    public function setDireccion($newval) {
        parent::setVar("direccion", $newval);
    }

    public function getDireccion() {
        return parent::getVar("direccion");
    }

    public function setTipoNegocio($newval) {
        parent::setVar("tipo_negocio", $newval);
    }

    public function getTipoNegocio() {
        return parent::getVar("tipo_negocio");
    }

    public function setTelefono($newval) {
        parent::setVar("telefono", $newval);
    }

    public function getTelefono() {
        return parent::getVar("telefono");
    }

    public function setPaginaWeb($newval) {
        parent::setVar("pagina_web", $newval);
    }

    public function getPaginaWeb() {
        return parent::getVar("pagina_web");
    }

    public function setRazonSocial($newval) {
        parent::setVar("razon_social", $newval);
    }

    public function getRazonSocial() {
        return parent::getVar("razon_social");
    }

    public function setMoneda($newval) {
        parent::setVar("moneda", $newval);
    }

    public function getMoneda() {
        return parent::getVar("moneda");
    }

    public function setSerieBoleta($newval) {
        parent::setVar("serie_boleta", $newval);
    }

    public function getSerieBoleta() {
        return parent::getVar("serie_boleta");
    }

    public function setSerieFactura($newval) {
        parent::setVar("serie_factura", $newval);
    }

    public function getSerieFactura() {
        return parent::getVar("serie_factura");
    }

    public function setEstadoFila($newval) {
        parent::setVar("estado_fila", $newval);
    }
    
    public function getAlmacenPrincipal() {
        return parent::getVar("almacen_principal");
    }

    public function setAlmacenPrincipal($newval) {
        parent::setVar("almacen_principal", $newval);
    }

    public function getLogoTicket() {
        return parent::getVar("logo_ticket");
    }

    public function setLogoTicket($newval) {
        parent::setVar("logo_ticket", $newval);
    }

    public function getLogoBoleta() {
        return parent::getVar("logo_boleta");
    }

    public function setLogoBoleta($newval) {
        parent::setVar("logo_boleta", $newval);
    }

    public function getLogoFactura() {
        return parent::getVar("logo_factura");
    }

    public function setLogoFactura($newval) {
        parent::setVar("logo_factura", $newval);
    }

    public function getEstadoFila() {
        return parent::getVar("estado_fila");
    }

}

?>