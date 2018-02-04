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

		$('.aflevering.gestart').hide();
	} );
</script>

<div>
<hr/>
<span class='titel' id="displayAll">Toon alles</span>&nbsp;|&nbsp;
<span class='titel' id="displayOnlyNext">Toon alleen eerstvolgende</span>
<hr/>
</div>

<?php
// deelnemer id set?
if (!isset($_GET["id"]))
{
	header("Location: deelnemers.php");
	exit();
}

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

// get all voorspellingen
$voorspellingen = Database::getVoorspellingen($id);

echo "<h1 class='titel'>Voorspellingen van $deelnemer->voornaam</h1>\n";

$currentDateTime = new DateTime('now');
foreach ($voorspellingen as $voorspelling)
{
	$aflevering = $afleveringen['id-' . $voorspelling->afleveringId];

	$afvaller = null;
	$winnaar = null;
	$mol = null;

	$afleveringGestart = ($currentDateTime > $aflevering->startTijd);

	if ($voorspelling->afvallerId != 0)
		$afvaller = $kandidaten['id-' . $voorspelling->afvallerId];
	if ($voorspelling->winnaarId != 0)
		$winnaar = $kandidaten['id-' . $voorspelling->winnaarId];
	if ($voorspelling->molId != 0)
		$mol = $kandidaten['id-' . $voorspelling->molId];

	// print titel (might be '?')
	echo "<h2 class='titel'>Aflevering $aflevering->id. '$aflevering->titel'</h3>\n";

	// container voor (complete) aflevering
	if ($afleveringGestart)
		echo "<div class='aflevering gestart'>";
	else
		echo "<div class='aflevering eerstvolgende'>";

	PrintAfleveringDatumTijd($aflevering);

	// afvaller
	PrintKandidaat($afvaller, "afvaller");

	// winnaar		
	PrintKandidaat($winnaar, "winnaar");

	// mol
	PrintKandidaat($mol, "mol");

	if (!$afleveringGestart)
	{
		if ($deelnemerLoggedIn)
			echo "<p><a href='edit_deelnemer.php'>wijzig afvaller/winnaar/mol</a></p>\n";

		break;
	}
	
	echo "</div>\n";	// div voor (complete) afdeling
}

include("footer.php");
?>