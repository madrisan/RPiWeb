<?php
session_start();

if($_SESSION['username'] == ""){
	die("You are not logged in");
}
	system('sudo /sbin/reboot');
?>
	
	<center> Your Raspberry Pi is now rebooting! </center>
