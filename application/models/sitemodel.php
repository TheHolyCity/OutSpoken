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
	
	public function topevents(){
		$events = $this->checkevents();
		$total_events = 4;
		$eventids = $return = $returnevents = array();
		foreach($events as $eventid){
			$eventids[] = $eventid->id;
			
		}
		$eventids = implode(',',$eventids);
		$sql = "SELECT COUNT(id) AS rsvp FROM event_users  WHERE eventid IN($eventids) GROUP BY eventid LIMIT $total_events";
		$query = $this->db->query($sql);
		
		$result = $query->result();
		foreach($result as $r){
			$return[] = $r->rsvp;
			 
		}
		if($return){
			foreach($return as $re){
				$returnevents[] = $this->checkevents("id=$re");
			}
		}else{
			$returnevents = $this->checkevents(null,$total_events);
		}
		return $returnevents;
		
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
		unset($data['default_times']);
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
	
	public function pad($str = null, $length = 2) {
		$size = strlen($str);
		
		$total = $length - $size;
		
		if($total > 0) {
			for($i = 0; $i < $total; $i++) {
				$str = '0' . $str;
			}
		}
		return $str;
	}
	
	public function times() {
		$hours = array(12,1,2,3,4,5,6,7,8,9,10,11);
		$master_list = $minutes = array();
		$interval = 15;
		$appends = array('PM','AM');
		
		for($i = 0; $i < 4; $i++) {
			$minutes[] = ($i * $interval);
		}
		
		foreach($appends as $append) {
			foreach($hours as $hour) {
				foreach($minutes as $minute) {
					$str = $this->pad($hour);
					$str .= ':' . $this->pad($minute);
					$str .= ' ' . $append;
					$master_list[] = $str;
					$str = '';
				}
			}
		}		
		return $master_list;
			
	}
	
	public function checkevents($where=array(), $limit = 6){
		$where["date"] = ">=NOW()";
		$wherestr = " WHERE ";
		if($limit){
			$limit = " LIMIT $limit";
		}
		$i=0;
		foreach($where as $k=>$v){
			$wherestr.="$k $v";
			$i++;
			if($i<sizeof($where)){
				$wherestr.= " and ";
			}
		}
		$query = $this->db->query("SELECT * from events".($where? $wherestr:"")." ORDER BY date DESC $limit");
		if($query->num_rows()){
			$result = $query->result();
			$result = $result;
			foreach($result as &$event){
				$event->creator = $this->idtousername($event->userid);
				$event->thumb = ($event->image ? $this->imgthumb($event->image) : '');
			}
			return $result;
		}else{
			return false;
		}
	}
	
	public function profilegallery($data)
	{
		$sql = $this->db->insert_string('gallery', $data);
		$this->db->query($sql);
		$data['id'] = $this->db->insert_id();
		$this->session->set_userdata($data);

		return isset($data['id']);
	}
	
	public function gallery($data){
		$query = $this->db->query("SELECT * from gallery WHERE userid = $data[userid] ORDER BY created DESC");
		$result = $query->result();
		foreach($result as &$r){
			$r->thumb=$this->imgthumb($r->name);
		}
		return $result;
			
	}
	
	public function imgthumb($name){

		$name = explode(".",$name);
		$name[(sizeof($name)-2)].="_thumb";
		$name = implode(".",$name);
		return $name;
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