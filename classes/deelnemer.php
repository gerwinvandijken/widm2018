<?

class Deelnemer {
	public $id;
	public $gebruikersnaam;
	public $voornaam;
	public $achternaam;
	public $punten;
	public $superuser;
	public $actief;

	public function __construct() {
		$this->id = -1;
		$this->gebruikersnaam = "";
		$this->voornaam = "";
		$this->achternaam = "";
		$this->punten = 0;
		$this->superuser = 0;
		$this->actief = 0;
	}
}

?>