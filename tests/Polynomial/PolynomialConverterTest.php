<?php

declare(strict_types=1);

namespace Guillaumetissier\GaloisFields\Tests\Polynomial;

use Guillaumetissier\BitString\BitStringImmutable;
use Guillaumetissier\GaloisFields\GaloisField;
use Guillaumetissier\GaloisFields\Polynomial\PolynomialConverter;
use Guillaumetissier\GaloisFields\Polynomial\PolynomialImmutable;
use PHPUnit\Framework\TestCase;

class PolynomialConverterTest extends TestCase
{
    private GaloisField $gf256;
    private PolynomialConverter $converter;

    protected function setUp(): void
    {
        $this->gf256     = new GaloisField(256);
        $this->converter = new PolynomialConverter($this->gf256);
    }

    public function testRejectsPrimeField(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new PolynomialConverter(new GaloisField(7));
    }

    public function testFromBinaryStringSingleByte(): void
    {
        $poly = $this->converter->fromBinaryString('10110101'); // 181

        $this->assertSame(0, $poly->degree());
        $this->assertSame(181, $poly->coefficientAt(0));
    }

    public function testFromBinaryStringTwoBytes(): void
    {
        // 181x + 50
        $poly = $this->converter->fromBinaryString('1011010100110010');

        $this->assertSame(1, $poly->degree());
        $this->assertSame(181, $poly->coefficientAt(1));
        $this->assertSame(50, $poly->coefficientAt(0));
    }

    public function testFromBinaryStringThreeBytes(): void
    {
        // 255x² + 0x + 128
        $poly = $this->converter->fromBinaryString('111111110000000010000000');

        $this->assertSame(2, $poly->degree());
        $this->assertSame(255, $poly->coefficientAt(2));
        $this->assertSame(0, $poly->coefficientAt(1));
        $this->assertSame(128, $poly->coefficientAt(0));
    }

    public function testFromBinaryStringLeadingZeroCoefficientNormalized(): void
    {
        // [0, 181] → normalized to degree 0
        $poly = $this->converter->fromBinaryString('0000000010110101');

        $this->assertSame(0, $poly->degree());
        $this->assertSame(181, $poly->coefficientAt(0));
    }

    public function testFromBinaryStringAllZeros(): void
    {
        $poly = $this->converter->fromBinaryString('0000000000000000');

        $this->assertTrue($poly->isZero());
    }

    public function testFromBinaryStringLengthNotMultipleThrows(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('not a multiple of codeword width (8');
        $this->converter->fromBinaryString('1011010100'); // 10 bits
    }

    public function testGF16UsesWidth4(): void
    {
        $gf16      = new GaloisField(16); // GF(2^4) → 4-bit codewords
        $converter = new PolynomialConverter($gf16);

        // '1011' = 11, '0101' = 5  →  11x + 5
        $poly = $converter->fromBinaryString('10110101');

        $this->assertSame(1, $poly->degree());
        $this->assertSame(11, $poly->coefficientAt(1));
        $this->assertSame(5, $poly->coefficientAt(0));
    }

    public function testGF16RoundTrip(): void
    {
        $gf16      = new GaloisField(16);
        $converter = new PolynomialConverter($gf16);

        $poly   = PolynomialImmutable::fromCoefficients($gf16, [11, 5, 3]);
        $binary = $converter->toBinaryString($poly);
        $back   = $converter->fromBinaryString($binary);

        $this->assertTrue($poly->equals($back));
    }

    public function testFromBitString(): void
    {
        $bitString = BitStringImmutable::fromString('1011010100110010');
        $poly      = $this->converter->fromBitString($bitString);

        $this->assertSame(1, $poly->degree());
        $this->assertSame(181, $poly->coefficientAt(1));
        $this->assertSame(50, $poly->coefficientAt(0));
    }

    public function testToBinaryStringSingleCoefficient(): void
    {
        $poly   = PolynomialImmutable::constant($this->gf256, 181);
        $binary = $this->converter->toBinaryString($poly);

        $this->assertSame('10110101', $binary);
    }

    public function testToBinaryStringMultipleCoefficients(): void
    {
        $poly   = PolynomialImmutable::fromCoefficients($this->gf256, [181, 50]);
        $binary = $this->converter->toBinaryString($poly);

        $this->assertSame('1011010100110010', $binary);
    }

    public function testToBinaryStringZeroPadding(): void
    {
        // 5x + 3  →  '00000101 00000011'
        $poly   = PolynomialImmutable::fromCoefficients($this->gf256, [5, 3]);
        $binary = $this->converter->toBinaryString($poly);

        $this->assertSame('0000010100000011', $binary);
    }

    public function testToBinaryStringZeroPolynomial(): void
    {
        $poly   = PolynomialImmutable::zero($this->gf256);
        $binary = $this->converter->toBinaryString($poly);

        $this->assertSame('', $binary);
    }

    public function testToBitString(): void
    {
        $poly   = PolynomialImmutable::fromCoefficients($this->gf256, [181, 50]);
        $result = $this->converter->toBitString($poly);

        $this->assertInstanceOf(BitStringImmutable::class, $result);
        $this->assertSame('1011010100110010', $result->toString());
    }

    public function testRoundTripFromToBinary(): void
    {
        $binary = '10110101001100101111111100000001';

        $poly   = $this->converter->fromBinaryString($binary);
        $result = $this->converter->toBinaryString($poly);

        $this->assertSame($binary, $result);
    }

    public function testRoundTripToBinaryFrom(): void
    {
        $poly   = PolynomialImmutable::fromCoefficients($this->gf256, [200, 100, 50, 25]);
        $binary = $this->converter->toBinaryString($poly);
        $back   = $this->converter->fromBinaryString($binary);

        $this->assertTrue($poly->equals($back));
    }

    public function testRoundTripBitString(): void
    {
        $original = BitStringImmutable::fromString('10110101001100101111111100000001');

        $poly   = $this->converter->fromBitString($original);
        $result = $this->converter->toBitString($poly);

        $this->assertSame($original->toString(), $result->toString());
    }

    public function testRoundTripLeadingZeroCoefficientLoss(): void
    {
        // Leading zero is normalized away — documented, expected.
        $poly         = $this->converter->fromBinaryString('0000000010110101');
        $backToBinary = $this->converter->toBinaryString($poly);

        $this->assertSame('10110101', $backToBinary);
    }
}
