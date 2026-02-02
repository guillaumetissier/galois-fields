<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Guillaumetissier\GaloisFields\GaloisField;

echo "=== Alpha Power Representation in GF(256) ===\n\n";

$gf = new GaloisField(256);

// Show first few elements as alpha powers
echo "First 20 elements as alpha powers:\n";
for ($i = 0; $i < 20; $i++) {
    $alpha = $gf->toAlphaPower($i);
    printf("%3d = %-6s", $i, $alpha);

    if (($i + 1) % 4 === 0) {
        echo "\n";
    } else {
        echo "  |  ";
    }
}
echo "\n\n";

// Show multiplication in alpha notation
echo "--- Multiplication in Alpha Notation ---\n";
$a = 53;
$b = 45;
$product = $gf->multiply($a, $b);

echo "Element form: $a × $b = $product\n";
echo "Alpha form:   " . $gf->toAlphaPower($a) . " × " . $gf->toAlphaPower($b) . " = " . $gf->toAlphaPower($product) . "\n\n";

// Demonstrate that α^a × α^b = α^(a+b)
echo "--- Why Alpha Powers are Useful ---\n";
$powerA = 10;
$powerB = 20;
$elemA = $gf->fromAlphaPower($powerA);
$elemB = $gf->fromAlphaPower($powerB);
$productAB = $gf->multiply($elemA, $elemB);
$expectedPower = ($powerA + $powerB) % 255; // Cyclic group of order 255

echo "α^$powerA = $elemA\n";
echo "α^$powerB = $elemB\n";
echo "α^$powerA × α^$powerB = α^" . ($powerA + $powerB) . " = α^$expectedPower = $productAB\n";
echo "Verification: " . $gf->toAlphaPower($productAB) . "\n\n";

// Show inverse in alpha notation
echo "--- Inverse in Alpha Notation ---\n";
$elem = 123;
$inv = $gf->inverse($elem);

echo "Element: $elem = " . $gf->toAlphaPower($elem) . "\n";
echo "Inverse: $inv = " . $gf->toAlphaPower($inv) . "\n";
echo "Product: $elem × $inv = " . $gf->multiply($elem, $inv) . " (should be 1)\n\n";

// Show the cyclic nature
echo "--- Cyclic Property of the Multiplicative Group ---\n";
echo "The multiplicative group GF(256)* has order 255\n";
echo "So α^255 = α^0 = 1\n\n";

for ($power = 253; $power <= 258; $power++) {
    $normalized = $power % 255;
    $element = $gf->fromAlphaPower($power);
    echo "α^$power = α^$normalized = $element\n";
}
echo "\n";

// Practical example: Building a multiplication table in alpha notation
echo "--- Multiplication Table (Alpha Notation) ---\n";
echo "Selected elements: 2, 3, 5, 7, 11, 13\n\n";

$elements = [2, 3, 5, 7, 11, 13];

echo "       ";
foreach ($elements as $elem) {
    printf("%-8s", $gf->toAlphaPower($elem));
}
echo "\n";

foreach ($elements as $row) {
    printf("%-8s", $gf->toAlphaPower($row));
    foreach ($elements as $col) {
        $product = $gf->multiply($row, $col);
        printf("%-8s", $gf->toAlphaPower($product));
    }
    echo "\n";
}

echo "\n=== Alpha Powers Make Field Arithmetic Transparent ===\n";
