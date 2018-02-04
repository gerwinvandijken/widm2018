<?php>
include("header.php");
require_once('classes/database.php');

function AfleveringIngevuld($afleveringId, $deelnemerId)
{
	// TODO: extra Database-function om alleen (deelnemer)voorspellingen van bepaalde aflevering op te vragen... 
	$voorspellingen = Database::getVoorspellingen($deelnemerId);

	foreach ($voorspellingen as $voorspelling)
	{
		if ($voorspelling->afleveringId == $afleveringId)
		{	
			if (($voorspelling->afvallerId != 0) && ($voorspelling->winnaarId != 0) && ($voorspelling->molId != 0))
				return true;
			break;
		}
	}	
	
	return false;
}

function UpdatePunten($deelnemers, $afleveringen)
{
	$aantalDeelnemers = 0;
	$aantalUpdates = 0;

	foreach ($deelnemers as $deelnemer)
	{
		$deelnemerPunten = 0;

		// vraag alle voorspellingen van gebruiker op
		$voorspellingen = Database::getVoorspellingen($deelnemer->id);

		// doorloop alle afleveringen
		foreach ($afleveringen as $aflevering)
		{
			// skip alle afleveringen zonder aangewezen afvaller
			if ($aflevering->afvallerId == 0)
				continue;	// kan ook 'break' omdat de afleveringen op volgorde zijn...

			// neem voorspelling (van deelnemer) behorende bij huidige aflevering
			$deVoorspelling = null;
			foreach ($voorspellingen as $voorspelling)
			{
				if ($voorspelling->afleveringId == $aflevering->id)
				{
					$deVoorspelling = $voorspelling;
					break;
				}
			}

			// deelnemer heeft geen voorspelling voor huidige aflevering?
			if (is_null($deVoorspelling))
				continue;

			// deelnemer heeft geen afvaller geselecteerd voor huidige aflevering?
			if ($deVoorspelling->afvallerId == 0)
				continue;

			//$afvaller = $kandidaten['id-' . $aflevering->afvallerId];
			if ($aflevering->afvallerId == $deVoorspelling->afvallerId)
			{
				$deelnemerPunten = $deelnemerPunten + 1;
			}
		}

		// update deelnemer
		if ($deelnemer->punten != $deelnemerPunten)
		{
			$deelnemer->punten = $deelnemerPunten;
			Database::updateDeelnemerPunten($deelnemer->id, $deelnemerPunten);
			$aantalUpdates++;
		}

		$aantalDeelnemers++;
	}

	echo "Update klaar ($aantalDeelnemers deelnemers verwerkt, $aantalUpdates geupdate)<br />\n";
}

$superUserLoggedIn = false;
if ($_SESSION['s_logged_n'] == 'true')
{
	$loggedInDeelnemer = unserialize($_SESSION['s_deelnemer']);
	$superUserLoggedIn = $loggedInDeelnemer->superuser;
}

// get all (actieve) deelnemers
$deelnemers = Database::getDeelnemers();

if ($superUserLoggedIn)
{
	// get all afleveringen
	$afleveringen = Database::getAfleveringen();
	
	$action = $_POST['action'];
	if ($action == 'updatescores')
	{
		// update punten van alle deelnemers
		UpdatePunten($deelnemers, $afleveringen);
		echo "<br />\n";

		// als deelnemers op volgorde van punten is opgehaald, 
		// => dan is de volgorde van deelnemers mogelijk niet correct meer...
	}
}

echo "<table>\n";
foreach ($deelnemers as $deelnemer)
{
	echo "<tr>\n";

	echo "<td><a href='./toon_deelnemer.php?id=$deelnemer->id'>$deelnemer->voornaam $deelnemer->achternaam</a></td>\n";
	echo "<td>$deelnemer->punten</td>\n";

	// superuser gets more information
	if ($superUserLoggedIn)
	{
		foreach ($afleveringen as $aflevering)
		{
			if (AfleveringIngevuld($aflevering->id, $deelnemer->id))
				echo "<td style='width:20px; align=right;'><img src='./images/check.png' alt='ingevuld' title='aflevering $aflevering->id ingevuld'></td>\n";
			else
				echo "<td style='width:20px; align=right;'><img src='./images/question.png' alt='nog niet ingevuld' title='aflevering $aflevering->id nog niet ingevuld'></td>\n";
		}
	}
	
	echo "</tr>\n";
}
echo "</table>\n";
?>

<?php
if ($superUserLoggedIn)
{
?>
<form method='post' id='deelnemersform' action='deelnemers.php'>
	<input type="hidden" name="action" value="updatescores">
	<input type="submit" value="Update scores">
</form>
<br />
<?php
}
include("footer.php");
?>