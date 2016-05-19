# SAMP-Server-class
Able to manage sa:mp servers with this php class

<h1>Functions</h1>

```PHP
public __construct($user, $password, $exe = "samp03svr", $port = 7777, $dir = "~", $ip = "127.0.0.1", $sshport = 22)
public isRunning()
public getConfig($var)
public setConfig($var,$value)
public startServer()
public stopServer($removelog = false)
public restartServer($removelog = false)

private execute($command)
```

<h1>Example</h1>

An example is available in the example script. 
Do note it's recommended to create new instances in a try-catch.
