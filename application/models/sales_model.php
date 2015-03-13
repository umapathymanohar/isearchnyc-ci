<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Sales_model extends CI_Model {

	public function getSales(){


		$this->db->where('list_type', 'SALES');
		$query = $this->db->get('property');
		$total_rows = $query->num_rows();
		if($query->num_rows() > 0){
			return $res = array('result' => $query->result_array(), 'response' => 200);
			exit();
		}
		else{
			return $res = array('result' => '', 'response' => 500);
			
		}
	}


	public function saveFavorite(){

		if(!$_POST['userid'] || !$_POST['id'] || !$_POST['item']   ) {
			$obj=array(
				'response'=>400,
				); 
			return $obj;
			
		}

			$data = array(
				'userid' => $this->input->post('userid'),
				'related_id' => $this->input->post('id'),
				'related_item' => $this->input->post('item'),
				);
			if($this->db->insert('favorites', $data)) {

		$obj=array(
				'status'=>'success',
				'response'=>200,
				);
			return $obj;
 			

		}
		else {
			$obj=array(
				'response'=>500,
				); 
			return $obj;
			
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

	public function send_email($email_id, $subject, $data, $from = array('no-reply@thunderbirds.com' =>'THUNDERBIRDS')){
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
