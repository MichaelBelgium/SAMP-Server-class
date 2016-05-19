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
		$this->ssh["Port"] = (int)$sshport;
		$this->ssh["User"] = $user;
		$this->ssh["Password"] = $password;
		$this->server["Dir"] = $dir;
		$this->server["Exe"] = $exe;
		$this->server["Port"] = (int)$port;

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

	public function getConfig($var)
	{
		$value = $this->execute("grep -oP '(?<=$var ).*' server.cfg");
		return trim($value);
	}

	public function setConfig($var,$value)
	{
		$this->execute("sed -i -- 's/$var {$this->getConfig($var)}/$var $value/g' server.cfg");
		return true;
	}

	public function startServer()
	{
		if($this->isRunning())
			return false;

		if(!file_exists("/home/{$this->ssh["User"]}/".(($this->server["Dir"] !== "~" || $this->server["Dir"] !== ".") ? "{$this->server["Dir"]}/": "")."{$this->server["Exe"]}"))
			return false;

		if($this->getConfig("port") != $this->server["Port"])
			$this->setConfig("port",$this->server["Port"]);

		ssh2_exec($this->con, "cd {$this->server["Dir"]} && ./{$this->server["Exe"]} &");
		return true;
	}

	public function stopServer($removelog = false)
	{
		if(!$this->isRunning())
			return false;

		if($removelog)
			$this->execute("rm server_log.txt");

		$this->execute("killall -9 {$this->server["Exe"]}");
		return true;
	}

	public function restartServer($removelog = false)
	{
		$this->stopServer($removelog);
		sleep(1);
		$this->startServer();
		return true;
	}
}

?>
