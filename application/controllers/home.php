<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends CI_Controller {

	public function index($page = '')
	{
		$data['base_url'] = base_url();
		$data['is_logged'] = $this->datauser->is_logged;
		
		if($page == '')
			$page = $this->datauser->is_logged ? 'home_logged' : 'home';
		
		$pages = array('register', 'login', 'home', 'home_logged', 'chat');
		$page_not_login = array('register', 'login', 'home');
		$page_login = array('chat', 'home_logged');
		
		if( ! in_array($page, $pages))
			show_404();
		
		if($this->datauser->is_logged && in_array($page, $page_not_login))
			redirect('');
		elseif( ! $this->datauser->is_logged && in_array($page, $page_login))
			redirect('');
			
		if($page == 'register' && $this->session->flashdata('fb_user') !== FALSE)
			$page = 'register_fb';
			
		$data['user_id'] = $this->datauser->is_logged ? $this->datauser->user_id : 0;
		$data['username'] = $this->datauser->is_logged ? $this->datauser->username : '';
		$data['page'] = $page;
		
		switch($page)
		{
			case 'register_fb':
				$fb_user = $this->session->flashdata('fb_user');
				$data['fb_email'] = $fb_user['fb_email'];
				$data['fb_id'] = $fb_user['fb_id'];
				$this->session->set_flashdata(array('fb_user' => $fb_user));
			case 'register':
				$data['register_status'] = $this->session->flashdata('register_status') ? $this->session->flashdata('register_status') : 1;
				$this->load->helper('captcha');
				$captcha = array(
					'img_path'	 => './captcha/',
					'img_url'	 => base_url().'captcha/',
					'img_width'	 => '150',
					'img_height' => 27,
					'expiration' => 0
				);
				$data['captcha'] = create_captcha($captcha);
				$this->session->set_flashdata('register_captcha', $data['captcha']['word']);
				break;
			case 'login':
				$data['login_status'] = $this->session->flashdata('login_status') ? $this->session->flashdata('login_status') : 1;
				$data['login_samples'] = $this->session->userdata('login_samples');
				$this->load->helper('captcha');
				$captcha = array(
					'img_path'	 => './captcha/',
					'img_url'	 => base_url().'captcha/',
					'img_width'	 => '150',
					'img_height' => 27,
					'expiration' => 0
				);
				$data['captcha'] = create_captcha($captcha);
				$this->session->set_flashdata('login_captcha', $data['captcha']['word']);
				break;
			default:
				break;
		}
		$data['content'] = trim($this->load->view('default/pages/'.$page, $data, TRUE));
			
		$this->load->view('default/home', $data);		
	}
	
	public function login_validation()
	{
		if($this->input->post() === FALSE)
			show_404('Error');
		
		$this->load->library('form_validation');
		$rules = $this->form_validation;
		$rules->set_rules('login_username', 'Username', 'required|min_length[3]|max_length[32]|trim');
		$rules->set_rules('login_password', 'Password', 'required|min_length[6]|max_length[40]|trim');
		
		if($this->session->userdata('login_samples') && $this->session->userdata('login_samples') > 3 && ! $this->captcha_check('login_captcha'))
		{
			$this->session->set_flashdata('login_status', '-2');
			redirect('home/index/login');
		}
		
		$this->session->set_userdata('login_samples', $this->session->userdata('login_samples') ? (int)$this->session->userdata('login_samples') + 1 : 1);
		
		if( ! $rules->run())
		{
			$this->session->set_flashdata('login_status', '-1');
			redirect('home/index/login');
		}
			
		$this->load->model('user_model');
		
		if( ! $data_user = $this->user_model->get_account($this->input->post('login_username'), $this->input->post('login_password')))
		{
			$this->session->set_flashdata('login_status', '-1');
			redirect('home/index/login');
		}
		
		$data_session = array(
			'is_logged'		=> TRUE,
			'user_id'		=> $data_user['id'],
			'nickname'		=> $data_user['nickname'],
			'facebook_user'	=> $data_user['facebook_user'],
			'username'		=> $this->input->post('login_username'),
			'ini' 			=> TRUE
		);
		
		$this->session->set_userdata(array('data_user' => $data_session));
		$this->session->set_flashdata('login_status', '1');
		$this->session->unset_userdata('login_samples');
		if($this->input->post('login_remember_me') == '1')
		{
			$this->load->helper('cookie');
				
			$remember_me_token = strtoupper($this->input->post('login_username')).'-'.$this->user_model->sha_password($this->input->post('login_username'), $this->input->post('login_password'));
			$cookie = array(
				'name'   => 'remember_me_token',
				'value'  => $remember_me_token,
				'expire' => '31536000',
				'secure' => FALSE
			);
			$this->input->set_cookie($cookie);    
		}
		
		redirect('home/index/login');
	}
	
	public function register_validation()
	{
		if($this->input->post() === FALSE)
			show_404('Error');
		
		$this->load->library('form_validation');
		$rules = $this->form_validation;
		$rules->set_rules('register_username', 'Username', 'required|min_length[3]|max_length[40]|trim');
		$rules->set_rules('register_password', 'Password', 'required|min_length[6]|max_length[40]|trim');
		$rules->set_rules('register_password_confirm', 'Password Confirm', 'required|matches[register_password]|trim');
		$rules->set_rules('register_email', 'Email', 'required|valid_email|trim');
		
		$this->session->set_flashdata(array('fb_user' => $this->session->flashdata('fb_user')));
		
		if( ! $rules->run())
		{
			$this->session->set_flashdata('register_status', '-1');
			redirect('home/index/register');
		}
		
		if( ! $this->captcha_check('register_captcha'))
		{
			$this->session->set_flashdata('register_status', '-4');
			redirect('home/index/register');
		}
			
		$this->load->model('user_model');
		
		if($this->user_model->check_data($this->input->post('register_username')) != 0)
		{
			$this->session->set_flashdata('register_status', '-2');
			redirect('home/index/register');
		}
		
		if($this->user_model->check_data($this->input->post('register_email'), TRUE) != 0)
		{
			$this->session->set_flashdata('register_status', '-3');
			redirect('home/index/register');
		}
		
		$fb_user_id = '';
		
		if($this->session->flashdata('fb_user') !== FALSE)
		{
			$fb_user = $this->session->flashdata('fb_user');
			$fb_user_id = $fb_user['fb_id'];
		}
		
		$this->user_model->create_account($this->input->post('register_username'), $this->input->post('register_password'), $this->input->post('register_email'), $fb_user_id);
		
		$this->session->set_flashdata('register_status', '2');
		redirect('home/index/register');
	}
	
	public function login_validation_fb($remember_me)
	{
		require_once(FCPATH.'facebook_api/facebook.php');
		$facebook = new Facebook(array('appId'  => '194753930687466', 'secret' => 'e2c3f19892c3570145acbdbcdd9ee78e'));
				
		if( ! $user = $facebook->getUser())
			redirect('');
			
		try {
			$user_profile = $facebook->api('/me');
		} catch (FacebookApiException $e) {
			redirect('');
		}
			
		$this->load->model('user_model');
		if( ! $data_user = $this->user_model->get_account_by_id($user_profile['id'], $fb = TRUE))
		{
			$data_user = array (
				'fb_id' => $user_profile['id'],
				'fb_email' => $user_profile['email']
			);
			$this->session->set_flashdata(array('fb_user' => $data_user));
			redirect('home/index/register');
		}
						
		$data_session = array( 
			'user_id'		=>  $data_user['id'],
			'username'  	=>  $data_user['username'],
			'nickname' 		=>	$data_user['nickname'],
			'facebook_user'	=>	$data_user['facebook_user'],
			'is_logged' 	=>	TRUE,
			'ini'       	=>  TRUE
		);
		
		$this->session->set_userdata(array('data_user' => $data_session));
		
		if($remember_me)
		{
			$this->load->helper('cookie');
				
			$remember_me_token = strtoupper($data_user['username']).'-'.$data_user['sha_pass_hash'];
			$cookie = array(
				'name'   => 'remember_me_token',
				'value'  => $remember_me_token,
				'expire' => '31536000',
				'secure' => FALSE
			);
			$this->input->set_cookie($cookie);  
		}
		redirect('');
	}
	
	function captcha_check($captcha)
	{
		return $this->session->flashdata($captcha) === $this->input->post($captcha) ? TRUE : FALSE;
	}
	
	public function log_out()
    {
		$this->load->helper('cookie');
        $this->session->sess_destroy();
        delete_cookie("remember_me_token");
		redirect('');
	}
}