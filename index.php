<?php
require 'Parsedown.php';

// Display a page with links to all php files and display the readme file
$files = glob('*.php');

// Filter out specific .php files
$filtered = ['index.php', 'Parsedown.php'];

$files = array_diff($files, $filtered);
$readme = file_get_contents('README.md');

$Parsedown = new Parsedown();
$readmeHtml = $Parsedown->text($readme);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KKS - Text Classification</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        pre {
            background-color: #f4f4f4;
            padding: 1rem;
            border-radius: 0.25rem;
            overflow: auto;
        }
        .prose h1 {
            font-size: 2rem;
            margin-top: 1.5rem;
            margin-bottom: 1rem;
        }
        .prose h2 {
            font-size: 1.5rem;
            margin-top: 1.5rem;
            margin-bottom: 1rem;
        }
        code {
            background-color: #e0e0e0;
            border-radius: 0.25rem;
            padding: 0.25rem;
        }
    </style>
</head>
<body class="bg-gray-100 text-gray-900">
    <div class="container mx-auto p-4 sm:p-6 lg:p-8">
        <h1 class="text-3xl sm:text-4xl font-bold mb-4">Directory</h1>
        <ul class="list-disc list-inside mb-8 space-y-2">
            <?php foreach ($files as $file): 
                if ($file === 'blocked.php') {
                    $name = 'Blocked Words/Phrases List';
                }
                else {
                    $name = $file;
                }
                ?>
                <li class="ml-4"><a href="<?= htmlspecialchars($file) ?>" class="text-blue-500 hover:underline"><?= $name ?></a></li>
            <?php endforeach; ?>
        </ul>
        <h2 class="text-2xl sm:text-3xl font-semibold mb-4">README</h2>
        <hr class="mb-4">
        <div class="prose max-w-none">
            <?= $readmeHtml ?>
        </div>
    </div>
</body>
</html>