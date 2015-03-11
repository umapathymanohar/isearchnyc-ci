<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Users_model extends CI_Model {


	public function createUser(){

		
		$query = $this->db->get_where('users', array('username' => $this->input->post('username')));
		if($query->num_rows() > 0){
			$obj=array(
				'response'=>404,
				); 
			return $obj;
			exit();
			
		}
 
		
		
	}


 



	public function login(){

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
		$query=$this->db->from('users');			
		$query=$this->db->get();
		if ($query->num_rows()===1) {				
			$row=array(
				'id'=>$query->row()->id,
				'username'=>$query->row()->username,				
				'first_name'=>$query->row()->first_name,				
				'last_name'=>$query->row()->last_name,				
				'email'=>$query->row()->email,
				'gender'=>$query->row()->gender,
				'region'=>$query->row()->region,
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


	public function forgot_password(){

		if(!$_POST['username'] ) {
			$obj=array(
				'response'=>400,
				); 
			return $obj;
			
		}

		$username = $this->input->post('username');
		$this->db->where('username', $username);
		$query=$this->db->from('users');			
		$query=$this->db->get();
		if ($query->num_rows()===1) {


			$newpassword = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'), 0, 8);

			
			$data['password'] = sha1($newpassword);	
			

			$this->db->where('username', $username);
			$this->db->update('users', $data);



			$row=array(
				'username'=>$query->row()->username,				
				'password'=>$newpassword,				
				'email'=>$query->row()->email,
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
	}



	public function reset_password($code){

		if(!$_POST['username'] || !$_POST['password']) {
			$obj=array(
				'response'=>400,
				); 
			return $obj;
			
		}


		$username = $this->input->post('username');
		$this->db->where('username', $username);
		$this->db->where('password', sha1($code));
		$query=$this->db->from('users');			
		$query=$this->db->get();
		if ($query->num_rows()===1) {


			$newpassword = $_POST['password'];

			
			$data['password'] = sha1($newpassword);	
			

			$this->db->where('username', $username);
			$this->db->update('users', $data);



			$row=array(
				'username'=>$query->row()->username,				
				'email'=>$query->row()->email,
				'response'=>200,
				);

			
			return $row;
			exit();
		}else{
			$row=array(
				'status'=>'Invalid Authentication',
				'response'=>203,
				);
			return $row;
			exit();
		}
	}
	public function forgot_username(){

		if(!$_POST['email'] ) {
			$obj=array(
				'response'=>400,
				); 
			return $obj;
			
		}

		$this->db->select('u.id, u.username, u.user_type, u.first_name, u.last_name' );	
		$this->db->from('users as u');
		$this->db->where('email', $_POST['email']);
		$query = $this->db->get();
		
		if ($query->num_rows()>0) {				
			
			return $res = array('result' => $query->result_array(), 'response' => 200);
			exit();

		// $row=array(
		// 	'username'=>$query->row()->username,				
		// 	'email'=>$query->row()->email,
		// 	'response'=>200,
		// 	);


			
		// 	return $row;
		// 	exit();
		}else{
			$row=array(
				'response'=>202,
				);
			return $row;
			exit();
		}
	}





	public function delete_user (){

		if(!$_POST['user_id'] ) {
			$obj=array(
				'response'=>400,
				); 
			return $obj;
			
		}

		$user_id = $this->input->post('user_id');
		$this->db->where('user_id', $user_id);
		if ($this->db->delete('users')) {				
			$row=array(
				'status'=>'success',
				'response'=>200,
				);

			return $row;
			exit();
		}else{
			$row=array(
				'response'=>202,
				);
			return $row;
			exit();
		}
	}


	public function cancelAccount(){

		if(!$_POST['auth_token'] || !$_POST['username']  || !$_POST['password']  ) {
			$obj=array(
				'response'=>400,
				); 
			return $obj;
			
		}

		$auth_token = $this->input->post('auth_token');
		$username = $this->input->post('username');
		$password = $this->input->post('password');
		$data['user_status'] = 'Inactive';
		$this->db->where('auth_token', $auth_token);
		$this->db->where('username', $username);
		$this->db->where('password', $password);
		if($this->db->update('users', $data)){
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


	public function authorizeChild(){

		if(!$_POST['child_id']  ) {
			$obj=array(
				'response'=>400,
				); 
			return $obj;
			
		}

		
		$child_id = $this->input->post('child_id');
		$data['user_status'] = 'active';
		$this->db->where('id', $child_id);
		if($this->db->update('users', $data)){


			$data = array(
				'child_id'    =>  $this->input->post('child_id'),
				'parent_id'    		=>  $this->input->post('parent_id'),
				);
			
			$this->db->insert('user_relationship', $data);





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

	public function deauthorizeChild(){

		if(!$_POST['parent_id']  || !$_POST['child_id'] ) {
			$obj=array(
				'response'=>400,
				); 
			return $obj;
			
		}

		$parent_id = $this->input->post('parent_id');
		$child_id = $this->input->post('child_id');
		
		$this->db->where('parent_id', $parent_id);
		$this->db->where('child_id', $child_id);

		$query=$this->db->from('user_relationship');			
		$query=$this->db->get();
		if ($query->num_rows()===1) {				
			
			$parent_id = $this->input->post('parent_id');
			$child_id = $this->input->post('child_id');
			$data['user_status'] = 'inactive';
			$this->db->where('id', $child_id);
			if($this->db->update('users', $data)){
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
		}else{
			$row=array(
				'status'=>'Invalid parameters',
				'response'=>203,
				);
			return $row;
			exit();
		}
	}

	public function getChildren(){

		if(!$_POST['auth_token'] || !$_POST['parent_id']  || !$_POST['access_type']  ) {
			$obj=array(
				'response'=>400,
				); 
			return $obj;
			
		}

		
		$auth_token = $this->input->post('auth_token');
		$parent_id = $this->input->post('parent_id');	
		$this->db->select('u.id, u.email, u.user_type, u.user_status');	
		$this->db->from('users as u');
		$this->db->join('user_relationship as ur', 'u.id = ur.child_id');
		$this->db->where('ur.parent_id', $parent_id);
		$query = $this->db->get();
		$total_rows = $query->num_rows();
		if($query->num_rows() > 0){
			return $res = array('children' => $query->result_array(), 'response' => 200);
			exit();
		}
		else{
			return $res = array('children' => '', 'response' => 500);
			
		}
	}


	public function searchusers(){

		$region = $this->input->post('region');
		$user_status = $this->input->post('user_status');	
		$gender = $this->input->post('gender');	
		$newsletter = $this->input->post('newsletter');	
		if($region != "") {
			$this->db->where('region', $region);
		}

		if($gender != "") {
			$this->db->where('gender', $gender);
		}
		if($user_status != "") {
			$this->db->where('user_status', $user_status);
		}
		if($user_status != "") {
			$this->db->where('user_status', $user_status);
		}
		if($user_status != "") {
			$this->db->where('user_status', $user_status);
		}

		$this->db->from('users');
		$query = $this->db->get();
		$total_rows = $query->num_rows();
		if($query->num_rows() > 0){
			return $res = array('users' => $query->result_array(), 'response' => 200);
			exit();
		}
		else{
			return $res = array('users' => '', 'response' => 204);
			
		}
	}

	public function updateProfile(){

		if(!$_POST['auth_token'] || !$_POST['user_id'] ) {
			$obj=array(
				'response'=>400,
				); 
			return $obj;
			
		}

		$auth_token = $this->input->post('auth_token');
		$user_id = $this->input->post('user_id');
		$first_name = $this->input->post('first_name');
		$last_name = $this->input->post('last_name');
		$newsletter = $this->input->post('newsletter');
		$region = $this->input->post('region');
		$password = sha1($this->input->post('password'));
		
		if($first_name) {
			$data['first_name'] = $first_name;	
		}

		if($last_name) {
			$data['last_name'] = $last_name;	
		}

		if($newsletter) {
			$data['newsletter'] = $newsletter;	
		}

		if($region) {
			$data['region'] = $region;	
		}

		if($this->input->post('password')) {
			$data['password'] = $password;	
		}

		$this->db->where('id', $user_id);
		if($this->db->update('users', $data)){
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


	public function getProfile(){

		if( !$_POST['user_id']  || !$_POST['access_type']  ) {
			$obj=array(
				'response'=>400,
				); 
			return $obj;
			
		}

		
		// $auth_token = $this->input->post('auth_token');
		$user_id = $this->input->post('user_id');	
		$this->db->select('u.id, u.username, u.email, u.newsletter, u.gender, u.first_name, u.user_type, u.user_status, u.newsletter, u.last_name, u.region, u.created_dt');	
		$this->db->from('users as u');
		$this->db->limit('1');
		$this->db->where('u.id', $user_id);
		$query = $this->db->get();
		$total_rows = $query->num_rows();
		if ($query->num_rows()===1) {				
			$row=array(
				'id'=>$query->row()->id,
				'username'=>$query->row()->username,				
				'first_name'=>$query->row()->first_name,				
				'last_name'=>$query->row()->last_name,				
				'email'=>$query->row()->email,
				'gender'=>$query->row()->gender,
				'newsletter'=>$query->row()->newsletter,
				'region'=>$query->row()->region,
				'user_type'=> $query->row()->user_type,
				'user_status'=> $query->row()->user_status,
				'created_dt'=> $query->row()->created_dt,
				'response'=>200,
				);						
			return $row;
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
		if($this->db->update('users', $data)){
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

	public function reset_user_relationship(){



		$this->db->where('user_type', 'minor');
		$this->db->from('users');
		$query = $this->db->get();
		$total_rows = $query->num_rows();

		foreach ($query->result_array() as $user) {
			$parent_email = $user['email'];
			$child_id = $user['id'];

			$this->db->from('users');
			$this->db->where('user_type !=', 'minor');
			$this->db->where('email', $parent_email);
			$parent_query = $this->db->get();
			
			if ($parent_query->num_rows()>0) {		
				

				$this->db->from('user_relationship');
				$this->db->where('child_id', $child_id);
				$child_query = $this->db->get();
				
				if ($child_query->num_rows()>0) {	

				}
				else {

					foreach ($parent_query->result_array() as $parent_user) {
						$parent_id = $parent_user['id']; 

						$parent_data = array(
							'parent_id'    		=>  $parent_id,
							'child_id'  		=>  $child_id,

							);
						
						$this->db->insert('user_relationship', $parent_data);
						
					}
				}
				
			}else{


				$data = array(
					'email'    		=>  $parent_email,
					'user_status'  		=>  $this->input->post('user_status'),
					'user_type'		    =>  'parent',
					'region'    =>  'GB',
					);
				
				$this->db->insert('users', $data);
				$parent_id =$this->db->insert_id();



				$data = array(
					'parent_id'    		=>  $parent_id,
					'child_id'  		=>  $child_id,

					);
				
				$this->db->insert('user_relationship', $data);
				

			}

		}

		$row=array(
			'status'=>'success',
			'response'=>200,
			);
		return $row;
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
		$query=$this->db->from('users');			
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

	
	public function sugarcrm($username = "", $firstname = "", $lastname = "", $gender = "", $dob = "", $password = "", $type = "", $status = "", $newsletter = "", $email = "", $parent_username = "", $region ="", $user_id="") {
    // $url = "http://10.0.100.30/service/v4_1/rest.php";
		$url = CRM_URL;
		$username = CRM_USERNAME;
		$crm_password = CRM_PASSWORD;




    //login --------------------------------------------     
		$login_parameters = array(
			"user_auth" => array(
				"user_name" => $username,
				"password" => md5($crm_password),
				"version" => "1"
				),
			"application_name" => "RestTest",
			"name_value_list" => array(),
			);

		$login_result = $this->call("login", $login_parameters, $url);

    /*
    echo "<pre>";
    print_r($login_result);
    echo "</pre>";
    */

    //get session id
    $session_id = $login_result->id;

    //create contacts ------------------------------------     
    $set_entries_parameters = array(
         //session id
    	"session" => $session_id,

         //The name of the module from which to retrieve records.
    	"module_name" => "Accounts",

         //Record attributes
    	"name_value_list" => array(
    		array(
                //to update a record, you will nee to pass in a record id as commented below
                //array("name" => "id", "value" => "912e58c0-73e9-9cb6-c84e-4ff34d62620e"),
    			array("name" => "name", "value" => $username),
    			array("name" => "first_name_c", "value" => $firstname),
    			array("name" => "last_name_c", "value" => $lastname),
    			array("name" => "gender_c", "value" => $gender),
    			array("name" => "dob_c", "value" => $dob),
    			array("name" => "password_c", "value" => $password),
    			array("name" => "type_c", "value" => $type),
    			array("name" => "status_c", "value" => $status),
    			array("name" => "newsletter_c", "value" => $newsletter),
    			array("name" => "email1", "value" => $email),
    			array("name" => "country_code_c", "value" => $region),
    			array("name" => "isearchnyc_user_id_c", "value" => $user_id),
    			array("name" => "parent_user_id_c", "value" => $parent_username)

    			)
             /*,
             array(
                //to update a record, you will nee to pass in a record id as commented below
                //array("name" => "id", "value" => "99d6ddfd-7d52-d45b-eba8-4ff34d684964"),
                array("name" => "first_name", "value" => "Jane"),
                array("name" => "last_name", "value" => "Doe"),
                ),*/
)
);

$set_entries_result = $this->call("set_entries", $set_entries_parameters, $url);

return $set_entries_result->ids[0];





}

 //function to make cURL request
public function call($method, $parameters, $url)
{
	ob_start();
	$curl_request = curl_init();

	curl_setopt($curl_request, CURLOPT_URL, $url);
	curl_setopt($curl_request, CURLOPT_POST, 1);
	curl_setopt($curl_request, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
	curl_setopt($curl_request, CURLOPT_HEADER, 1);
	curl_setopt($curl_request, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($curl_request, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl_request, CURLOPT_FOLLOWLOCATION, 0);

	$jsonEncodedData = json_encode($parameters);

	$post = array(
		"method" => $method,
		"input_type" => "JSON",
		"response_type" => "JSON",
		"rest_data" => $jsonEncodedData
		);

	curl_setopt($curl_request, CURLOPT_POSTFIELDS, $post);
	$result = curl_exec($curl_request);
	curl_close($curl_request);

	$result = explode("\r\n\r\n", $result, 2);
	$response = json_decode($result[1]);
	ob_end_flush();

	return $response;
}
}/* End of file home_model.php */
