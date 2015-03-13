<?php
header('Access-Control-Allow-Origin:*');
defined('BASEPATH') OR exit('No direct script access allowed');
error_reporting(0);
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
        
        // $this->load->library('sugar_rest');
        
        // Sugar_REST($rest_url=null,$username=null,$password=null,$md5_password=true)
        // $this->sugar_rest->Sugar_REST('10.0.100.30/service/v4_1/rest.php', 'web', 'Sud7utRu');
        
        $this->load->model('users_model');
        $this->methods['user_get']['limit']    = 500; //500 requests per hour per user/key
        $this->methods['user_post']['limit']   = 100; //100 requests per hour per user/key
        $this->methods['user_delete']['limit'] = 50; //50 requests per hour per user/key
    }
    
    
    function register_post()
    {
        
        $required_fields = ['username', 'email', 'password'];
        if ($this->check_validation($required_fields)) {
              
            // $sugarcrm_id = $this->users_model->createSugarCRMAccount();
             $obj =$this->users_model->createUser();
            // $this->users_model->updateSugarCRM($sugarcrm_id);
            
            $result=$this->createResponse($obj);
            
            exit();
 
        }
        
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
        $required_fields = ['email'];
        if ($this->check_validation($required_fields)) {
            
            
            $obj = $this->users_model->forgot_password();
             $content = '<table width="100%" cellpadding="10" cellspacing="0" bgcolor="#E5E5E5"><tbody><tr><td valign="top" align="center"><table width="550" cellpadding="20" cellspacing="0" bgcolor="#FFFFFF"><tbody><tr><td bgcolor="#FFFFFF" valign="top" style="font-size:12px;color:#000000;line-height:150%;font-family:arial"><p style="width:500px"><span style="padding-top:5px"><a><img src="http://irhq.com/images/IR_logo.png" border="0" title="Thunderbirds" alt="Thunderbirds" align="center" style="width:60px" class="CToWUd"></a><span style="font-size:15px;font-weight:bold;font-family:arial;line-height:0%;margin-left:20px">THUNDERBIRDS </span></span><br><br>Hello <strong style="word-break:break-all">'.$this->input->post('first_name').' '.$this->input->post('last_name').'</strong></p><br><p>Please click the following link for resetting your password <a href="http://thunderbirds.dev/user" target="_blank">Reset</a>.</p><br><p>Best Regards,</p><p>The Thunderbirds Team</p></td></tr></tbody></table></td></tr></tbody></table>';
            
            $result=$this->createResponse($obj);
            $this->send_email($_POST['email'], 'Reset Password', $content);
      
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
        $config['protocol'] = 'smtp';
        $config['smtp_host'] = 'ssl://smtp.googlemail.com';
        $config['smtp_port'] = '465';
        $config['smtp_user'] = 'indicustech@gmail.com';
        $config['smtp_pass'] = 'Thisdn890';
        $config['charset'] = 'utf-8';
        $config['mailtype'] = 'html';
        $config['newline'] = "\r\n";

        $this->load->library('email');
        $this->email->initialize($config);

        $this->email->from('indicustech@gmail.com', 'ISearchNYC');
        $this->email->to($mailto); 

        $this->email->subject($subject);
        $this->email->message($message); 

        $this->email->send();

         
    }
}