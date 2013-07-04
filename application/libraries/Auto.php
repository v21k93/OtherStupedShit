<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class datauser {
    
    public  $username = NULL, 
            $user_id = 0,
            $is_logged = FALSE,
			$facebook_user = NULL,
			$nickname = NULL,
            $ini = FALSE; 
    
    public function __construct($id = '') 
    {
        if( ! $this->init($id))
            return FALSE;
    }
    
    public function init($id)
    {
        $CI =& get_instance();
        
        if(is_int((int)$id) && $id > 0)
        {
            $CI->load->model('user_model');
            if( ! $data_user = $CI->user_model->get_account_by_id($id, $fb = TRUE))
				return FALSE;
            
            $data_user = array( 
                'user_id'		=>  $data_user['id'],
                'username'  	=>  $data_user['username'],
				'nickname' 		=>	$data_user['nickname'],
				'facebook_user'	=>	$data_user['facebook_user'],
				'is_logged' 	=>	TRUE,
                'ini'       	=>  TRUE
            );
        }
        elseif($CI->session->userdata('data_user'))
        {
            $data_user = $CI->session->userdata('data_user');
        }
		else
		{
			$CI->load->helper('cookie');			
			$remember_me_token = get_cookie('remember_me_token');
		
			if(empty($remember_me_token))
				return FALSE;
				
			$CI->load->model("user_model");
			$data = explode('-', $remember_me_token);
			if( ! $data_user = $CI->user_model->get_account($data[0], $data[1], FALSE))
			{
				delete_cookie("remember_me_token");
				return FALSE;
			}
			$data_user = array( 
				'user_id'		=>  $data_user['id'],
				'username'  	=>  $data_user['username'],
				'nickname' 		=>	$data_user['nickname'],
				'facebook_user'	=>	$data_user['facebook_user'],
				'is_logged' 	=>	TRUE,
				'ini'       	=>  TRUE
			);
			
			$CI->session->set_userdata(array('data_user' => $data_user));
		}
        
        foreach($data_user as $key=>$val)
            $this->$key = $val;
        
        return TRUE;
    }
    
    public function changeData($data, $new)
    {
        if( ! $this->ini)
            return FALSE;
        
        $CI =& get_instance();
        $newdata = $CI->session->userdata('data_user');
        
        $newdata[$data] = $new;
        $CI->session->set_userdata(array('data_user' => $newdata));
    }
    
}

class Auto {
    
    public function __construct() 
    {
        $CI =& get_instance();
        $CI->datauser = new datauser();
    }
}