<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require APPPATH . '/libraries/REST_Controller.php';
class Group_api extends REST_Controller{
    function __construct() {
        parent::__construct();
        $this->load->model('api/Group_model', 'group');
        $this->load->model('api/Common_model', 'common');
    }
    
    function group_create_post(){
        $key_empty = '';
        if(empty($this->post('userid')))
        {
            $key_empty = 'userid';
        }
        if(empty($this->post('access_token')))
        {
            $key_empty = 'access_token';
        }
        if(empty(($this->post('group_name'))))
        {
            $key_empty = 'group_name';
        }
        if(empty(($this->post('group_type'))))
        {
            $key_empty = 'group_type';
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
        
        $group_id = $this->group->group_create($this->post('userid'), $this->post('group_name'), $this->post('group_type'));
        if($group_id){
            $this->response([
                    'status' => TRUE,
                    'group_id' => $group_id
                ], REST_Controller::HTTP_CREATED); 
        }else{
            $this->response([
                    'status' => False,
                    'Message' => "Group not created."
                ], REST_Controller::HTTP_BAD_REQUEST); 
        }
    }
    
    function group_delete_post(){
        $key_empty = '';
        if(empty($this->post('userid')))
        {
            $key_empty = 'userid';
        }
        if(empty($this->post('access_token')))
        {
            $key_empty = 'access_token';
        }
        if(empty(($this->post('group_id'))))
        {
            $key_empty = 'group_id';
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
        
        $success = $this->group->group_delete($this->post('userid'), $this->post('group_id'));
        if($success){
            $this->response([
                    'status' => TRUE
                ], REST_Controller::HTTP_OK); 
        }else{
            $this->response([
                    'status' => False,
                    'Message' => "Group not deleted."
                ], REST_Controller::HTTP_BAD_REQUEST); 
        }
    }
    
    function relation_create_post(){
        $key_empty = '';
        if(empty($this->post('userid')))
        {
            $key_empty = 'userid';
        }
        if(empty($this->post('access_token')))
        {
            $key_empty = 'access_token';
        }
        if(empty($this->post('contact_number')))
        {
            $key_empty = 'contact_number';
        }
        if(empty($this->post('relation_type')))
        {
            $key_empty = 'relation_type'; 
        }
        if(empty(($this->post('group_id'))))
        {
            $group_id = NULL;
        }else{
            $group_id = $this->post('group_id');;
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
        
        $user_id_from_contact = $this->common->get_user_id_from_contact_number($this->post('contact_number'));
        if(empty($user_id_from_contact)){
            $this->response([
                'status' => FALSE,
                'message' => 'User not exist in application.'
                ], REST_Controller::HTTP_BAD_REQUEST); // NOT_FOUND (404) being the HTTP response code
        }else{
            $relation_id = $this->group->relation_create($this->post('userid'), $user_id_from_contact, $this->post('relation_type'), $group_id);
            if($relation_id){
                $this->response([
                'status' => TRUE
                ], REST_Controller::HTTP_CREATED); 
            }else{
                $this->response([
                'status' => FALSE,
                'message' => 'Relation not created.'    
                ], REST_Controller::HTTP_BAD_REQUEST); 
            }
        }
    }
}

