<?php

use JetBrains\PhpStorm\ArrayShape;

const DEFAULT_PROPERTIES_SEPARATOR = '_';
const DEFAULT_AMOUNT_OF_BATCHES = 10;
const DEFAULT_OUTPUT_WIDTH = 100;
const DEFAULT_OUTPUT_HEIGHT = 100;
const DEFAULT_PROBABILITY = 'random';

function parseConfig(): array
{
    $currentPath = getcwd();
    $configPath = "$currentPath/config.json";

    $contents = file_get_contents($configPath);

    // TODO add try-catch
    $contents = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);

    $contents = parseContents($contents);

    parseProbabilities($contents['categories_probabilities']);

    return $contents;
}

#[ArrayShape([
    'properties_separator' => "string",
    'amount_of_batches' => "int",
    'output_resolution' => "int[]",
    'categories' => "array",
    'categories_probabilities' => "array"
])]
function parseContents($contents): array
{
    return [
        'properties_separator' => $contents['properties_separator'] ?? DEFAULT_PROPERTIES_SEPARATOR,
        'amount_of_batches' => $contents['amount_of_batches'] ?? DEFAULT_AMOUNT_OF_BATCHES,
        'output_resolution' => [
            'width' => (int) ($contents['output_resolution']['width'] ?? DEFAULT_OUTPUT_WIDTH),
            'height' => (int) ($contents['output_resolution']['height'] ?? DEFAULT_OUTPUT_HEIGHT),
        ],
        'categories' => $contents['categories'] ?? [],
        'categories_probabilities' => $contents['categories_probabilities'] ?? [],
    ];
}

function parseProbabilities(array $categoriesProbabilities): array
{
    foreach ($categoriesProbabilities as $categoryName => $category) {
        if (isset($category['special'])) {
            continue;
        }

        $propertyOneRule = $category['property_1'];
        $propertyTwoRule = $category['property_2'];

        $something = [
            $categoryName => [
                'property_1' => parsePropertyRule($propertyOneRule, $categoriesProbabilities),
                'property_2' => parsePropertyRule($propertyTwoRule, $categoriesProbabilities),
            ],
        ];

        print_r($something);
    }
    return [];
}

function parsePropertyRule(string $propertyRule, array $categories): array|string
{
    switch ($propertyRule) {
        case str_starts_with($propertyRule, '='):
            $rule = getRuleForEquality($propertyRule);
            break;
        case str_contains($propertyRule, ":"):
            $rule = getRuleForConstants($propertyRule);
            break;
        default:
            $rule = "random";
            break;
    }

    return $rule;
}

function getRuleForEquality(string $propertyRule): array
{
    $splits = explode('.', substr($propertyRule, 1));
    return [trim($splits[0]) => trim($splits[1])];
}

function getRuleForConstants(string $propertyRule): array
{
    $splits = [];
    $resultRules = [];
    if (str_contains($propertyRule, ',')) {
        $splits = explode(',', $propertyRule);
    }

    foreach ($splits as $split) {
        $split = trim($split);

        $rules = explode(':', $split);

        $resultRules[trim($rules[0])] = trim($rules[1]);
    }

    return $resultRules;
}
