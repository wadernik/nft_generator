<?php

/**
 * @param array $category
 * @return string
 */
function pickRandomPropertyOne(array $category): string
{
    return array_rand($category);
}

/**
 * @param array $category
 * @param string $propertyOneKey
 * @return string
 * @throws Exception
 */
function pickRandomPropertyTwo(array $category, string $propertyOneKey): string
{
    $randomPropertyTwoKey = random_int(0, count($category[$propertyOneKey]) - 1);
    return $category[$propertyOneKey][$randomPropertyTwoKey];
}

/**
 * @param array $category
 * @return int
 */
function countPropertiesVariations(array $category): int
{
    $amount = 0;

    foreach ($category as $propertiesVariations) {
        $amount+= count($propertiesVariations);
    }

    return $amount;
}

/**
 * @param array $data
 * @return string
 * @throws JsonException
 */
function convertToJson(array $data): string
{
    $toJson = [];
    foreach ($data as $dataEntry) {
        $toJson[] = $dataEntry;
    }

    return json_encode($toJson, JSON_THROW_ON_ERROR);
}
