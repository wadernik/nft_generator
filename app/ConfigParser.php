<?php

class ConfigParser
{
    public const DEFAULT_PROPERTIES_SEPARATOR = '_';
    public const DEFAULT_OUTPUT_DIRECTORY = 'results';
    public const DEFAULT_AMOUNT_OF_BATCHES = 10;
    public const DEFAULT_OUTPUT_WIDTH = 100;
    public const DEFAULT_OUTPUT_HEIGHT = 100;

    /**
     * @return array
     */
    public static function parse(): array
    {
        $currentPath = getcwd();
        $configPath = "$currentPath/config.json";

        $contents = file_get_contents($configPath);

        try {
            $contents = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);

            return [
                'properties_separator' => $contents['properties_separator'] ?? self::DEFAULT_PROPERTIES_SEPARATOR,
                'amount_of_batches' => $contents['amount_of_batches'] ?? self::DEFAULT_AMOUNT_OF_BATCHES,
                'output_directory' => $contents['output_directory'] ?? self::DEFAULT_OUTPUT_DIRECTORY,
                'output_resolution' => [
                    'width' => (int) ($contents['output_resolution']['width'] ?? self::DEFAULT_OUTPUT_WIDTH),
                    'height' => (int) ($contents['output_resolution']['height'] ?? self::DEFAULT_OUTPUT_HEIGHT),
                ],
                'categories_layers' => $contents['categories_layers'] ?? [],
                'color_ratio' => $contents['color_ratio'] ?? [],
                'rarity' => $contents['rarity'] ?? [],
            ];
        } catch (\Exception $e) {
            return [];
        }
    }
}
