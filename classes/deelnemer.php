<?

class Deelnemer {
	private $id;
	private $gebruikersnaam;
	private $voornaam;
	private $achternaam;
	private $superuser;
	private $actief;
	private $punten;

	public function __construct($id, $gebruikersnaam, $voornaam, $achternaam,
										$superuser, $actief) {
		$this->id = $id;
		$this->gebruikersnaam = $gebruikersnaam;
		$this->voornaam = $voornaam;
		$this->achternaam = $achternaam;
		$this->superuser = $superuser;
		$this->actief = $actief;
		$this->punten = 0;
	}

	public function getId()
	{
		return $this->id;
	}

	public function getGebruikersnaam()
	{
		return $this->gebruikersnaam;
	}

	public function getVoornaam()
	{
		return $this->voornaam;
	}

	public function getAchternaam()
	{
		return $this->achternaam;
	}

	public function getSuperuser()
	{
		return $this->superuser;
	}

	public function getActief()
	{
		return $this->actief;
	}

	public function getPunten()
	{
		return $this->punten;
	}

	public function setPunten($punten)
	{
		$this->punten = $punten;
	}
}

?>