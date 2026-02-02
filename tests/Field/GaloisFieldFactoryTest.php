<?php

declare(strict_types=1);

namespace Guillaumetissier\GaloisFields\Tests\Field;

use Guillaumetissier\GaloisFields\Exception\InvalidFieldOrderException;
use Guillaumetissier\GaloisFields\Field\GaloisFieldFactory;
use Guillaumetissier\GaloisFields\GaloisField;
use PHPUnit\Framework\TestCase;

final class GaloisFieldFactoryTest extends TestCase
{
    /**
     * @dataProvider dataValidPrimePowers
     */
    public function testValidPrimePowers(int $validOrder): void
    {
        $this->assertTrue(
            GaloisFieldFactory::isValidOrder($validOrder),
            "Order $validOrder should be valid"
        );
    }

    public static function dataValidPrimePowers(): \Generator
    {
        yield [2];
        yield [3];
        yield [4];
        yield [5];
        yield [7];
        yield [8];
        yield [9];
        yield [11];
        yield [13];
        yield [16];
        yield [17];
        yield [19];
        yield [23];
        yield [25];
        yield [27];
        yield [29];
        yield [31];
        yield [32];
        yield [64];
        yield [128];
        yield [256];
    }

    /**
     * @dataProvider dataInvalidPrimePowers
     */
    public function testInvalidOrders(int $invalidOrder): void
    {
        $this->assertFalse(
            GaloisFieldFactory::isValidOrder($invalidOrder),
            "Order $invalidOrder should be invalid"
        );
    }

    public static function dataInvalidPrimePowers(): \Generator
    {
        yield [6];
        yield [10];
        yield [12];
        yield [14];
        yield [15];
        yield [18];
        yield [20];
        yield [21];
        yield [22];
        yield [24];
        yield [26];
        yield [28];
        yield [30];
    }

    /**
     * @param array{int, int}|null $expected
     *
     * @dataProvider dataGetPrimeAndExponent
     */
    public function testGetPrimeAndExponent(int $order, ?array $expected): void
    {
        $this->assertSame($expected, GaloisFieldFactory::getPrimeAndExponent($order));
    }

    public static function dataGetPrimeAndExponent(): \Generator
    {
        yield [2, [2, 1]];
        yield [256, [2, 8]];
        yield [9, [3, 2]];
        yield [125, [5, 3]];
        yield [7, [7, 1]];
        yield [6, null];
    }

    public function testCreatePrimeField(): void
    {
        $field = GaloisFieldFactory::create(7);

        $this->assertSame(7, $field->getOrder());
        $this->assertSame(7, $field->getCharacteristic());
        $this->assertSame(1, $field->getDegree());
    }

    public function testCreateBinaryExtensionField(): void
    {
        $field = GaloisFieldFactory::create(256);

        $this->assertSame(256, $field->getOrder());
        $this->assertSame(2, $field->getCharacteristic());
        $this->assertSame(8, $field->getDegree());
    }

    public function testInvalidOrderThrowsException(): void
    {
        $this->expectException(InvalidFieldOrderException::class);
        GaloisFieldFactory::create(6);
    }

    public function testTooSmallOrderThrowsException(): void
    {
        $this->expectException(InvalidFieldOrderException::class);
        GaloisFieldFactory::create(1);
    }

    public function testGaloisFieldWrapper(): void
    {
        $gf = new GaloisField(256);

        $info = $gf->getInfo();
        $this->assertSame(256, $info['order']);
        $this->assertSame(2, $info['characteristic']);
        $this->assertSame(8, $info['degree']);
        $this->assertSame('GF(2^8)', $info['notation']);
    }

    /**
     * @dataProvider dataDifferentFieldSizes
     */
    public function testDifferentFieldSizes(int $order, int $expectedChar, int $expectedDegree): void
    {
        $gf = new GaloisField($order);
        $this->assertSame($expectedChar, $gf->getCharacteristic());
        $this->assertSame($expectedDegree, $gf->getDegree());
    }

    public static function dataDifferentFieldSizes(): \Generator
    {
        yield 'GF(2^2)' => [4, 2, 2];
        yield 'GF(2^3)' => [8, 2, 3];
        yield 'GF(2^4)' => [16, 2, 4];
        yield 'GF(2^5)' => [32, 2, 5];
        yield 'GF(2^6)' => [64, 2, 6];
        yield 'GF(2^7)' => [128, 2, 7];
        yield 'GF(2^8)' => [256, 2, 8];
    }
}
