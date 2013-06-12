<?php
/////////////////////////////////////////////////
// Term Extraction example from FiveFilters.org
// See http://fivefilters.org/term-extraction/
/////////////////////////////////////////////////

// Text to extract terms from
// --------------------------
$text = 'Inevitably, then, corporations do not restrict themselves merely to the arena of economics. Rather, as John Dewey observed, "politics is the shadow cast on society by big business". Over decades, corporations have worked together to ensure that the choices offered by \'representative democracy\' all represent their greed for maximised profits.

This is a sensitive task. We do not live in a totalitarian society - the public potentially has enormous power to interfere. The goal, then, is to persuade the public that corporate-sponsored political choice is meaningful, that it makes a difference. The task of politicians at all points of the supposed \'spectrum\' is to appear passionately principled while participating in what is essentially a charade.';

// TermExtractor PHP class
require '../TermExtractor.php';

// Filters
// -------
// Permissive - accept everything
//require '../TermExtractor/PermissiveFilter.php';
//$filter = new PermissiveFilter();

// Default - accept terms based on occurrence and word count
// min_occurrence - specify the number of times the term must appear in the original text for it be accepted.
// keep_if_strength - keep a term if the term's word count is equal to or greater than this, regardless of occurrence.
require '../DefaultFilter.php';
$filter = new DefaultFilter($min_occurrence=2, $keep_if_strength=2);

// Tagger
// ------
// Create Tagger instance.
// English is the only supported language at the moment.
$tagger = new Tagger('english');
// Initialise the Tagger instance.
// Use APC if available to store the dictionary file in memory 
// (otherwise it gets loaded from disk every time the Tagger is initialised).
$tagger->initialize($use_apc=true); 

// Term Extractor
// --------------
// Creater TermExtractor instance
$extractor = new TermExtractor($tagger, $filter);
// Extract terms from the text
$terms = $extractor->extract($text);
// We're outputting results in plain text...
header('Content-Type: text/plain; charset=UTF-8');
// Loop through extracted terms and print each term on a new line
foreach ($terms as $term_info) {
	// index 0: term
	// index 1: number of occurrences in text
	// index 2: word count
	list($term, $occurrence, $word_count) = $term_info;
	echo "$term\n";
	echo "  ->  occurrence: $occurrence, word count: $word_count\n\n";
}