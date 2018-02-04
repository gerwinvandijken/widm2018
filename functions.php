<?php
// vul array met dag-namen en maand-namen
$dagen = array("zondag", "maandag", "dinsdag", "woensdag", "donderdag", "vrijdag", "zaterdag");
$maanden = array("januari", "februari", "maart", "april", "mei", "juni", "juli", "augustus", "september", "oktober", "november", "december");

function PrintAfleveringDatumTijd($aflevering)
{
	global $dagen, $maanden;
	
	$dayNr = date_format($aflevering->startTijd, 'w');	// 0=zondag, 1=maandag, etcetera
	$monthNr = date_format($aflevering->startTijd, 'n') - 1;
	$tijd = date_format($aflevering->startTijd, 'H:i') . " uur";

	$dag = $dagen[$dayNr];
	$startDatumTijd = $dag . " " . date_format($aflevering->startTijd, 'j') . " " . $maanden[$monthNr] . " " . date_format($aflevering->startTijd, 'Y') . ", " . $tijd;

	echo "<h4>$startDatumTijd</h4>\n";
}

function PrintKandidaat($kandidaat, $titel)
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
?>