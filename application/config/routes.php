<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'welcome';
//login registration api's
$route['api_server/sign_in'] = "api/users_api/sign_in";
$route['api_server/sign_up'] = "api/users_api/sign_up";
$route['api_server/verify_number'] = "api/users_api/otp_verify";
$route['api_server/update_number_and_resend_otp'] = "api/users_api/edit_number";
$route['api_server/forgot_password'] = "api/users_api/forgot_password";
$route['api_server/sync_contact'] = "api/users_api/sync_user_contact";
$route['api_server/group_create'] = "api/group_api/group_create";
$route['api_server/group_delete'] = "api/group_api/group_delete";
$route['api_server/relation_create'] = "api/group_api/relation_create";
$route['api_server/brand_create'] = "api/brand_api/brand_create";
$route['api_server/my_brand_list'] = "api/brand_api/brand_list_user";
$route['api_server/brand_delete'] = "api/brand_api/brand_delete";
$route['api_server/password_change'] = "api/users_api/password_change";

$route['404_override'] = 'errors/page_missing';;
$route['translate_uri_dashes'] = FALSE;
