<?php
session_start();
require_once('classes/deelnemer.php');
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html;charset=ISO-8859-1">
<title>Wie Is De Mol - 2018</title>
<style type="text/css">
@font-face {
    font-family: "MolFont";
    src: url(http://www.vandijkenonline.nl/widm2018/fonts/wie-is-de-mol.ttf) format("truetype");
}
</style>
<link href="./stylesheet/style.css" rel="stylesheet" type="text/css" media="screen" />
</head>

<body>

	<div class="header">
	
		<ul>
			<li><a href="afleveringen.php">Afleveringen</a></li>
			<li><a href="kandidaten.php">WIDM kandidaten</a></li>
			<li><a href="deelnemers.php">Deelnemers</a></li>
<?php
	if($_SESSION['s_logged_n'] == 'true')
	{
		// test
		$loggedInDeelnemer = unserialize($_SESSION['s_deelnemer']);
		$volledigeNaam = $loggedInDeelnemer->getVoornaam() . " " . $loggedInDeelnemer->getAchternaam();
		if ($loggedInDeelnemer->getSuperuser())
			$displayName = "SUPERUSER: " . $volledigeNaam;
		else
			$displayName = $volledigeNaam;
		echo "<li style='float:right'><a href='logout.php'>Uitloggen (" . $displayName . ")</a></li>\n";
	}
	else
	{
		echo "<li style='float:right'><a href='login.php'>Inloggen</a></li>\n";		
	}
?>
		</ul>
	</div>