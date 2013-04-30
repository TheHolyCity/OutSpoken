<?php
class Sitemodel extends CI_Model{
	/************** Check Login checks rows returned from db matched against login information from landing **************/
	public function checklogin($email,$password){
		$password = md5($password);
		$query = $this->db->query("SELECT * from users WHERE password = '$password' AND email = '$email'");
		$this->load->library('session');
		if($query->num_rows()){
			$result = $query->result();
			$result = $result[0];
			$this->session->set_userdata($result);
			return true;
		}else{
	
			return false;
		}
	}
	
	public function checkevents(){
		$query = $this->db->query("SELECT * from events");
		if($query->num_rows()){
			$result = $query->result();
			$result = $result;
			return $result;
		}else{
			return false;
		}
	}

	/************** CRUD from useredit functionality passed from edit user panel **************/
	public function updateuser($data){
		$this->load->database();
		$data['password'] = md5($data['password']);
		$query = $this->db->query("UPDATE users set  username = '$data[username]', password = '$data[password]', biography = '$data[biography]' where id = '$data[id]'");
	}

	/************** app_detail_submit takes information from the appdetail form and pushes to db **************/
	public function app_detail_submit($data){
		$this->load->database();
		if($data['rflag']){
			$query = $this->db->query("UPDATE trails set iterations = `iterations`+1 where id = '$data[trailid]'");
		}else{
			$query = $this->db->query("UPDATE trails set name = '$data[rname]', distance = '$data[rdist]',tags = '$data[rtag]', description = '$data[rdesc]', userid = ".$this->session->userdata('id')." where id = '$data[trailid]'");
		}
		
	}
	/************** Pulls back the userinfo associated with the user **************/
	public function userinfo($username) {
		$this->load->library('session');
		$this->load->database();
		$query = $this->db->query("SELECT id,email,biography FROM users WHERE username = '" . $username . "'");
		$result = $query->result();
		return $result;
	}
	/************** Takes Info from the route where **************/
	public function route_info($id){ 
		$ret = array();
		
		$sql = "SELECT * FROM trails WHERE id = $id LIMIT 1";
		$query = $this->db->query($sql);
		$result = $query->result();
		
		$result = $result[0];
		
		$ret['info'] = $result;
		
		$sql = "SELECT * FROM points WHERE trail_id = $id ORDER BY id ASC";
		$query = $this->db->query($sql);
		$results = $query->result();
		
		$ret['points'] = $results;
		
		return $ret;
	}
}
?>