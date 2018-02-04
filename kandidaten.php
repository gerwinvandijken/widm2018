<?php
include("header.php");
require_once('classes/database.php');

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

foreach ($kandidaten as $kandidaat)
{
	echo "<div class='kandidaat-box'>\n";
	$volledigeNaam = $kandidaat->voornaam . " " . $kandidaat->achternaam;
	if ($kandidaat->afgevallen)
	{
		echo "<img src='./images/" . strtolower($kandidaat->voornaam) . "-disabled.jpg' alt='$volledigeNaam (afgevallen)' title='$volledigeNaam (afgevallen)'><br />\n";
	}
	else
	{
		echo "<img src='./images/" . strtolower($kandidaat->voornaam) . ".jpg' alt='$volledigeNaam' title='$volledigeNaam'><br />\n";
	}
	echo "<span>$volledigeNaam</span><br />\n";
	echo "<span>$kandidaat->beroep</span>\n";
	echo "</div>\n";
}

include("footer.php");
?>