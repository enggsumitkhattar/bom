<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function is_email_format($email)
{
  if (filter_var($email, FILTER_VALIDATE_EMAIL))
  {        
        return true;
  }      
  else
  {    
        return false;  
  }      
}

function encrypt_text($txt) 
{
    return hash_hmac('sha256', $txt, 'bom[201510]');
}

function get_user_type($user_id=NULL){
    $CI = & get_instance();
    $CI->load->model('api/Common_model','common');
    return $CI->common->get_user_type($user_id);
}

function get_user_prefix_and_table($user_id=NULL){
    $CI = & get_instance();
    $CI->load->model('api/Common_model','common');
    return $CI->common->get_user_prefix_and_table($user_id);
}

function generate_otp($length = 8) {
    $result = '';
    $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

    for ($p = 0; $p < $length; $p++) {
        $result .= ($p % 2) ? $chars[mt_rand(26, 35)] : $chars[mt_rand(0, 25)];
    }

    return $result;
}

function send_mail($to, $fromname, $fromemail, $subject, $message) {
    $CI = & get_instance();
    $CI->load->library('email');
    $mail = $CI->email;

    $config['charset'] = 'utf-8';
    $config['wordwrap'] = TRUE;
    $config['mailtype'] = 'html';
    $mail->initialize($config);

    $mail->from($fromemail, $fromname);
    $mail->to($to);
    $mail->reply_to('sumit@itglobalconsulting.com', 'Be A Boss');

    $mail->subject($subject);
    $mail->message($message);

    return $mail->send();
}

function from_email() {
    //return 'support@beaboss.com';
    return 'enggsumitkhattar@gmail.com';
}

function is_access_token_valid($user_id=0, $access_token=0){
    $CI = & get_instance();
    $CI->load->model('api/Common_model','common');
    return $CI->common->is_access_token_valid($user_id, $access_token);
}

