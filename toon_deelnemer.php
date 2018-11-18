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

$db = Database::getInstance();

// get deelnemer
$id = $_GET["id"];
$deelnemer = $db->getDeelnemerById($id);

// is ingelogde gebruiker dezelfde als geselecteerde deelnemer?
$deelnemerLoggedIn = false;
if($_SESSION['s_logged_n'] == 'true')
{
	$loggedInDeelnemer = unserialize($_SESSION['s_deelnemer']);
	$deelnemerLoggedIn = ($deelnemer->getId() == $loggedInDeelnemer->getId());
}

// get all kandidaten
$kandidaten = $db->getKandidaten();

// get all afleveringen
$afleveringen = $db->getAfleveringen();

// get all voorspellingen
$voorspellingen = $db->getVoorspellingen($id);

echo "<h1 class='titel'>Voorspellingen van {$deelnemer->getVoornaam()}</h1>\n";

$currentDateTime = new DateTime('now');
foreach ($voorspellingen as $voorspelling)
{
	$aflevering = $afleveringen['id-' . $voorspelling->getAfleveringId()];

	$afvaller = null;
	$winnaar = null;
	$mol = null;

	$afleveringGestart = ($currentDateTime > $aflevering->getStartTijd());

	if ($voorspelling->getAfvallerId() != 0)
		$afvaller = $kandidaten['id-' . $voorspelling->getAfvallerId()];
	if ($voorspelling->getWinnaarId() != 0)
		$winnaar = $kandidaten['id-' . $voorspelling->getWinnaarId()];
	if ($voorspelling->getMolId() != 0)
		$mol = $kandidaten['id-' . $voorspelling->getMolId()];

	// print titel (might be '?')
	echo "<h2 class='titel'>Aflevering {$aflevering->getId()}. '{$aflevering->getTitel()}'</h3>\n";

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