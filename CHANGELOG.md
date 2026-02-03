# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.2.0] - 2025-02-03

### Added
- **Polynomial support**: Polynomials with coefficients in any Galois field
  - `PolynomialInterface` — shared contract between mutable and immutable variants
  - `PolynomialImmutable` — every operation returns a new instance 
  - `Polynomial` — in-place operations with fluent chaining
  - `PolynomialFormatter` — string representation extracted into its own class (SRP)
  - `PolynomialArithmetic` — higher-level operations: GCD, Lagrange interpolation, formal derivative

#### PolynomialImmutable / Polynomial
- Factory methods: `fromCoefficients()`, `zero()`, `one()`, `constant()`, `monomial()`
- Arithmetic: `add()`, `sub()`, `mul()`, `scalarMul()`
- Euclidean division: `divmod()` → `[quotient, remainder]`, `div()`, `mod()`
- Evaluation: `evaluate(int $x)` using Horner's method (O(n))
- Comparison: `equals()`
- `Polynomial` additionally exposes setters: `setCoefficients()`, `setCoefficientAt()`

#### PolynomialFormatter
- `toString()` — human-readable representation: `"5x^2 + 3x + 7"`
  - Leading coefficient 1 is omitted (`x^2`), zero coefficients are skipped
- `toAlphaString()` — α^n notation for coefficients: `"α^25x^2 + α^1x + 1"`
  - Restricted to GF(2^n) fields, throws `BadMethodCallException` otherwise
- Works on any `PolynomialInterface` implementation (mutable or immutable)

#### PolynomialArithmetic
- `gcd(PolynomialInterface, PolynomialInterface): PolynomialImmutable`
  - Euclidean algorithm, result is monic (leading coefficient = 1)
  - Never mutates the input arguments (copies to immutable internally)
- `areCoprime(): bool`
- `multiEvaluate(PolynomialInterface, array $points): array`
- `interpolate(array $xs, array $ys): PolynomialImmutable`
  - Lagrange interpolation, unique polynomial of degree < n passing through the given points
- `derivative(PolynomialInterface): PolynomialImmutable`
  - Correct formal derivative in characteristic p (terms x^(kp) vanish)
  - In GF(2^n) in particular: all even-degree terms disappear

### Technical Details
- Coefficients are stored in descending order: `[a_n, ..., a_1, a_0]`
- Automatic normalization: leading zeros are stripped at construction time
- `Polynomial::divmod()` returns `[new quotient, $this mutated as remainder]` — consistent with mutable semantics
- `PolynomialImmutable::divmod()` returns two new instances
- `PolynomialArithmetic` is agnostic to the concrete input type: mutable and immutable polynomials can be mixed freely
- All polynomials validate that operands belong to the same field (`assertSameField`)

## [1.1.0] - 2025-02-02

### Added
- **Discrete Logarithm Method**: `log(int $element): int`
  - Returns the power n such that α^n = element
  - Essential for converting elements to their exponential representation
  - Example: `log(4)` returns `2` (because α^2 = 4)
  - Throws `InvalidArgumentException` for 0 (logarithm undefined)
- **Exponential Method**: `exp(int $power): int`
  - Returns the element α^power
  - Handles negative powers (α^-1 = α^254 in GF(256))
  - Automatic normalization for powers outside [0, order-2]
  - Example: `exp(5)` returns the element corresponding to α^5
- **Alpha Power Representation**: Enhanced symbolic notation for GF(2^n) elements
  - `toAlphaPower(int $element): string` - Convert element to α^n notation (e.g., 4 → "α^2")
  - `fromAlphaPower(string $alphaPower): int` - Convert α^n string to integer element (BREAKING: now accepts string)
  - Handles special cases: 0 → "0", 1 → "1" (canonical form α^0)
  - Multiple input formats supported: `'α^5'`, `'a^5'`, `'alpha^5'`
- **AlphaPowerTest**: Comprehensive test suite for alpha power conversion
  - Round-trip conversion tests
  - Cyclic property verification (α^255 = α^0 = 1 in GF(256))
  - Multiplication and inverse in alpha notation
  - Edge cases: negative powers, powers > order-1
  - UTF-8 handling tests for Greek alpha character
  - Tests for multiple input formats
- **examplesAlpha.php**: Demonstration file showing:
  - Element conversion to/from alpha notation
  - New `log()` and `exp()` methods usage
  - Why alpha powers simplify multiplication (α^a × α^b = α^(a+b))
  - Multiplication tables in alpha notation
  - Cyclic group properties

### Technical Details
- Alpha notation only available for binary extension fields GF(2^n)
- Canonical representation: always uses smallest positive power [0, order-2]
- Example: `toAlphaPower(1)` returns `"α^0"`, not `"α^255"`
- Logarithm table is used for efficient conversion
- `log()` and `exp()` are inverse operations: `exp(log(x)) = x`
- Clear separation of concerns:
  - `log()`/`exp()`: int ↔ int conversions (mathematical operations)
  - `toAlphaPower()`/`fromAlphaPower()`: int ↔ string conversions (symbolic representation)

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