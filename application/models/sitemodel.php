<?php
class Sitemodel extends CI_Model{

	/************** Check Login checks rows returned from db matched against login information from landing **************/
	public function checklogin($email,$password)
	{
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
	
	
	public function register($data)
	{
		
		$sql = $this->db->insert_string('users', $data);
		$this->db->query($sql);
		$data['id'] = $this->db->insert_id();
		$this->session->set_userdata($data);

		return isset($data['id']);
	}
	
	
	
	public function idtousername($id = null)
	{
		$query = $this->db->query("SELECT username from users WHERE id  = '$id'");
		if($query->num_rows()){
			$result = $query->result();
			$result = $result[0];
			$username = $result->username;
			return $username;
		}else{
			return false;
		}
	}
	

	public function createevent($data){
		$sql = $this->db->insert_string('events', $data);
		$this->db->query($sql);
		$data['id'] = $this->db->insert_id();
		$this->session->set_userdata($data);
		if($data['id']) {
			return true;
		} else {
			return false;
		}
	}
	
	public function checkevents($where=array()){
		$where["date"] = ">=NOW()";
		$wherestr = " WHERE ";
		$i=0;
		foreach($where as $k=>$v){
			$wherestr.="$k $v";
			$i++;
			if($i<sizeof($where)){
				$wherestr.= " and ";
			}
		}
		$query = $this->db->query("SELECT * from events".($where? $wherestr:"")." ORDER BY date DESC");
		if($query->num_rows()){
			$result = $query->result();
			$result = $result;
			foreach($result as &$event){
				$event->creator = $this->idtousername($event->userid);
			}
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

	/************** Pulls back the userinfo associated with the user **************/
	public function userinfo($username) {
		$this->load->library('session');
		$this->load->database();
		$query = $this->db->query("SELECT id,email,biography FROM users WHERE username = '" . $username . "'");
		$result = $query->result();
		return $result;
	}
	
}
?>