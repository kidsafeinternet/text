<?php

class TrieNode {
    public $children = [];
    public $isEndOfWord = false;
}

class Trie {
    private $root;

    public function __construct() {
        $this->root = new TrieNode();
    }

    public function insert($word) {
        $node = $this->root;
        $word = strtolower($word);
        for ($i = 0; $i < strlen($word); $i++) {
            $char = $word[$i];
            if (!isset($node->children[$char])) {
                $node->children[$char] = new TrieNode();
            }
            $node = $node->children[$char];
        }
        $node->isEndOfWord = true;
    }

    public function search($word) {
        $node = $this->root;
        $word = strtolower($word);
        for ($i = 0; $i < strlen($word); $i++) {
            $char = $word[$i];
            if (!isset($node->children[$char])) {
                return false;
            }
            $node = $node->children[$char];
        }
        return $node->isEndOfWord;
    }
}

// Function to read and sanitize profane words from a file
function getProfaneWords($filePath) {
    if (!file_exists($filePath)) {
        return [];
    }

    $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $profaneWords = array_filter($lines, function ($line) {
        return strpos($line, '>>>') !== 0; // Comments start with >>>
    });

    return array_map(function ($line) {
        return htmlspecialchars(explode('|', $line)[0]);
    }, $profaneWords);
}

// Function to sanitize and validate input
function sanitizeInput($input) {
    return htmlspecialchars(trim($input));
}

// Function to convert comma-separated string to array
function convertToArray($string) {
    return array_map('trim', explode(',', $string));
}

// Main script
$profaneWords = getProfaneWords('raw/blocked.txt');
$input = isset($_REQUEST['input']) ? sanitizeInput($_REQUEST['input']) : '';
$whitelisted = isset($_REQUEST['whitelisted']) ? sanitizeInput($_REQUEST['whitelisted']) : '';
$blacklisted = isset($_REQUEST['blacklisted']) ? sanitizeInput($_REQUEST['blacklisted']) : '';

$whitelisted = convertToArray($whitelisted);
$blacklisted = convertToArray($blacklisted);

// Build the Trie for profane words
$trie = new Trie();
foreach ($profaneWords as $word) {
    $trie->insert($word);
}

// Check for profane words/phrases
$foundProfaneWords = [];

// Function to check for profane words using Trie
function checkProfaneWords($trie, $input, $whitelisted) {
    $foundWords = [];
    $words = explode(' ', $input);
    foreach ($words as $word) {
        if ($trie->search($word) && !in_array($word, $whitelisted)) {
            $foundWords[] = $word;
        }
    }
    return $foundWords;
}

$foundProfaneWords = array_merge(
    checkProfaneWords($trie, $input, $whitelisted),
    checkProfaneWords($trie, implode(' ', $blacklisted), $whitelisted)
);

$foundProfaneWords = array_unique($foundProfaneWords);
$hasProfaneWords = !empty($foundProfaneWords);

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
            input, textarea {
                padding: 0.5rem;
            }
        </style>
    </head>
    <body class="bg-gray-100 text-gray-900">
        <div class="container mx-auto p-4">
            <h1 class="text-3xl font-bold mb-4">Profanity Checker</h1>
            <form id="profanityForm" action="detect.php" method="post" class="bg-white p-6 rounded shadow-md">
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
            <div id="jsonResponse" class="bg-gray-200 p-4 rounded mt-4"></div>
        </div>
        <script>
            document.getElementById('profanityForm').addEventListener('submit', function(event) {
                event.preventDefault();
                const formData = new FormData(this);
                fetch('detect.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    document.getElementById('jsonResponse').innerText = JSON.stringify(data, null, 2);
                })
                .catch(error => console.error('Error:', error));
            });
        </script>
    </body>
    </html>
    <?php
    exit;
}

// Output the result as JSON
header('Content-Type: application/json');
echo json_encode([
    'input' => $input,
    'whitelisted' => $whitelisted,
    'blacklisted' => $blacklisted,
    'found' => $foundProfaneWords,
    'profane' => $hasProfaneWords ? 1 : 0
]);
