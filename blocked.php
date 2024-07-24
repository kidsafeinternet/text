<?php
// Read the profane words/phrases from a text file
$profaneWords = array_filter(file('blocked.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES), function($line) {
    return strpos($line, '>>>') !== 0;
});

// Initialize an array to hold the words by their first letter
$dictionary = [];

// Organize words by their first letter
foreach ($profaneWords as $word) {
    $firstLetter = strtoupper($word[0]);
    if (is_numeric($firstLetter)) {
        $firstLetter = '#';
    }
    if (!isset($dictionary[$firstLetter])) {
        $dictionary[$firstLetter] = [];
    }
    $dictionary[$firstLetter][] = $word;
}

// Sort the dictionary by keys (letters)
ksort($dictionary);

// Sort each section's words alphabetically
foreach ($dictionary as $letter => $words) {
    sort($words);
    $dictionary[$letter] = $words;
}

// Display the dictionary
header('Content-Type: text/html');
echo '<html><head>';
echo '<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">';
echo '</head><body class="bg-gray-100 text-gray-900">';
echo '<div class="container mx-auto p-4">';
echo '<h1 class="text-3xl font-bold mb-4">Blocked Words/Phrases</h1>';
echo '<p class="mb-4">Number of words/phrases blocked currently: ' . count($profaneWords) . '</p>';
foreach ($dictionary as $letter => $words) {
    echo '<h2 class="text-2xl font-semibold mt-6 mb-2">' . htmlspecialchars($letter) . '</h2>';
    echo '<ul class="list-disc list-inside">';
    foreach ($words as $word) {
        echo '<li class="ml-4">' . htmlspecialchars($word) . '</li>';
    }
    echo '</ul>';
}
echo '</div>';
echo '</body></html>';
