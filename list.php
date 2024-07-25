<?php
// Read the profane words/phrases from a text file
$profaneWords = array_filter(file('raw/blocked.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES), function ($line) {
    return strpos($line, '>>>') !== 0; // Comments start with >>>
});

// Initialize an array to hold the words by their first letter
$dictionary = [];

// Organize words by their first letter
foreach ($profaneWords as $line) {
    list($word, $creator) = explode('|', $line);
    $firstLetter = strtoupper($word[0]);
    if (is_numeric($firstLetter)) {
        $firstLetter = '#';
    }
    if (!isset($dictionary[$firstLetter])) {
        $dictionary[$firstLetter] = [];
    }
    $dictionary[$firstLetter][] = ['word' => $word, 'creator' => $creator];
}

// Sort the dictionary by keys (letters)
ksort($dictionary);

// Sort each section's words alphabetically
foreach ($dictionary as $letter => $words) {
    usort($words, function ($a, $b) {
        return strcmp($a['word'], $b['word']);
    });
    $dictionary[$letter] = $words;
}

// Display the dictionary
header('Content-Type: text/html');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blocked Words/Phrases</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .sticky-nav {
            position: sticky;
            top: 1rem;
        }

        html {
            scroll-behavior: smooth;
        }
    </style>
</head>

<body class="bg-gray-100 text-gray-900">
    <div class="container mx-auto p-4 sm:p-6 lg:p-8">
        <h1 class="text-3xl sm:text-4xl font-bold mb-4">Blocked Words/Phrases</h1>
        <p class="mb-4">Number of words/phrases blocked currently: <?= count($profaneWords) ?></p>
        <div class="flex flex-col lg:flex-row">
            <div class="lg:w-3/4">
                <?php foreach ($dictionary as $letter => $words) : ?>
                    <h2 id="<?= htmlspecialchars($letter) ?>" class="text-2xl sm:text-3xl font-semibold mt-6 mb-2"><?= htmlspecialchars($letter) ?></h2>
                    <ul class="list-disc list-inside ml-4">
                        <?php foreach ($words as $entry) : ?>
                            <li><?= htmlspecialchars($entry['word']) ?> <span class="text-gray-500">(<?= htmlspecialchars($entry['creator']) ?>)</span></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endforeach; ?>
            </div>
            <div class="lg:w-1/4 lg:pl-4 sticky-nav mt-6 lg:mt-0">
                <h2 class="text-xl sm:text-2xl font-semibold mb-2">Jump to Section</h2>
                <ul class="flex flex-wrap list-none">
                    <li class="mr-2 mb-2"><a href="#top" class="text-blue-500 hover:underline">Top</a></li>
                    <?php foreach ($dictionary as $letter => $words) : ?>
                        <li class="mr-2 mb-2"><a href="#<?= htmlspecialchars($letter) ?>" class="text-blue-500 hover:underline"><?= htmlspecialchars($letter) ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</body>

</html>

<!-- IGNORE THIS SECTION -->
<!--
add a created by for the blocked list. split by `|` for who it created by, e.g. `badword|Google`
-->
<!-- IGNORE THIS SECTION -->