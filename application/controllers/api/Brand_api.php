<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require APPPATH . '/libraries/REST_Controller.php';
class Brand_api extends REST_Controller{
    function __construct() {
        parent::__construct();
        $this->load->model('api/Brand_model', 'brand');
    }
    
    function brand_create_post(){
        $key_empty = '';
        if(empty($this->post('userid')))
        {
            $key_empty = 'userid';
        }
        if(empty($this->post('access_token')))
        {
            $key_empty = 'access_token';
        }
        if(empty(($this->post('brand_name'))))
        {
            $key_empty = 'brand_name';
        }
        if (!empty($key_empty))
        {
            $this->response([
                'status' => FALSE,
                'message' => $key_empty.' not found'
                ], REST_Controller::HTTP_BAD_REQUEST); // NOT_FOUND (404) being the HTTP response code
        }
        if(!is_access_token_valid($this->post('userid'), $this->post('access_token'))){
            $this->response([
                'status' => FALSE,
                'message' => 'Access token not valid'
                ], REST_Controller::HTTP_BAD_REQUEST); // NOT_FOUND (404) being the HTTP response code
        }
        
        $brand_id = $this->brand->brand_create($this->post('userid'), $this->post('brand_name'));
        if($brand_id){
            $this->response([
                    'status' => TRUE,
                    'brand_id' => $brand_id
                ], REST_Controller::HTTP_CREATED); 
        }else{
            $this->response([
                    'status' => False,
                    'Message' => "Brand not created."
                ], REST_Controller::HTTP_BAD_REQUEST); 
        }
    }
    
    function brand_delete_post(){
        $key_empty = '';
        if(empty($this->post('userid')))
        {
            $key_empty = 'userid';
        }
        if(empty($this->post('access_token')))
        {
            $key_empty = 'access_token';
        }
        if(empty(($this->post('brand_id'))))
        {
            $key_empty = 'brand_id';
        }
        if (!empty($key_empty))
        {
            $this->response([
                'status' => FALSE,
                'message' => $key_empty.' not found'
                ], REST_Controller::HTTP_BAD_REQUEST); // NOT_FOUND (404) being the HTTP response code
        }
        if(!is_access_token_valid($this->post('userid'), $this->post('access_token'))){
            $this->response([
                'status' => FALSE,
                'message' => 'Access token not valid'
                ], REST_Controller::HTTP_BAD_REQUEST); // NOT_FOUND (404) being the HTTP response code
        }
        
        $success = $this->brand->brand_delete($this->post('userid'), $this->post('brand_id'));
        if($success){
            $this->response([
                    'status' => TRUE
                ], REST_Controller::HTTP_OK); 
        }else{
            $this->response([
                    'status' => False,
                    'Message' => "Brand not deleted."
                ], REST_Controller::HTTP_BAD_REQUEST); 
        }
    }
    
    function brand_list_user_post(){
        $key_empty = '';
        if(empty($this->post('userid')))
        {
            $key_empty = 'userid';
        }
        if(empty($this->post('access_token')))
        {
            $key_empty = 'access_token';
        }
        if (!empty($key_empty))
        {
            $this->response([
                'status' => FALSE,
                'message' => $key_empty.' not found'
                ], REST_Controller::HTTP_BAD_REQUEST); // NOT_FOUND (404) being the HTTP response code
        }
        if(!is_access_token_valid($this->post('userid'), $this->post('access_token'))){
            $this->response([
                'status' => FALSE,
                'message' => 'Access token not valid'
                ], REST_Controller::HTTP_BAD_REQUEST); // NOT_FOUND (404) being the HTTP response code
        }
        
        $result = $this->brand->brand_list_user($this->post('userid'));
        if($result){
            $this->response([
                    'status' => TRUE,
                    'brand_list'=> $result
                ], REST_Controller::HTTP_OK); 
        }else{
            $this->response([
                    'status' => False,
                    'Message' => "Brand list not found."
                ], REST_Controller::HTTP_BAD_REQUEST); 
        }
    }
}