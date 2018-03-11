<?php
/**
 * Recognize image extension
 */
$extension = ["jpg", "jpeg", "png", "gif"];
/**
 * Get folder name based on Workflow config
 */
$folder = $_ENV["folder"];
/**
 * Get search term
 */
$search = $argv[1];

function alfred($items)
{
    echo json_encode(array("items" => $items));
}

if (!is_dir($folder)) {
    return alfred([["title" => "Did you set correct path to your meme directory?", "valid" => "false"]]);
}

$recursiveDirectory = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($folder));
$items = array();
foreach ($recursiveDirectory as $file) {
    if ($file->isDir()) {
        continue;
    }

    if (!in_array($file->getExtension(), $extension)) {
        continue;
    }

    $path = $file->getPathname();
    $title = $file->getBasename();

    $items[] = array(
        "icon" => array("path" => $path),
        "title" => $title,
        "arg" => $path,
    );
}

/**
 * Lame sort :)
 */
if ($search) {
    usort($items, function ($item1, $item2) {
        global $search;

        return substr_count($item2["title"], $search) <=> substr_count($item1["title"], $search);
    });
}

/**
 * Send those results to Alfred
 */
alfred($items);
