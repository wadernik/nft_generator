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
 * @param int $probability
 * @param string $probabilityRule
 * @param bool $useKey
 * @return array
 * @throws Exception
 */
function pickPropertyWithRule( array $category, int $probability, string $probabilityRule, bool $useKey = true): array
{
    $randomInt = random_int(1, 10);
    $shouldUseRule = false;

    if ($randomInt <= ($probability / 10)) {
        $shouldUseRule = true;
    }

    $propertiesToChoose = array_filter(
        $category,
        static function ($entryValue, $entryKey) use ($probabilityRule, $shouldUseRule, $useKey) {
            $hayStack = $useKey ? $entryKey : $entryValue;
            return $shouldUseRule
                ? str_starts_with($hayStack, $probabilityRule)
                : !str_starts_with($hayStack, $probabilityRule);
        },
        ARRAY_FILTER_USE_BOTH
    );

    return [$shouldUseRule, $propertiesToChoose];
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
 * @return array
 */
function resetKeys(array $data): array
{
    return array_values($data);
}

/**
 * @param array $data
 * @return string
 * @throws JsonException
 */
function convertToJson(array $data): string
{
    return json_encode($data, JSON_THROW_ON_ERROR);
}
