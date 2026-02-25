<?php
    class Producto {
        private $idproducto;
        private $nombre;
        private $marca;
        private $modelo;
        private $precio;
        private $stock;


        public function __construct($idproducto, $nombre, $marca, $modelo, $precio, $stock) {
            $this->idproducto = $idproducto;
            $this->nombre = $nombre;
            $this->marca = $marca;
            $this->modelo = $modelo;
            $this->precio = $precio;
            $this->stock = $stock;
        }

        public function __get($atributo) {
            return $this->$atributo;
        }

        public function __set($atributo, $valor) {
            $this->$atributo = $valor;
        }
    }