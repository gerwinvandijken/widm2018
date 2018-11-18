<?

class Aflevering {
	private $id;
	private $startTijd;
	private $titel;
	private $afvallerId;

	public function __construct($id, $startTijd, $titel, $afvallerId) {
		$this->id = $id;
		$this->startTijd = $startTijd;
		$this->titel = $titel;
		$this->afvallerId = $afvallerId;
	}

	public function getId()
	{
		return $this->id;
	}

	public function getStartTijd()
	{
		return $this->startTijd;
	}

	public function getTitel()
	{
		return $this->titel;
	}

	public function getAfvallerId()
	{
		return $this->afvallerId;
	}
}

?>