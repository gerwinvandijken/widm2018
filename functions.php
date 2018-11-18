<?php
// vul array met dag-namen en maand-namen
$dagen = array("zondag", "maandag", "dinsdag", "woensdag", "donderdag", "vrijdag", "zaterdag");
$maanden = array("januari", "februari", "maart", "april", "mei", "juni", "juli", "augustus", "september", "oktober", "november", "december");

function PrintAfleveringDatumTijd($aflevering)
{
	global $dagen, $maanden;
	
	$afleveringStartTijd = $aflevering->getStartTijd();
	$dayNr = date_format($afleveringStartTijd, 'w');	// 0=zondag, 1=maandag, etcetera
	$monthNr = date_format($afleveringStartTijd, 'n') - 1;
	$tijd = date_format($afleveringStartTijd, 'H:i') . " uur";

	$dag = $dagen[$dayNr];
	$startDatumTijd = $dag . " " . date_format($afleveringStartTijd, 'j') . " " . $maanden[$monthNr] . " " . date_format($afleveringStartTijd, 'Y') . ", " . $tijd;

	echo "<h4>$startDatumTijd</h4>\n";
}

function PrintKandidaat($kandidaat, $titel)
{
	echo "<div class='kandidaat-box'>\n";
	echo "<h4>$titel</h4>\n";
	if (is_null($kandidaat))
		echo "<img src='./images/widm.png' alt='geen $titel geselecteerd' title='geen $titel geselecteerd'><br />\n";
	else {
		$volledigeNaam = $kandidaat->getVoornaam() . " " . $kandidaat->getAchternaam();
		echo "<img src='./images/" . strtolower($kandidaat->getVoornaam()) . ".jpg' alt='$volledigeNaam' title='$volledigeNaam'><br />\n";
		echo "<span>$volledigeNaam</span><br />\n";
	}
	echo "</div>\n";
}
?>