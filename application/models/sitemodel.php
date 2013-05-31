<?php
class Sitemodel extends CI_Model{

	/************** Check Login checks rows returned from db matched against login information from landing **************/
	public function checklogin($email,$password) {
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
	
	public function register($data) {
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
		foreach($events as $eventid) {
			$eventids[] = $eventid->id;
		}
		/*
$eventids = implode(',',$eventids);
		$sql = "SELECT COUNT(id) AS rsvp FROM event_users WHERE eventid IN($eventids) GROUP BY eventid LIMIT $total_events";
		$query = $this->db->query($sql);
		
		$result = $query->result();
		foreach($result as $r){
			$rsvpd = $r;
		}
*/
		
		$returnevents = $this->checkevents(array(),$total_events);
		
		return $returnevents;
	}
	
	public function idtousername($id = null) {
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
		
		if(!$data['image']) { unset($data['image']);}
		
		if($data['eid']) {
			$id = $data['eid'];
			$this->db->where('id',$id);
			unset($data['eid']);
			$this->db->update('events',$data);
			$data['id'] = $id;
		} else {
			$sql = $this->db->insert_string('events', $data);
			$this->db->query($sql);
			$data['id'] = $this->db->insert_id();
		}
		//$this->session->set_userdata($data);
		if($data['id']) {
			return true;
		} else {
			return false;
		}
	}
	
	public function get_event($id = null) {
		$query = $this->db->query('SELECT * FROM events WHERE id = ' . $id);
		if($query->num_rows()) {
			$result = $query->result();
			$result = $result[0];
			$result = (array)$result;
			
			$date = explode(' ',$result['date']);
			
			$result['etime'] = $date[1];
			$result['edate'] = $date[0];
			
		} else {
			$result = 'This event doesn\'t exist.';
		}
		return $result;
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
		/*
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
*/
		
		$master_list = array();
		
		$hours 	 = array(12,1,2,3,4,5,6,7,8,9,10,11);
		$minutes = array('00','15','30','45');
		$ampm	 = array('AM','PM');
		
		$master_list = array(
			'hours'   => $hours,
			'minutes' => $minutes,
			'ampm'	  => $ampm
		);
		
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
				$wherestr.= " AND ";
			}
		}
	
		$query = $this->db->query("SELECT * from events".($wherestr ? $wherestr:"")." ORDER BY date DESC $limit");
		if($query->num_rows()){
			$result = $query->result();
			$result = $result;
			foreach($result as &$event){
				$event->editable = $this->is_users_event($event->id);
				$event->creator = $this->idtousername($event->userid);
				$event->thumb = ($event->image ? $this->imgthumb($event->image) : '');
				$event->med = ($event->image ? $this->imgthumb($event->image,'_med') : '');
				
				$query2 = $this->db->query("SELECT * FROM event_users WHERE eventid = '" . $event->id . "'");
				$event->rsvpd = $query2->num_rows();
			}
			
			return $result;
		}else{
			return false;
		}
	}
	
	public function profilegallery($data) {
		$sql = $this->db->insert_string('gallery', $data);
		$this->db->query($sql);
		$data['event']['id'] = $this->db->insert_id();
		$this->session->set_userdata($data);
		return isset($data['event']['id']);
	}
	
	public function gallery($data){
		$query = $this->db->query("SELECT * from gallery WHERE userid = $data[userid] ORDER BY created DESC");
		$result = $query->result();
		foreach($result as &$r){
			$r->thumb=$this->imgthumb($r->name);
		}
		return $result;
	}
	
	public function imgthumb($name = null, $append = '_thumb'){
		if(!$name) {return;}
		$name = explode(".",$name);
		$name[(sizeof($name)-2)].= $append;
		$name = implode(".",$name);
		return $name;
	}

	public function userupdate($data){
		//$query = $this->db->query("UPDATE users SET password,city,about, WHERE userid = $data[userid]")
	}
	
	public function rsvp($id = null, $add = true) {
		
	}
	
	public function is_users_event($id = null) {
		if(!$id || !$this->session->userdata('id')) { return false;}
		
		$this->load->library('session');
		
		$user_id = $this->session->userdata('id');
		
		$query = $this->db->query('SELECT * FROM events WHERE userid = ' . $user_id . ' AND id = ' . $id);
		
		if($query->num_rows()){
			return true;
		} else {
			return false;
		}
	}
}
?>