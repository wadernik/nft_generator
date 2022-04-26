<?php

require 'Helpers.php';
require 'ConfigParser.php';
require 'NFTBuilder.php';
require 'SourcesHandler.php';

/**
 * @param int $amountOfBatches
 * @param array $topGarment
 * @param array $bottomGarment
 * @param array $shoes
 * @param array $bg
 * @param int $bgProbability
 * @param string $bgProbabilityRule
 * @param string $propertiesSeparator
 * @return array
 * @throws Exception
 */
function generateDataForFirstAndFourthJson(
    int $amountOfBatches,
    array $topGarment,
    array $bottomGarment,
    array $shoes,
    array $bg,
    int $bgProbability,
    string $bgProbabilityRule,
    string $propertiesSeparator
): array {
    $firstJsonData = [];
    $fourthJsonData = [];

    $topGarmentPropertiesAmount = countPropertiesVariations($topGarment);
    $bottomGarmentPropertiesAmount = countPropertiesVariations($bottomGarment);
    $shoesPropertiesAmount = countPropertiesVariations($shoes);

    $firstJsonTotalUniqueVariationsAmount = $topGarmentPropertiesAmount
        * $bottomGarmentPropertiesAmount
        * $shoesPropertiesAmount;

    $generatedDataCounter = 1;

    while ($generatedDataCounter <= $amountOfBatches) {
        // ------ Top Garment ----- //
        $topGarmentPropertyOne = pickRandomPropertyOne($topGarment);
        $topGarmentPropertyTwo = pickRandomPropertyTwo($topGarment, $topGarmentPropertyOne);
        // ------------------------ //

        // ---- Bottom Garment ---- //
        $bottomGarmentPropertyOne = pickRandomPropertyOne($bottomGarment);
        $bottomGarmentPropertyTwo = pickRandomPropertyTwo($bottomGarment, $bottomGarmentPropertyOne);
        // ------------------------ //

        // -------- Shoes --------- //
        $shoesPropertyOne = pickRandomPropertyOne($shoes);
        $shoesPropertyTwo = pickRandomPropertyTwo($shoes, $shoesPropertyOne);
        // ------------------------ //

        // --------- BG ----------- //
        // Правило: property_1 должно начинаться с $bgProbabilityRule, property_2 должно быть выбрано рандомно из
        // зависимостей $dependencies.
        // Загадываем число от 1 до 10. Если загаданное число будет меньше $bgProbability, значит применяем правило.

        //Если правило не применяем, тогда property_1 берется рандомно из того, что не подходит под правило выше,
        // а property_2 просто рандом.
        $dependencies = [$topGarmentPropertyTwo, $bottomGarmentPropertyTwo];
        [$shouldUseRule, $propertiesToChoose] = pickPropertyWithRule($bg, $bgProbability, $bgProbabilityRule);
        if ($shouldUseRule) {
            $bgPropertyOne = array_key_first($propertiesToChoose);
            $bgPropertyTwo = $dependencies[array_rand($dependencies)];
        } else {
            $bgPropertyOne = array_rand($propertiesToChoose);
            $bgPropertyTwo = pickRandomPropertyTwo($bg, $bgPropertyOne);
        }
        // ------------------------ //

        $dataToAppendToFirstJson = [
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

        $dataToAppendToFourthJson = [
            'BG' => [
                'property_1' => $bgPropertyOne,
                'property_2' => $bgPropertyTwo,
            ],
        ];

        // Пытаемся сначала генерить только уникальные сеты.
        // Как только добираемся до числа максимально возможных уникальных вариаций,
        // недостающее количество просто добавляем без проверки на уникальность
        if ($generatedDataCounter <= $firstJsonTotalUniqueVariationsAmount) {
            $firstJsonCombinedKey = $topGarmentPropertyOne . $propertiesSeparator . $topGarmentPropertyTwo
                . $propertiesSeparator . $bottomGarmentPropertyOne . $propertiesSeparator . $bottomGarmentPropertyTwo
                . $propertiesSeparator . $shoesPropertyOne . $propertiesSeparator . $shoesPropertyTwo;

            if (!isset($firstJsonData[$firstJsonCombinedKey])) {
                $firstJsonData[$firstJsonCombinedKey] = $dataToAppendToFirstJson;
                $fourthJsonData[] = $dataToAppendToFourthJson;
            } else {
                $generatedDataCounter--;
            }
        } else {
            $firstJsonData[] = $dataToAppendToFirstJson;
            $fourthJsonData[] = $dataToAppendToFourthJson;
        }

        $generatedDataCounter++;
    }

    return [$firstJsonData, $fourthJsonData];
}

/**
 * @param int $amountOfBatches
 * @param array $head
 * @param array $body
 * @param int $bodyProbability
 * @param string $bodyProbabilityRule
 * @param string $propertiesSeparator
 * @return array
 * @throws Exception
 */
function generateDataForSecondJson(
    int $amountOfBatches,
    array $head,
    array $body,
    int $bodyProbability,
    string $bodyProbabilityRule,
    string $propertiesSeparator
): array {
    $headPropertiesAmount = countPropertiesVariations($head);
    $bodyPropertiesAmount = countPropertiesVariations($body);

    $totalUniqueVariationsAmount = $headPropertiesAmount * $bodyPropertiesAmount / count($head);

    $generatedDataCounter = 1;
    $jsonData = [];

    while ($generatedDataCounter <= $amountOfBatches) {
        // --------- Head --------- //
        $headPropertyOne = pickRandomPropertyOne($head);
        $headPropertyTwo = pickRandomPropertyTwo($head, $headPropertyOne);
        // ------------------------ //

        // --------- Body --------- //
        $bodyPropertyOne = $headPropertyOne;
        [$shouldUseRule, $propertiesToChoose] = pickPropertyWithRule(
            $body[$bodyPropertyOne],
            $bodyProbability,
            $bodyProbabilityRule,
            useKey: false
        );

        if ($shouldUseRule) {
            $bodyPropertyTwo = $propertiesToChoose[array_key_first($propertiesToChoose)];
        } else {
            $bodyPropertyTwo = $propertiesToChoose[array_rand($propertiesToChoose)];
        }
        // ------------------------ //

        $dataToAppend = [
            'head' => [
                'property_1' => $headPropertyOne,
                'property_2' => $headPropertyTwo,
            ],
            'body' => [
                'property_1' => $bodyPropertyOne,
                'property_2' => $bodyPropertyTwo,
            ],
        ];

        if ($generatedDataCounter <= $totalUniqueVariationsAmount) {
            $combinedKey = $headPropertyOne . $propertiesSeparator . $headPropertyTwo
                . $propertiesSeparator . $bodyPropertyOne . $propertiesSeparator . $bodyPropertyTwo;

            if (!isset($jsonData[$combinedKey])) {
                $jsonData[$combinedKey] = $dataToAppend;
            } else {
                $generatedDataCounter--;
            }
        } else {
            $jsonData[] = $dataToAppend;
        }

        $generatedDataCounter++;
    }

    return $jsonData;
}

/**
 * @param int $amountOfBatches
 * @param array $handR
 * @param array $handL
 * @param array $bgOne
 * @param array $bgTwo
 * @param string $propertiesSeparator
 * @return array
 * @throws Exception
 */
function generateDataForThirdJson(
    int $amountOfBatches,
    array $handR,
    array $handL,
    array $bgOne,
    array $bgTwo,
    string $propertiesSeparator
): array {
    $handRPropertiesAmount = countPropertiesVariations($handR);
    $handLPropertiesAmount = countPropertiesVariations($handL);
    $bgOnePropertiesAmount = countPropertiesVariations($bgOne);
    $bgTwoPropertiesAmount = countPropertiesVariations($bgTwo);

    $totalUniqueVariationsAmount = $handRPropertiesAmount
        * $handLPropertiesAmount
        * $bgOnePropertiesAmount
        * $bgTwoPropertiesAmount;

    $generatedDataCounter = 1;
    $jsonData = [];

    while ($generatedDataCounter <= $amountOfBatches) {
        // -------- Hand R -------- //
        $handRPropertyOne = pickRandomPropertyOne($handR);
        $handRPropertyTwo = pickRandomPropertyTwo($handR, $handRPropertyOne);
        // ------------------------ //

        // -------- Hand L -------- //
        $handLPropertyOne = pickRandomPropertyOne($handL);
        $handLPropertyTwo = pickRandomPropertyTwo($handL, $handLPropertyOne);
        // ------------------------ //

        // --------- BG1 ---------- //
        $bgOnePropertyOne = pickRandomPropertyOne($bgOne);
        $bgOnePropertyTwo = pickRandomPropertyTwo($bgOne, $bgOnePropertyOne);
        // ------------------------ //

        // --------- BG2 ---------- //
        $bgTwoPropertyOne = pickRandomPropertyOne($bgTwo);
        $bgTwoPropertyTwo = pickRandomPropertyTwo($bgTwo, $bgTwoPropertyOne);
        // ------------------------ //

        $dataToAppend = [
            'hand-R' => [
                'property_1' => $handRPropertyOne,
                'property_2' => $handRPropertyTwo,
            ],
            'hand-L' => [
                'property_1' => $handLPropertyOne,
                'property_2' => $handLPropertyTwo,
            ],
            'BG1' => [
                'property_1' => $bgOnePropertyOne,
                'property_2' => $bgOnePropertyTwo,
            ],
            'BG2' => [
                'property_1' => $bgTwoPropertyOne,
                'property_2' => $bgTwoPropertyTwo,
            ],
        ];

        if ($generatedDataCounter <= $totalUniqueVariationsAmount) {
            $combinedKey = $handRPropertyOne . $propertiesSeparator . $handRPropertyTwo
                . $propertiesSeparator . $handLPropertyOne . $propertiesSeparator . $handLPropertyTwo
                . $propertiesSeparator . $bgOnePropertyOne . $propertiesSeparator . $bgOnePropertyTwo
                . $propertiesSeparator . $bgTwoPropertyOne . $propertiesSeparator . $bgTwoPropertyTwo;

            if (!isset($jsonData[$combinedKey])) {
                $jsonData[$combinedKey] = $dataToAppend;
            } else {
                $generatedDataCounter--;
            }
        } else {
            $jsonData[] = $dataToAppend;
        }

        $generatedDataCounter++;
    }

    return $jsonData;
}

/**
 * @param array $firstJson
 * @param array $secondJson
 * @param array $thirdJson
 * @param array $fourthJson
 * @param array $layers
 * @param array $outputResolution
 * @param string $outputDirectory
 * @param string $sourceDirectory
 * @param string $propertiesSeparator
 * @return array
 */
function buildFinalJsonWithNFT(
    array $firstJson,
    array $secondJson,
    array $thirdJson,
    array $fourthJson,
    array $layers,
    array $outputResolution,
    string $outputDirectory,
    string $sourceDirectory,
    string $propertiesSeparator
): array {
    // Берем первый json для определения количества сгенерированных элементов
    $amountOfElements = count($firstJson);
    $finalJson = [];

    if (!mkdir($outputDirectory) && !is_dir($outputDirectory)) {
        throw new \RuntimeException(sprintf('Directory "%s" was not created', $outputDirectory));
    }

    for ($i = 0; $i < $amountOfElements; $i++) {
        $jsonEntry = array_merge(
            $firstJson[$i],
            $secondJson[$i],
            $thirdJson[$i],
            $fourthJson[$i]
        );

        foreach ($layers as $layerName => $layerValue) {
            $finalJson[$i][$layerName] = $jsonEntry[$layerName];
        }

        buildNftImage(
            $finalJson[$i],
            $outputResolution,
            $sourceDirectory,
            $outputDirectory,
            $propertiesSeparator,
            (string) ($i + 1)
        );
    }

    return $finalJson;
}

/**
 * Основная функция
 * @throws Exception
 */
function main(): void
{
    $config = parseConfig();

    // Settings
    $amountOfBatches = $config['amount_of_batches'];
    $outputResolution = $config['output_resolution'];
    $outputDirectory = $config['output_directory'];
    $propertiesSeparator = $config['properties_separator'];
    $layers = $config['categories_layers'];

    $bgProbability = $config['categories_probabilities']['BG']['probability'] ?? 80;
    $bgProbabilityRule = $config['categories_probabilities']['BG']['rule'] ?? 'color';
    $bodyProbability = $config['categories_probabilities']['body']['probability'] ?? 50;
    $bodyProbabilityRule = $config['categories_probabilities']['body']['rule'] ?? 'clear';

    $currentPath = getcwd();
    $sourceDirectory = "$currentPath/images";
    $contents = [];
    parseFolders($sourceDirectory, $contents);

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

    [$firstJsonData, $fourthJsonData] = generateDataForFirstAndFourthJson(
        $amountOfBatches,
        $topGarment,
        $bottomGarment,
        $shoes,
        $bg,
        $bgProbability,
        $bgProbabilityRule,
        $propertiesSeparator
    );

    $secondJsonData = generateDataForSecondJson(
        $amountOfBatches,
        $head,
        $body,
        $bodyProbability,
        $bodyProbabilityRule,
        $propertiesSeparator
    );

    $thirdJsonData = generateDataForThirdJson(
        $amountOfBatches,
        $handR,
        $handL,
        $bgOne,
        $bgTwo,
        $propertiesSeparator
    );

    $firstJsonData = resetKeys($firstJsonData);
    $secondJsonData = resetKeys($secondJsonData);
    $thirdJsonData = resetKeys($thirdJsonData);
    $fourthJsonData = resetKeys($fourthJsonData);

    $finalJsonData = buildFinalJsonWithNFT(
        $firstJsonData,
        $secondJsonData,
        $thirdJsonData,
        $fourthJsonData,
        $layers,
        $outputResolution,
        $outputDirectory,
        $sourceDirectory,
        $propertiesSeparator
    );

    echo 'First json generated combinations amount: ' . count($firstJsonData) . "\n";
    echo 'Second json generated combinations amount: ' . count($secondJsonData) . "\n";
    echo 'Third json generated combinations amount: ' . count($thirdJsonData) . "\n";
    echo 'Fourth json generated combinations amount: ' . count($fourthJsonData) . "\n";
    echo 'Final json data amount: ' . count($finalJsonData) . "\n";

    $firstJsonDataAsJson = convertToJson($firstJsonData);
    $secondJsonDataAsJson = convertToJson($secondJsonData);
    $thirdJsonDataAsJson = convertToJson($thirdJsonData);
    $fourthJsonDataAsJson = convertToJson($fourthJsonData);
    $finalJsonDataAsJson = convertToJson($finalJsonData);


    try {
        file_put_contents('1.json', $firstJsonDataAsJson);
        file_put_contents('2.json', $secondJsonDataAsJson);
        file_put_contents('3.json', $thirdJsonDataAsJson);
        file_put_contents('4.json', $fourthJsonDataAsJson);
        file_put_contents('final.json', $finalJsonDataAsJson);
    } catch (\Exception $e) {
        // do nothing, idk
    }
}
