<?php

	// $host = 'localhost';
	// $user = 'root';
	// $password = '';
	// $database = 'wedding_db';


	// host
	$host = '62.149.150.170';
	$user = 'Sql922749';
	$password = 'b108ry364k';
	$database = "Sql922749_1";

	//dati per unbit
	$con = mysqli_connect($host,$user,$password, $database) or die ("Non riesco a connettermi al DB");

	//seleziono il database
	mysqli_select_db($con, $database) or die ("Non riesco a selezionare il database");
?>
