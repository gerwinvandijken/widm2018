<?

class Kandidaat {
	public $id;
	public $voornaam;
	public $achternaam;
	public $beroep;
	public $afgevallen;

	public function __construct() {
		$this->id = -1;
		$this->voornaam = "";
		$this->achternaam = "";
		$this->beroep = "";
		$this->afgevallen = false;
	}
}

?>