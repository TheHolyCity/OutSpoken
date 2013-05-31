<?php  if( ! defined('BASEPATH')) exit('No direct script access allowed');
	class Site extends CI_Controller{
		public function index(){
			$this->home();
		}
		
		/************** Function added to all pages that needed to be protected from external penetration without login **************/
		public function protect(){
			$this->load->library('session');
			if(!$this->session->userdata('username')){
				redirect(base_url('index.php/site/register'));
			}
		}
		
		public function home(){
			$this->load->model('sitemodel');
			$topevents = $this->sitemodel->topevents();
			$data = array("events" => $topevents);
			$this->load->library('form_validation');
			$this->header();
			$this->load->view('site/content_landing', $data);
			$this->load->view('site/footer');
		}
		
		public function header(){
			$this->load->library('session');
			$loggedin = false;
			/********* Checks to see if user is logged in *********/
			if($this->session->userdata("username")){ $loggedin = true;}
			$data = array(
				"loggedin" => $loggedin, 
				'username' => $this->session->userdata("username")
			);
			$this->load->view('site/header',$data);
		}
		public function disqus(){
			return '<div id="disqus_thread"></div>
					    <script type="text/javascript">
					        /* * * CONFIGURATION VARIABLES: EDIT BEFORE PASTING INTO YOUR WEBPAGE * * */
					        var disqus_shortname = \'outspokenbikes\'; // required: replace example with your forum shortname
					
					        /* * * DON\'T EDIT BELOW THIS LINE * * */
					        (function() {
					            var dsq = document.createElement(\'script\'); dsq.type = \'text/javascript\'; dsq.async = true;
					            dsq.src = \'//\' + disqus_shortname + \'.disqus.com/embed.js\';
					            (document.getElementsByTagName(\'head\')[0] || document.getElementsByTagName(\'body\')[0]).appendChild(dsq);
					        })();
					    </script>
					    <noscript>Please enable JavaScript to view the <a href="http://disqus.com/?ref_noscript">comments powered by Disqus.</a></noscript>
					    <a href="http://disqus.com" class="dsq-brlink">comments powered by <span class="logo-disqus">Disqus</span></a>';
		}
		public function signup($data = null){
			$this->load->model('sitemodel');
			$this->load->library('form_validation');
			$this->header();
			$this->load->view('site/content_signin',$data);
			$this->load->view('site/footer');
		}
		
		private function append_file_name($str = null, $append = null) {
			$str = explode('.',$str);
			$str[(sizeof($str) - 2)] .= $append;
			$str = implode('.',$str);
			return $str;
		}
		
		/************** Registeration Form + Validation | $data: Passed to userview and sent to DB. **************/
		public function register(){
			$this->load->library('form_validation');
			$this->load->model('sitemodel');
			
			$this->form_validation->set_rules("reguser", "Username", 'required');
			$this->form_validation->set_rules("regemail", "Email", 'required');
			$this->form_validation->set_rules("regpass", "Password", 'required|matches[regrepass]');
			$this->form_validation->set_rules("regrepass", "Retype Password", 'required');
			$this->form_validation->set_rules('regbio','');
			$this->form_validation->set_rules('reglocat','');
			
			if(! $this->form_validation->run()) {
				
				$this->session->set_flashdata('register',validation_errors());
				
				$data = array(
					'username'=>set_value('reguser'),
					'userimg'=>set_value('regimg'),
					'password'=> md5(set_value('regpass')),
					'email'=>set_value('regemail'),
					'aboutme'=>set_value('regbio'),
					'location'=>set_value('reglocat')
				);
				
				$this->signup($data);
			} else {
				$this->load->library('session');
				$data = array(
					'username' => set_value('reguser'),
					'password' => md5(set_value('regpass')),
					'email'    => set_value('regemail'),
					'aboutme'  => set_value('regbio'),
					'location' => set_value('reglocat')
				);

				$query = $this->db->query("SELECT username,email from users WHERE username ='". set_value("reguser") ."' OR email = '". set_value("regemail")."'");
				if($query->num_rows()){
					$error = 'This Email or Username is already active.';
					$this->form_validation->set_message('email_username', $error);
					$this->session->set_flashdata("register", $error);
					$this->signup($data);
					return;
				}
				
				// img upload
				$config = array(
					'allowed_types' => 'jpg|jpeg|gif|png',
					'upload_path'   => realpath(APPPATH.'../uploads'),
					'max_size'      => 2000,
					'encrypy_name'  => true,
				);
							
				$this->load->library('upload', $config);
							
				if (! $this->upload->do_upload()) {
					$this->upload->display_errors();
				} else {
					$upload_data = $this->upload->data();
					$image_file = $upload_data['file_name'];
					$data['userimg'] = $image_file;
					
					$newheight = 200;
					$newwidth  = 200;
					$file_name = $image_file;
					$file_name_new = $this->append_file_name($image_file,'_thumb');
					
					$this->image_resize($newheight, $newwidth, $file_name, $file_name_new);
				}
								
				if($this->sitemodel->register($data)) {
					redirect('site/profile', $data);
				} else {
					$this->session->set_flashdata('register', 'An error has occurred');
					$this->signup($data);
				}
			}
		}
		public function gallery(){
			$data["id"] = $this->session->userdata('id');
			$this->load->model('sitemodel', $data);
			$this->sitemodel('gallery');
		}
		
		public function galleryupload()
		{
			$config = array(
				'allowed_types' => 'jpg|jpeg|gif|png',
				'upload_path'   => realpath(APPPATH.'../uploads'),
				'max_size'      => 2000,
				'encrypy_name'  => true,
			);
			$this->load->model('sitemodel');
			$this->load->library('session');			
			$this->load->library('upload', $config);
			
			if (! $this->upload->do_upload()) {
				$this->upload->display_errors();
			} else {
				
				$upload_data = $this->upload->data();
				$image_file = $upload_data['file_name'];
				$data['userid'] = $this->session->userdata('id');
				$data['name'] = $image_file;
				$config = array(
					'source_image'  => realpath(APPPATH . '../uploads') . '/' . $image_file,
					'new_image'		=> realpath(APPPATH.'../uploads') . '/' . $image_file,
					'create_thumb'	=> true,
					'width'			=> '180',
					'height'		=> '180',
					'master_dim'	=> 'auto',
					'allowed_types' => 'jpg|jpeg|gif|png',
					'upload_path'   => realpath(APPPATH.'../uploads'),
					'max_size'      => 2000,
					'encrypy_name'  => true
				);
				
				$this->load->library('image_lib',$config);
			
				if ( ! $this->image_lib->resize()) {
					echo $this->image_lib->display_errors();
				}
					
				if($this->sitemodel->profilegallery($data)) {
					redirect('site/profile', $data);
				} else {
					//$this->signup($data);
				}
			}
		}
		
		public function saveuser() {
			$this->protect();
			$data = $_POST;
			$qry_str = array();
			$fields = array('password','aboutme','location');
			
			foreach($fields as $field) {
				if($field == 'password' && !$data[$field]) { continue;}
				
				if($data[$field]) {
					if($field == 'password') {
						$data[$field] = md5($data[$field]);
					}
					$qry_str[] = "`$field` = '" . $data[$field] . "'";
				}
			}
						
			$qry_str = implode(',',$qry_str);
			
			$user_id = $this->session->userdata('id');
			$query = $this->db->query("UPDATE users SET $qry_str WHERE id = '$user_id'");
			
			foreach($data as $k => $v) {
				$this->session->set_userdata($k, $v);
			}
			
			$this->session->set_userdata();
			redirect('site/profile');
		}

		public function edituser(){
			$this->protect();
			$this->load->library('session');
			$this->load->library('form_validation');
			$data = $this->session->all_userdata();
			$this->header();
			$this->load->view('site/edit_user',$data);
			$this->load->view('site/footer');
		}
				
		public function editevent($id) {
			$this->protect();
			$this->load->model('sitemodel');
			
			$data = $this->sitemodel->get_event($id);
			$data = (array)$data;
					
			$default_time = $this->sitemodel->times();
			$data['default_times'] = $default_time;
			
			$times = explode(':',$data['etime']);
			$hours = $times[0];
			$minutes = $times[1];
			
			$data['hours'] 		= $hours;
			$data['minutes'] 	= $minutes;
			$data['elocat'] 	= $data['city'];
			$data['edesc'] 		= $data['description'];
			$data['ename'] 		= $data['name'];
						
			$data['ampm'] = 'AM';
			
			/********* Tells if time is AM / PM *********/
			$real_hours = $data['hours'] - 12;
			
			if($real_hours > -1) {
				$data['ampm'] = 'PM';
				$data['hours'] = $real_hours;
			}
			
			$this->header();
			$this->load->view('site/event',$data);
			$this->load->view('site/footer');
		}
		
		public function create($data = null) {
			$this->protect();
			$this->load->library('session');			
			$this->load->library('form_validation');
			$this->header();
			$this->load->model('sitemodel');
			$data = array();
			$default_time = $this->sitemodel->times();
			$data['default_times'] = $default_time;
			$this->load->view('site/event',$data);
			$this->load->view('site/footer');
						
			$this->form_validation->set_rules("ename", 'required');
			$this->form_validation->set_rules("eimg", '');
			$this->form_validation->set_rules("hours", 'required');
			$this->form_validation->set_rules("minutes", 'required');
			$this->form_validation->set_rules("ampm", 'required');
			$this->form_validation->set_rules("edate",'required');
			$this->form_validation->set_rules('edesc','required');
			$this->form_validation->set_rules('elocat','required');
			
			if(!$this->form_validation->run()){
				$data = array(
				'id' => '',
				'name'=>set_value('ename'),
				'image'=>set_value('eimg'),
				'date'=> set_value('etime'),
				'date'=>set_value('edate'),
				'description'=>set_value('edisc'),
				'city'=>set_value('elocat'),
				'default_times' => $default_time);
			} else {
				$hours = set_value('hours');
				$minutes = set_value('minutes');
				$ampm	 = set_value('ampm');
				
				$time = $hours . ':' . $minutes . ' ' . $ampm;
				
				if(strstr($time,'PM')) {
					$raw = str_replace(' PM','',$time);
					$raw = explode(':',$raw);
					$raw[0] += 12;
					$raw = implode(':',$raw);
					$time = $raw;
					$is_pm = true;
				}
				
				$datetime = date('Y-m-d',strtotime(set_value('edate'))).' '.date('H:i:s',strtotime($time));
							
				$data = array(
					'name'=>set_value('ename'),
					'image'=>set_value('eimg'),
					'date'=>$datetime,
					'description'=>set_value('edesc'),
					'city'=>set_value('elocat'),
					'userid'=>$this->session->userdata('id'),
					'default_times' => $default_time,
					'eid'			=> $_POST['eid']
				);
				
				if(!$data['eid']) {
					unset($data['eid']);
				}
				
				//imgupload
				$config = array(
					'allowed_types' => 'jpg|jpeg|gif|png',
					'upload_path'   => realpath(APPPATH.'../uploads'),
					'max_size'      => 2000,
					'encrypy_name'  => true,
				);
				
				$this->load->model('sitemodel');
				$this->load->library('upload', $config);
				
				if (!$this->upload->do_upload()) {
					$this->upload->display_errors();
				} else {
					$upload_data = $this->upload->data();
					$image_file = $upload_data['file_name'];
					$data['userid'] = $this->session->userdata('id');
					$data['image'] = $image_file;
					
					$this->image_resize(90, 90, $image_file, $this->sitemodel->imgthumb($image_file,'_thumb'));
					$this->image_resize(275, 275, $image_file, $this->sitemodel->imgthumb($image_file,'_med'));
					
					$this->load->library('image_lib',$config);
				}
				if($this->sitemodel->createevent($data)) {
					redirect('site/find');
				} else {
					//	$this->signup($data);
				}
			}
		}
		
		private function image_crop($newheight, $newwidth, $file_name) {
			$base_path = realpath(APPPATH . '../uploads');
			list($width, $height) = getimagesize($base_path . '/' . $file_name);
			
			$x_axis = floor(($width - $newwidth) / 2);
		    $y_axis = floor(($height - $newheight) / 2);
			
			$config['image_library'] = 'gd2';
			$config['source_image'] = $base_path . '/' . $file_name;
			$config['new_image'] = $base_path . '/' . $file_name;
			$config['quality'] = "100%";
			$config['maintain_ratio'] = FALSE;
			$config['width'] = $newwidth;
			$config['height'] = $newheight;
			$config['x_axis'] = $x_axis;
			$config['y_axis'] = $y_axis;
			
			$this->load->library('image_lib'); 
			$this->image_lib->initialize($config);
			$this->image_lib->crop();
		}
		
		private function image_resize($newheight, $newwidth, $file_name, $file_name_new,$crop = true) {
			if($crop == true) {
				$newheight += 100;
				$newwidth += 100;
			}
			
			$base_path = realpath(APPPATH . '../uploads');
			list($width, $height) = getimagesize($base_path . '/' . $file_name);
			
			$config["image_library"] = "gd2";
			$config["source_image"] = $base_path . '/' . $file_name;
			$config['create_thumb'] = FALSE;
			$config['maintain_ratio'] = TRUE;
			$config['new_image'] = $base_path . '/' . $file_name_new;
			$config['quality'] = "100%";
			$config['width'] = $newwidth;
			$config['height'] = $newheight;
			$dim = (intval($width / $height)) - ($config['width'] / $config['height']);
			$config['master_dim'] = ($dim > 0) ? "height" : "width";
			
			$this->load->library('image_lib');
			$this->image_lib->initialize($config);
			
			$this->image_lib->resize();
			
			if($crop) { 
				$newwidth -= 100;
				$newheight -= 100;
				$this->image_crop($newheight,$newwidth,$file_name_new);
			}
		}
		
		public function profile(){			
			$events = $galleryimgs = $data = array();			
			$events["userid"] ="=". $this->session->userdata("id");
			$this->protect();
			$this->header();
			$this->load->model("sitemodel");
			$username = '';
			
			if(isset($_GET['username'])) {
				$username = addslashes($_GET['username']);
			} else {
				redirect('/site/profile?username=' . $this->session->userdata('username'));
			}
						
			if($username) {
				$query = $this->db->query("SELECT * FROM users WHERE username = '$username' LIMIT 1");
				$result = $query->result();
				if($query->num_rows()){
					foreach($result as $r){
						$data = $r;
					}
				} else {
					$data['error'] = 'This user doesn\'t exist';
				}
			} else {
				$data = $this->session->all_userdata();
			}
			
			$data = (array)$data;
			
			$data['current_user'] = $this->session->userdata('username');
			
			$data['thumb'] = $this->sitemodel->imgthumb($data['userimg']);
			$data["events"] = $this->sitemodel->checkevents($events);
			$galleryimgs = array("userid" => $data['id']);
			$data["galleryimgs"] = $this->sitemodel->gallery($galleryimgs);
			$data['disqus'] = $this->disqus();
			
			$this->load->view('site/profile',$data);
			$this->load->view('site/footer');
		}
		
		public function eventdetail($id = null){
			$this->load->model('sitemodel');
			
			$query = $this->db->query("SELECT * FROM events WHERE id = $id");
			$result = $query->result();
			if($query->num_rows()){
				$data = $result[0];
			} else {
				$data['error'] = 'This user doesn\'t exist';
			}
			
			$data = (array)$data;
			
			$attendee_ids = array();
			
			$query = $this->db->query("SELECT userid FROM event_users WHERE eventid = '$data[id]'");
			$data['attendee_count'] = $query->num_rows();
			if($query->num_rows()) {
				$result = $query->result();
				foreach($result as $r) {
					$attendee_ids[] = $r->userid;
				}
			}
			
			$data['attendee_ids'] = $attendee_ids;
			$data['username'] = $this->sitemodel->idtousername($data['userid']);
			$data['thumb']  = $this->sitemodel->imgthumb($data['image'],'_med');
			$data['disqus'] = $this->disqus();
			$this->header();
			$this->load->view('site/eventdetail',$data);
			$this->load->view('site/footer');
		}
		
		public function rsvp($action = null, $id = null) {
			$this->protect();
			$this->load->library('session');
			$user_id = $this->session->userdata('id');
			
			if(!$id || !$user_id) {return;}
			
			/********* Update user according to action *********/
			switch($action) { 
				case 'attend':
					$this->db->query("INSERT INTO event_users (eventid,userid) VALUES ('$id','$user_id')");
					break;
				case 'unattend':
					$this->db->query("DELETE FROM event_users WHERE eventid = '$id' AND userid = '$user_id'");
					break;
			}
			
			/********* Get # of people going *********/
			$query = $this->db->query("SELECT * FROM event_users WHERE eventid = '$id'");
			
			echo $query->num_rows();
		}
		
		/************** Subnav Dynamically load navigation displaying the logged in user pulling information from session **************/
		public function subnav(){
			$this->load->library('session');
		 	$data = array(
		 				'id' => $this->session->userdata("id"),
						'username'=> $this->session->userdata("username")
					);
			$this->load->view('site/loggedin_subnav',$data);
		}
		
		/************** Userview setting active uer by default or passing information from friends into an active view  **************/
		public function eventfind(){
			$this->protect();
			$this->load->library('session');
			
			$username = '';
			if(isset($_GET['username'])) {
				$this->load->model('sitemodel');
				$username = $_GET['username'];
				$user_info = $this->sitemodel->userinfo($username);
				if(!$user_info) { unset($username);} else {
					$id = $user_info[0]->id;
					$biography = $user_info[0]->biography;
					$email = $user_info[0] -> email;
					$logged_in = false;
				}
			} else {
				$id = $this->session->userdata('id');
				$username = $this->session->userdata('username');
				$biography = $this->session->userdata('biography');
				$email = $this->session->userdata('email');
				$logged_in = true;
			}
			
			$data = array('id' => $id,
						  'logged_in_id' => $this->session->userdata('id'),
						  'username' => $username,
						  'email' => $email,
						  'biography' => $biography,
						  'username' => $username,
						  'logged_in' => $logged_in);
			
			$this->header();
			$this->load->view('site/content_find',$data);
			$this->load->view('site/footer');
		}
		
		function find($params=null){
			$search = array();
			$this->load->model('sitemodel');
			if($params) {
				$search = array("city"=>"= '$params'");
			}
			$events = $this->sitemodel->checkevents($search);
			$this->sitemodel->times();
			$data = array('events' => $events);
			$this->header();
			$this->load->view('site/find',$data);
			$this->load->view('site/footer');
		}
		
		function search(){
			$this->load->library('form_validation');
			$this->load->model('sitemodel');
			$this->form_validation->set_rules("citysearch", '');
			
			if($this->form_validation->run()){
				$this->find(set_value('citysearch'));
			}
		}		
				
		/******** Login information is checked against the database and authenticates the user as well as beginning session. **************/
		public function login(){
			$this->load->library('form_validation');
			$this->load->model('sitemodel');
			
			$this->form_validation->set_rules("siemail", "email", 'required');
			$this->form_validation->set_rules("sipass", "password", 'required');
			
			if(! $this->form_validation->run()){
				$this->session->set_flashdata('login',validation_errors());
				$data = array(
					'email' => set_value('siemail'),
					'location' => '',
					'aboutme' => ''
				);
				$this->signup($data);
			} else {
				$email = set_value('siemail');
				$password = set_value('sipass');
				
				$data = array(
					'email' => $email,
					'location' => '',
					'aboutme' => ''
				);
				
				if($this->sitemodel->checklogin($email,$password)){
					redirect('site/find');
				}else{
					$this->session->set_flashdata('login', 'Login information is incorrect');
					$this->signup($data);
				}
			}
		}
		
		/************** takes users back to landing and destroys session **************/
		public function logout() {
			$this->session->sess_destroy();
			redirect(base_url(),'refresh');
		}
	}
?>