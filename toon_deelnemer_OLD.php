<?php>
include("header.php");
require_once('classes/database.php');

// deelnemer id set?
if (isset($_GET["id"]))
{
	// get deelnemer
	$id = $_GET["id"];
	$deelnemer = Database::getDeelnemerById($id);

	// is ingelogde gebruiker dezelfde als geselecteerde deelnemer?
	$deelnemerLoggedIn = false;
	if($_SESSION['s_logged_n'] == 'true')
	{
		$loggedInDeelnemer = unserialize($_SESSION['s_deelnemer']);
		$deelnemerLoggedIn = ($deelnemer->id == $loggedInDeelnemer->id);
	}
	
	// get all kandidaten
	$kandidaten = Database::getKandidaten();

	// get all afleveringen
	$afleveringen = Database::getAfleveringen();

	echo "<h2>Voorspellingen van $deelnemer->voornaam</h2>\n";
	
	// get all voorspellingen
	$voorspellingen = Database::getVoorspellingen($id);
	
	// vul array met dag-namen en maand-namen
	$dagen = array("zondag", "maandag", "dinsdag", "woensdag", "donderdag", "vrijdag", "zaterdag");
	$maanden = array("januari", "februari", "maart", "april", "mei", "juni", "juli", "augustus", "september", "oktober", "november", "december");

	$currentDateTime = new DateTime('now');
	$eerstKomendeAflevering = false;
	foreach ($voorspellingen as $voorspelling)
	{
		$aflevering = $afleveringen['id-' . $voorspelling->afleveringId];

		$afvaller = null;
		$winnaar = null;
		$mol = null;

		$magEditen = false;
		if (!$eerstKomendeAflevering) {
			if ($aflevering->startTijd > $currentDateTime) {
				$eerstKomendeAflevering = true;
				$magEditen = $deelnemerLoggedIn;
			}
		}

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

		echo "<h2>$startDatumTijd</h2>\n";
		
		// print titel?
		if (strlen($aflevering->titel) > 1)		// might be '?'
			echo "<h4>$aflevering->titel</h4>\n";

		// afvaller		
		echo "<div class='kandidaat-box'>\n";
		echo "<h4>Afvaller</h4>\n";
		if (is_null($afvaller))
			echo "<img src='./images/widm.png' alt='geen afvaller geselecteerd' title='geen afvaller geselecteerd'><br />\n";
		else {
			$volledigeNaam = $afvaller->voornaam . " " . $afvaller->achternaam;
			echo "<img src='./images/" . strtolower($afvaller->voornaam) . ".jpg' alt='" . $volledigeNaam . "' title='" . $volledigeNaam . "'><br />\n";
			echo "<span>" . $volledigeNaam . "</span><br />\n";
		}
		echo "</div>\n";

		// winnaar		
		echo "<div class='kandidaat-box'>\n";
		echo "<h4>Winnaar</h4>\n";
		if (is_null($winnaar))
			echo "<img src='./images/widm.png' alt='geen winnaar geselecteerd' title='geen winnaar geselecteerd'><br />\n";
		else {
			$volledigeNaam = $winnaar->voornaam . " " . $winnaar->achternaam;
			echo "<img src='./images/" . strtolower($winnaar->voornaam) . ".jpg' alt='" . $volledigeNaam . "' title='" . $volledigeNaam . "'><br />\n";
			echo "<span>" . $volledigeNaam . "</span><br />\n";
		}
		echo "</div>\n";

		// mol
		echo "<div class='kandidaat-box'>\n";
		echo "<h4>Mol</h4>\n";
		if (is_null($mol))
			echo "<img src='./images/widm.png' alt='geen mol geselecteerd' title='geen mol geselecteerd'><br />\n";
		else {
			$volledigeNaam = $mol->voornaam . " " . $mol->achternaam;
			echo "<img src='./images/" . strtolower($mol->voornaam) . ".jpg' alt='" . $volledigeNaam . "' title='" . $volledigeNaam . "'><br />\n";
			echo "<span>" . $mol->voornaam . " " . $mol->achternaam . "</span><br />\n";
		}
		echo "</div>\n";

		if ($magEditen)
			echo "<p><a href='edit_deelnemer.php'>wijzig afvaller/winnaar/mol</a></p>\n";
		
		if ($eerstKomendeAflevering)
			break;
	}
}
else
{
	header("Location: deelnemers.php");
}

include("footer.php");
?>