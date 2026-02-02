# Galois Fields (Finite Fields) Library for PHP

A comprehensive PHP library for working with Galois fields (finite fields), supporting both prime fields GF(p) and binary extension fields GF(2^n).

## What are Galois Fields?

Galois fields (also called finite fields) are mathematical structures used in cryptography, error correction codes, and computer science. Every finite field has order p^n where p is prime and n ≥ 1.

**Key properties:**
- **GF(p)**: Prime fields with p elements (e.g., GF(7), GF(11))
- **GF(2^n)**: Binary extension fields (e.g., GF(256) used in QR codes and AES)
- All fields of the same order are isomorphic (essentially identical)

## Installation

```bash
composer require guillaumetissier/galois-fields
```

## Features

- ✅ Prime fields GF(p) for any prime p
- ✅ Binary extension fields GF(2^n) for n = 2 to 16
- ✅ All basic field operations: add, subtract, multiply, divide, power, inverse
- ✅ Efficient logarithm table implementation for GF(2^n)
- ✅ Full test coverage
- ✅ Type-safe with PHP 8.1+ strict types
- ⚠️ Extension fields GF(p^n) where p > 2 (planned)

## Usage

### Basic Example

```php
use Guillaumetissier\GaloisFields\GaloisField;

// Create GF(256) - used in QR codes
$gf256 = new GaloisField(256);

// Basic operations
$sum = $gf256->add(123, 45);        // Addition (XOR for binary fields)
$product = $gf256->multiply(53, 78); // Multiplication
$quotient = $gf256->divide(200, 17);  // Division
$inverse = $gf256->inverse(123);      // Multiplicative inverse

// Check field properties
echo $gf256->getOrder();              // 256
echo $gf256->getCharacteristic();     // 2
echo $gf256->getDegree();             // 8
```

### Prime Fields

```php
// Create GF(7)
$gf7 = new GaloisField(7);

// All operations work modulo 7
echo $gf7->add(5, 3);        // 1  (8 mod 7)
echo $gf7->multiply(4, 2);   // 1  (8 mod 7)
echo $gf7->power(3, 2);      // 2  (9 mod 7)

// Every non-zero element has an inverse
echo $gf7->multiply(3, $gf7->inverse(3)); // 1
```

### QR Code Error Correction (GF(256))

```php
$gf = new GaloisField(256);

// Reed-Solomon error correction uses GF(256)
// Polynomial evaluation in GF(256)
function evaluatePolynomial(GaloisField $gf, array $coefficients, int $x): int {
    $result = 0;
    foreach ($coefficients as $coeff) {
        $result = $gf->add($gf->multiply($result, $x), $coeff);
    }
    return $result;
}

$polynomial = [1, 2, 3]; // 1*x^2 + 2*x + 3
$value = evaluatePolynomial($gf, $polynomial, 5);
```

### Different Field Sizes

```php
// Small fields
$gf4 = new GaloisField(4);    // GF(2^2)
$gf8 = new GaloisField(8);    // GF(2^3)
$gf16 = new GaloisField(16);  // GF(2^4)

// Medium fields
$gf256 = new GaloisField(256);   // GF(2^8) - QR codes
$gf65536 = new GaloisField(65536); // GF(2^16) - requires implementation

// Prime fields
$gf2 = new GaloisField(2);    // GF(2) - simplest field
$gf11 = new GaloisField(11);  // GF(11)
$gf31 = new GaloisField(31);  // GF(31)
```

### Field Properties

```php
$gf = new GaloisField(256);

// Get detailed information
$info = $gf->getInfo();
print_r($info);
// Output:
// [
//     'order' => 256,
//     'characteristic' => 2,
//     'degree' => 8,
//     'notation' => 'GF(2^8)'
// ]

// Validate elements
$gf->isValidElement(123);  // true
$gf->isValidElement(256);  // false (out of range)
```

## Mathematical Background

### Field Operations

In a Galois field GF(q), the following operations are defined:

1. **Addition**: Closed, associative, commutative with identity 0
2. **Multiplication**: Closed, associative, commutative with identity 1
3. **Inverse**: Every non-zero element has a multiplicative inverse
4. **Distributivity**: a × (b + c) = (a × b) + (a × c)

### Binary Fields GF(2^n)

- Elements represented as polynomials over GF(2)
- Addition is XOR (⊕)
- Multiplication uses a primitive polynomial
- Efficient implementation using logarithm tables

### Prime Fields GF(p)

- Elements are integers {0, 1, ..., p-1}
- Operations are modular arithmetic mod p
- Simple and efficient implementation

## Supported Fields

### Currently Supported

- **Prime fields**: GF(p) for any prime p
- **Binary extension fields**: GF(2^n) for n in [2, 16]

### Coming Soon

- Extension fields GF(p^n) for p > 2 (e.g., GF(3^5), GF(5^3))
- Polynomial arithmetic over fields
- Reed-Solomon encoding/decoding

## Applications

- **Cryptography**: AES, elliptic curve cryptography
- **Error correction**: Reed-Solomon codes in QR codes, CDs, DVDs
- **Coding theory**: Linear codes, cyclic codes
- **Computer algebra**: Symbolic computation
- **Networking**: CRC checksums

## Testing

```bash
composer test
```

## Performance

The library uses logarithm tables for efficient multiplication in GF(2^n):

- Addition: O(1)
- Multiplication: O(1) using log tables
- Division: O(1) using log tables
- Inverse: O(1) using log tables
- Power: O(1) using log tables

For prime fields GF(p), operations are O(1) modular arithmetic.

## Contributing

Contributions are welcome! Please feel free to submit pull requests.

## License

MIT License

## References

- [Finite Field Arithmetic](https://en.wikipedia.org/wiki/Finite_field_arithmetic)
- [Reed-Solomon Error Correction](https://en.wikipedia.org/wiki/Reed%E2%80%93Solomon_error_correction)
- [Primitive Polynomials](https://en.wikipedia.org/wiki/Primitive_polynomial_(field_theory))