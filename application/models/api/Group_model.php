<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Group_model extends CI_Model{
    function group_create($user_id=0, $group_name=NULL, $group_type=NULL){
        try{
            return $this->pdb->insert("bom_group", array("group_name"=>$group_name, "group_type"=>$group_type, "manager_id"=>$user_id, "created_on"=>  current_date_time(), "updated_on"=>  current_date_time()));
        }catch(PDOException $e){
            return false;
        } 
    }
    
    function group_delete($user_id=0, $group_id=0){
        $this->pdb->beginTransaction();
        try{
            $this->pdb->delete("bom_group", "group_id=$group_id AND manager_id=$user_id");
            $this->pdb->update("bom_user_relation", array("group_id"=>NULL), "group_id=$group_id");
            $this->pdb->commit();
            return true;
        }catch(PDOException $e){
            return false;
        } 
    }
    
    function relation_create($user_id=0, $user_id_from_contact=0, $relation_type=NULL, $group_id=NULL){
        try{
            return $this->pdb->insert("bom_user_relation", array("parent_id"=>$user_id, "child_id"=>$user_id_from_contact, "relation_type"=> $relation_type, "group_id"=>$group_id, "created_on"=> current_date_time(), "updated_on"=> current_date_time()));
        }catch(PDOException $e){
            $e->getMessage();
            return false;
        }
    }
}

