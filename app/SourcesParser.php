<?php

class SourcesParser
{
    /**
     * @param string $sourceDirectory
     * @param string $propertiesSeparator
     * @param array $resultContent
     */
    public static function parse(string $sourceDirectory, string $propertiesSeparator, array &$resultContent): void
    {
        $content = scandir($sourceDirectory);

        foreach ($content as $folder) {
            if ($folder !== '.' && $folder !== '..') {
                $path = realpath($sourceDirectory . DIRECTORY_SEPARATOR . $folder);
                $resultContent[$folder] = self::parseFiles($path, $propertiesSeparator);
            }
        }
    }

    /**
     * @param string $directory
     * @param string $propertiesSeparator
     * @return array
     */
    private static function parseFiles(string $directory, string $propertiesSeparator): array
    {
        $files = scandir($directory);
        $parsedFiles = [];

        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..') {
                $fileProperties = explode($propertiesSeparator, $file);

                $parsedFiles[$fileProperties[0]][] = substr($fileProperties[1], 0, -4);
                // $parsedFiles['files'][] = "$directory/$file";
            }
        }

        return $parsedFiles;
    }
}
