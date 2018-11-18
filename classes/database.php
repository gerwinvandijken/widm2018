<?
require_once('kandidaat.php');
require_once('deelnemer.php');
require_once('voorspelling.php');
require_once('aflevering.php');

class Database {
	private static $instance;	// the single instance
	private $hostname = 'localhost';
	private $database = 'widm2017';
	private $username = 'widm2017';
	private $password = 'laura123';
	//private $conn;

	// private contructor (using Singleton pattern...)
	private function __construct() {
		// ...
	}

	// static method for getting instance/object (using Singleton pattern...)
	public static function getInstance()
	{
		if (is_null(self::$instance))	 {
			self::$instance = new Database();
		}
		return self::$instance;
	}

	private function connect() {
		$conn = mysqli_connect(localhost, $this->username, $this->password, $this->database);
		if (!$conn)
			die( "Connection failed: " . mysqli_connect_error());
		return $conn;
	}

	public function getKandidaten() {
		// connect to database
		$conn = $this->connect();

		$query = "SELECT * FROM Kandidaten ORDER BY Id ASC";
		$result = $conn->query($query);

		$i=0;
		$kandidaten = array();
		while ($i < $result->num_rows) {
			$row = $result->fetch_assoc();
			$kandidaat = $this->createKandidaat($row);

			// add kandidaat to array
			$kandidaten['id-' . $kandidaat->getId()] = $kandidaat;

			$i++;
		}

		// disconnect database connection
		$conn->close();

		// return kandidaten
		return $kandidaten;
	}

	private function createKandidaat($row)
	{
		$id = $row["Id"];
		$voornaam = $row["Voornaam"];
		$achternaam = $row["Achternaam"];
		$beroep = $row["Beroep"];

		$kandidaat = new Kandidaat($id, $voornaam, $achternaam, $beroep);
		
		return $kandidaat;
	}

	public function getDeelnemers() {
		// connect to database
		$conn = $this->connect();

		$query = "SELECT * FROM Deelnemers WHERE Actief = 1 ORDER BY Punten DESC, Voornaam ASC";
		$result = $conn->query($query);

		$i=0;
		$deelnemers = array();
		while ($i < $result->num_rows) {
			$row = $result->fetch_assoc();
			$deelnemer = $this->createDeelnemer($row);

			// add deelnemer to array
			$deelnemers['id-' . $deelnemer->getId()] = $deelnemer;

			$i++;
		}

		// disconnect database connection
		$conn->close();

		// return deelnemers
		return $deelnemers;
	}

	public function getDeelnemer($gebruikersnaam, $wachtwoord) {
		// connect to database
		$conn = $this->connect();

		$query = "SELECT * FROM Deelnemers WHERE Actief = 1 AND GebruikersNaam = '$gebruikersnaam' AND Wachtwoord = '$wachtwoord' LIMIT 1";
		$result = $conn->query($query);

		$deelnemer = null;
		if ($result->num_rows == 1) {
			$row = $result->fetch_assoc();
			$deelnemer = $this->createDeelnemer($row);
		}

		// disconnect database connection
		$conn->close();

		// return deelnemer
		return $deelnemer;
	}

	public function getDeelnemerById($id) {
		// connect to database
		$conn = $this->connect();

		$query = "SELECT * FROM Deelnemers WHERE Actief = 1 AND Id = $id LIMIT 1";
		$result = $conn->query($query);

		$deelnemer = null;
		if ($result->num_rows == 1) {
			$row = $result->fetch_assoc();
			$deelnemer = $this->createDeelnemer($row);
		}

		// disconnect database connection
		$conn->close();

		// return deelnemer
		return $deelnemer;
	}

	private function createDeelnemer($row)
	{
		$id = $row["Id"];
		$gebruikersnaam = $row["GebruikersNaam"];
		$voornaam = $row["Voornaam"];
		$achternaam = $row["Achternaam"];
		$superuser = $row["SuperUser"];
		$actief = $row["Actief"];
		$punten = $row["Punten"];

		$deelnemer = new Deelnemer($id, $gebruikersnaam, $voornaam, $achternaam,
											$superuser, $actief);
		$deelnemer->setPunten($punten);
		
		// return deelnemer
		return $deelnemer;
	}

	public function getVoorspellingen($deelnemerId) {
		// connect to database
		$conn = $this->connect();

		$query = "SELECT * FROM Voorspellingen WHERE DeelnemerId = $deelnemerId ORDER BY AfleveringId ASC";
		$result = $conn->query($query);

		$i=0;
		$voorspellingen = array();
		while ($i < $result->num_rows) {
			$row = $result->fetch_assoc();
			$voorspelling = $this->createVoorspelling($row);

			// add voorspelling to array
			$voorspellingen['id-' . $voorspelling->getId()] = $voorspelling;

			$i++;
		}

		// disconnect database connection
		$conn->close();

		// return voorspellingen
		return $voorspellingen;
	}
	
	private function createVoorspelling($row)
	{
		$id = $row["Id"];
		$afleveringId = $row["AfleveringId"];
		$deelnemerId = $row["DeelnemerId"];
		$afvallerId = $row["Afvaller_KandidaatId"];
		$winnaarId = $row["Winnaar_KandidaatId"];
		$molId = $row["Mol_KandidaatId"];
		$laatstGewijzigd = $row["LaatstGewijzigd"];

		$voorspelling = new Voorspelling($id, $afleveringId, $deelnemerId,
					$afvallerId, $winnaarId, $molId);
		$voorspelling->setLaatstGewijzigd($laatstGewijzigd);

		// return voorspelling
		return $voorspelling;
	}

	public function getAfleveringen() {
		// connect to database
		$conn = $this->connect();

		$query = "SELECT * FROM Afleveringen ORDER BY StartTijd ASC";
		$result = $conn->query($query);
		
		$i=0;
		$afleveringen = array();
		while ($i < $result->num_rows) {
			$row = $result->fetch_assoc();
			$aflevering = $this->createAflevering($row);

			// add aflevering to array
			$afleveringen['id-' . $aflevering->getId()] = $aflevering;

			$i++;
		}

		// disconnect database connection
		$conn->close();

		// return afleveringen
		return $afleveringen;
	}

	private function createAflevering($row)
	{
		$id = $row["Id"];
		$startTijd = date_create($row["StartTijd"]);
		$titel = $row["Titel"];
		$afvallerId = $row["Afvaller_KandidaatId"];

		$aflevering = new Aflevering($id, $startTijd, $titel, $afvallerId);

		return $aflevering;
	}

	public function updateAfvaller($afleveringid, $afvallerid) {
		// connect to database
		$conn = $this->connect();

		// todo: use prepared statement (to prevent SQL injection)

		$query = "UPDATE Afleveringen SET Afvaller_KandidaatId=$afvallerid WHERE Id=$afleveringid";
		$result = $conn->query($query);	// or die(mysqli_error());

		// check results...

		// disconnect database connection
		$conn->close();
	}

	public function updateDeelnemerAfvaller($afleveringid, $deelnemerId, $afvallerid) {
		// connect to database
		$conn = $this->connect();

		// todo: use prepared statement (to prevent SQL injection)

		$query = "UPDATE Voorspellingen SET Afvaller_KandidaatId=$afvallerid, LaatstGewijzigd=NOW()
							WHERE AfleveringId=$afleveringid AND DeelnemerId=$deelnemerId";
		$result = $conn->query($query);	// or die(mysqli_error());

		// check results...

		// disconnect database connection
		$conn->close();
	}

	public function updateDeelnemerWinnaar($afleveringid, $deelnemerId, $winnaarid) {
		// connect to database
		$conn = $this->connect();

		// todo: use prepared statement (to prevent SQL injection)

		$query = "UPDATE Voorspellingen SET Winnaar_KandidaatId=$winnaarid, LaatstGewijzigd=NOW()
							WHERE AfleveringId=$afleveringid AND DeelnemerId=$deelnemerId";
		$result = $conn->query($query);	// or die(mysqli_error());

		// check results...

		// disconnect database connection
		$conn->close();
	}

	public function updateDeelnemerMol($afleveringid, $deelnemerId, $molid) {
		// connect to database
		$conn = $this->connect();

		// todo: use prepared statement (to prevent SQL injection)

		$query = "UPDATE Voorspellingen SET Mol_KandidaatId=$molid, LaatstGewijzigd=NOW()
							WHERE AfleveringId=$afleveringid AND DeelnemerId=$deelnemerId";
		$result = $conn->query($query);	// or die(mysqli_error());

		// check results...

		// disconnect database connection
		$conn->close();
	}

	public function updateDeelnemerPunten($deelnemerId, $punten) {
		// connect to database
		$conn = $this->connect();

		// todo: use prepared statement (to prevent SQL injection)

		$query = "UPDATE Deelnemers SET Punten=$punten WHERE Id=$deelnemerId";
		$result = $conn->query($query); // or die(mysqli_error());

		// check results...

		// disconnect database connection
		$conn->close();
	}
}

?>