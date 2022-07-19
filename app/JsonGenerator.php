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

        while ($generatedDataCounter <= $amountOfBatches) {
            $currentBatchData = [];
            foreach ($objects as $objectName => $object) {
                // ------------ Color handling ------------------ //
                $colorRatioSource = $colorRatio[$objectName]['source'] ?? null;
                $colorRatioColors = $colorRatio[$objectName]['colors'] ?? [];
                $sourceColor = $currentBatchData[$colorRatioSource]['property_1'] ?? null;

                // print_r("Object: $objectName\n");
                // print_r("Source: $colorRatioSource\n");
                // print_r("Source color: $sourceColor\n");
                // print_r("Colors:\n");
                // print_r($colorRatioColors);

                // TODO: optimize this
                if (!empty($colorRatioSource) && empty($colorRatioColors) && $sourceColor) {
                    // print_r("Color is set to one from source\n");
                    // if (!isset($object[$sourceColor])) {
                    //     print_r("Color '$sourceColor' doesn't exist in '$objectName' pool. Picking random one.\n");
                    // }
                    $colorProperty = isset($object[$sourceColor]) ? $sourceColor : self::pickRandomProperty($object);
                }
                elseif (isset($colorRatioColors[$sourceColor])) {
                    if ($sourceColor) {
                        $pickedColor = self::pickPropertyWithProbability($colorRatioColors[$sourceColor]);
                    } else {
                        $pickedColor = self::pickPropertyWithProbability($colorRatioColors);
                    }

                    $colorProperty = isset($object[$pickedColor]) ? $pickedColor : self::pickRandomProperty($object);
                } else {
                    $colorProperty = self::pickRandomProperty($object);
                }

                // ------------ Item handling ------------------- //
                $raritySource = $rarity[$objectName] ?? [];

                if ($raritySource) {
                    $pickedItem = self::pickPropertyWithProbability($raritySource);
                    $item = in_array($pickedItem, $object[$colorProperty], true)
                        ? $pickedItem
                        : $object[$colorProperty][(int)self::pickRandomProperty($object[$colorProperty])];
                } else {
                    $item = $object[$colorProperty][(int)self::pickRandomProperty($object[$colorProperty])];
                }

                $currentBatchData[$objectName] = [
                    'property_1' => $colorProperty,
                    'property_2' => $item,
                ];
            }

            if ($generatedDataCounter <= $totalUniqueVariationsAmount) {
                $propertiesToJoin = [];
                foreach ($currentBatchData as $properties) {
                    $propertiesToJoin[] = $properties['property_1'];
                    $propertiesToJoin[] = $properties['property_2'];
                }

                $combinedKey = implode('_', $propertiesToJoin);

                if (!isset($jsonData[$combinedKey])) {
                    $jsonData[$combinedKey] = $currentBatchData;
                } else {
                    $generatedDataCounter--;
                }
            } else {
                $jsonData[] = $currentBatchData;
            }

            $generatedDataCounter++;
            // print_r($currentBatchData);
        }

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
