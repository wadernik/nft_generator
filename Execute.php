<?php

require "app/ConfigParser.php";
require "app/SourcesParser.php";
require "app/JsonGenerator.php";

$config = ConfigParser::parse();
$amountOfBatches = $config['amount_of_batches'];
$outputResolution = $config['output_resolution'];
$outputDirectory = $config['output_directory'];
$propertiesSeparator = $config['properties_separator'];
$colorRatio = $config['color_ratio'];
$rarity = $config['rarity'];

$currentPath = getcwd();
$sourceDirectory = "$currentPath/source_materials";
$contents = [];
SourcesParser::parse($sourceDirectory, $propertiesSeparator, $contents);

// print_r($contents);

$topGarment = $contents['top-garment'];
$bottomGarment = $contents['bottom-garment'];
$shoes = $contents['shoes'];
$head = $contents['head'];
$body = $contents['body'];
$handL = $contents['hand-L'];
$handR = $contents['hand-R'];
$bgOne = $contents['BG1'];
$bgTwo = $contents['BG2'];
$bg = $contents['BG'];

$firstJsonData = JsonGenerator::generate(
    $amountOfBatches,
    ['top-garment' => $topGarment, 'bottom-garment' => $bottomGarment, 'shoes' => $shoes],
    $colorRatio,
    $rarity
);
// print_r($firstJsonData);

// $secondJsonData = JsonGenerator::generate(
//     $amountOfBatches,
//     ['head' => $head, 'body' => $body],
//     $colorRatio,
//     $rarity
// );
// print_r($secondJsonData);

// $thirdJsonData = JsonGenerator::generate(
//     $amountOfBatches,
//     ['hand-L' => $handL, 'hand-R' => $handR, 'BG1' => $bgOne, 'BG2' => $bgTwo]
// );
// print_r($thirdJsonData);

// $fourthJsonData = JsonGenerator::generate(
//     $amountOfBatches,
//     ['BG' => $bg],
//     $colorRatio,
//     $rarity
// );
// print_r($fourthJsonData);
