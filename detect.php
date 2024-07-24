<?php
// Read the profane words/phrases from a text file and filter out lines starting with >>>
$profaneWords = array_filter(file('blocked.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES), function($line) {
    return strpos($line, '>>>') !== 0;
});

// Get the input words/phrases from the GET or POST request
$input = isset($_REQUEST['input']) ? $_REQUEST['input'] : '';
$whitelisted = isset($_REQUEST['whitelisted']) ? $_REQUEST['whitelisted'] : '';
$blacklisted = isset($_REQUEST['blacklisted']) ? $_REQUEST['blacklisted'] : '';

// Convert the whitelist and blacklist to arrays
$whitelisted = explode(',', $whitelisted);
$blacklisted = explode(',', $blacklisted);

// Check for profane words/phrases
$foundProfaneWords = [];

// Check against the profane words list
foreach ($profaneWords as $profaneWord) {
    // Use word boundaries to match whole words/phrases
    if (preg_match('/\b' . preg_quote($profaneWord, '/') . '\b/i', $input) && !in_array($profaneWord, $whitelisted)) {
        $foundProfaneWords[] = $profaneWord;
    }
}

// Check against the blacklist
foreach ($blacklisted as $blacklistedWord) {
    if (preg_match('/\b' . preg_quote($blacklistedWord, '/') . '\b/i', $input) && !in_array($blacklistedWord, $whitelisted)) {
        $foundProfaneWords[] = $blacklistedWord;
    }
}

// Remove duplicates from found profane words
$foundProfaneWords = array_unique($foundProfaneWords);

// Determine if any profane words were found
$hasProfaneWords = !empty($foundProfaneWords);

// Return the result as a JSON response
header('Content-Type: application/json');
echo json_encode([
    'input' => $input,
    'profane_words_found' => $foundProfaneWords,
    'profane' => $hasProfaneWords ? 1 : 0
]);
?>