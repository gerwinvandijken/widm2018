<?

class Voorspelling {
	private $id;
	private $afleveringId;
	private $deelnemerId;
	private $afvallerId;
	private $winnaarId;
	private $molId;
	private $laatstGewijzigd;

	public function __construct($id, $afleveringId,
						$deelnemerId, $afvallerId, $winnaarId, $molId) {
		$this->id = $id;
		$this->afleveringId = $afleveringId;
		$this->deelnemerId = $deelnemerId;
		$this->afvallerId = $afvallerId;
		$this->winnaarId = $winnaarId;
		$this->molId = $molId;
		$this->laatstGewijzigd = "";
	}
	
	public function getId()
	{
		return $this->id;
	}

	public function getAfleveringId()
	{
		return $this->afleveringId;
	}

	public function getDeelnemerId()
	{
		return $this->deelnemerId;
	}

	public function getAfvallerId()
	{
		return $this->afvallerId;
	}

	public function getWinnaarId()
	{
		return $this->winnaarId;
	}

	public function getMolId()
	{
		return $this->molId;
	}

	public function getLaatstGewijzigd()
	{
		return $this->laatstGewijzigd;
	}

	public function setLaatstGewijzigd($laatstGewijzigd)
	{
		$this->laatstGewijzigd = $laatstGewijzigd;
	}
}

?>