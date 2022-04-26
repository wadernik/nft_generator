<?php

/**
 * @param array $layers
 * @param array $outputResolution
 * @param string $sourcesDirectory
 * @param string $outputDirectory
 * @param string $propertiesSeparator
 * @param string $filename
 */
function buildNftImage(
    array $layers,
    array $outputResolution,
    string $sourcesDirectory,
    string $outputDirectory,
    string $propertiesSeparator,
    string $filename
): void {
    $baseNft = imagecreatetruecolor($outputResolution['width'], $outputResolution['height']);
    imagesavealpha($baseNft, true);

    // Последний аргумент - прозрачность.
    // Максимально возможное значение - 127, что означает полную прозрачность. Не путать!
    $color = imagecolorallocatealpha($baseNft, 0, 0, 0, 127);

    imagefill($baseNft, 0, 0, $color);

    $imageSourceWidth = $outputResolution['width'];
    $imageSourceHeight = $outputResolution['height'];

    foreach ($layers as $layerName => $properties) {
        $imageFilename = $properties['property_1'] . $propertiesSeparator . $properties['property_2'] . '.png';
        $imageFullPath = "$sourcesDirectory/$layerName/$imageFilename";

        // echo "$imageFullPath\n";
        $imageSourceSize = getimagesize($imageFullPath);
        [$imageSourceWidth, $imageSourceHeight] = [$imageSourceSize[0], $imageSourceSize[1]];
        $imageSource = imagecreatefrompng($imageFullPath);

        imagecopy($baseNft, $imageSource, 0, 0, 0, 0, $imageSourceWidth, $imageSourceHeight);
        imagedestroy($imageSource);
    }

    $resultNft = imagecreatetruecolor($outputResolution['width'], $outputResolution['height']);
    imagesavealpha($resultNft, true);
    $color = imagecolorallocatealpha($resultNft, 0, 0, 0, 127);

    imagefill($resultNft, 0, 0, $color);
    imagecopyresampled(
        $resultNft,
        $baseNft,
        0,
        0,
        0,
        0,
        $outputResolution['width'],
        $outputResolution['height'],
        $imageSourceWidth,
        $imageSourceHeight
    );

    imagepng($resultNft, './' . $outputDirectory . "/$filename.png");
    imagedestroy($resultNft);
}
