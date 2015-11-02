<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class Errors extends CI_Controller{
    function __construct() {
        parent::__construct();
    }
    
    function page_missing(){
        echo $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        //print_r($_SERVER);
    }
}

