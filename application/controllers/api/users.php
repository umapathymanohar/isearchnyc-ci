<?php
header('Access-Control-Allow-Origin:*');
defined('BASEPATH') OR exit('No direct script access allowed');
// error_reporting(0);
/**
* Example
*
* This is an example of a few basic user interaction methods you could use
* all done with a hardcoded array.
*
* @package   CodeIgniter
* @subpackage  Rest Server
* @category  Controller
* @author    Phil Sturgeon
* @link    http://philsturgeon.co.uk/code/
*/

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require APPPATH . '/libraries/REST_Controller.php';

class Users extends REST_Controller
{
    function __construct()
    {
    // Construct our parent class
        parent::__construct();
        
    // Configure limits on our controller methods. Ensure
    // you have created the 'limits' table and enabled 'limits'
    // within application/config/rest.php
        
        
        
    // Sugar_REST($rest_url=null,$username=null,$password=null,$md5_password=true)
        
        
        $this->load->model('users_model');
    $this->methods['user_get']['limit']    = 500; //500 requests per hour per user/key
    $this->methods['user_post']['limit']   = 100; //100 requests per hour per user/key
    $this->methods['user_delete']['limit'] = 50; //50 requests per hour per user/key
}


function register_post()
{
   $this->load->library('sugar_rest');
   $this->sugar_rest->Sugar_REST(CRM_URL, CRM_USERNAME, CRM_PASSWORD);
   $required_fields = ['username', 'email', 'password', 'first_name', 'last_name', 'dob', 'gender', 'status', 'type', 'newsletter'];
   if ($this->check_validation($required_fields)) {
       
       $obj =$this->users_model->createUser();
       
      if ($obj['response'] == 200) {
             
       if  (($_POST['user_type'] == 'adult') || ( $_POST['user_type'] == 'parent')) {

        $data = array(
         'first_name' => $_POST['first_name'],
         'last_name' => $_POST['last_name'],
         'username' => $_POST['username'],
         );

        $content = $this->load->view('email/over13_confirmation', $data, true);
        $this->send_email($_POST['email'], 'isearchnyc Are Go | International Rescue Training Programme', $content);
    }
    else {

        $data = array(
         'first_name' => $_POST['first_name'],
         'last_name' => $_POST['last_name'],
         'parent_email' => $_POST['email'],
         'username' => $_POST['username'],
         'password' => $_POST['password'],
         'created_dt' => $obj['created_dt'],
         
         'id' =>  $obj['id'],
         );


        //first mail
        $content = $this->load->view('email/under13_parent_confirmation', $data, true);
        $this->send_email($_POST['email'], 'isearchnyc Are Go | International Rescue Training Programme', $content);
        // Second Mail


        }
    }

    $result=$this->createResponse($obj);
    
    exit();

}

}


function create_username_post()
{
    
    $required_fields = ['color', 'animal'];
    if ($this->check_validation($required_fields)) {
        
        $obj = $this->users_model->createusername();
        
        $this->createResponse($obj);
    }
}

function search_post()
{
    
    
    $obj = $this->users_model->searchusers();
    $this->createResponse($obj);
    
}



function login_post()
{
    
    $required_fields = ['username', 'password'];
    if ($this->check_validation($required_fields)) {
        
        $obj = $this->users_model->login();
        $this->createResponse($obj);
        
    }
}


function forgot_password_post()
{
    $required_fields = ['username'];
    if ($this->check_validation($required_fields)) {
        
        
        $obj = $this->users_model->forgot_password();
        

        $data = array(
         'username' => $obj['username'],
         'password' => $this->encrypt_decrypt('encrypt', $obj['password']),
         'email' => $obj['email'],
         'username' => $obj['username'],
         
         );
        

        $content = $this->load->view('email/forgot_password', $data, true);
        $this->send_email($obj['email'], 'isearchnyc Are Go | International Rescue Training Programme', $content);

        
        $result=$this->createResponse($obj);
        
    }
    
}

function reset_password_post()
{
    $required_fields = ['username', 'code', 'password'];
    if ($this->check_validation($required_fields)) {
        
        $code =  $this->encrypt_decrypt('decrypt', $_POST['code']);
        $obj = $this->users_model->reset_password(trim($code));
        

        
        
        $result=$this->createResponse($obj);
        
    }
    
}


function forgot_username_post()
{
    
    $required_fields = ['email'];
    if ($this->check_validation($required_fields)) {
        
        $obj = $this->users_model->forgot_username();

        $content = $this->load->view('email/forgot_username', $obj , true);
        $this->send_email($_POST['email'], 'isearchnyc Are Go | International Rescue Training Programme', $content);


        
        $result=$this->createResponse($obj);
        
    }
    
}


function cancel_account_post()
{
    $required_fields = ['auth_token', 'username', 'password'];
    if ($this->check_validation($required_fields)) {
        
        $obj = $this->users_model->cancelAccount();
        
        $this->createResponse($obj);
    }
}
function authorize_child_post()
{
    
    $required_fields = ['child_id'];
    if ($this->check_validation($required_fields)) {
        
        $obj = $this->users_model->authorizeChild();
        
        $this->createResponse($obj);
    }
}
function deauthorize_child_post()
{
    
    $required_fields = ['auth_token', 'parent_id', 'child_id', 'access_type'];

    if ($this->check_validation($required_fields)) {
        
        $obj = $this->users_model->deauthorizeChild();
        
        $this->createResponse($obj);
    }
}

function get_children_post()
{
    $required_fields = ['auth_token', 'parent_id'];
    if ($this->check_validation($required_fields)) {
        
        $obj = $this->users_model->getChildren();
        
        $this->createResponse($obj);
    }
}



function update_profile_post()
{
    
    $required_fields = ['auth_token', 'user_id'];
    if ($this->check_validation($required_fields)) {
        
        $obj = $this->users_model->updateProfile();
        
        $this->createResponse($obj);
    }
}

function get_profile_post()
{
    $required_fields = ['auth_token', 'user_id'];
    
    if ($this->check_validation($required_fields)) {
        
        $obj = $this->users_model->getProfile();
        
        $this->createResponse($obj);
    }
}
function delete_user_post()
{
    $required_fields = ['auth_token', 'user_id'];
    
    if ($this->check_validation($required_fields)) {
        
        $obj = $this->users_model->delete_user();
        
        $this->createResponse($obj);
    }
}


function reset_user_relationship_post()
{
    
    $obj = $this->users_model->reset_user_relationship();
    
    $this->createResponse($obj);
}

function logout_post()
{
    
    $required_fields = ['auth_token', 'user_id'];
    if ($this->check_validation($required_fields)) {
        
        $obj = $this->users_model->logout();
        
        $this->createResponse($obj);
    }
}


function createResponse($obj)
{
    
    if ($obj) {
        
        if ($obj['response'] == 200) {
            
            $this->response($obj, 200);
        }
        if ($obj['response'] == 201) {
            $this->response(array(
                'error' => 'Incorrect Credentials',
                'response' => 400
                ), 400);
        }
        if ($obj['response'] == 202) {
            $this->response(array(
                'error' => 'Email not registered with us',
                'response' => 400
                ), 400);
        }
        if ($obj['response'] == 203) {
            $this->response(array(
                'error' => 'Authentication Failed',
                'response' => 400
                ), 400);
        }

        if ($obj['response'] == 204) {
            
            $this->response(array(
                'error' => 'No results found',
                'response' => 400
                ), 400);
            
        }        
        if ($obj['response'] == 404) {
            $this->response(array(
                'error' => 'Email Already Exists',
                'response' => 404
                ), 404);
        }
        
        if ($obj['response'] == 400) {
            $this->response(array(
                'error' => 'Please provide all required parameters',
                'missing' => $obj["missing"],
                'response' => 400
                ), 400);
        }
        if ($obj['response'] == 500) {
            
            $this->response(array(
                'error' => 'Something went wrong.  Please try again later',
                'response' => 500
                ), 500);
            
        }
        
        
    }
    
    
}



function check_validation($required_fields)
{
    $validation = true;
    $missing_fields = [];
    
    foreach ($_POST as $name => $value) {
        if (in_array($name, $required_fields)) {
            if ($_POST[$name] == "") {
                $validation == false;
                array_push($missing_fields, $name);
            }
        }
    }
    if (count($missing_fields) > 0) {
        $obj = array(
            'missing' => $missing_fields,
            'response' => 400
            );
        $this->createResponse($obj);
        return false;
    } else {
        return true;
    }
    
}


function send_email($mailto, $subject, $message){

    include( "awsemail.functions.php" );
    sendAWSEamil( $mailto, $subject, $message );


    
}

function encrypt_decrypt($action, $string) {
 $output = false;

 $key = 'My strong random secret key';

   // initialization vector 
 $iv = md5(md5($key));

 if( $action == 'encrypt' ) {
     $output = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), $string, MCRYPT_MODE_CBC, $iv);
     $output = base64_encode($output);
 }
 else if( $action == 'decrypt' ){
     $output = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($key), base64_decode($string), MCRYPT_MODE_CBC, $iv);
     $output = rtrim($output, "");
 }
 return $output;
}


}
