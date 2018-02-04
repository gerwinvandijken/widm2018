<?

class Voorspelling {
	public $id;
	public $afleveringId;
	public $deelnemerId;
	public $afvallerId;
	public $winnaarId;
	public $molId;
	public $laatstGewijzigd;

	public function __construct() {
		$this->id = -1;
		$this->afleveringId = 0;
		$this->deelnemerId = 0;
		$this->afvallerId = 0;
		$this->winnaarId = 0;
		$this->molId = 0;
		$this->laatstGewijzigd = "";
	}
}

?>