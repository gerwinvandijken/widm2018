<?php
include("header.php");
require_once('classes/database.php');

$db = Database::getInstance();

// get all kandidaten
$kandidaten = $db->getKandidaten();

// get all afleveringen
$afleveringen = $db->getAfleveringen();

// update status van (afgevallen) kandidaten
foreach ($afleveringen as $aflevering)
{
	$afvallerId = $aflevering->getAfvallerId();
	if ($afvallerId != 0)
	{
		$kandidaat = $kandidaten['id-' . $afvallerId];
		$kandidaat->setAfgevallen(true);
	}
}

// uitslag...
$molId = 1;			// 1=Jan, 2=Olcay, 4=Ruben
$winnaarId = 4;
$verliezerId = 2;

foreach ($kandidaten as $kandidaat)
{
	echo "<div class='kandidaat-box'>\n";
	$volledigeNaam = $kandidaat->getVoornaam() . " " . $kandidaat->getAchternaam();
	$voornaam = $kandidaat->getVoornaam();
	if ($kandidaat->getAfgevallen())
	{
		echo "<img src='./images/" . strtolower($voornaam) . "-disabled.jpg' alt='$volledigeNaam (afgevallen)' title='$volledigeNaam (afgevallen)'><br />\n";
	}
	else
	{
		$kandidaatId = $kandidaat->getId();
		if ($kandidaatId == $molId)
		{
			echo "<img src='./images/" . strtolower($voornaam) . "-mol.jpg' alt='$volledigeNaam' title='$volledigeNaam'><br />\n";
		}
		else if ($kandidaatId == $verliezerId)
		{
			echo "<img src='./images/" . strtolower($voornaam) . "-verliezer.jpg' alt='$volledigeNaam' title='$volledigeNaam'><br />\n";
		}
		else if ($kandidaatId == $winnaarId)
		{
			echo "<img src='./images/" . strtolower($voornaam) . "-winnaar.jpg' alt='$volledigeNaam' title='$volledigeNaam'><br />\n";
		}
		else
		{
			echo "<img src='./images/" . strtolower($voornaam) . ".jpg' alt='$volledigeNaam' title='$volledigeNaam'><br />\n";
		}
	}
	echo "<span>$volledigeNaam</span><br />\n";
	echo "<span>{$kandidaat->getBeroep()}</span>\n";
	echo "</div>\n";
}

include("footer.php");
?>