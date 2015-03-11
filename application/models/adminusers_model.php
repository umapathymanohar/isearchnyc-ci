<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Adminusers_model extends CI_Model {


	public function createAdminUser(){

	 
		$_POST['email'] = strtolower($_POST['email']);
		$query = $this->db->get_where('admin_users', array('email' => $this->input->post('email')));
		if($query->num_rows() > 0){
			$obj=array(
				'response'=>404,
				); 
			return $obj;
			
		}else{
			 $sugarcrm_id="";

		$data = array(
				'username'    =>  $this->input->post('username'),
				'email'    		=>  $this->input->post('email'),
				'password'      =>  sha1($this->input->post('password')),
				'first_name'    =>  $this->input->post('first_name'),
				'last_name'     =>  $this->input->post('last_name'),
				'user_status'  		=>  $this->input->post('user_status'),
				'user_type'		    =>  $this->input->post('user_type'),
				'auth_token'    =>  substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'), 0, 16),
				'auth_token_ttl'    =>  substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'), 0, 16),
				);
				
				;



 	if ($this->db->insert('admin_users', $data)) {				
		$row=array(
			'status'=>'success',
			'response'=>200,
			);

 
			return $row;
			exit();
		}else{
			$row=array(
				'status'=>'Email not registered with us',
				'response'=>202,
				);
		}

			// 	$row=array(
			// 		'id'=>$this->db->insert_id(),
			// 		'email'=>$this->input->post('email'),                
			// 		'first_name'=>$this->input->post('first_name'),              
			// 		'last_name'=>$this->input->post('last_name'),               
			// 		'response'=>200,               
			// 		);  
 
			// return $row;
 
		}
	}
  

public function adminlogin(){

	if(!$_POST['username'] || !$_POST['password'] ) {
		$obj=array(
			'response'=>400,
			); 
		return $obj;
		
	}

	$username = $this->input->post('username');
	$password = $this->input->post('password');
	$this->db->where('username', $username);
	$this->db->where('password', sha1($password));
	$query=$this->db->from('admin_users');			
	$query=$this->db->get();
	if ($query->num_rows()===1) {				
		$row=array(
			'id'=>$query->row()->user_id,
			'username'=>$query->row()->username,				
			'first_name'=>$query->row()->first_name,				
			'last_name'=>$query->row()->last_name,				
			'email'=>$query->row()->email,
			'user_type'=> $query->row()->user_type,
			'user_status'=> $query->row()->user_status,
			'auth_token'=> $query->row()->auth_token,
			'response'=>200,
			);						
		return $row;
		exit();
	}else{
		$obj=array(
			'response'=>201,
			); 
		return $obj;
		exit();
	}
}


public function admin_forgot_password(){

	if(!$_POST['email'] ) {
		$obj=array(
			'response'=>400,
			); 
		return $obj;
		
	}

	$email = $this->input->post('email');
	$this->db->where('email', $email);
	$query=$this->db->from('admin_users');			
	$query=$this->db->get();
	if ($query->num_rows()===1) {				
		$row=array(
			'status'=>'success',
			'response'=>200,
			);

 
			return $row;
			exit();
		}else{
			$row=array(
				'status'=>'Email not registered with us',
				'response'=>202,
				);
			return $row;
			exit();
		}
	}


 

	public function adminupdateProfile(){
		$date = new DateTime();
		

		if(!$_POST['auth_token'] || !$_POST['user_id'] || !$_POST['access_type']  ) {
			$obj=array(
				'response'=>400,
				); 
			return $obj;
			
		}

		$auth_token = $this->input->post('auth_token');
		$user_id = $this->input->post('user_id');
		$first_name = $this->input->post('first_name');
		$last_name = $this->input->post('last_name');
		$password = sha1($this->input->post('password'));
		
		if($first_name) {
		$data['first_name'] = $first_name;	
		}

		if($last_name) {
		$data['last_name'] = $last_name;	
		}


		if($this->input->post('password')) {
		$data['password'] = $password;	
		}

		$data['modified_dt'] = $date->getTimestamp();	

		$this->db->where('id', $user_id);
		if($this->db->update('admin_users', $data)){
			$row=array(
				'status'=>'success',
				'response'=>200,
				);
			return $row;
		}	
		else
		{
			$row=array(
				'response'=>500,
				);
			return $row;
			exit();
		}		
	}


		public function admingetProfile(){

		if(!$_POST['auth_token'] || !$_POST['user_id']  || !$_POST['access_type']  ) {
			$obj=array(
				'response'=>400,
				); 
			return $obj;
			
		}

	
		$auth_token = $this->input->post('auth_token');
		$user_id = $this->input->post('user_id');	
		$this->db->select('u.id, u.username, u.email,  u.first_name, u.last_name');	
		$this->db->from('admin_users as u');
		$this->db->where('u.id', $user_id);
		$query = $this->db->get();
		$total_rows = $query->num_rows();
		if($query->num_rows() > 0){
			return $res = array('profile' => $query->result_array(), 'response' => 200);
			exit();
		}
		else{
			return $res = array('profile' => '', 'response' => 500);
			
		}
	}

	public function logout(){

		if(!$_POST['auth_token'] || !$_POST['user_id']  || !$_POST['access_type']  ) {
			$obj=array(
				'response'=>400,
				); 
			return $obj;
			
		}

		$auth_token = $this->input->post('auth_token');
		$user_id = $this->input->post('user_id');
		$new_auth_token = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'), 0, 16);
		
		
		 
		$data['auth_token'] = $new_auth_token;	
		 
		$this->db->where('id', $user_id);
		if($this->db->update('admin_users', $data)){
			$row=array(
				'status'=>'success',
				'response'=>200,
				);
			return $row;
		}	
		else
		{
			$row=array(
				'response'=>500,
				);
			return $row;
			exit();
		}		
	}

	public function validate_token($token, $user_id){

	if(!$token|| !$user_id) {
		$obj=array(
			'response'=>400,
			); 
		return $obj;
		
	}

 
	$this->db->where('auth_token', $token);
	$this->db->where('id', $user_id);
	$query=$this->db->from('admin_users');			
	$query=$this->db->get();
	if ($query->num_rows()===1) {				
		 return TRUE;
	}else{
	 return FALSE;
	}

	}

	public function send_email($email_id, $subject, $data, $from = array('no-reply@isearchnyc.com' =>'isearchnyc')){
		$this->load->library('smtp_lib/smtp_email');
		$to = array(
			$email_id	
			);

		$is_fail = $this->smtp_email->sendEmail($from, $to, $subject, $data);
		if($is_fail){
			return FALSE;
			// echo "ERROR :";
			// print_r($is_fail);
		}
		else{
			return TRUE;
		}
	}

  
}/* End of file home_model.php */
