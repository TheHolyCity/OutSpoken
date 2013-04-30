<?php
class Searchusers extends CI_Model{
	
	public function search_user($city) {
		$return = array();
		$query = $this->db->query('SELECT * FROM users WHERE city = "' . $city . '" ORDER BY username ASC');	
		$results = $query->result();
		
		foreach($results as $result) {
			// Find type
			$query = $this->db->query('SELECT name FROM types WHERE id = ' . $result->type);
			$row = $query->row();
			$result->type = $row->name;
			$return[] = $result;
		}
		
		return $return;	
	}
	
	public function grab_all() {
		$return = array();
		$query = $this->db->query('SELECT * FROM users ORDER BY city,username ASC');
		$results = $query->result();
		
		foreach($results as $result) {
			// Find type
			$query = $this->db->query('SELECT name FROM types WHERE id = ' . $result->type);
			$row = $query->row();
			$result->type = $row->name;
			$return[] = $result;
		}
		
		return $return;
	}
}

?>