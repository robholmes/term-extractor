<?php 
// This is a PHP port of the Topia's Term Extractor: http://pypi.python.org/pypi/topia.termextract/
// It was ported by Keyvan Minoukadeh for the Five Filters project: http://fivefilters.org
// It also incorporates some of the changes made by Joseph Turian (jtp) to Topia's Term Extractor: https://github.com/turian/topia.termextract
// It is licensed under the original license (ZPL 2.1) and GPL 3.0.

/*
This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

require_once 'Tagger.php';

class TermExtractor {
	const SEARCH = 0;
	const NOUN = 1;
	
	public $tagger;
	public $filter;
	
	public function __construct($tagger=null, $filter=null) {
		if ($tagger === null) {
			$this->tagger = new Tagger();
			$this->tagger->initialize();
		} else {
			$this->tagger = $tagger;
		}
		if ($filter === null) {
			require_once 'DefaultFilter.php';
			$this->filter = new DefaultFilter();
		} else {
			$this->filter = $filter;
		}
	}
	
	private function _add($term, $norm, &$multiterm, &$terms) {
		$multiterm[] = array($term, $norm);
		// This was originally in the code, but we don't want unigram terms
		// if we keep the multiterms -jpt			
		//if (!isset($terms[$norm])) $terms[$norm] = 0;
		//$terms[$norm] += 1;
		//echo "$norm: {$terms[$norm]} (_add())\n";		
	}
	
	private function _keepterm($multiterm, &$terms) {
		$word = array();
		foreach ($multiterm as $term_norm) {
			$word[] = $term_norm[0];
		}
		$word = implode(' ', $word);
		if (!isset($terms[$word])) $terms[$word] = 0;
		$terms[$word] += 1;
		//echo "$word: {$terms[$word]} (_keepterm())\n";
	}
	
	// $tags should be an array of tags produced by a Tagger instance
	// if a string is given, we'll get tags by passing it to the Tagger instance associated with this object
	public function extract($tags) {
		if (is_string($tags)) {
			// treat $tags as input string
			$tags = $this->tagger->tag($tags);
		}
		$terms = array(); //{}
		// Phase 1: A little state machine is used to build simple and
		// composite terms.
		$multiterm = array(); //[]
		$state = self::SEARCH;
		while (count($tags) > 0) {
			list($term, $tag, $norm) = array_shift($tags);
			if (($state == self::SEARCH) && (substr($tag, 0, 1) == 'N')) {
				$state = self::NOUN;
				$this->_add($term, $norm, $multiterm, $terms);
			} elseif (($state == self::SEARCH) && ($tag == 'JJ') && ctype_upper($term[0])) { //TODO: test UTF8 for $term[0]?
					$state = self::NOUN;
					$this->_add($term, $norm, $multiterm, $terms);
			} elseif (($state == self::NOUN) && (substr($tag, 0, 1) == 'N')) {
					$this->_add($term, $norm, $multiterm, $terms);
			} elseif (($state == self::NOUN) && (substr($tag, 0, 1) != 'N')) {
					$state = self::SEARCH;
					if (count($multiterm) > 0) {
						$this->_keepterm($multiterm, $terms);
					}
				$multiterm = array(); //[]
			}
		}
		// Potentially keep the last term, if there is one. -jpt
		if (count($multiterm) > 0) {
			$this->_keepterm($multiterm, $terms);
		}
		$multiterm = array(); //[]
		// Phase 2: Only select the terms that fulfill the filter criteria.
		// Also create the term strength.
		$return = array();
		foreach ($terms as $word => $occur) {
			$word_count = count(preg_split('!\s+!', $word, null, PREG_SPLIT_NO_EMPTY));
			if ($this->filter->accept($word, $occur, $word_count)) {
				$return[] = array($word, $occur, $word_count);
			}
		}
		return $return;
	}
}