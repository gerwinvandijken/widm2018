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

$db = Database::getInstance();

$superUserLoggedIn = false;
if($_SESSION['s_logged_n'] == 'true')
{
	$loggedInDeelnemer = unserialize($_SESSION['s_deelnemer']);

	// need to update deelnemer?
	if (isset($_POST["afleveringid"]) && isset($_POST["action"]) && isset($_POST["kandidaatid"])) {
		$afleveringid = $_POST["afleveringid"];
		$action = $_POST["action"];
		$kandidaatid = $_POST["kandidaatid"];

		$deelnemerId = $loggedInDeelnemer->getId();
		if ($action == "update-afvaller")
			$db->updateDeelnemerAfvaller($afleveringid, $deelnemerId, $kandidaatid);
		elseif ($action == "update-winnaar")
			$db->updateDeelnemerWinnaar($afleveringid, $deelnemerId, $kandidaatid);
		elseif ($action == "update-mol")
			$db->updateDeelnemerMol($afleveringid, $deelnemerId, $kandidaatid);
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
		$volledigeNaam = $kandidaat->getVoornaam() . " " . $kandidaat->getAchternaam();
		$voornaam = $kandidaat->getVoornaam();
		if ($kandidaat->getAfgevallen())
		{
			echo "<img src='./images/" . strtolower($voornaam) . "-disabled.jpg' alt='$volledigeNaam (afgevallen)' title='$volledigeNaam (afgevallen)'><br />\n";
		}
		else
		{
			echo "<img onclick='SelecteerKandidaat($afleveringId, \"$action\", {$kandidaat->getId()})' src='./images/" . strtolower($voornaam) . ".jpg' alt='$volledigeNaam' title='$volledigeNaam'><br />\n";
		}
		echo "<span>$voornaam</span><br />\n";
		echo "</div>\n";
	}
}

// get all kandidaten
$kandidaten = $db->getKandidaten();

// get all afleveringen
$afleveringen = $db->getAfleveringen();

// update status van (afgevallen) kandidaten
foreach ($afleveringen as $aflevering)
{
	if ($aflevering->getAfvallerId() != 0)
	{
		$kandidaat = $kandidaten['id-' . $aflevering->getAfvallerId()];
		$kandidaat->setAfgevallen(true);
	}
}

echo "<h1 class='titel'>Edit voorspellingen van {$loggedInDeelnemer->getVoornaam()}</h1>\n";

// get all voorspellingen
$voorspellingen = $db->getVoorspellingen($loggedInDeelnemer->getId());

$currentDateTime = new DateTime('now');
foreach ($voorspellingen as $voorspelling)
{
	$aflevering = $afleveringen['id-' . $voorspelling->getAfleveringId()];

	// aflevering al begonnen? dan skippen
	if ($aflevering->getStartTijd() < $currentDateTime) {
		continue;
	}

	$afvaller = null;
	$winnaar = null;
	$mol = null;

	// toon voorspelling van deze (eerstvolgende) aflevering
	if ($voorspelling->getAfvallerId() != 0)
		$afvaller = $kandidaten['id-' . $voorspelling->getAfvallerId()];
	if ($voorspelling->getWinnaarId() != 0)
		$winnaar = $kandidaten['id-' . $voorspelling->getWinnaarId()];
	if ($voorspelling->getMolId() != 0)
		$mol = $kandidaten['id-' . $voorspelling->getMolId()];

	// print titel (might be '?')
	echo "<h2 class='titel'>Aflevering {$aflevering->getId()}. '{$aflevering->getTitel()}'</h2>\n";

	PrintAfleveringDatumTijd($aflevering);

	// geselecteerde afvaller
	echo "<h4>Geselecteerde afvaller</h4>";
	PrintKandidaat($afvaller, "afvaller");

	// selectie mogelijkheid voor afvaller
	echo "<h4>Selecteer afvaller</h4>\n";
	DisplayKandidaten($kandidaten, $aflevering->getId(), "update-afvaller");

	// geselecteerde winnaar
	echo "<h4>Geselecteerde winnaar</h4>";
	PrintKandidaat($winnaar, "winnaar");

	// selectie mogelijkheid voor winnaar
	echo "<h4>Selecteer winnaar</h4>\n";
	DisplayKandidaten($kandidaten, $aflevering->getId(), "update-winnaar");

	// geselecteerde mol
	echo "<h4>Geselecteerde mol</h4>";
	PrintKandidaat($mol, "mol");

	// selectie mogelijkheid voor mol
	echo "<h4>Selecteer mol</h4>\n";
	DisplayKandidaten($kandidaten, $aflevering->getId(), "update-mol");

	// volgende afleveringen kunnen pas gewijzigd worden als eerstvolgende aflevering is geweest...
	break;
}

echo "</form>\n";

include("footer.php");
?>