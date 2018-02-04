<?php
include("header.php");
require_once('classes/database.php');
?>

<script type="text/javascript">

function SelecteerAfvaller(aflevering_id, afvaller_id){
	var form = document.getElementById('afleveringform');
	document.getElementById("afleveringid").value = aflevering_id;
	document.getElementById("afvallerid").value = afvaller_id;
	form.submit();
}
</script>

<form method='post' id='afleveringform' action='afleveringen.php'>
<input type='hidden' name='afvallerid' id='afvallerid' value='' />
<input type='hidden' name='afleveringid' id='afleveringid' value='' />

<?php

// need to update aflevering (afvaller kandidaat)?
if (isset($_POST["afleveringid"]) && isset($_POST["afvallerid"])) {
	$afleveringid = $_POST["afleveringid"];
	$afvallerid = $_POST["afvallerid"];
	Database::updateAfvaller($afleveringid, $afvallerid);
}

// get all afleveringen
$afleveringen = Database::getAfleveringen();

// get all kandidaten
$kandidaten = Database::getKandidaten();

$superUserLoggedIn = false;
if($_SESSION['s_logged_n'] == 'true')
{
	$loggedInDeelnemer = unserialize($_SESSION['s_deelnemer']);
	if ($loggedInDeelnemer->id == 1)		// Laura van Dijken
		$superUserLoggedIn = true;
	elseif ($loggedInDeelnemer->id == 3)	// Marian Berbee
		$superUserLoggedIn = true;
}

// vul array met dag-namen en maand-namen
$dagen = array("zondag", "maandag", "dinsdag", "woensdag", "donderdag", "vrijdag", "zaterdag");
$maanden = array("januari", "februari", "maart", "april", "mei", "juni", "juli", "augustus", "september", "oktober", "november", "december");

$currentDateTime = new DateTime('now');

// toon de afleveringen
foreach ($afleveringen as $aflevering)
{
	$dayNr = date_format($aflevering->startTijd, 'w');	// 0=zondag, 1=maandag, etcetera
	$monthNr = date_format($aflevering->startTijd, 'n') - 1;
	$tijd = date_format($aflevering->startTijd, 'H:i') . " uur";

	$dag = $dagen[$dayNr];
	$startDatumTijd = $dag . " " . date_format($aflevering->startTijd, 'j') . " " . $maanden[$monthNr] . " " . date_format($aflevering->startTijd, 'Y') . ", " . $tijd;
	
	echo "<h2>$startDatumTijd</h2>\n";
	
	// print titel?
	if (strlen($aflevering->titel) > 1)		// might be '?'
		echo "<h4 class='titel'>Aflevering $aflevering->id. '$aflevering->titel'</h4>\n";

	$afleveringGestart = ($currentDateTime > $aflevering->startTijd);
		
	// toon afvaller van deze aflevering
	if ($aflevering->afvallerId != 0)
	{
		$afvaller = $kandidaten['id-' . $aflevering->afvallerId];
		
		echo "<div class='kandidaat-box'>\n";
		$volledigeNaam = $afvaller->voornaam . " " . $afvaller->achternaam;
		echo "<img src='./images/" . strtolower($afvaller->voornaam) . ".jpg' alt='$volledigeNaam' title='$volledigeNaam'><br />\n";
		echo "<span>$volledigeNaam</span>\n";
		echo "</div>\n";
	}
	elseif ($afleveringGestart)
	{
		echo "<div class='kandidaat-box'>\n";
		echo "<img src='./images/widm.png' alt='geen mol geselecteerd' title='geen mol geselecteerd'><br />\n";
		echo "<span>?</span>\n";
		echo "</div>\n";
	}

	// stop als (deze 'huidige') aflevering nog niet begonnen is	
	if (!$afleveringGestart)
	{
		echo "<h4>Aflevering nog niet begonnen...</h4>\n";
		break;
	}
	elseif ($superUserLoggedIn)
	{			
		// super user kan een kandidaat als afvaller kiezen (zodra aflevering gestart is)
		echo "<h4>Selecteer afvaller</h4>\n";

		// select-optie voor 'geen afvaller'
		echo "<div class='kandidaat1-box'>\n";
		echo "<img onclick='SelecteerAfvaller(" . $aflevering->id . ", 0)' src='./images/widm.png' alt='geen afvaller'><br />\n";
		echo "</div>\n";

		// display all WIDM kandidaten
		foreach ($kandidaten as $kandidaat)
		{
			echo "<div class='kandidaat1-box'>\n";
			$volledigeNaam = $kandidaat->voornaam . " " . $kandidaat->achternaam;
			echo "<img onclick='SelecteerAfvaller(" . $aflevering->id . ", " . $kandidaat->id . ")' src='./images/" . strtolower($kandidaat->voornaam) . ".jpg' alt='$volledigeNaam' title='$volledigeNaam'><br />\n";
			echo "</div>\n";
		}
	}
}

echo "</form>\n";

include("footer.php");
?>