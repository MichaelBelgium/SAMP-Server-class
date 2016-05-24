<?php
	require("inc/samp-server.class.php");

	try 
	{
		$server = new SAMPServer("michael1","abc123","samp03svr10","8000","samp03");
		//or:
		//$server = new SAMPServer("michael1","abc123","samp03svr10");
		//$server = new SAMPServer("michael1","abc123");
		//...
		$server->setConfig("rcon_password","myrconpassword");
		$server->setConfig("maxplayers",100);
		//...
	}
	catch (Exception $e) 
	{
		die($e->getMessage());
	}

	if(isset($_GET["action"]))
	{
		switch ($_GET["action"]) {
			case "start":
				$server->startServer();
				die(header("Location: ?started"));
				break;
			
			case "stop":
				$server->stopServer();
				die(header("Location: ?stopped"));
				break;

			case "restart":
				$server->restartServer();
				die(header("Location: ?restarted"));
				break;
		}
	}
?>

<!DOCTYPE html>
<html>
	<head>
		<title>Class example</title>
	</head>
	<body>
		<?php 
			$action = "";

			if(isset($_GET["started"]))			$action = "started";
			else if(isset($_GET["stopped"])) 	$action = "stopped";
			else if(isset($_GET["restarted"])) 	$action = "restarted";

			if(!empty($action)) echo "<p>Server has been $action";
		?>
		<div>
			<h1>Control/info</h1>
			<p><a href="?action=start">Start</a> / <a href="?action=stop">stop</a> / <a href="?action=restart">restart</a> the server.</p>
			<p>
				<table>
					<tbody>
						<tr>
							<td>Online</td>
							<td><?php echo ($server->isRunning()) ? "Yes" : "No";?></td>
						</tr>
						<tr>
							<td>Server name</td>
							<td><?php echo $server->getConfig("hostname"); ?></td>
						</tr>
						<tr>
							<td>Port</td>
							<td><?php echo $server->getConfig("port"); ?></td>
						</tr>
					</tbody>
				</table>
			</p>
		</div>
		
		<div>
			<h1>Log</h1>
			<p><?php echo $server->getLog(25); ?></p>
		</div>
	</body>
</html>
