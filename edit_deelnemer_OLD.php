<?php
include("header.php");
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
}

function DisplayGeselecteerdeKandidaat($kandidaat, $titel)
{
	echo "<div class='kandidaat-box'>\n";
	echo "<h4>$titel</h4>\n";
	if (is_null($kandidaat))
		echo "<img src='./images/widm.png' alt='geen $titel geselecteerd' title='geen $titel geselecteerd'><br />\n";
	else {
		$volledigeNaam = $kandidaat->voornaam . " " . $kandidaat->achternaam;
		echo "<img src='./images/" . strtolower($kandidaat->voornaam) . ".jpg' alt='$volledigeNaam' title='$volledigeNaam'><br />\n";
		echo "<span>$volledigeNaam</span><br />\n";
	}
	echo "</div>\n";
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

//echo "<h2>Edit voorspellingen van $loggedInDeelnemer->voornaam</h2>";

// get all voorspellingen
$voorspellingen = Database::getVoorspellingen($loggedInDeelnemer->id);

// vul array met dag-namen en maand-namen
$dagen = array("zondag", "maandag", "dinsdag", "woensdag", "donderdag", "vrijdag", "zaterdag");
$maanden = array("januari", "februari", "maart", "april", "mei", "juni", "juli", "augustus", "september", "oktober", "november", "december");

$currentDateTime = new DateTime('now');
//$eerstKomendeAflevering = false;
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

	$dayNr = date_format($aflevering->startTijd, 'w');	// 0=zondag, 1=maandag, etcetera
	$monthNr = date_format($aflevering->startTijd, 'n') - 1;
	$tijd = date_format($aflevering->startTijd, 'H:i') . " uur";

	$dag = $dagen[$dayNr];
	$startDatumTijd = $dag . " " . date_format($aflevering->startTijd, 'j') . " " . $maanden[$monthNr] . " " . date_format(	$aflevering->startTijd, 'Y') . ", " . $tijd;

	echo "<h2>$startDatumTijd</h2>";

	// print titel?
	if (strlen($aflevering->titel) > 1)		// might be '?'
		echo "<h4>$aflevering->titel</h4>";

	// geselecteerde afvaller
	echo "<h4>Geselecteerde afvaller</h4>";
	DisplayGeselecteerdeKandidaat($afvaller, "afvaller");

	// selectie mogelijkheid voor afvaller
	echo "<h4>Selecteer afvaller</h4>\n";
	DisplayKandidaten($kandidaten, $aflevering->id, "update-afvaller");

	// geselecteerde winnaar
	echo "<h4>Geselecteerde winnaar</h4>";
	DisplayGeselecteerdeKandidaat($winnaar, "winnaar");

	// selectie mogelijkheid voor winnaar
	echo "<h4>Selecteer winnaar</h4>\n";
	DisplayKandidaten($kandidaten, $aflevering->id, "update-winnaar");

	// geselecteerde mol
	echo "<h4>Geselecteerde mol</h4>";
	DisplayGeselecteerdeKandidaat($mol, "mol");

	// selectie mogelijkheid voor mol
	echo "<h4>Selecteer mol</h4>\n";
	DisplayKandidaten($kandidaten, $aflevering->id, "update-mol");

	// volgende afleveringen kunnen pas gewijzigd worden als eerstvolgende aflevering is geweest...
	break;
}

echo "</form>\n";

include("footer.php");
?>