<?php

class item
{
    private $name;
    private $price;
    private $dollarSign;

    public function __construct($name = '', $price = '', $dollarSign = false)
    {
        $this->name = $name;
        $this->price = $price;
        $this->dollarSign = $dollarSign;
    }

    public function getAsString($width = 45)
    {
        /*$rightCols = 8;
        $leftCols = $width - $rightCols;
        if ($this->dollarSign) {
            $leftCols = $leftCols / 2 - $rightCols / 2;
        }*/
        $left = $this->name;

       // $sign = ($this->dollarSign ? 'S/ ' : '');
        $right = $this->price;
        return "$left$right\n";
    }

    public function __toString()
    {
        return $this->getAsString();
    }
}
