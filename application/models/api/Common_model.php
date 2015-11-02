<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class Common_model extends CI_Model{
    
    function get_user_type($user_id=NULL){
        return $this->pdb->singleVal("bom_users","user_id='$user_id'","user_type");
    }
    
    function get_user_prefix_and_table($user_id=NULL){
        $user_type = $this->pdb->singleVal("bom_users","user_id='$user_id'","user_type");
        $user_detail_table = $user_type == 'm'?'bom_manufacturers':($user_type == 'v'?'bom_vendors':'bom_customers');
            
        $user_detail_prefix = $user_type == 'm'?'manu':($user_type == 'v'?'vndr':'cstmr');
        
        $user_detail = array();
        
        $user_detail['user_detail_table'] = $user_detail_table;
        $user_detail['user_detail_prefix'] = $user_detail_prefix;
        
        return $user_detail;
    }
    
    function is_access_token_valid($user_id=0, $access_token=0){
        $user_id = intval($user_id);
        $access_token = intval($access_token);
        return $this->pdb->singleVal("bom_users","user_id=$user_id AND access_token=$access_token", "user_id");
    }
    
    function get_user_id_from_contact_number($contact_number = 0){
        $user_id = 0;
        $user_id = $this->pdb->singleVal("bom_customers", "cstmr_contact_number=$contact_number", "user_id");
        if(empty($user_id)){
            $user_id = $this->pdb->singleVal("bom_manufacturers", "manu_contact_number=$contact_number", "user_id");
           if(empty($user_id)){
               $user_id = $this->pdb->singleVal("bom_vendors", "vndr_contact_number=$contact_number", "user_id");
           } 
        }
        return $user_id;
    }
}

