<?

class Kandidaat {
	private $id;
	private $voornaam;
	private $achternaam;
	private $beroep;
	private $afgevallen;

	public function __construct($id, $voornaam, $achternaam, $beroep) {
		$this->id = $id;
		$this->voornaam = $voornaam;
		$this->achternaam = $achternaam;
		$this->beroep = $beroep;
		$this->afgevallen = false;
	}

	public function getId()
	{
		return $this->id;
	}

	public function getVoornaam()
	{
		return $this->voornaam;
	}

	public function getAchternaam()
	{
		return $this->achternaam;
	}

	public function getBeroep()
	{
		return $this->beroep;
	}

	public function getAfgevallen()
	{
		return $this->afgevallen;
	}

	public function setAfgevallen($afgevallen)
	{
		$this->afgevallen = $afgevallen;
	}
}

?>