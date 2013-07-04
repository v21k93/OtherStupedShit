<?php
class User_model extends CI_Model
{
        public function __construct() 
        {
            parent::__construct();
            $this->db = $this->load->database('default', TRUE);  
        } 
        
        function sha_password($username, $password)
        {
            $username = strtoupper($username);
            $password = strtoupper($password);
            return SHA1($username.':'.$password);
        }
        
        function get_account($username, $password, $sha = TRUE)
        {
			if($sha)
				$password = $this->sha_password($username, $password);
				
            $this->db->where('username', $username);
            $this->db->where('sha_pass_hash', $password);
            $query = $this->db->get('account');
			
			return $query->num_rows > 0 ? $query->row_array() : FALSE;
        }
		
		function get_account_by_id($id, $fb = FALSE)
        {				
            $this->db->where($fb ? 'facebook_user' : 'id', $id);
            $query = $this->db->get('account');
			
			return $query->num_rows() > 0 ? $query->row_array() : FALSE;
        }
		
		function check_data($data, $email = FALSE)
		{
            $this->db->where($email ? 'email' : 'username', $data);
            $query = $this->db->get('account');

            return $this->db->affected_rows();
		}
		
		function create_account($username, $password, $email, $fb_user_id)
        {
            $password = $this->sha_password($username, $password);
            $data = array(
                   'username' => $username,
                   'sha_pass_hash' => $password,
				   'email' => $email,
				   'facebook_user' => $fb_user_id
                );

            $query = $this->db->insert('account', $data);

            return $this->db->affected_rows();
        }
}
?>