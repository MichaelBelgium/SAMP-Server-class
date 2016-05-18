<?php
class SAMPServer
{
	private 
		$con = null,
		$ssh = array("IP" => null, "Port" => null, "User" => null, "Password" => null),
		$server = array("Port" => null, "Dir" => null, "Exe" => null);

	function __construct($user, $password, $exe = "samp03svr", $port = 7777, $dir = "~", $ip = "127.0.0.1", $sshport = 22)
	{
		$this->ssh["IP"] = $ip;
		$this->ssh["Port"] = $sshport;
		$this->ssh["User"] = $user;
		$this->ssh["Password"] = $password;
		$this->server["Dir"] = $dir;
		$this->server["Exe"] = $exe;
		$this->server["Port"] = $port;

		if(!($this->con = ssh2_connect($this->ssh["IP"]))) 
			throw new Exception("Can't connect to SSH server ({$this->ssh["IP"]})");

		if(!ssh2_auth_password($this->con, $this->ssh["User"], $this->ssh["Password"]))
			throw new Exception("Can't authenticate to SSH server");
	}

	private function execute($command) 
	{
		$stream = ssh2_exec($this->con, "cd {$this->server["Dir"]} && $command");
		stream_set_blocking($stream, true);
		$output = stream_get_contents($stream);
		fclose($stream);
		return $output;
	}

	public function isRunning() 
	{
		$result = $this->execute("ps cax | grep {$this->server["Exe"]}");
		return !empty($result);
	}
}

?>
