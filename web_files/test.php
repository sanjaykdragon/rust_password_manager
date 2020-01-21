<?php
    $server_username = "root";
    $server_password = "";
    $server_dbname = "pw_mgr";
    $server_server = "localhost";

    try {
        $conn = new PDO('mysql:host=' . $server_server . ';dbname=' . $server_dbname, $server_username, $server_password, [PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]);
    } catch (Exception $e) {
        die('Error connecting to database: ' . $e);
    }
    
	$json_input = json_decode(file_get_contents('php://input'), true);
	$option = $_POST["option"] ?? $json_input["option"];
	
    if($option == null)
	{
        die(json_encode(array("error" => "option not set")));
	}
        
    if($option == "save")
    {
		$username = $_POST["username"] ?? $json_input["username"];
		$password = $_POST["password"] ?? $json_input["password"];
		$site = $_POST["site"] ?? $json_input["site"];
		
        $insert_credentials = $conn->prepare("INSERT INTO info (site, username, password, time) VALUES (:site, :username, :password, :time);");
        $insert_credentials->bindValue(":site", $site);
        $insert_credentials->bindValue(":username", $username);
        $insert_credentials->bindValue(":password", $password);
        $insert_credentials->bindValue(":time", time());
        if($insert_credentials->execute())
		{
			die(json_encode(array("status" => "success")));
		}
		else
		{
			die(json_encode(array("status" => "failed", "detail" => "failed to add to db")));
		}
    }
	
    if($option == "get_list")
	{
		$get_credentials = $conn->prepare("SELECT * FROM info");
		$creds = array("status" => "success", "values" => array());
		if($get_credentials->execute())
		{
			while ($row = $get_credentials->fetch(PDO::FETCH_ASSOC)) {
				$creds["values"][] = $row;
			}
			die(json_encode($creds));
		}
		else
		{
			die(json_encode(array("status" => "failed", "detail" => "failed when getting from db")));
		}
	}
?>