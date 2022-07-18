<?php

class JsonGenerator
{
    /**
     * @param int $amountOfBatches
     * @param array $objects
     * @param array $colorRatio
     * @param array $rarity
     * @return array
     */
    public static function generate(
        int $amountOfBatches,
        array $objects,
        array $colorRatio = [],
        array $rarity = []
    ): array {
        $jsonData = [];

        $totalUniqueVariationsAmount = 1;
        $generatedDataCounter = 1;

        foreach ($objects as $object) {
            $totalUniqueVariationsAmount *= self::countPropertiesVariations($object);
        }

        $dataToAppend = [];
        foreach ($objects as $objectName => $object) {
            // print_r($object);

            $colorRatioSource = $colorRatio[$objectName]['source'] ?? null;
            $colorRatioColors = $colorRatio[$objectName]['colors'] ?? [];

            $sourceColor = $dataToAppend[$colorRatioSource]['property_1'] ?? null;

            if ($sourceColor && isset($colorRatioColors[$sourceColor])) {
                $pickedColor = self::pickPropertyWithProbability($colorRatioColors[$sourceColor]);
                $colorProperty = isset($object[$pickedColor]) ? $pickedColor : self::pickRandomProperty($object);
            } else {
                $colorProperty = self::pickRandomProperty($object);
            }

            $raritySource = $rarity[$objectName] ?? [];

            if ($raritySource) {
                $pickedItem = self::pickPropertyWithProbability($raritySource);
                $item = in_array($pickedItem, $object[$colorProperty], true)
                    ? $pickedItem
                    : $object[$colorProperty][self::pickRandomProperty($object[$colorProperty])];
            } else {
                $item = $object[$colorProperty][self::pickRandomProperty($object[$colorProperty])];
            }

            $dataToAppend[$objectName] = [
                'property_1' => $colorProperty,
                'property_2' => $item,
            ];
        }

        print_r($dataToAppend);

        return $jsonData;
    }

    /**
     * @param array $category
     * @return int
     */
    private static function countPropertiesVariations(array $category): int
    {
        $amount = 0;

        foreach ($category as $propertiesVariations) {
            $amount+= count($propertiesVariations);
        }

        return $amount;
    }

    /**
     * @param array $object
     * @return string
     */
    private static function pickRandomProperty(array $object): string
    {
        return array_rand($object);
    }

    /**
     * @param array $sets
     * @return string
     */
    private static function pickPropertyWithProbability(array $sets): string
    {
        try {
            $length = 10000;

            $leftBorder = 0;

            // Convert probabilities to desired form
            foreach ($sets as $name => $rightBorder) {
                $sets[$name] = $leftBorder + ($rightBorder * $length);
                $leftBorder = $sets[$name];
            }

            $randomInt = random_int(1, $length);
            $leftBorder = 1;

            foreach ($sets as $name => $rightBorder) {
                if ($randomInt >= $leftBorder && $randomInt <= $rightBorder) {
                    return $name;
                }

                $leftBorder = $rightBorder;
            }
        } catch (\Exception $e) {
            print_r($e->getMessage());
            return '';
        }

        return '';
    }
}
