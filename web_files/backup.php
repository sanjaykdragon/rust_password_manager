<?php
    
    function send_email($title, $log_message, $filename)
    {
    $receiving_email = "sanjaykdragon@gmail.com";
	
	$semi_rand = md5(time()); 
	$mime_boundary = "==Multipart_Boundary_x{$semi_rand}x"; 
	
    $headers = 'From: ' . 'mailer@password_mgr.gov' . "\r\n" .
        'Reply-To: ' . $receiving_email . "\r\n" .
        'X-Mailer: PHP/' . phpversion() . "\nMIME-Version: 1.0\n"
		. "Content-Type: multipart/mixed;\n"
		. " boundary=\"{$mime_boundary}\"";
		
		$message = "--{$mime_boundary}\n" . "Content-Type: text/html; charset=\"UTF-8\"\n" .
		"Content-Transfer-Encoding: 7bit\n\n" . $log_message . "\n\n"; 
		
		
		$file = $filename;
		
		$message .= "--{$mime_boundary}\n";
        $fp =    @fopen($file,"rb");
        $data =  @fread($fp,filesize($file));

        @fclose($fp);
        $data = chunk_split(base64_encode($data));
        $message .= "Content-Type: application/octet-stream; name=\"".basename($file)."\"\n" . 
        "Content-Description: ".basename($file)."\n" .
        "Content-Disposition: attachment;\n" . " filename=\"".basename($file)."\"; size=".filesize($file).";\n" . 
        "Content-Transfer-Encoding: base64\n\n" . $data . "\n\n";
		
		$message .= "--{$mime_boundary}--";
		
		@mail($receiving_email, $title, $message, $headers);
    }
	

	//https://phppot.com/php/how-to-backup-mysql-database-using-php/
    $server_username = "root";
    $server_password = "";
    $server_dbname = "pw_mgr";
    $server_server = "localhost";

    try {
        $conn = new PDO('mysql:host=' . $server_server . ';dbname=' . $server_dbname, $server_username, $server_password, [PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]);
    } catch (Exception $e) {
        die('Error connecting to database: ' . $e);
    }


// Get All Table Names From the Database
$tables = array();
$sql = "SHOW TABLES";
$result = $conn->prepare($sql);
$result->execute();

while ($row = $result->fetch(PDO::FETCH_BOTH)) {
    $tables[] = $row[0];
}

$sqlScript = "";
foreach ($tables as $table) {
    
    // Prepare SQLscript for creating table structure
    $query = "SHOW CREATE TABLE $table";
    $result = $conn->prepare($query);
	$result->execute();
    $row = $result->fetch(PDO::FETCH_BOTH);
    
    $sqlScript .= "\n\n" . $row[1] . ";\n\n";
    
    
    $query = "SELECT * FROM $table";
    $result = $conn->prepare($query);
	$result->execute();
    
    $columnCount = $result->columnCount();
    
    // Prepare SQLscript for dumping data for each table
    for ($i = 0; $i < $columnCount; $i ++) {
        while ($row = $result->fetch(PDO::FETCH_BOTH)) {
            $sqlScript .= "INSERT INTO $table VALUES(";
            for ($j = 0; $j < $columnCount; $j ++) {
                $row[$j] = $row[$j];
                
                if (isset($row[$j])) {
                    $sqlScript .= '"' . $row[$j] . '"';
                } else {
                    $sqlScript .= '""';
                }
                if ($j < ($columnCount - 1)) {
                    $sqlScript .= ',';
                }
            }
            $sqlScript .= ");\n";
        }
    }
    
    $sqlScript .= "\n"; 
}

if(!empty($sqlScript))
{
    // Save the SQL script to a backup file
    $backup_file_name = $server_dbname . '_backup_' . time() . '.sql';
    $fileHandler = fopen($backup_file_name, 'w+');
    $number_of_lines = fwrite($fileHandler, $sqlScript);
    fclose($fileHandler); 
	
	send_email("backup", "password backup.", $backup_file_name);
}