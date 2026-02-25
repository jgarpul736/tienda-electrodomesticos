<?php
    class Usuario {
        private $idusuario;
        private $dni;
        private $apellidos;
        private $nombre;
        private $email;
        private $password;
        private $rol;


        public function __construct($dni, $apellidos, $nombre, $email, $password, $rol) {
            $this->dni = $dni;
            $this->apellidos = $apellidos;
            $this->nombre = $nombre;
            $this->email = $email;
            $this->password = $password;
            $this->rol = (int)$rol;
        }

        public function __get($atributo) {
            return $this->$atributo;
        }

        public function __set($atributo, $valor) {
            $this->$atributo = $valor;
        }
    }