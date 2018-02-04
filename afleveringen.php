<?php
include("header.php");
require_once('functions.php');
require_once('classes/database.php');
?>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

<script type="text/javascript">
	$(document).ready( function() {	
		$('h2.titel').click(function () {
				// hide/show next element
				$(this).next().toggle(0);
			} );

		$('#displayAll').click(function () {
			$('.aflevering.gestart').show();
		} );

		$('#displayOnlyNext').click(function () {
			$('.aflevering.gestart').hide();
		} );

		// default: show only 'eerstvolgende aflevering' (hide all 'gestart')
		$('.aflevering.gestart').hide();
	} );

	function SelecteerAfvaller(aflevering_id, afvaller_id) {
		var form = document.getElementById('afleveringform');
		document.getElementById("afleveringid").value = aflevering_id;
		document.getElementById("afvallerid").value = afvaller_id;
		form.submit();
	}
</script>

<form method='post' id='afleveringform' action='afleveringen.php'>
<input type='hidden' name='afvallerid' id='afvallerid' value='' />
<input type='hidden' name='afleveringid' id='afleveringid' value='' />

<div>
<hr/>
<span class='titel' id="displayAll">Toon alles</span>&nbsp;|&nbsp;
<span class='titel' id="displayOnlyNext">Toon alleen eerstvolgende</span>
<hr/>
</div>

<?php

// get all afleveringen
$afleveringen = Database::getAfleveringen();

// get all kandidaten
$kandidaten = Database::getKandidaten();

function PrintAfvaller($aflevering)
{
	global $kandidaten;

	if ($aflevering->afvallerId != 0)
	{
		$afvaller = $kandidaten['id-' . $aflevering->afvallerId];

		echo "<div class='kandidaat-box'>\n";
		$volledigeNaam = $afvaller->voornaam . " " . $afvaller->achternaam;
		echo "<img src='./images/" . strtolower($afvaller->voornaam) . ".jpg' alt='$volledigeNaam' title='$volledigeNaam'><br />\n";
		echo "<span>$volledigeNaam</span>\n";
		echo "</div>\n";
	}
	else
	{
		echo "<div class='kandidaat-box'>\n";
		echo "<img src='./images/widm.png' alt='geen mol geselecteerd' title='geen mol geselecteerd'><br />\n";
		echo "<span>?</span>\n";
		echo "</div>\n";
	}
}

function SelecteerAfvaller($aflevering)
{
	global $kandidaten;
	
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
		echo "<img onclick='SelecteerAfvaller(" . $aflevering->id . ", " . $kandidaat->id . ")' src='./images/" . strtolower(	$kandidaat->voornaam) . ".jpg' alt='$volledigeNaam' title='$volledigeNaam'><br />\n";
		echo "</div>\n";
	}
}

$superUserLoggedIn = false;
if($_SESSION['s_logged_n'] == 'true')
{
	$loggedInDeelnemer = unserialize($_SESSION['s_deelnemer']);
	$superUserLoggedIn = $loggedInDeelnemer->superuser;	
}

if ($superUserLoggedIn)
{
	// need to update aflevering (afvaller kandidaat)?
	if (isset($_POST["afleveringid"]) && isset($_POST["afvallerid"])) {
		$afleveringid = $_POST["afleveringid"];
		$afvallerid = $_POST["afvallerid"];
		Database::updateAfvaller($afleveringid, $afvallerid);
	}
}

// toon de afleveringen (op chronologische volgorde)
$currentDateTime = new DateTime('now');
foreach ($afleveringen as $aflevering)
{
	// print titel (might be '?)
	echo "<h2 class='titel'>Aflevering $aflevering->id. '$aflevering->titel'</h3>\n";

	$afleveringGestart = ($currentDateTime > $aflevering->startTijd);

	// aflevering nog niet gestart?
	if (!$afleveringGestart)
	{
		// aflevering nog niet gestart
		echo "<div class='aflevering eerstvolgende'>";
		PrintAfleveringDatumTijd($aflevering);
		echo "<h4>Aflevering nog niet begonnen...</h4>\n";
		echo "</div>\n";

		// laat niet meer afdelingen zien	
		break;
	}

	// aflevering gestart (of reeds geweest)
	
	// container voor (complete) aflevering
	echo "<div class='aflevering gestart'>";
	
	PrintAfleveringDatumTijd($aflevering);

	// toon afvaller van deze aflevering
	PrintAfvaller($aflevering);

	// superuser kan een mol selecteren
	// (bij alle aflevering die gestart zijn, of  helemaal afgelopen zijn)
	if ($superUserLoggedIn)
	{
		SelecteerAfvaller($aflevering);
	}

	echo "</div>\n";	// div voor (complete) afdeling
}

echo "</form>\n";

include("footer.php");
?>