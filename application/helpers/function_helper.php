<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function otp_generate()
{
    $six_digit_random_number = mt_rand(100000, 999999);
    return $six_digit_random_number;
}

//Otp expire after 15 minutes
function otp_expire()
{
    $time_after_15_minutes = date('Y-m-d h:i:s',time()+900);
    return $time_after_15_minutes;
}

function current_date_time()
{
    return date('Y-m-d h:i:s', time());
}

function get_access_token($user_id){
    return $user_id + time();
}

