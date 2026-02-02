<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Guillaumetissier\GaloisFields\GaloisField;

echo "=== Galois Fields Library Examples ===\n\n";

// Example 1: GF(256) - Used in QR codes
echo "--- Example 1: GF(256) for QR Codes ---\n";
$gf256 = new GaloisField(256);

echo "Field: GF(256) = GF(2^8)\n";
echo "Order: " . $gf256->getOrder() . "\n";
echo "Characteristic: " . $gf256->getCharacteristic() . "\n";
echo "Degree: " . $gf256->getDegree() . "\n\n";

// Basic operations
$a = 123;
$b = 45;
echo "Let a = $a, b = $b\n";
echo "a + b = " . $gf256->add($a, $b) . "\n";
echo "a × b = " . $gf256->multiply($a, $b) . "\n";
echo "a ÷ b = " . $gf256->divide($a, $b) . "\n";
echo "a⁻¹ = " . $gf256->inverse($a) . "\n";
echo "a² = " . $gf256->power($a, 2) . "\n\n";

// Verify inverse
$inv_a = $gf256->inverse($a);
echo "Verification: a × a⁻¹ = " . $gf256->multiply($a, $inv_a) . " (should be 1)\n\n";

// Example 2: GF(7) - Prime field
echo "--- Example 2: GF(7) Prime Field ---\n";
$gf7 = new GaloisField(7);

echo "Field: GF(7)\n";
echo "Elements: {0, 1, 2, 3, 4, 5, 6}\n\n";

echo "Addition table (mod 7):\n";
for ($i = 0; $i < 7; $i++) {
    for ($j = 0; $j < 7; $j++) {
        printf("%d ", $gf7->add($i, $j));
    }
    echo "\n";
}
echo "\n";

echo "Multiplication table (mod 7):\n";
for ($i = 0; $i < 7; $i++) {
    for ($j = 0; $j < 7; $j++) {
        printf("%d ", $gf7->multiply($i, $j));
    }
    echo "\n";
}
echo "\n";

// Example 3: Powers in GF(7)
echo "--- Example 3: Powers in GF(7) ---\n";
$base = 3;
echo "Powers of $base in GF(7):\n";
for ($exp = 0; $exp <= 6; $exp++) {
    $result = $gf7->power($base, $exp);
    echo "$base^$exp = $result\n";
}
echo "\n";

// Fermat's Little Theorem: a^(p-1) ≡ 1 (mod p)
echo "Fermat's Little Theorem: 3^6 mod 7 = " . $gf7->power(3, 6) . " (should be 1)\n\n";

// Example 4: Different binary fields
echo "--- Example 4: Different Binary Fields ---\n";
$fields = [
    4 => 'GF(2²) - 4 elements',
    8 => 'GF(2³) - 8 elements',
    16 => 'GF(2⁴) - 16 elements',
    256 => 'GF(2⁸) - 256 elements (QR codes)',
];

foreach ($fields as $order => $description) {
    $gf = new GaloisField($order);
    echo "$description\n";
    echo "  Characteristic: " . $gf->getCharacteristic() . "\n";
    echo "  Degree: " . $gf->getDegree() . "\n";

    // Test multiplicative group order
    $x = 2; // Non-zero element
    $groupOrder = $order - 1;
    echo "  2^$groupOrder = " . $gf->power($x, $groupOrder) . " (should be 1)\n\n";
}

// Example 5: Polynomial evaluation in GF(256)
echo "--- Example 5: Polynomial Evaluation in GF(256) ---\n";
$gf = new GaloisField(256);

// Polynomial: 1 + 2x + 3x²
$coeffs = [3, 2, 1]; // Highest degree first

function evaluatePolynomial(GaloisField $gf, array $coeffs, int $x): int {
    $result = 0;
    foreach ($coeffs as $coeff) {
        $result = $gf->add($gf->multiply($result, $x), $coeff);
    }
    return $result;
}

echo "Polynomial: p(x) = 1 + 2x + 3x²\n";
for ($x = 0; $x <= 5; $x++) {
    $value = evaluatePolynomial($gf, $coeffs, $x);
    echo "p($x) = $value\n";
}
echo "\n";

// Example 6: Demonstrating field properties
echo "--- Example 6: Field Properties in GF(11) ---\n";
$gf11 = new GaloisField(11);

$a = 7;
$b = 4;
$c = 9;

echo "Testing distributivity: a(b + c) = ab + ac\n";
$left = $gf11->multiply($a, $gf11->add($b, $c));
$right = $gf11->add($gf11->multiply($a, $b), $gf11->multiply($a, $c));
echo "  Left side: $a × ($b + $c) = $left\n";
echo "  Right side: ($a × $b) + ($a × $c) = $right\n";
echo "  Equal: " . ($left === $right ? 'YES ✓' : 'NO ✗') . "\n\n";

echo "Testing associativity: (ab)c = a(bc)\n";
$left = $gf11->multiply($gf11->multiply($a, $b), $c);
$right = $gf11->multiply($a, $gf11->multiply($b, $c));
echo "  Left side: ($a × $b) × $c = $left\n";
echo "  Right side: $a × ($b × $c) = $right\n";
echo "  Equal: " . ($left === $right ? 'YES ✓' : 'NO ✗') . "\n\n";

// Example 7: Finding generators of multiplicative group
echo "--- Example 7: Primitive Elements in GF(7) ---\n";
$gf = new GaloisField(7);
$order = 6; // |GF(7)*| = 6

echo "A primitive element generates all non-zero elements.\n";
echo "Testing which elements are primitive (generators):\n\n";

for ($g = 2; $g < 7; $g++) {
    $powers = [];
    for ($i = 1; $i <= $order; $i++) {
        $powers[] = $gf->power($g, $i);
    }
    $unique = array_unique($powers);
    $isPrimitive = count($unique) === $order;

    echo "g = $g: powers = [" . implode(', ', $powers) . "]";
    echo " → " . ($isPrimitive ? "PRIMITIVE ✓" : "not primitive") . "\n";
}

echo "\n=== Examples Complete ===\n";