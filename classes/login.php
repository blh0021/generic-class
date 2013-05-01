<?php 
class Login
{
	private $_username;
	private $_password;

	/**
	 *	LDAP Settings
	 *
	 */
	private $_ldap=false;
	private $_domain;
	private $_ldap_server;

	/**
	 *  MySql Settings
	 *
	 */
	private $_mysql=false;
	private $_mysql_conn;
	private $_mysql_query;


	public $error_msg;

	protected function _credentials()
	{
		$ldap_conn=false;
		$mysql_conn=false;
		if ($this->_ldap)
			$ldap_conn=$this->ldapCheckUser();

		if ($this->_mysql)
			$mysql_conn=$this->mysqlCheckUser();
		if ($ldap_conn==$this->_ldap and $mysql_conn==$this->_mysql)
			return true;
		else
			return false;
	}

	private function ldapCheckUser()
	{
		$auth_user=$this->_username."@".$this->_domain;
		if (extension_loaded('ldap')){
		   if($connect=@ldap_connect($this->_ldap_server)){
			   if($bind=@ldap_bind($connect, $auth_user, $this->_password)){
				return(true);
				}//if bound to ldap
			}
			$this->error_msg=ldap_error($connect);	   
			@ldap_close($connect);
			return(false);
		}//if connected to ldap
		$this->error_msg = 'PHP LDAP Extension LDAP Missing';
		return(false);

	}

	/**
	 *  mysqlCheckUser()
	 *
	 *  query must return as username, password
	 */
	private function mysqlCheckUser()
	{
		$conn=$this->_mysql_conn;
		$stmt=$conn->prepare($this->_mysql_query);
		$stmt->bind_param("s", $this->_username);
		$stmt->execute();
		$stmt->bind_result($user, $pass);
/*		if (!$result = $->_mysql_query)) {
			$this->error_msg='Query Failed : '.$this->_mysql_query.mysqli_connect_error();
			return false;
		}
*/		
		$stmt->fetch();
		//$account=$result->fetch_array();
		if ($user==$this->_username and $pass==$this->_password)
			return true;
		else 
			return false;

	}

	public function checkUser($username, $password)
	{
		$this->_username = mysql_real_escape_string($username);
		$this->_password = mysql_real_escape_string($password);

		return $user = $this->_credentials();
	}	

	public function setLdapSettings($domain, $server)
	{
		$this->_ldap=true;
		$this->_domain=$domain;
		$this->_ldap_server=$server;
	}
	
	public function setMysqlSettings($host, $user, $pass, $query)
	{
		$this->_mysql=true;
		if (!$this->_mysql_conn=mysqli_connect($host, $user, $pass)) {
			$this->error_msg='Connection Error : '.mysqli_connect_error();
			return false;
		}

		$this->_mysql_query=$query;
		return true;
	}

	public function setMysqlConnection($conn, $userColumn, $passColumn, $table)
	{
		$this->_mysql=true;
		$this->_mysql_conn=$conn;
		
		$query='select '.$userColumn.' as username, '.$passColumn.' as password from '
				.$table.' where '.$userColumn.' = ?';
		$this->_mysql_query=$query;
		return true;
	}

}


?>