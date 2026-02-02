# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0] - 2025-02-02

### Added
- Initial release of Galois Fields PHP library
- **Prime Fields (GF(p))**: Complete implementation for any prime p
    - Modular arithmetic operations
    - Extended Euclidean algorithm for multiplicative inverse
    - Efficient exponentiation with binary method
- **Binary Extension Fields (GF(2^n))**: Implementation for n = 2 to 16
    - Logarithm and exponential table construction
    - XOR-based addition (characteristic 2)
    - Efficient multiplication using log tables
    - Support for primitive polynomials up to degree 16
- **Primitive Polynomials Storage**:
    - GF(2^n) for n = 2..16 (hexadecimal format)
    - GF(3^n), GF(5^n), GF(7^n) (array format, reserved for future use)
- **Factory Pattern**: Automatic field instantiation based on order
    - Validates that order is a prime power (p^n)
    - Factorizes order into prime and exponent
    - Creates appropriate field implementation
- **Complete Field Operations**:
    - Addition and subtraction
    - Multiplication and division
    - Multiplicative inverse
    - Exponentiation
    - Element validation
- **Comprehensive Test Suite**:
    - GF256Test: Tests for GF(256) used in QR codes
    - PrimeFieldTest: Tests for various prime fields (GF(7), GF(11), GF(13))
    - GaloisFieldFactoryTest: Tests for factory and field creation
    - OriginalAlgorithmTest: Validation of table construction algorithm
    - All field axioms verified (associativity, commutativity, distributivity)
- **Type Safety**:
    - PHP 8.1+ with strict types
    - PHPStan level max compliance
    - Full type annotations for all methods
    - Proper PHPDoc for complex return types
- **Documentation**:
    - Comprehensive README.md with examples
    - QUICKSTART.md for rapid onboarding
    - Mathematical background and references
    - Inline code documentation
    - Usage examples for QR codes and cryptography
- **Development Tools**:
    - PHPUnit configuration
    - PHPStan configuration
    - PHP_CodeSniffer setup
    - Composer scripts for testing and linting
    - .gitignore for common files

### Technical Details
- **Supported Fields**: GF(p) for any prime p, GF(2^n) for n=2..16
- **Performance**: O(1) operations using precomputed logarithm tables for GF(2^n)
- **Standards**: PSR-4 autoloading, PSR-12 code style
- **PHP Version**: Requires PHP 8.1 or higher
- **Dependencies**: PHPUnit 10.x for testing, PHPStan 1.x for static analysis

### Known Limitations
- Extension fields GF(p^n) where p > 2 are not yet implemented
- Maximum supported binary field is GF(2^16) = GF(65536)
- Polynomial arithmetic over fields not yet available
- Reed-Solomon encoding/decoding not yet implemented

### Use Cases
- QR code error correction (GF(256))
- AES cryptography operations
- Reed-Solomon codes for data recovery
- CRC checksums and error detection
- Computer algebra systems
- Coding theory applications

[1.0.0]: https://github.com/guillaumetissier/galois-fields/releases/tag/v1.0.0