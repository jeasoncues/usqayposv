<?php

/**
 * @author Gino Lluen
 * @copyright 2013
 * @Modded in febrrury 2016
 */
include_once('MasterConexion.php');

class SuperClass extends MasterConexion {

    private $allvars = array();
    private $my_name;

    function __construct($imput_vars, $imput_name) {
        parent::__construct();
        $this->allvars = $imput_vars;
        $this->my_name = $imput_name;
    }

    function setVar($name, $value) {
        $this->allvars[$name] = $value;
        return true;
    }

    function getVar($name_v) {
        return $this->allvars[$name_v];
    }

    function insertDB() {
        $query = "Insert into " . $this->my_name . " (";
        for ($i = 0; $i < count($this->allvars); $i++) {
            $query .= "" . key($this->allvars) . ",";
            next($this->allvars);
        }
        $query = substr($query, 0, -1);
        $query .= ") values(";
        reset($this->allvars);
        for ($i = 0; $i < count($this->allvars); $i++) {
            //Aqui habia un error de comparacion, toma los 0 como null
            //Corregido al poner ===
            if (current($this->allvars) === NULL) {
                $query .= "NULL,";
            } else {
                if (current($this->allvars) === '') {
                    $query .= "NULL,";
                }else{
                    $query .= "'" . utf8_encode(current($this->allvars)) . "',";
                }
            }
            next($this->allvars);
        }
        $query = substr($query, 0, -1);
        $query .= ")";

        $this->allvars["id"] = parent::consulta_id($query);

        return $this->allvars["id"];
    }

    function getDB() {
        for ($i = 0; $i < count($this->allvars); $i++) {
            $query = "Select " . key($this->allvars) . " FROM " . $this->my_name . " WHERE id = '" . $this->allvars["id"] . "'";
            // var_dump($query);
            // $query="Select * FROM usuario WHERE id = '1'";
            $resultado = parent::consulta_arreglo($query);           
            $this->allvars[key($this->allvars)] = $resultado[0];
            next($this->allvars);
        }
    }

    function where($rules)
    {
        //rules = ['campo', 'operador', 'valor'];
        $query = "Select * from {$this->my_name} where ";


        $query .= "{$rules[0]} {$rules[1]} {$rules[2]}";

        $resultado = parent::consulta_matriz($query);
        return $resultado;
    }



    function whereWithLogicOperator($rules)
    {
        //rules = [['campo', 'operador', 'valor', 'next logic operator']];

        $query = "Select * from {$this->my_name} where ";

        foreach ($rules as $key => $rule) {
            $query .= "{$rule[0]} {$rule[1]} {$rule[2]}";

            if ($rule[3]) {
                $query .= " {$rule[3]} ";
            }
        }

        $resultado = parent::consulta_matriz($query);
        return $resultado;
    }


    function getDBC() {
        for ($i = 0; $i < count($this->allvars); $i++) {
            $query = "Select " . key($this->allvars) . " FROM " . $this->my_name . " WHERE id = '" . $this->allvars["id"] . "'";
            // var_dump($query);
            // $query="Select * FROM usuario WHERE id = '1'";
            $resultado = parent::consulta_arreglo_c($query);
            // var_dump($resultado);
            $this->allvars[key($this->allvars)] = $resultado[0];
            next($this->allvars);
        }
    }


    function searchDB($rule, $variable_name, $type = 1) {
        //rule = valor a buscar
        //variable_name = variable a comparar
        //type = 1 exacta, 2 aproximada
        if ($type == 1) {
            $query = "Select * from " . $this->my_name . " where " . $variable_name . " = '" . $rule . "'";
            $resultado = parent::consulta_matriz($query);
            return $resultado;
        } else if ($type!=0) {
            $query = "Select * from " . $this->my_name . " where " . $variable_name . " LIKE '" . $rule . "%'";
            $resultado = parent::consulta_matriz($query);
            return $resultado;
        }

        //QUERY para login de usuario
        if ($type == 0) {
            $query = "SELECT u.id as id_usuario, ru.usuario_padre as id_referido
            FROM {$this->my_name} u 
            INNER JOIN relacion_usuario ru on u.id = ru.usuario_hijo
            WHERE {$variable_name} = {$rule}";

            return parent::consulta_matriz($query);
        }
    }
    

    function deleteDB() {
        $query = "Delete from " . $this->my_name . " WHERE id = '" . $this->allvars["id"] . "' ";
        $r1 = parent::consulta_simple($query);
        if ($r1 === 0) {
            $r1 = parent::consulta_simple("Update " . $this->my_name . " set estado_fila = 0 WHERE id = '" . $this->allvars["id"] . "' ");
        }
        return $r1;
    }

    function updateDB() {
        $resultado = false;
        for ($i = 0; $i < count($this->allvars); $i++) {
            if (current($this->allvars) != NULL) {
                $query = "UPDATE " . $this->my_name . " SET " . key($this->allvars) . " = '" . utf8_encode(current($this->allvars)) . "' WHERE id = '" . $this->allvars["id"] . "'";
                $resultado = parent::consulta_simple($query);
            }
            next($this->allvars);
        }
        return $resultado;
    }

    function getNumberofRows() {
        $query = "Select * from " . $this->my_name . " ";
        return parent::consulta_cantidad($query);
    }

    function listDB($limit = 10, $pagination = 0) {
        $resultado = array();
        if ($pagination == 0) {
            $query = "Select * from " . $this->my_name . " where estado_fila = 1";
            $resultado = parent::consulta_matriz($query);
            return $resultado;
        } else {
            $total = $this->getNumberofRows();
            $total_paginas = round(($total / $limit), 0, PHP_ROUND_HALF_EVEN);
            $actual = 0;
            $proximo = 10;
            for ($i = 0; $i < $total_paginas; $i++) {
                $query = "Select * from " . $this->my_name . " where estado_fila = 1 LIMIT " . $actual . "," . $proximo . "";
                $resultado[$i] = parent::consulta_matriz($query);
                $actual = $actual + 10;
                $proximo = $proximo + 10;
            }
            return $resultado;
        }
    }

    function listDBImp($limit = 10, $pagination = 0) {
        $resultado = array();
        if ($pagination == 0) {
            $query = "Select * from " . $this->my_name . "";
            $resultado = parent::consulta_matriz($query);
            return $resultado;
        } else {
            $total = $this->getNumberofRows();
            $total_paginas = round(($total / $limit), 0, PHP_ROUND_HALF_EVEN);
            $actual = 0;
            $proximo = 10;
            for ($i = 0; $i < $total_paginas; $i++) {
                $query = "Select * from " . $this->my_name . " LIMIT " . $actual . "," . $proximo . "";
                $resultado[$i] = parent::consulta_matriz($query);
                $actual = $actual + 10;
                $proximo = $proximo + 10;
            }
            return $resultado;
        }
    }

    function listDBUser($limit = 10, $pagination = 0) {
        $resultado = array();
        if ($pagination == 0) {
            $query = "Select * from " . $this->my_name . " where id>1 and estado_fila = 1";
            $resultado = parent::consulta_matriz($query);
            return $resultado;
        } else {
            $total = $this->getNumberofRows();
            $total_paginas = round(($total / $limit), 0, PHP_ROUND_HALF_EVEN);
            $actual = 0;
            $proximo = 10;
            for ($i = 0; $i < $total_paginas; $i++) {
                $query = "Select * from " . $this->my_name . " where id>1 and estado_fila = 1 LIMIT " . $actual . "," . $proximo . "";
                $resultado[$i] = parent::consulta_matriz($query);
                $actual = $actual + 10;
                $proximo = $proximo + 10;
            }
            return $resultado;
        }
    }

    function __destruct() {
        parent::__destruct();
    }

    function returnObject() {
        return $this->allvars;
    }

    // AGREGADO
    function listPag($request, $columns, $inner = "", $default_where = "") {

        $limit = self::limit($request);
        $order = self::order($request);
        $where = self::filter($request, $columns);
        if (empty($where)) {
            $where = !empty($default_where) ? " WHERE " . $default_where : $default_where;
        } else {
            $where = !empty($default_where) ? $where . " AND " . $default_where : $where;
        }


        $query = "SELECT SQL_CALC_FOUND_ROWS " . implode(", ", self::pluck($columns, 'db')) .
                " FROM $this->my_name
                        $inner
			$where
			$order
			$limit";

        //echo $query;

        $data = parent::consulta_matriz($query);

        $resFilterLength = $this->getFoundRow();
        // Total data set length
        $recordsTotal = $this->getNumberofRows();

        return
                array(
                    "draw" => isset($request['draw']) ? intval($request['draw']) : 0,
                    "recordsTotal" => $recordsTotal,
                    "recordsFiltered" => $resFilterLength,
                    "data" => $data
        );
    }

    function limit($request) {
        $limit = '';

        if (isset($request['start']) && $request['length'] != -1) {
            $limit = "LIMIT " . intval($request['start']) . ", " . intval($request['length']);
        }

        return $limit;
    }

    function order($request) {
        $order = '';

        if (isset($request['order']) && count($request['order'])) {
            $orderBy = array();

            for ($i = 0, $ien = count($request['order']); $i < $ien; $i++) {
                // Convert the column index into the column data property
                $columnIdx = intval($request['order'][$i]['column']);
                $requestColumn = $request['columns'][$columnIdx];

                $column = $requestColumn['data'];
                if ($requestColumn['orderable'] == 'true') {
                    $dir = $request['order'][$i]['dir'] === 'asc' ?
                            'ASC' :
                            'DESC';

                    $orderBy[] = "" . $column . " " . $dir;
                }
            }

            $order = 'ORDER BY ' . implode(', ', $orderBy);
        }

        return $order;
    }

    function pluck($a, $prop) {
        $out = array();

        for ($i = 0, $len = count($a); $i < $len; $i++) {
            $out[] = $a[$i][$prop];
        }

        return $out;
    }

    function filter($request, $columns) {
        $globalSearch = array();
        $columnSearch = array();
        $djsColumns = self::pluck($columns, 'djs');

        if (isset($request['search']) && $request['search']['value'] != '') {
            $str = $request['search']['value'];

            for ($i = 0, $ien = count($request['columns']); $i < $ien; $i++) {
                $requestColumn = $request['columns'][$i];
                $columnIdx = array_search($requestColumn['data'], $djsColumns);
                $column = $columns[$columnIdx]['dt'];


                if ($requestColumn['searchable'] == 'true') {
                    $binding = "'%" . $str . "%'";
                    $globalSearch[] = "" . $column . " LIKE " . $binding;
                }
            }
        }

        // Individual column filtering
        if (isset($request['columns'])) {
            for ($i = 0, $ien = count($request['columns']); $i < $ien; $i++) {
                $requestColumn = $request['columns'][$i];
                $columnIdx = array_search($requestColumn['data'], $djsColumns);
                $column = $columns[$columnIdx]['dt'];

                $str = $requestColumn['search']['value'];

                if ($requestColumn['searchable'] == 'true' &&
                        $str != '') {
                    $binding = '%' . $str . '%';
                    $columnSearch[] = "'" . $column . "' LIKE " . $binding;
                }
            }
        }

        // Combine the filters into a single string
        $where = '';

        if (count($globalSearch)) {
            $where = '(' . implode(' OR ', $globalSearch) . ')';
        }

        if (count($columnSearch)) {
            $where = $where === '' ?
                    implode(' AND ', $columnSearch) :
                    $where . ' AND ' . implode(' AND ', $columnSearch);
        }

        if ($where !== '') {
            $where = 'WHERE ' . $where;
        }

        return $where;
    }

    function getFoundRow() {
        return parent::consulta_found_row();
    }

    

}
