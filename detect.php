<?php
// Read the profane words/phrases from a text file and filter out lines starting with >>>
$profaneWords = array_filter(file('raw/blocked.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES), function($line) {
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

// If no input is provided, display the GUI form
if (empty($input)) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Profanity Checker</title>
        <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
        <style>
            input {
                padding: 0.5rem;
            }
        </style>
    </head>
    <body class="bg-gray-100 text-gray-900">
        <div class="container mx-auto p-4">
            <h1 class="text-3xl font-bold mb-4">Profanity Checker</h1>
            <form action="detect.php" method="post" class="bg-white p-6 rounded shadow-md">
                <div class="mb-4">
                    <label for="input" class="block text-sm font-medium text-gray-700">Input Text</label>
                    <textarea id="input" name="input" rows="4" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></textarea>
                </div>
                <div class="mb-4">
                    <label for="whitelisted" class="block text-sm font-medium text-gray-700">Whitelisted Words/Phrases (comma-separated)</label>
                    <input type="text" id="whitelisted" name="whitelisted" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>
                <div class="mb-4">
                    <label for="blacklisted" class="block text-sm font-medium text-gray-700">Blacklisted Words/Phrases (comma-separated)</label>
                    <input type="text" id="blacklisted" name="blacklisted" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>
                <div class="mb-4">
                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md">Check</button>
                </div>
            </form>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// Return the result as a JSON response
header('Content-Type: application/json');
echo json_encode([
    'input' => $input,
    'profane_words_found' => $foundProfaneWords,
    'profane' => $hasProfaneWords ? 1 : 0
]);
