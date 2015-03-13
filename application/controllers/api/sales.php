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

class Sales extends REST_Controller
{
    function __construct()
    {
        // Construct our parent class
        parent::__construct();
        
        $this->load->model('sales_model');
        $this->methods['user_get']['limit']    = 500; //500 requests per hour per user/key
        $this->methods['user_post']['limit']   = 100; //100 requests per hour per user/key
        $this->methods['user_delete']['limit'] = 50; //50 requests per hour per user/key
    }
    
    function get_sales_post()
    {
        $required_fields = [];
        
        if ($this->check_validation($required_fields)) {
            
            $obj = $this->sales_model->getSales();
            
            $this->createResponse($obj);
        }
    }
    

    function get_pulltorefreshsales_post()
    {
        $required_fields = [];
        
        if ($this->check_validation($required_fields)) {
            
            $obj = $this->sales_model->getSales();
            
            $this->createResponse($obj);
        }
    }
    

    function save_favorite_post()
    {
        $required_fields = ['userid', 'id', 'item'];
        
        if ($this->check_validation($required_fields)) {
            
            $obj = $this->sales_model->saveFavorite();
            
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