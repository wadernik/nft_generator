<?php

function parseFolders(string $sourceDirectory, array &$resultContent): void
{
    $content = scandir($sourceDirectory);

    foreach ($content as $folder) {
        if ($folder !== '.' && $folder !== '..') {
            $path = realpath($sourceDirectory . DIRECTORY_SEPARATOR . $folder);
            $resultContent[$folder] = parseFiles($path);
        }
    }
}

function parseFiles(string $directory): array
{
    $files = scandir($directory);
    $parsedFiles = [];

    foreach ($files as $file) {
        if ($file !== '.' && $file !== '..') {
            $fileProperties = explode('_', $file);

            $parsedFiles[$fileProperties[0]][] = substr($fileProperties[1], 0, -4);
            // $parsedFiles['files'][] = "$directory/$file";
        }
    }

    return $parsedFiles;
}
