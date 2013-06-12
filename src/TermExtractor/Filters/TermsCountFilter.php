<?php

namespace TermExtractor\Filters;

use TermExtractor\Filters\FilterInterface;

class TermsCountFilter implements FilterInterface {

	private $termsToWordRatio;
	private $singleStrengthMinOccur;
	private $noLimitStrength;

	public function __construct($termsToWordRatio = 16, $singleStrengthMinOccur = 3, $noLimitStrength = 2) {
		$this->termsToWordRatio       = $termsToWordRatio > 1         ? $termsToWordRatio         : 1;
		$this->singleStrengthMinOccur = $singleStrengthMinOccur > 1   ? $singleStrengthMinOccur   : 1;
		$this->noLimitStrength        = $noLimitStrength;
	}

	public function accept($word, $occur, $strength, $allTerms) {
		// Work out how many words are needed based upon how many terms we have
		$singleStrengthMinOccurBasedOnTermCount = max(1, floor(count($allTerms) / $this->termsToWordRatio));

		return 	$occur >= $singleStrengthMinOccurBasedOnTermCount ||
				(($strength == 1 && $occur >= $this->singleStrengthMinOccur) ||
				($strength >= $this->noLimitStrength));
	}
}
