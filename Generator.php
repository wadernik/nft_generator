<?php

require 'FolderHandler.php';
require 'ConfigParser.php';

function pickRandomPropertyOne(array $category): string
{
    return array_rand($category);
}

function pickRandomPropertyTwo(array $category, string $propertyOneKey): string
{
    $randomPropertyTwoKey = random_int(0, count($category[$propertyOneKey]) - 1);
    return $category[$propertyOneKey][$randomPropertyTwoKey];
}

$amountOfBatches = 10000;

// Settings
$outputResolution = [
    'width' => 1080,
    'height' => 1080,
];

$propertiesSeparator = '_';

// Probabilities
$backgroundProbabilities = [
    'property_1' => 80,
    'property_2' => 20,
];

$currentPath = getcwd();
$sourceDirectory = "$currentPath/images";
$content = [];
parseFolders($sourceDirectory, $content);

parseConfig();

$firstJsonData = [];
$counter = 0;
for ($i = 1; $i <= $amountOfBatches; $i++) {
    $topGarment = $content['top-garment'];
    $bottomGarment = $content['bottom-garment'];
    $shoes = $content['shoes'];

    // ------ Top Garment ------ //
    $topGarmentPropertyOne = pickRandomPropertyOne($topGarment);
    $topGarmentPropertyTwo = pickRandomPropertyTwo($topGarment, $topGarmentPropertyOne);
    // ------------------------ //

    // ---- Bottom Garment ---- //
    $bottomGarmentPropertyOne = pickRandomPropertyOne($bottomGarment);
    $bottomGarmentPropertyTwo = pickRandomPropertyTwo($bottomGarment, $bottomGarmentPropertyOne);
    // ------------------------ //


    // -------- Shoes -------- //
    $shoesPropertyOne = pickRandomPropertyOne($shoes);
    $shoesPropertyTwo = pickRandomPropertyTwo($shoes, $shoesPropertyOne);
    // ------------------------ //

    $combinedKey = $topGarmentPropertyOne . $propertiesSeparator . $topGarmentPropertyTwo
        . $propertiesSeparator . $bottomGarmentPropertyOne . $propertiesSeparator . $bottomGarmentPropertyTwo
        . $propertiesSeparator . $shoesPropertyOne . $propertiesSeparator . $shoesPropertyTwo;

    if (!isset($firstJsonData[$combinedKey])) {
        $counter++;
        $firstJsonData[$combinedKey] = [
            'top-garment' => [
                'property_1' => $topGarmentPropertyOne,
                'property_2' => $topGarmentPropertyTwo,
            ],
            'bottom-garment' => [
                'property_1' => $bottomGarmentPropertyOne,
                'property_2' => $bottomGarmentPropertyTwo,
            ],
            'shoes' => [
                'property_1' => $shoesPropertyOne,
                'property_2' => $shoesPropertyTwo,
            ],
        ];
    }
}

$firstJsonDataAsJson = [];
foreach ($firstJsonData as $dataEntry) {
    $firstJsonDataAsJson[] = $dataEntry;
}

file_put_contents('1.json', json_encode($firstJsonDataAsJson, JSON_THROW_ON_ERROR));

echo $counter . "\n";
