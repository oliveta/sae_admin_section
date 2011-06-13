<?php
include_once('../config.php');
$host = $txpcfg['host'];
$db	= $txpcfg['db'];
$user = $txpcfg['user'];
$pass = $txpcfg['pass'];
$client_flags = isset($txpcfg['client_flags']) ? $txpcfg['client_flags'] : 0;
$link = @mysql_connect($host, $user, $pass);
	
		if (!$link) die(db_down());
		$i=0;
		mysql_select_db($db);
		$tab=$_GET['tab'];
foreach(explode(",",$_GET['sectionorder']) as $value) {
$query=mysql_query("update ".$tab." set sectionorder=".$i." where name='".$value."'");
if (!$query)
{

}
else {

}
$i++;
}




?>
