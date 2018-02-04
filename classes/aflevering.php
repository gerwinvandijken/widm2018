<?

class Aflevering {
	public $id;
	public $startTijd;
	public $titel;
	public $afvallerId;

	public function __construct() {
		$this->id = -1;
		$this->startTijd = "";
		$this->titel = "";
		$this->afvallerId = 0;
	}
}

?>