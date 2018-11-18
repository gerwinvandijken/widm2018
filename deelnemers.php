<?php>
include("header.php");
require_once('classes/database.php');

function GetVoorspelling($voorspellingen, $afleveringId)
{
	$retVoorspelling = NULL;
	foreach ($voorspellingen as $voorspelling)
	{
		if ($voorspelling->getAfleveringId() == $afleveringId)
		{
			$retVoorspelling = $voorspelling;
			break;
		}
	}	
	
	return $retVoorspelling;
}

function UpdatePunten($deelnemers, $afleveringen)
{
	$aantalDeelnemers = 0;
	$aantalUpdates = 0;

	// uitslag...
	$uitslagBekend = true;
	$molId = 1;			// TODO!! (1=Jan, 2=Olcay, 4=Ruben)
	$winnaarId = 4;		// TODO!!

	$db = Database::getInstance();

	foreach ($deelnemers as $deelnemer)
	{
		$deelnemerPunten = 0;

		// vraag alle voorspellingen van gebruiker op
		$voorspellingen = $db->getVoorspellingen($deelnemer->getId());

		// doorloop alle afleveringen
		foreach ($afleveringen as $aflevering)
		{
			// neem voorspelling (van deelnemer) behorende bij huidige aflevering
			$voorspelling = GetVoorspelling($voorspellingen, $aflevering->getId());

			// deelnemer heeft geen voorspelling voor huidige aflevering?
			if (is_null($voorspelling))
				continue;

			// is er een afvaller bij deze aflevering?
			if ($aflevering->getAfvallerId() != 0)
			{
				// was afvaller (van deze aflevering) correct voorspeld?
				if ($aflevering->getAfvallerId() == $voorspelling->getAfvallerId())
				{
					$deelnemerPunten = $deelnemerPunten + 1;	// 1 punt voor juiste afvaller
				}
			}
			
			if ($uitslagBekend)
			{
				if ($voorspelling->getMolId() == $molId)
				{
					$deelnemerPunten = $deelnemerPunten + 2;	// 2 punten voor juiste mol
				}
				if ($voorspelling->getWinnaarId() == $winnaarId)
				{
					$deelnemerPunten = $deelnemerPunten + 1;	// 1 punt voor juiste winnaar
				}
			}
		}

		// update deelnemer
		if ($deelnemer->getPunten() != $deelnemerPunten)
		{
			$deelnemer->setPunten($deelnemerPunten);
			$db->updateDeelnemerPunten($deelnemer->getId(), $deelnemerPunten);
			$aantalUpdates++;
		}

		$aantalDeelnemers++;
	}

	echo "Update klaar ($aantalDeelnemers deelnemers verwerkt, $aantalUpdates geupdate)<br />\n";
}

function DisplayInfo($deelnemer)
{
	global $afleveringen;
	global $kandidaten;

	// uitslag...
	$uitslagBekend = true;
	$molId = 1;			// TODO!! (1=Jan, 2=Olcay, 4=Ruben)
	$winnaarId = 4;		// TODO!!

	$db = Database::getInstance();
	$voorspellingen = $db->getVoorspellingen($deelnemer->getId());

	$currentDateTime = new DateTime('now');
	foreach ($afleveringen as $aflevering)
	{
		$afleveringGestart = ($currentDateTime > $aflevering->getStartTijd());
		$afleveringEindtijd = clone $aflevering->getStartTijd();
		$afleveringEindtijd->modify("+1 hour");
		$afleveringAfgelopen = ($currentDateTime > $afleveringEindtijd);

		$voorspelling = GetVoorspelling($voorspellingen, $aflevering->getId());

		echo "<td>&nbsp;|&nbsp;</td>";
		
		if ($voorspelling->getAfvallerId() == 0)
		{
			echo "<td>-</td>";
		}
		else
		{
			// afvaller van aflevering bekend?
			$afvallerId = $aflevering->getAfvallerId();
			if ($afvallerId != 0)
			{
				// was afvaller (van deze aflevering) correct voorspeld?
				if ($afvallerId == $voorspelling->getAfvallerId())
					echo "<td>1</td>";
				else
					echo "<td>0</td>";
			}
			else
			{
				if (true)	//($afleveringAfgelopen, uitgecommentarieerd tijdens laatste uitzending!!)
				{
					echo "<td>0</td>";	// blijkbaar geen afvaller in deze (afgelopen) aflevering
				}
				else
				{
					echo "<td>?</td>";
				}
			}	
		}

		// heeft deelnemer een winnaar voorspeld (in deze aflevering)?
		if ($voorspelling->getWinnaarId() == 0)
		{
			echo "<td>-</td>";
		}
		else
		{
			if ($uitslagBekend)
			{
				// was winnaar correct voorspeld (in deze aflevering)?
				if ($voorspelling->getWinnaarId() == $winnaarId)
				{
					echo "<td>1</td>";
				}
				else
				{
					echo "<td>0</td>";
				}
			}
			else
			{
				// punten voor winnaar nog mogelijk? (kandidaat niet afgevallen?)
				$kandidaat = $kandidaten['id-' . $voorspelling->getWinnaarId()];
				if ($kandidaat->getAfgevallen())
				{
					echo "<td>0</td>";
				}
				else
				{
					echo "<td>?</td>";
				}
			}
		}

		// heeft deelnemer een mol voorspeld (in deze aflevering)?
		if ($voorspelling->getMolId() == 0)
		{
			echo "<td>-</td>";
		}
		else
		{
			if ($uitslagBekend)
			{
				// was mol correct voorspeld (in deze aflevering)?
				if ($voorspelling->getMolId() == $molId)
				{
					echo "<td>2</td>";
				}
				else
				{
					echo "<td>0</td>";
				}
			}
			else
			{
				// punten voor mol nog mogelijk? (kandidaat niet afgevallen?)
				$kandidaat = $kandidaten['id-' . $voorspelling->getMolId()];
				if ($kandidaat->getAfgevallen())
				{
					echo "<td>0</td>";
				}
				else
				{
					echo "<td>?</td>";
				}
			}
		}
	}
}

$superUserLoggedIn = false;
$userLoggedIn = false;
if ($_SESSION['s_logged_n'] == 'true')
{
	$loggedInDeelnemer = unserialize($_SESSION['s_deelnemer']);
	$userLoggedIn = true;
	$superUserLoggedIn = $loggedInDeelnemer->getSuperuser();
}

// get all (actieve) deelnemers
$db = Database::getInstance();
$deelnemers = $db->getDeelnemers();

// get all afleveringen
$afleveringen = $db->getAfleveringen();

if ($superUserLoggedIn)
{
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

// get all kandidaten
$kandidaten = $db->getKandidaten();

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

echo "<table>\n";
$ranking = 1;
$previous_punten = -1;
foreach ($deelnemers as $deelnemer)
{
	echo "<tr>\n";

	$deelnemerPunten = $deelnemer->getPunten();
	if ($deelnemerPunten != $previous_punten) {
		echo "<td style='width:20px; text-align:right;'>$ranking.</td>";
		$previous_punten = $deelnemerPunten;
	}
	 else {
		echo "<td style='width:20px; text-align:right;'>&nbsp;</td>";
	}
	$ranking++;

	$deelnemerId = $deelnemer->getId();
	$volledigeNaam = $deelnemer->getVoornaam() . " " . $deelnemer->getAchternaam();

	echo "<td><a href='./toon_deelnemer.php?id=$deelnemerId'>$volledigeNaam</a></td>\n";
	echo "<td style='width:40px;'>$deelnemerPunten</td>\n";

	// loggedin user gets more information
	//if ($userLoggedIn)	// uitgecommentarieerd, poule is afgelopen...
	{
		DisplayInfo($deelnemer);
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