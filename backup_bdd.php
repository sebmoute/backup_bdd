<?php
//------------------------------------------|||>
//                backup BDD                |||>
//       Ce cron est appelé toutes les 2H   |||>
//------------------------------------------|||>

// init
set_time_limit(200);
$success = false;
$localPath = '../../tmp/';

// BDD credentials
$database['db_user'] = '';
$database['db_password'] = '';
$database['db_name'] = '';
$database['sql_file'] = "dump_" . date('Y-m-d--G-i-s') . "_{$database['db_name']}.sql";

// SFTP credentials
$dataFile = $database['sql_file'] . '.gz';
$ftpServer = '';
$ftpUsername = '';
$ftpPassword = '';
$ftpRemoteDir = '/backup_bdd/';
$ftpPort = 22;


//Create SQL dump and gzip the dumped file
exec("mysqldump -u {$database['db_user']} -p{$database['db_password']} --allow-keywords --add-drop-table --complete-insert --hex-blob --quote-names --triggers {$database['db_name']} > " . $localPath . "{$database['sql_file']}");
if (file_exists($localPath . $database['sql_file'])) {
	$output = '* Fichier généré => ' . $localPath . $database['sql_file'] . '<br>';
	try {
		$output .= '* Compression du fichier : ';
		exec("gzip " . $localPath . "{$database['sql_file']}");
		$output .= 'OK<br>';
	} catch (Exception $e) {
		$output .= 'KO -> Message d\'erreur : ' . $e->getMessage();
		$output .= '<br>';
		$output .= 'Code d\'erreur : ' . $e->getCode();
		$output .= '<br>';
		$output .= $e->getFile();
	}
}

//UPLOAD
$output .= '* Destination du fichier à uploader :<br>' . '/backup_bdd' . '/' . basename($dataFile) . '<br>';

// (B) CONNECT TO FTP SERVER
$ftp = ftp_connect($ftpServer) or die("Echec de la connexion à  $ftpServer");

// (C) LOGIN & UPLOAD
if (ftp_login($ftp, $ftpUsername, $ftpPassword)) {
	if(ftp_put($ftp, $ftpRemoteDir . $dataFile, $localPath . $dataFile, FTP_BINARY)) {
		$success = true;
		$output .= "[OK] Backup déposé sur $ftpRemoteDir <br>";
		} else {
		$output .= "[KO] Erreur uploading $ftpRemoteDir <br>";
	}
} else {
	echo "[KO] Echec de la connexion : identifiants invalides <br>";
}

// (D) CLOSE FTP CONNECTION
ftp_close($ftp);

// delete local file
$output .= '* Suppression du fichier de sauvegarde du serveur : ';
$output .= (unlink($localPath . $dataFile)) ? 'OK' : 'KO';

// result
if (!$success) {
	/* Set you email alert here */
}
?>