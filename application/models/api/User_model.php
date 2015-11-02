<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class User_model extends CI_Model
{
   
   function is_email_exist($email_id=NULL, $user_id = 0)
   {
        $cond = "login_email='$email_id'";
        if(!empty($user_id))
            $cond.=" AND user_id!='$user_id'";
        return $this->pdb->singleVal("bom_users", $cond, "user_id");
   }
    
    function is_mobile_exist($mobile=NULL, $user_id = 0)
    {
        $user_prefix_and_table = get_user_prefix_and_table($user_id);
        
        $user_detail_prefix = $user_prefix_and_table['user_detail_prefix'];
        $user_detail_table = $user_prefix_and_table['user_detail_table'];
        
        $columnName = $user_detail_prefix."_contact_number";
        
        $cond = "$columnName='$mobile'";
        if(!empty($user_id))
            $cond.=" AND user_id!='$user_id'";
        return $this->pdb->singleVal($user_detail_table, $cond, "user_id");
    }
    
    function sign_up_user($user = array())
    {
        $user_id = $this->pdb->insert('bom_users', $user);
        return $user_id;
    }
    
    function sign_up_user_detail($user_detail_table=NULL, $user_detail = array())
    {
        $user_detail_id = $this->pdb->insert($user_detail_table, $user_detail);
        return $user_detail_id;
    }
    
    function otp_verify($user_id=NULL, $passcode=NULL)
    {
        $current_date_time = current_date_time();
        $otp_match_cond = "user_id='$user_id' AND otp='$passcode'";
        $otp_match = $this->pdb->singleVal("bom_users", $otp_match_cond, "user_id");
        if(!$otp_match)
        {
            return "Please enter correct One time password.";
        }
        $otp_expire_check_cond = "user_id='$user_id' AND otp_expire_time<='$current_date_time'";
        $otp_expire_check = $this->pdb->singleval("bom_users", $otp_expire_check_cond, 'user_id'); 
        if($otp_expire_check)
        {
            return "One time password expired, please press resend otp and verify.";
        }else
        {
            $this->pdb->update("bom_users", array("is_otp_verified"=>1, "status"=>1), "user_id='$user_id'");
            return 1;
        }    
    }
    
    function update_user_contact_number($user_id=NULL, $table=NULL, $new_number=array()){
        $this->pdb->update($table, $new_number,"user_id='$user_id'");
    }
    
    function new_otp($user_id=NULL, $new_otp=array()){
        try{
            $this->pdb->update("bom_users", $new_otp, "user_id='$user_id'");
            return true;
        }catch(Exception $e){
            return "One time password not send, please try again.";
        }    
    }
    
    function sign_in_using_email($login_email=NULL, $password=NULL){ 
        $password = encrypt_text($password);
        return $this->pdb->singleVal("bom_users", "login_email='$login_email' AND password='$password'", "user_id");
    }
    
    function sign_in_using_social($social_id=NULL, $social_type=NULL){
        return $this->pdb->singleVal("bom_users", "social_id='$social_id' AND social_type='$social_type'", "user_id");
    }
    
    function update_device_info($user_id=0, $device_type=NULL, $device_id=NULL){
        $this->pdb->update("bom_users", array("device_type"=>$device_type, "device_id"=>$device_id), "user_id='$user_id'", "user_id");
    }
    
    function user_detail($user_id){ 
        $upload_url = UPLOAD_URL;
        $access_token = get_access_token($user_id);
        
        $this->pdb->update("bom_users", array("access_token"=>$access_token), "user_id='$user_id'");
        $user_prefix_and_table = get_user_prefix_and_table($user_id);
        
        $user_detail_prefix = $user_prefix_and_table['user_detail_prefix'];
        $user_detail_table = $user_prefix_and_table['user_detail_table'];
        
        $brandimage    = $user_detail_prefix."_brand_logo";
        $companyimage  = $user_detail_prefix."_company_photo";
        $displayname   = $user_detail_prefix."_display_name";
        $companyname   = $user_detail_prefix."_company_name";
        $country       = $user_detail_prefix."_country";
        $state         = $user_detail_prefix."_state";
        $city         = $user_detail_prefix."_city";
        $mobile        = $user_detail_prefix."_contact_number";
        
        
       $query = "SELECT  U.user_id userid, U.user_type usertype, U.login_email emailid, U.is_otp_verified, U.is_notification, U.access_token, U.status,
                          CONCAT('$upload_url','/',$brandimage) brandimage, 
                          CONCAT('$upload_url','/',$companyimage) companyimage,
                          $displayname displayname, $companyname companyname,
                          $country country, $state state, $city city,
                          $mobile mobile 
                          FROM bom_users U 
                          JOIN  $user_detail_table  ON U.user_id = $user_detail_table.user_id
                          WHERE U.user_id ='$user_id'";
        
        $result = $this->pdb->query($query);
        
        return $result[0];
        
    }
    
    function forgot_password($email_id=NULL, $password=NULL){
        try{
            $this->pdb->update("bom_users", array("password"=>$password), "login_email='$email_id'");
            return true;
        }catch(Exception $e){
            return false;
        }
    }
    
    function user_name($email_id=NULL){
        $user_id = $this->pdb->singleVal("bom_users", "login_email='$email_id'", "user_id");
        
        $user_prefix_and_table = get_user_prefix_and_table($user_id);
        
        $user_detail_prefix = $user_prefix_and_table['user_detail_prefix'];
        $user_detail_table = $user_prefix_and_table['user_detail_table'];
        
        $display_name    = $user_detail_prefix."_display_name";
        
        return $this->pdb->singleVal($user_detail_table, "user_id='$user_id'", $display_name);
    }
    
    function contact_add($user_id=0, $contact=array()){
        $data = [];
        $data['user_id'] = $user_id;
        //print_r($contact);
        $stmt = $this->pdb->prepare("INSERT INTO bom_user_contacts(user_id, contact_number) values(:user_id, :contact_number)");
        try{
            foreach($contact as $row){
                $data['contact_number'] = $row;
                $stmt->execute($data);
            }
            return true;
        }catch(Exception $e){
            return false;
        }
    }
    
    function contact_delete($user_id=0, $contact=array()){
        $contacts  = implode(',', $contact);
        $this->pdb->delete("bom_user_contacts", "user_id=$user_id AND contact_number IN ($contacts)");
    }
    
    function contact_existing($contacts=array()){
//        $contacts_number = [];
//        foreach($contacts as $row){
//            $contacts_number[] = $row;
//        }
        $contacts_existing = [];
        $contacts_implode  = implode(',', $contacts);
        $manu_contact_number = $this->pdb->select("bom_manufacturers", "manu_contact_number IN ($contacts_implode)", "manu_contact_number AS number");
        $cstmr_contact_number = $this->pdb->select("bom_customers", "cstmr_contact_number IN ($contacts_implode)", "cstmr_contact_number AS number");
        $vndr_contact_number = $this->pdb->select("bom_vendors", "vndr_contact_number IN ($contacts_implode)", "vndr_contact_number AS number");
        
        if(!empty($manu_contact_number)){
            foreach($manu_contact_number as $row){
                array_push($contacts_existing, $row['number']);
            }
        }
        if(!empty($cstmr_contact_number)){
            foreach($cstmr_contact_number as $row){
                array_push($contacts_existing, $row['number']);
            }
        }
        if(!empty($vndr_contact_number)){
            foreach($vndr_contact_number as $row){
                array_push($contacts_existing, $row['number']);
            }
        }
        return $contacts_existing;
    }
    
    function password_current_match($user_id=0, $password=NULL){
        return $this->pdb->singleVal("bom_users", "user_id=$user_id AND password='$password'", "user_id");
    }
    
    function password_change($user_id=0, $password=NULL){
        try{
            $this->pdb->update("bom_users", array("password"=>$password), "user_id=$user_id");
            return true;
        }  catch (PDOException $e){
            return false;
        }
    }
}

