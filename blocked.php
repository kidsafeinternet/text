<?php
// Read the profane words/phrases from a text file
$profaneWords = array_filter(file('raw/blocked.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES), function ($line) {
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
            position: fixed;
            top: 1rem;
            right: 1rem;
        }

        html {
            scroll-behavior: smooth;
        }
    </style>
</head>

<body class="bg-gray-100 text-gray-900">
    <div class="container mx-auto p-4">
        <h1 class="text-3xl font-bold mb-4">Blocked Words/Phrases</h1>
        <p class="mb-4">Number of words/phrases blocked currently: <?= count($profaneWords) ?></p>
        <div class="flex">
            <div class="w-3/4">
                <?php foreach ($dictionary as $letter => $words) : ?>
                    <h2 id="<?= htmlspecialchars($letter) ?>" class="text-2xl font-semibold mt-6 mb-2"><?= htmlspecialchars($letter) ?></h2>
                    <ul class="list-disc list-inside ml-4">
                        <?php foreach ($words as $word) : ?>
                            <li><?= htmlspecialchars($word) ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endforeach; ?>
            </div>
            <div class="w-1/4 pl-4 sticky-nav">
                <h2 class="text-xl font-semibold mb-2">Jump to Section</h2>
                <div class="w-1/4 pl-4 sticky-nav">
                    <h2 class="text-xl font-semibold mb-2">Jump to Section</h2>
                    <ul class="list-none">
                        <li><a href="#top" class="text-blue-500 hover:underline">Top</a></li>
                        <?php foreach ($dictionary as $letter => $words) : ?>
                            <li><a href="#<?= htmlspecialchars($letter) ?>" class="text-blue-500 hover:underline"><?= htmlspecialchars($letter) ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
</body>

</html>