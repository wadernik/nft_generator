<?php

require 'FolderHandler.php';

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

$backgroundFolderName = 'BG';
$backgroundAdditionalFirstFolderName = 'BG1';
$backgroundAdditionalSecondFolderName = 'BG2';

$layers = [
    'BG' => 0,
    'BG2' => 1,
    'BG1' => 2,
    'body' => 3,
    'shoes' => 4,
    'bottom-garment' => 5,
    'top-garment' => 6,
    'head' => 7,
    'hand-L' => 8,
    'hand-R' => 9,
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

$forJsonTest = [];
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

    if (!isset($forJsonTest[$combinedKey])) {
        $counter++;
        $forJsonTest[$combinedKey] = [
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

$forJsonTestAsJson = json_encode($forJsonTest);
file_put_contents('1.json', $forJsonTestAsJson);

echo $counter . "\n";
