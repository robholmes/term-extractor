<?php 
class DefaultFilter {
	private $singleStrengthMinOccur;
	private $noLimitStrength;

	public function __construct($singleStrengthMinOccur=3, $noLimitStrength=2) {
		$this->singleStrengthMinOccur = $singleStrengthMinOccur;
		$this->noLimitStrength = $noLimitStrength;
	}
	
	public function accept($word, $occur, $strength) {
		return (($strength == 1 && $occur >= $this->singleStrengthMinOccur) ||
						($strength >= $this->noLimitStrength));
	}
}