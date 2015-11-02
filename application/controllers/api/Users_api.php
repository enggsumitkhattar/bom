<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Users_api extends REST_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -  
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in 
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */
    
       
	function __construct()
	{
		parent::__construct();
                $this->load->model('api/User_model','user');
	}
        
        public function sign_up_post()
        {
            $key_empty = '';
            $user = [];
            $user_detail = [];
            $brandimage = '';
            $companyimage = '';
            
            if(empty($this->post('usertype')))
            {
                $key_empty = 'usertype';
            }
            if(empty($this->post('displayname')))
            {
                $key_empty = 'displayname';
            }
            if(empty($this->post('companyname')))
            {
                $key_empty = 'companyname';
            }
            if(empty($this->post('emailid')))
            {
                if(empty($this->post('socialid')))
                {
                    $key_empty = 'socialid';
                }
                else
                {
                    if(empty($this->post('socialtype')))
                    {
                        $key_empty = 'socialtype';
                    }        
                }    
            }
            else
            {
                if (!is_email_format($this->post('emailid')))
                {    
                    $this->response([
                    'status' => FALSE,
                    'message' => 'Email Id not valid',
                    'code'=>    REST_Controller::HTTP_BAD_REQUEST,
                    ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
                }    
                else if (!empty($this->user->is_email_exist($this->post('emailid'))))
                {
                    $this->response([
                    'status' => FALSE,
                    'message' => 'Email Id already exists',
                    'code'=> REST_Controller::HTTP_BAD_REQUEST
                    ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
                }    
                
            }
            if(empty($this->post('country')))
            {
                $key_empty = 'country';
            }
            if(empty($this->post('state')))
            {
                $key_empty = 'state';
            }
            if(empty($this->post('city')))
            {
                $key_empty = 'city';
            }
            if(empty($this->post('mobile')))
            {
                $key_empty = 'mobile';
            }
            
            if(empty($this->post('socialid'))){
                if(empty($this->post('password')))
                {
                    $key_empty = 'password';
                }
            }    
            if(empty($_FILES['brandimage']['name']))
            {
                //$key_empty = 'brandimage';
            }
            if(empty($_FILES['companyimage']['name']))
            {
                //$key_empty = 'companyimage';
            }
            if(empty($this->post('devicetype')))
            {
                $key_empty = 'devicetype';
            }
            if(empty($this->post('deviceid')))
            {
                //$key_empty = 'deviceid';
            }
            
            if (!empty($key_empty))
            {
                // Set the response and exit
                $this->response([
                    'status' => FALSE,
                    'message'=> $key_empty.' not found',
                    'code'   => REST_Controller::HTTP_BAD_REQUEST
                    ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
            }
            
            $config = array(
                'upload_path' => "./assets/uploads/",
                'allowed_types' => "gif|jpg|png|jpeg",
                'overwrite' => TRUE,
                'max_size' => "2048000", // Can be set to particular file size , here it is 2 MB(2048 Kb)
                'max_height' => "768",
                'max_width' => "1024"
                );
            
//            if(!$this->upload->do_upload('brandimage'))
//            {
//                $this->response([
//                    'status' => FALSE,
//                    'message' => 'Could not create user at this time',
//                    'code' =>REST_Controller::HTTP_INTERNAL_SERVER_ERROR
//                ], REST_Controller::HTTP_OK); // INTERNAL_SERVER_ERROR (500) being the HTTP response code
//                
//            }else{
//                echo 'errr';
//                echo $this->upload->display_errors();die();
//                $brandimage = $this->upload->data('orig_name');
//            }
            
//            if(!$this->upload->do_upload('companyimage'))
//            {
//                $this->response([
//                    'status' => FALSE,
//                    'message' => 'Could not create user at this time',
//                    'code' =>REST_Controller::HTTP_INTERNAL_SERVER_ERROR
//                ], REST_Controller::HTTP_OK); // INTERNAL_SERVER_ERROR (500) being the HTTP response code
//                
//            }else{
//                echo $this->upload->display_errors();die();
//                $companyimage = $this->upload->data('orig_name');
//            }
            
            //$otp = otp_generate();
            $otp = 12345;
            
            $user = array(
                    'user_type'=>$this->post('usertype'), 
                    'login_email'=>$this->post('emailid'), 
                    'password'=>encrypt_text($this->post('password')), 
                    'social_type'=>$this->post('socialtype'),
                    'social_id'=>$this->post('socialid'),
                    'social_id'=>$this->post('socialid'),
                    'otp'=>$otp,
                    'otp_expire_time'=>otp_expire(),
                    'device_id'=>$this->post('deviceid'),
                    'device_type'=>$this->post('devicetype'),
                    'created_on'=>current_date_time(),
                    'updated_on'=>current_date_time(),
                );
            
            $user_id = $this->user->sign_up_user($user);
            
            if(empty($user_id)){
                $this->response([
                    'status' => FALSE,
                    'message' => 'Could not create user at this time',
                    'code' =>REST_Controller::HTTP_INTERNAL_SERVER_ERROR
                ], REST_Controller::HTTP_OK); // INTERNAL_SERVER_ERROR (500) being the HTTP response code
            }
            
            
            $user_detail_table = $this->post('usertype') == 'm'?'bom_manufacturers':($this->post('usertype') == 'v'?'bom_vendors':'bom_customers');
            
            $user_detail_prefix = $this->post('usertype') == 'm'?'manu':($this->post('usertype') == 'v'?'vndr':'cstmr');
            
            $user_detail = array(
                            'user_id'=>$user_id,
                            $user_detail_prefix.'_brand_logo' => $brandimage,
                            $user_detail_prefix.'_company_photo' => $companyimage,
                            $user_detail_prefix.'_display_name' => $this->post('displayname'),
                            $user_detail_prefix.'_company_name' => $this->post('companyname'),
                            $user_detail_prefix.'_email_id' => $this->post('emailid'),
                            $user_detail_prefix.'_country' => $this->post('country'),
                            $user_detail_prefix.'_state' => $this->post('state'),
                            $user_detail_prefix.'_city' => $this->post('city'),
                            $user_detail_prefix.'_contact_number' => $this->post('mobile'),
            );
            
            $user_detail_id = $this->user->sign_up_user_detail($user_detail_table, $user_detail);
            
            if(empty($user_detail_id)){
                $this->response([
                    'status' => FALSE,
                    'message' => 'Could not create user at this time',
                    'code'=> REST_Controller::HTTP_INTERNAL_SERVER_ERROR
                ], REST_Controller::HTTP_OK); // INTERNAL_SERVER_ERROR (500) being the HTTP response code
            }else
            {
                //send_otp();
                $this->response([
                    'brandimage' => UPLOAD_URL.$brandimage,
                    'companyimage' => UPLOAD_URL.$companyimage,
                    'status' => TRUE,
                    'userid' => $user_id,
                    'code'   => REST_Controller::HTTP_CREATED
                ], REST_Controller::HTTP_OK); // CREATED (201) being the HTTP response code
            }
                
            
            //add commit and rollback code
            
           
            
        }
        
        function otp_verify_post()
        {
            $key_empty = false;
            
            if(empty($this->post('userid')))
            {
                $key_empty = 'userid';
            }
            if(empty($this->post('passcode')))
            {
                $key_empty = 'passcode';
            }
            
            if (!empty($key_empty))
            {
                // Set the response and exit
                $this->response([
                    'status' => FALSE,
                    'message' => $key_empty.' not found',
                    'code'=>    REST_Controller::HTTP_BAD_REQUEST
                    ], REST_Controller::HTTP_OK); 
            }
            
            $success = $this->user->otp_verify($this->post('userid'), $this->post('passcode'));
            
            if($success == 1)
            {
                $this->response([
                    'status' => TRUE,
                    'userid' => $this->post('userid'),
                    'code' => REST_Controller::HTTP_OK
                ], REST_Controller::HTTP_OK); // CREATED (201) being the HTTP response code
            } 
            else
            {
                $this->response([
                    'status' => False,
                    'Message' => $success,
                    'code'    => REST_Controller::HTTP_INTERNAL_SERVER_ERROR
                ], REST_Controller::HTTP_OK); 
            }
        }
        
        function edit_number_post()
        {
            
            $key_empty = '';
            if(empty($this->post('userid')))
            {
                $key_empty = 'userid';
            }
            if(empty($this->post('mobile')))
            {
                $key_empty = 'mobile';
            }
            
            if (!empty($key_empty))
            {
                // Set the response and exit
                $this->response([
                    'status' => FALSE,
                    'message' => $key_empty.' not found',
                    'code'    => REST_Controller::HTTP_BAD_REQUEST
                    ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
            }
            
            
            
            //echo $user_type = get_user_type($this->post('userid'));exit;
            
            $user_prefix_and_table = get_user_prefix_and_table($this->post('userid'));
            
            //print_r($user_prefix_and_table);exit;
            
            $user_detail_prefix = $user_prefix_and_table['user_detail_prefix'];
            $user_detail_table = $user_prefix_and_table['user_detail_table'];
            
            $new_number = array(
                $user_detail_prefix.'_contact_number' => $this->post('mobile')
            );
            
            
            if($this->user->is_mobile_exist($this->post('mobile'), $this->post('userid'))){
                $this->response([
                    'status' => FALSE,
                    'message' => 'Mobile number already exists.',
                    'code'    => REST_Controller::HTTP_BAD_REQUEST
                    ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
            }
            
            $this->user->update_user_contact_number($this->post('userid'), $user_detail_table, $new_number);
            
            //$otp = otp_generate();
            $new_otp =array(
                'otp'=>12345,
                'otp_expire_time'=>otp_expire()
            );
            
            //$success = numeric and string; 
            $success = $this->user->new_otp($this->post('userid'),$new_otp);
            if($success == 1)
            {
                $this->response([
                    'status' => TRUE,
                    'userid' => $this->post('userid'),
                    'code' => REST_Controller::HTTP_OK
                ], REST_Controller::HTTP_OK); // CREATED (201) being the HTTP response code
            } 
            else
            {
                $this->response([
                    'status' => False,
                    'message' => $success,
                    'code' => REST_Controller::HTTP_INTERNAL_SERVER_ERROR
                ], REST_Controller::HTTP_OK); 
            }
            
        }
        
        function sign_in_post(){
            if(empty($this->post('emailid')))
            {
                if(empty($this->post('socialid')))
                {
                    $key_empty = 'socialid';
                }
                else
                {
                    if(empty($this->post('socialtype')))
                    {
                        $key_empty = 'socialtype';
                    }        
                }    
            }
            else
            {
                if (!is_email_format($this->post('emailid')))
                {    
                    $this->response([
                    'status' => FALSE,
                    'message' => 'Email Id not valid'
                    ], REST_Controller::HTTP_BAD_REQUEST); // NOT_FOUND (404) being the HTTP response code
                }    
                if (empty($this->user->is_email_exist($this->post('emailid'))))
                {
                    $this->response([
                    'status' => FALSE,
                    'message' => 'Email Id not exists'
                    ], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
                }  
                
                if(empty($this->post('password')))
                {
                    $key_empty = 'password';
                }
                
            }
            
            if(empty($this->post('devicetype')))
            {
                $key_empty = 'devicetype';
            }
            if(empty($this->post('deviceid')))
            {
                //$key_empty = 'deviceid';
            }
            
            if (!empty($key_empty))
            {
                // Set the response and exit
                $this->response([
                    'status' => FALSE,
                    'message' => $key_empty.' not found',
                    'code' => REST_Controller::HTTP_BAD_REQUEST
                    ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
            }
            
            //$user_id = int;
            if(!empty($this->post('emailid'))){
                $user_id = $this->user->sign_in_using_email($this->post('emailid'), $this->post('password'));
            }
            else {
                $user_id = $this->user->sign_in_using_social($this->post('socialid'), $this->post('socialtype'));
            }
            
            
            
            if(empty($user_id)){
                if(!empty($this->post('socialid'))){
                    $message = 'Account does not exist.';
                    $code    = REST_Controller::HTTP_UNAUTHORIZED;
                }else{
                    $message = 'Enter valid password';
                    $code    = REST_Controller::HTTP_BAD_REQUEST;
                }
                $this->response([
                    'status' => False,
                    'message' => $message,
                    'code' => $code
                ], REST_Controller::HTTP_OK); 
            }else{
                $this->user->update_device_info($user_id, $this->post('devicetype'), $this->post('deviceid'));
                $user_detail = $this->user->user_detail($user_id);
                $this->response([
                    'status' => TRUE,
                    'user_detail' => $user_detail, 
                    'code' => REST_Controller::HTTP_OK
                ], REST_Controller::HTTP_OK); // CREATED (201) being the HTTP response code
            }
            
            
        }
        
        function forgot_password_post(){
            $key_empty = '';
            if(empty($this->post('emailid')))
            {
                $key_empty = 'emailid';
            }
            
            if (!empty($key_empty))
            {
                // Set the response and exit
                $this->response([
                    'status' => FALSE,
                    'message' => $key_empty.' not found',
                    'code' => REST_Controller::HTTP_BAD_REQUEST
                    ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
            }
            
            if (empty($this->user->is_email_exist($this->post('emailid'))))
            {
                $this->response([
                'status' => FALSE,
                'message' => 'Email Id not exists.',
                'code' =>    REST_Controller::HTTP_BAD_REQUEST
                ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
            }
            
            $password = generate_otp(8);
            $success = $this->user->forgot_password($this->post('emailid'), encrypt_text($password));
            if(!$success){
                $this->response([
                'status' => FALSE,
                'message' => 'Password not send, Please try again.',
                'code'=>    REST_Controller::HTTP_BAD_REQUEST
                ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
            }else{
                $display_name = $this->user->user_name($this->post('emailid'));
                $detail = array("display_name"=>$display_name,
                                "password"=>$password
                        );
                $message = $this->load->view("email_template/forgot_password", $detail, true);
                send_mail($this->post('emailid'), "Bussiness on mobile", from_email(), "Password Recovery", $message);
                send_mail("enggsumitkhattar@gmail.com", "Bussiness on mobile", from_email(), "Password Recovery", $message);
                $this->response([
                'status' => TRUE,
                'code'=>    REST_Controller::HTTP_OK
                ], REST_Controller::HTTP_OK); 
            }
            
            
        }
        
        function sync_user_contact_post(){
            $key_empty = '';
            if(empty($this->post('userid')))
            {
                $key_empty = 'userid';
            }
            if(empty($this->post('access_token')))
            {
                $key_empty = 'access_token';
            }
            if(empty(json_decode($this->post('jsonData'), true)))
            {
                $key_empty = 'jsonData ';
            }
            if (!empty($key_empty))
            {
                $this->response([
                    'status' => FALSE,
                    'message' => $key_empty.' not found',
                    'code' => REST_Controller::HTTP_BAD_REQUEST
                    ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
            }
            
            if(!is_access_token_valid($this->post('userid'), $this->post('access_token'))){
                $this->response([
                    'status' => FALSE,
                    'message' => 'Access token not valid',
                    'code' => REST_Controller::HTTP_BAD_REQUEST
                    ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
            }
            
            $json_data = $this->post('jsonData');
            $json_data_decode = json_decode($json_data, true);
            $contact_existing = [];
            $success = false;
            //print_r($json_data_decode);
            if(!empty($json_data_decode['add'])){
                $success = $this->user->contact_add($this->post('userid'), $json_data_decode['add']);
                $contacts_existing = $this->user->contact_existing($json_data_decode['add']);
            }
            if(!empty($json_data_decode['delete'])){
                $this->user->contact_delete($this->post('userid'), $json_data_decode['delete']);
            }
            
            if($success){
                $this->response([
                'status' => TRUE, 
                'contacts_existing'=>$contacts_existing,
                'code'=> REST_Controller::HTTP_OK   
                ], REST_Controller::HTTP_OK); 
            }else{
                $this->response([
                    'status' => False,
                    'Message' => 'Contacts not updated',
                    'code'    => REST_Controller::HTTP_INTERNAL_SERVER_ERROR
                ], REST_Controller::HTTP_OK); 
            }
        }
        
        function password_change_post(){
            $key_empty = '';
            if(empty($this->post('userid')))
            {
                $key_empty = 'userid';
            }
            if(empty($this->post('access_token')))
            {
                $key_empty = 'access_token';
            }
            if(empty($this->post('password_current')))
            {
                $key_empty = 'password_current';
            }
            if(empty($this->post('password_new')))
            {
                $key_empty = 'password_new';
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
            
            $password_current = encrypt_text($this->post('password_current'));
            if(empty($this->user->password_current_match($this->post('userid'), $password_current))){
                $this->response([
                    'status' => FALSE,
                    'message' => 'Current password not correct.'
                    ], REST_Controller::HTTP_BAD_REQUEST); // NOT_FOUND (404) being the HTTP response code
            }else{
                $password_new = encrypt_text($this->post('password_new'));
                if($this->user->password_change($this->post('userid'), $password_new)){
                   $this->response([
                    'status' => TRUE
                    ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code 
                }else{
                    $this->response([
                    'status' => FALSE,
                    'message' => 'Password not change.'
                    ], REST_Controller::HTTP_BAD_REQUEST); // NOT_FOUND (404) being the HTTP response code 
                }
            }
        }
        
        
}

/* End of file Users_api.php */
/* Location: ./application/controllers/welcome.php */