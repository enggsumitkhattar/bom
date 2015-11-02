<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Brand_model extends CI_Model{
    function brand_create($user_id=0, $brand_name=NULL){
        try{
            return $this->pdb->insert("bom_brand", array("brand_name"=>$brand_name, "user_id"=>$user_id, "created_on"=>  current_date_time(), "updated_on"=>  current_date_time()));
        }catch(PDOException $e){
            return false;
        } 
    }
    
    function brand_delete($user_id=0, $brand_id=0){
        $this->pdb->beginTransaction();
        try{
            $this->pdb->delete("bom_brand", "brand_id=$brand_id AND user_id=$user_id");
            $this->pdb->commit();
            return true;
        }catch(PDOException $e){
            return false;
        } 
    }
    
    function brand_list_user($user_id=0){
        return $this->pdb->select("bom_brand", "user_id=$user_id AND status=1", "brand_id, brand_name");
    }

}
