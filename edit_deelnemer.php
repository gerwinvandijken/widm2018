<?php
include("header.php");
require_once('functions.php');
require_once('classes/database.php');
?>

<script type="text/javascript">

function SelecteerKandidaat(aflevering_id, action, kandidaat_id){
	var form = document.getElementById('editform');
	document.getElementById("afleveringid").value = aflevering_id;
	document.getElementById("action").value = action;
	document.getElementById("kandidaatid").value = kandidaat_id;
	form.submit();
}
</script>

<form method='post' id='editform' action='edit_deelnemer.php'>
<input type='hidden' name='afleveringid' id='afleveringid' value='' />
<input type='hidden' name='action' id='action' value='' />
<input type='hidden' name='kandidaatid' id='kandidaatid' value='' />

<?php

$superUserLoggedIn = false;
if($_SESSION['s_logged_n'] == 'true')
{
	$loggedInDeelnemer = unserialize($_SESSION['s_deelnemer']);

	// need to update deelnemer?
	if (isset($_POST["afleveringid"]) && isset($_POST["action"]) && isset($_POST["kandidaatid"])) {
		$afleveringid = $_POST["afleveringid"];
		$action = $_POST["action"];
		$kandidaatid = $_POST["kandidaatid"];
		
		if ($action == "update-afvaller")
			Database::updateDeelnemerAfvaller($afleveringid, $loggedInDeelnemer->id, $kandidaatid);
		elseif ($action == "update-winnaar")
			Database::updateDeelnemerWinnaar($afleveringid, $loggedInDeelnemer->id, $kandidaatid);
		elseif ($action == "update-mol")
			Database::updateDeelnemerMol($afleveringid, $loggedInDeelnemer->id, $kandidaatid);
	}
}
else
{
	header("Location: deelnemers.php");
	exit();
}

function DisplayKandidaten($kandidaten, $afleveringId, $action)
{
	foreach ($kandidaten as $kandidaat)
	{
		echo "<div class='kandidaat1-box'>\n";
		$volledigeNaam = $kandidaat->voornaam . " " . $kandidaat->achternaam;
		if ($kandidaat->afgevallen)
		{
			echo "<img src='./images/" . strtolower($kandidaat->voornaam) . "-disabled.jpg' alt='$volledigeNaam (afgevallen)' title='$volledigeNaam (afgevallen)'><br />\n";
		}
		else
		{
			echo "<img onclick='SelecteerKandidaat($afleveringId, \"$action\", $kandidaat->id)' src='./images/" . strtolower($kandidaat->voornaam) . ".jpg' alt='$volledigeNaam' title='$volledigeNaam'><br />\n";
		}
		echo "<span>$kandidaat->voornaam</span><br />\n";
		echo "</div>\n";
	}
}

// get all kandidaten
$kandidaten = Database::getKandidaten();

// get all afleveringen
$afleveringen = Database::getAfleveringen();

// update status van (afgevallen) kandidaten
foreach ($afleveringen as $aflevering)
{
	if ($aflevering->afvallerId != 0)
	{
		$kandidaat = $kandidaten['id-' . $aflevering->afvallerId];
		$kandidaat->afgevallen = true;
	}
}

echo "<h1 class='titel'>Edit voorspellingen van $loggedInDeelnemer->voornaam</h1>\n";

// get all voorspellingen
$voorspellingen = Database::getVoorspellingen($loggedInDeelnemer->id);

$currentDateTime = new DateTime('now');
foreach ($voorspellingen as $voorspelling)
{
	$aflevering = $afleveringen['id-' . $voorspelling->afleveringId];

	// aflevering al begonnen? dan skippen
	if ($aflevering->startTijd < $currentDateTime) {
		continue;
	}

	$afvaller = null;
	$winnaar = null;
	$mol = null;

	// toon voorspelling van deze (eerstvolgende) aflevering
	if ($voorspelling->afvallerId != 0)
		$afvaller = $kandidaten['id-' . $voorspelling->afvallerId];
	if ($voorspelling->winnaarId != 0)
		$winnaar = $kandidaten['id-' . $voorspelling->winnaarId];
	if ($voorspelling->molId != 0)
		$mol = $kandidaten['id-' . $voorspelling->molId];

	// print titel (might be '?')
	echo "<h2 class='titel'>Aflevering $aflevering->id. '$aflevering->titel'</h2>\n";

	PrintAfleveringDatumTijd($aflevering);

	// geselecteerde afvaller
	echo "<h4>Geselecteerde afvaller</h4>";
	PrintKandidaat($afvaller, "afvaller");

	// selectie mogelijkheid voor afvaller
	echo "<h4>Selecteer afvaller</h4>\n";
	DisplayKandidaten($kandidaten, $aflevering->id, "update-afvaller");

	// geselecteerde winnaar
	echo "<h4>Geselecteerde winnaar</h4>";
	PrintKandidaat($winnaar, "winnaar");

	// selectie mogelijkheid voor winnaar
	echo "<h4>Selecteer winnaar</h4>\n";
	DisplayKandidaten($kandidaten, $aflevering->id, "update-winnaar");

	// geselecteerde mol
	echo "<h4>Geselecteerde mol</h4>";
	PrintKandidaat($mol, "mol");

	// selectie mogelijkheid voor mol
	echo "<h4>Selecteer mol</h4>\n";
	DisplayKandidaten($kandidaten, $aflevering->id, "update-mol");

	// volgende afleveringen kunnen pas gewijzigd worden als eerstvolgende aflevering is geweest...
	break;
}

echo "</form>\n";

include("footer.php");
?>