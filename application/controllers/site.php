<?php  if( ! defined('BASEPATH')) exit('No direct script access allowed');

	class Site extends CI_Controller{
		public function index(){
				$this->session->sess_destroy();
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
				$this->load->library('form_validation');
				$this->header();
				$this->load->view('site/content_landing');
				$this->load->view('site/footer');
		}
		
		public function header(){
			$this->load->library('session');
			$loggedin = false;
			if($this->session->userdata("username")){
				$loggedin = true;
			}
			$data = array("loggedin" => $loggedin);
			$this->load->view('site/header',$data);
		}
		
		public function signup($data = null){
			//print_r($data);			
			$this->load->model('sitemodel');
			$this->load->library('form_validation');
			$this->header();
			$this->load->view('site/content_signin',$data);
			$this->load->view('site/footer');
		}
			/************** Registeration Form + Validation
							$data: Passed to userview and sent to DB. **************/
		public function register(){
			
			$this->load->library('form_validation');
			$this->load->model('sitemodel');
			
			$this->form_validation->set_rules("reguser", "Username", 'required');
			$this->form_validation->set_rules("regemail", "Email", 'required');
			$this->form_validation->set_rules("regpass", "Password", 'required|matches[regrepass]');
			$this->form_validation->set_rules("regrepass", "Retype Password", 'required');
			$this->form_validation->set_rules('regbio','');
			$this->form_validation->set_rules('reglocat','');
			
			if(! $this->form_validation->run())
			{
				$data = array(
					'username'=>set_value('reguser'),
					'userimg'=>set_value('regimg'),
					'password'=> md5(set_value('regpass')),
					'email'=>set_value('regemail'),
					'aboutme'=>set_value('regbio'),
					'location'=>set_value('reglocat')
				);
				
				$this->signup($data);
			}
			
			else
			{
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
					$this->session->set_flashdata("error", $error);
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
				
				if (! $this->upload->do_upload())
				{
					
					$this->upload->display_errors();
					
				}
				
				else
				{
					
					$upload_data = $this->upload->data();
					$image_file = $upload_data['file_name'];
					$data['userimg'] = $image_file;
					$config = array(
						'source_image'  => realpath(APPPATH . '../uploads') . '/' . $image_file,
						'new_image'		=> realpath(APPPATH.'../uploads') . '/' . $image_file,
						'create_thumb'	=> true,
						'width'			=> '100',
						'height'		=> '100',
						'master_dim'	=> 'auto',
						'allowed_types' => 'jpg|jpeg|gif|png',
						'upload_path'   => realpath(APPPATH.'../uploads'),
						'max_size'      => 2000,
						'encrypy_name'  => true,
					
					);
					
					$this->load->library('image_lib',$config);
				
					if ( ! $this->image_lib->resize())
						{
						    echo $this->image_lib->display_errors();
						}
				}
				

				
								
				if($this->sitemodel->register($data))
				{
					redirect('site/profile', $data);
				}
				else
				{
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
				
				if (! $this->upload->do_upload())
				{
					
					$this->upload->display_errors();
					
				}
				
				else
				{
					
					$upload_data = $this->upload->data();
					$image_file = $upload_data['file_name'];
					$data['userid'] = $this->session->userdata('id');
					$data['name'] = $image_file;
					$config = array(
						'source_image'  => realpath(APPPATH . '../uploads') . '/' . $image_file,
						'new_image'		=> realpath(APPPATH.'../uploads') . '/' . $image_file,
						'create_thumb'	=> true,
						'width'			=> '100',
						'height'		=> '100',
						'master_dim'	=> 'auto',
						'allowed_types' => 'jpg|jpeg|gif|png',
						'upload_path'   => realpath(APPPATH.'../uploads'),
						'max_size'      => 2000,
						'encrypy_name'  => true,
					
					);
					
					$this->load->library('image_lib',$config);
				
					if ( ! $this->image_lib->resize())
						{
						    echo $this->image_lib->display_errors();
						}
						
					if($this->sitemodel->profilegallery($data))
						{
							redirect('site/profile', $data);
						}
						else
						{
							$this->signup($data);
						}

					
				}
			}
		

		
		public function edituser(){
			$this->protect();
			$data = array("id" => $this->session->userdata("id"),
							"username" => $_POST["username"],
							"biography" => $_POST["biography"],
							"password" => $_POST["password"]);
			$this->load->library('form_validation');
			$this->load->model('sitemodel');
			$this->sitemodel->updateuser($data);
			
			$this->session->set_userdata($data);
			redirect('site/userview');
			
		}
		
		
		public function create($data = null){
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
			$this->form_validation->set_rules("etime", 'required');
			$this->form_validation->set_rules("edate",'required');
			$this->form_validation->set_rules('edesc','required');
			$this->form_validation->set_rules('elocat','required');
			
			if(! $this->form_validation->run()){
				$data = array(
				'name'=>set_value('ename'),
				'image'=>set_value('eimg'),
				'date'=> set_value('etime'),
				'date'=>set_value('edate'),
				'description'=>set_value('edisc'),
				'city'=>set_value('elocat'),
				'default_times' => $default_time);
		
								
			}else{
				$time = set_value('etime');
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
					'default_times' => $default_time
				);
												
				//imgupload
					
				$config = array(
					'allowed_types' => 'jpg|jpeg|gif|png',
					'upload_path'   => realpath(APPPATH.'../uploads'),
					'max_size'      => 2000,
					'encrypy_name'  => true,
				);
				
				$this->load->model('sitemodel');
				$this->load->library('upload', $config);
				
				if (! $this->upload->do_upload())
				{
					
					$this->upload->display_errors();
					
				}
				
				else
				{
					
					$upload_data = $this->upload->data();
					$image_file = $upload_data['file_name'];
					$data['userid'] = $this->session->userdata('id');
					$data['image'] = $image_file;
					$config = array(
						'source_image'  => realpath(APPPATH . '../uploads') . '/' . $image_file,
						'new_image'		=> realpath(APPPATH.'../uploads') . '/' . $image_file,
						'create_thumb'	=> true,
						'width'			=> '100',
						'height'		=> '100',
						'master_dim'	=> 'auto',
						'allowed_types' => 'jpg|jpeg|gif|png',
						'upload_path'   => realpath(APPPATH.'../uploads'),
						'max_size'      => 2000,
						'encrypy_name'  => true,
					
					);
					
					$this->load->library('image_lib',$config);
				
					if ( ! $this->image_lib->resize())
						{
						    echo $this->image_lib->display_errors();
						}
					
				}
				if($this->sitemodel->createevent($data)) {
					redirect('site/find');
				} else {
					$this->signup($data);
				}
			}
			
			
			
		}
		
		
		public function profile(){
			$events = $galleryimgs = $data = array();
			$events["id"] ="=". $this->session->userdata("id");
			$this->header();
			$this->load->model("sitemodel");
			$data = $this->session->all_userdata();
			$data["events"] = $this->sitemodel->checkevents($events);
			$galleryimgs = array("userid" => $data['id']);
			$data["galleryimgs"] = $this->sitemodel->gallery($galleryimgs);
			
			
			
			
			$this->load->view('site/profile',$data);
			$this->load->view('site/footer');
		}
		/************** Subnav Dynamically load navigation displaying the logged in user pulling information from session **************/
		public function subnav(){
			$this->load->library('session');
		 	$data = array("id" => $this->session->userdata("id"),
						"username"=> $this->session->userdata("username"));
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
			if($params){
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
				
			/************** Login information is checked against the database and authenticates the user as well as beginning session. **************/
		public function login(){
			$this->load->library('form_validation');
			$this->load->model('sitemodel');
			
			$this->form_validation->set_rules("siemail", "email", 'required');
			$this->form_validation->set_rules("sipass", "password", 'required');
			
			if(! $this->form_validation->run()){
				$this->session->set_flashdata('login','Please fill out the form');
				$this->home();	
			}else{
				
				$email = set_value('siemail');
				$password = set_value('sipass');
				
				if($this->sitemodel->checklogin($email,$password)){
					redirect('site/find');
				}else{
					$this->session->set_flashdata('login', 'Login information is incorrect');
					$this->home();
					redirect(base_url(),'refresh');
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