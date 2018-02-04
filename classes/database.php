<?
require_once('kandidaat.php');
require_once('deelnemer.php');
require_once('voorspelling.php');
require_once('aflevering.php');

class Database {
	static private $hostname = 'localhost';
	static private $database = 'widm2017';
	static private $username = 'widm2017';
	static private $password = 'laura123';
	static private $connections = 0;

	public static function connect() {
		if (Database::$connections == 0) {
			mysql_connect(localhost, Database::$username, Database::$password);
			@mysql_select_db(Database::$database) or die( "Unable to select database");
		}
		Database::$connections++;
	}

	public static function disconnect() {
		Database::$connections--;
		if (Database::$connections == 0) {
			mysql_close();
		}
	}

	public static function getKandidaten() {
		// connect to database
		Database::connect();

		$query = "SELECT * FROM Kandidaten ORDER BY Id ASC";
		$query_result = mysql_query($query);
		$rowCount = mysql_numrows($query_result);

		$i=0;
		$kandidaten = array();
		while ($i < $rowCount) {
			$kandidaat = new Kandidaat();

			$kandidaat->id = mysql_result($query_result,$i,"Id");
			$kandidaat->voornaam = mysql_result($query_result,$i,"Voornaam");
			$kandidaat->achternaam = mysql_result($query_result,$i,"Achternaam");
			$kandidaat->beroep = mysql_result($query_result,$i,"Beroep");

			// add kandidaat to array
			$kandidaten['id-' . $kandidaat->id] = $kandidaat;

			$i++;
		}

		// disconnect database connection
		Database::disconnect();

		// return kandidaten
		return $kandidaten;
	}

	public static function getDeelnemers() {
		// connect to database
		Database::connect();

		$query = "SELECT * FROM Deelnemers WHERE Actief = 1 ORDER BY Punten DESC, Voornaam ASC";
		$query_result = mysql_query($query);
		$rowCount = mysql_numrows($query_result);

		$i=0;
		$deelnemers = array();
		while ($i < $rowCount) {
			$deelnemer = Database::createDeelnemer($query_result, $i);
/*			$deelnemer = new Deelnemer();

			$deelnemer->id = mysql_result($query_result,$i,"Id");
			$deelnemer->gebruikersnaam = mysql_result($query_result,$i,"GebruikersNaam");
			$deelnemer->voornaam = mysql_result($query_result,$i,"Voornaam");
			$deelnemer->achternaam = mysql_result($query_result,$i,"Achternaam");
			$deelnemer->punten = mysql_result($query_result,$i,"Punten");
			$deelnemer->superuser = mysql_result($query_result,$i,"SuperUser");
			$deelnemer->actief = mysql_result($query_result,$i,"Actief");
*/
			// add deelnemer to array
			$deelnemers['id-' . $deelnemer->id] = $deelnemer;

			$i++;
		}

		// disconnect database connection
		Database::disconnect();

		// return deelnemers
		return $deelnemers;
	}

	public static function getDeelnemer($gebruikersnaam, $wachtwoord) {
		// connect to database
		Database::connect();

		$query = "SELECT * FROM Deelnemers WHERE Actief = 1 AND GebruikersNaam = '$gebruikersnaam' AND Wachtwoord = '$wachtwoord' LIMIT 1";
		$query_result = mysql_query($query);
		$rowCount = mysql_numrows($query_result);

		$deelnemer = null;
		if ($rowCount == 1) {
			$deelnemer = Database::createDeelnemer($query_result, 0);
/*			$deelnemer = new Deelnemer();

			$i = 0;
			$deelnemer->id = mysql_result($query_result,$i,"Id");
			$deelnemer->gebruikersnaam = mysql_result($query_result,$i,"GebruikersNaam");
			$deelnemer->voornaam = mysql_result($query_result,$i,"Voornaam");
			$deelnemer->achternaam = mysql_result($query_result,$i,"Achternaam");
			$deelnemer->punten = mysql_result($query_result,$i,"Punten");
			$deelnemer->superuser = mysql_result($query_result,$i,"SuperUser");
			$deelnemer->actief = mysql_result($query_result,$i,"Actief"); */
		}

		// disconnect database connection
		Database::disconnect();

		// return deelnemer
		return $deelnemer;
	}

	public static function getDeelnemerById($id) {
		// connect to database
		Database::connect();

		$query = "SELECT * FROM Deelnemers WHERE Actief = 1 AND Id = $id LIMIT 1";
		$query_result = mysql_query($query);
		$rowCount = mysql_numrows($query_result);

		$deelnemer = null;
		if ($rowCount == 1) {
			$deelnemer = Database::createDeelnemer($query_result, 0);
		}

		// disconnect database connection
		Database::disconnect();

		// return deelnemer
		return $deelnemer;
	}

	private static function createDeelnemer($query_result, $index)
	{
		$deelnemer = new Deelnemer();

		$deelnemer->id = mysql_result($query_result,$index,"Id");
		$deelnemer->gebruikersnaam = mysql_result($query_result,$index,"GebruikersNaam");
		$deelnemer->voornaam = mysql_result($query_result,$index,"Voornaam");
		$deelnemer->achternaam = mysql_result($query_result,$index,"Achternaam");
		$deelnemer->punten = mysql_result($query_result,$index,"Punten");
		$deelnemer->superuser = mysql_result($query_result,$index,"SuperUser");
		$deelnemer->actief = mysql_result($query_result,$index,"Actief");
		
		// return deelnemer
		return $deelnemer;
	}

	public static function getVoorspellingen($deelnemerId) {
		// connect to database
		Database::connect();

		$query = "SELECT * FROM Voorspellingen WHERE DeelnemerId = $deelnemerId ORDER BY AfleveringId ASC";
		$query_result = mysql_query($query);
		$rowCount = mysql_numrows($query_result);

		$i=0;
		$voorspellingen = array();
		while ($i < $rowCount) {
			$voorspelling = new Voorspelling();

			$voorspelling->id = mysql_result($query_result,$i,"Id");
			$voorspelling->afleveringId = mysql_result($query_result,$i,"AfleveringId");
			$voorspelling->deelnemerId = mysql_result($query_result,$i,"DeelnemerId");
			$voorspelling->afvallerId = mysql_result($query_result,$i,"Afvaller_KandidaatId");
			$voorspelling->winnaarId = mysql_result($query_result,$i,"Winnaar_KandidaatId");
			$voorspelling->molId = mysql_result($query_result,$i,"Mol_KandidaatId");
			$voorspelling->laatstGewijzigd = mysql_result($query_result,$i,"LaatstGewijzigd");

			// add voorspelling to array
			$voorspellingen['id-' . $voorspelling->id] = $voorspelling;

			$i++;
		}

		// disconnect database connection
		Database::disconnect();

		// return voorspellingen
		return $voorspellingen;
	}
	
	public static function getAfleveringen() {
		// connect to database
		Database::connect();

		$query = "SELECT * FROM Afleveringen ORDER BY StartTijd ASC";
		$query_result = mysql_query($query);
		$rowCount = mysql_numrows($query_result);

		$i=0;
		$afleveringen = array();
		while ($i < $rowCount) {
			$aflevering = new Aflevering();

			$aflevering->id = mysql_result($query_result,$i,"Id");
			$aflevering->startTijd = date_create(mysql_result($query_result,$i,"StartTijd"));
			$aflevering->titel = mysql_result($query_result,$i,"Titel");
			$aflevering->afvallerId = mysql_result($query_result,$i,"Afvaller_KandidaatId");

			// add aflevering to array
			$afleveringen['id-' . $aflevering->id] = $aflevering;

			$i++;
		}

		// disconnect database connection
		Database::disconnect();

		// return afleveringen
		return $afleveringen;
	}

	public static function updateAfvaller($afleveringid, $afvallerid) {
		// connect to database
		Database::connect();
		
		$query = "UPDATE Afleveringen SET Afvaller_KandidaatId=$afvallerid WHERE Id=$afleveringid";
		$result=mysql_query($query) or die(mysql_error());
	
		// disconnect database connection
		Database::disconnect();
	}

	public static function updateDeelnemerAfvaller($afleveringid, $deelnemerId, $afvallerid) {
		// connect to database
		Database::connect();
		
		$query = "UPDATE Voorspellingen SET Afvaller_KandidaatId=$afvallerid, LaatstGewijzigd=NOW()
							WHERE AfleveringId=$afleveringid AND DeelnemerId=$deelnemerId";
		$result=mysql_query($query) or die(mysql_error());
	
		// disconnect database connection
		Database::disconnect();
	}

	public static function updateDeelnemerWinnaar($afleveringid, $deelnemerId, $winnaarid) {
		// connect to database
		Database::connect();
		
		$query = "UPDATE Voorspellingen SET Winnaar_KandidaatId=$winnaarid, LaatstGewijzigd=NOW()
							WHERE AfleveringId=$afleveringid AND DeelnemerId=$deelnemerId";
		$result=mysql_query($query) or die(mysql_error());
	
		// disconnect database connection
		Database::disconnect();
	}

	public static function updateDeelnemerMol($afleveringid, $deelnemerId, $molid) {
		// connect to database
		Database::connect();
		
		$query = "UPDATE Voorspellingen SET Mol_KandidaatId=$molid, LaatstGewijzigd=NOW()
							WHERE AfleveringId=$afleveringid AND DeelnemerId=$deelnemerId";
		$result=mysql_query($query) or die(mysql_error());
	
		// disconnect database connection
		Database::disconnect();
	}
	
	public static function updateDeelnemerPunten($deelnemerId, $punten) {
		// connect to database
		Database::connect();
		
		$query = "UPDATE Deelnemers SET Punten=$punten WHERE Id=$deelnemerId";
		$result=mysql_query($query) or die(mysql_error());
	
		// disconnect database connection
		Database::disconnect();
	}
}

?>