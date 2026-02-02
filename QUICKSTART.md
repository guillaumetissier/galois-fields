# Quick Start Guide - Galois Fields PHP Library

## Installation

1. **Install dependencies:**
```bash
composer install
```

2. **Run tests:**
```bash
composer test
# or
./vendor/bin/phpunit
```

3. **Run examples:**
```bash
php examples.php
```

## Project Structure

```
galois-fields/
├── src/
│   ├── GaloisField.php              # Main entry point
│   ├── Field/
│   │   ├── GaloisFieldInterface.php      # Interface for all fields
│   │   ├── GaloisFieldFactory.php        # Factory to create fields
│   │   ├── PrimeField.php                # Implementation for GF(p)
│   │   ├── BinaryExtensionField.php      # Implementation for GF(2^n)
│   │   └── PrimitivePolynomials.php      # Storage for primitive polynomials
│   └── Exception/
│       └── InvalidFieldOrderException.php
├── tests/
│   ├── GF256Test.php                  # Tests for GF(256)
│   └── Field/
│       ├── PrimeFieldTest.php         # Tests for prime fields
│       └── GaloisFieldFactoryTest.php # Tests for factory
├── composer.json                      # Composer configuration
├── phpunit.xml                        # PHPUnit configuration
├── examples.php                       # Usage examples
└── README.md                          # Full documentation

```

## Quick Examples

### GF(256) - For QR Codes
```php
use Guillaumetissier\GaloisFields\GaloisField;

$gf = new GaloisField(256);
$result = $gf->multiply(53, 45);
```

### GF(7) - Prime Field
```php
$gf7 = new GaloisField(7);
echo $gf7->add(5, 3);  // 1 (8 mod 7)
```

### Check Valid Fields
```php
use Guillaumetissier\GaloisFields\Field\GaloisFieldFactory;

GaloisFieldFactory::isValidOrder(256);  // true
GaloisFieldFactory::isValidOrder(6);    // false (not a prime power)
```

## Development

### Run code quality checks:
```bash
composer phpstan     # Static analysis
composer cs-check    # Code style check
composer cs-fix      # Fix code style
```

### Code Coverage:
```bash
./vendor/bin/phpunit --coverage-html coverage
# Open coverage/index.html in browser
```

## What's Implemented

✅ **Prime Fields GF(p)**: Any prime p
✅ **Binary Extension Fields GF(2^n)**: n = 2 to 16
✅ **Operations**: add, subtract, multiply, divide, inverse, power
✅ **Efficient**: Logarithm tables for GF(2^n)

## What's Next

⏳ Extension fields GF(p^n) for p > 2
⏳ Polynomial arithmetic
⏳ Reed-Solomon encoding/decoding
⏳ More primitive polynomials (degree > 16)

## Need Help?

- Read the full README.md
- Check examples.php
- Look at the tests for usage patterns
- Study the inline documentation

## Contributing

1. Fork the repository
2. Create a feature branch
3. Write tests for your changes
4. Ensure all tests pass
5. Submit a pull request