<?php

declare(strict_types=1);

namespace Guillaumetissier\GaloisFields\Tests;

use Guillaumetissier\GaloisFields\GaloisField;
use PHPUnit\Framework\TestCase;

class AlphaPowerTest extends TestCase
{
    private GaloisField $gf;

    protected function setUp(): void
    {
        $this->gf = new GaloisField(256);
    }

    public function testToAlphaPowerForZero(): void
    {
        $this->assertSame('0', $this->gf->toAlphaPower(0));
    }

    public function testToAlphaPowerForOne(): void
    {
        $this->assertSame('α^0', $this->gf->toAlphaPower(1));
    }

    public function testToAlphaPowerForGenerator(): void
    {
        // In GF(256), 2 is the generator α
        $this->assertSame('α^1', $this->gf->toAlphaPower(2));
    }

    public function testLogOfZeroThrows(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Logarithm of 0 is undefined');
        $this->gf->log(0);
    }

    /**
     * @dataProvider dataToAlphaPower
     */
    public function testToAlphaPower(int $number, int $expectedPower): void
    {
        $this->assertSame("α^$expectedPower", $this->gf->toAlphaPower($number));
    }

    /**
     * @dataProvider dataToAlphaPower
     */
    public function testLog(int $number, int $expectedLog): void
    {
        $this->assertSame($expectedLog, $this->gf->log($number));
    }

    public static function dataToAlphaPower(): \Generator
    {
        yield [1, 0];
        yield [2, 1];
        yield [3, 25];
        yield [4, 2];
        yield [5, 50];
        yield [6, 26];
        yield [7, 198];
        yield [8, 3];
        yield [9, 223];
        yield [10, 51];
        yield [11, 238];
        yield [12, 27];
        yield [13, 104];
        yield [14, 199];
        yield [15, 75];
        yield [16, 4];
        yield [17, 100];
        yield [18, 224];
        yield [19, 14];
        yield [20, 52];
        yield [21, 141];
        yield [22, 239];
        yield [23, 129];
        yield [24, 28];
        yield [25, 193];
        yield [26, 105];
        yield [27, 248];
        yield [28, 200];
        yield [29, 8];
        yield [30, 76];
        yield [31, 113];
        yield [32, 5];
        yield [33, 138];
        yield [34, 101];
        yield [35, 47];
        yield [36, 225];
        yield [37, 36];
        yield [38, 15];
        yield [39, 33];
        yield [40, 53];
        yield [41, 147];
        yield [42, 142];
        yield [43, 218];
        yield [44, 240];
        yield [45, 18];
        yield [46, 130];
        yield [47, 69];
        yield [48, 29];
        yield [49, 181];
        yield [50, 194];
        yield [51, 125];
        yield [52, 106];
        yield [53, 39];
        yield [54, 249];
        yield [55, 185];
        yield [56, 201];
        yield [57, 154];
        yield [58, 9];
        yield [59, 120];
        yield [60, 77];
        yield [61, 228];
        yield [62, 114];
        yield [63, 166];
        yield [64, 6];
        yield [65, 191];
        yield [66, 139];
        yield [67, 98];
        yield [68, 102];
        yield [69, 221];
        yield [70, 48];
        yield [71, 253];
        yield [72, 226];
        yield [73, 152];
        yield [74, 37];
        yield [75, 179];
        yield [76, 16];
        yield [77, 145];
        yield [78, 34];
        yield [79, 136];
        yield [80, 54];
        yield [81, 208];
        yield [82, 148];
        yield [83, 206];
        yield [84, 143];
        yield [85, 150];
        yield [86, 219];
        yield [87, 189];
        yield [88, 241];
        yield [89, 210];
        yield [90, 19];
        yield [91, 92];
        yield [92, 131];
        yield [93, 56];
        yield [94, 70];
        yield [95, 64];
        yield [96, 30];
        yield [97, 66];
        yield [98, 182];
        yield [99, 163];
        yield [100, 195];
        yield [101, 72];
        yield [102, 126];
        yield [103, 110];
        yield [104, 107];
        yield [105, 58];
        yield [106, 40];
        yield [107, 84];
        yield [108, 250];
        yield [109, 133];
        yield [110, 186];
        yield [111, 61];
        yield [112, 202];
        yield [113, 94];
        yield [114, 155];
        yield [115, 159];
        yield [116, 10];
        yield [117, 21];
        yield [118, 121];
        yield [119, 43];
        yield [120, 78];
        yield [121, 212];
        yield [122, 229];
        yield [123, 172];
        yield [124, 115];
        yield [125, 243];
        yield [126, 167];
        yield [127, 87];
        yield [128, 7];
        yield [129, 112];
        yield [130, 192];
        yield [131, 247];
        yield [132, 140];
        yield [133, 128];
        yield [134, 99];
        yield [135, 13];
        yield [136, 103];
        yield [137, 74];
        yield [138, 222];
        yield [139, 237];
        yield [140, 49];
        yield [141, 197];
        yield [142, 254];
        yield [143, 24];
        yield [144, 227];
        yield [145, 165];
        yield [146, 153];
        yield [147, 119];
        yield [148, 38];
        yield [149, 184];
        yield [150, 180];
        yield [151, 124];
        yield [152, 17];
        yield [153, 68];
        yield [154, 146];
        yield [155, 217];
        yield [156, 35];
        yield [157, 32];
        yield [158, 137];
        yield [159, 46];
        yield [160, 55];
        yield [161, 63];
        yield [162, 209];
        yield [163, 91];
        yield [164, 149];
        yield [165, 188];
        yield [166, 207];
        yield [167, 205];
        yield [168, 144];
        yield [169, 135];
        yield [170, 151];
        yield [171, 178];
        yield [172, 220];
        yield [173, 252];
        yield [174, 190];
        yield [175, 97];
        yield [176, 242];
        yield [177, 86];
        yield [178, 211];
        yield [179, 171];
        yield [180, 20];
        yield [181, 42];
        yield [182, 93];
        yield [183, 158];
        yield [184, 132];
        yield [185, 60];
        yield [186, 57];
        yield [187, 83];
        yield [188, 71];
        yield [189, 109];
        yield [190, 65];
        yield [191, 162];
        yield [192, 31];
        yield [193, 45];
        yield [194, 67];
        yield [195, 216];
        yield [196, 183];
        yield [197, 123];
        yield [198, 164];
        yield [199, 118];
        yield [200, 196];
        yield [201, 23];
        yield [202, 73];
        yield [203, 236];
        yield [204, 127];
        yield [205, 12];
        yield [206, 111];
        yield [207, 246];
        yield [208, 108];
        yield [209, 161];
        yield [210, 59];
        yield [211, 82];
        yield [212, 41];
        yield [213, 157];
        yield [214, 85];
        yield [215, 170];
        yield [216, 251];
        yield [217, 96];
        yield [218, 134];
        yield [219, 177];
        yield [220, 187];
        yield [221, 204];
        yield [222, 62];
        yield [223, 90];
        yield [224, 203];
        yield [225, 89];
        yield [226, 95];
        yield [227, 176];
        yield [228, 156];
        yield [229, 169];
        yield [230, 160];
        yield [231, 81];
        yield [232, 11];
        yield [233, 245];
        yield [234, 22];
        yield [235, 235];
        yield [236, 122];
        yield [237, 117];
        yield [238, 44];
        yield [239, 215];
        yield [240, 79];
        yield [241, 174];
        yield [242, 213];
        yield [243, 233];
        yield [244, 230];
        yield [245, 231];
        yield [246, 173];
        yield [247, 232];
        yield [248, 116];
        yield [249, 214];
        yield [250, 244];
        yield [251, 234];
        yield [252, 168];
        yield [253, 80];
        yield [254, 88];
        yield [255, 175];
    }

    /**
     * @dataProvider dataFromAlphaPower
     */
    public function testFromAlphaPower(int $power, int $expectedNumber): void
    {
        $this->assertSame($expectedNumber, $this->gf->fromAlphaPower("α^$power"));
    }

    /**
     * @dataProvider dataFromAlphaPower
     */
    public function testExp(int $power, int $expectedExponent): void
    {
        $this->assertSame($expectedExponent, $this->gf->exp($power));
    }

    public static function dataFromAlphaPower(): \Generator
    {
        yield [0, 1];
        yield [1, 2];
        yield [2, 4];
        yield [3, 8];
        yield [4, 16];
        yield [5, 32];
        yield [6, 64];
        yield [7, 128];
        yield [8, 29];
        yield [9, 58];
        yield [10, 116];
        yield [11, 232];
        yield [12, 205];
        yield [13, 135];
        yield [14, 19];
        yield [15, 38];
        yield [16, 76];
        yield [17, 152];
        yield [18, 45];
        yield [19, 90];
        yield [20, 180];
        yield [21, 117];
        yield [22, 234];
        yield [23, 201];
        yield [24, 143];
        yield [25, 3];
        yield [26, 6];
        yield [27, 12];
        yield [28, 24];
        yield [29, 48];
        yield [30, 96];
        yield [31, 192];
        yield [32, 157];
        yield [33, 39];
        yield [34, 78];
        yield [35, 156];
        yield [36, 37];
        yield [37, 74];
        yield [38, 148];
        yield [39, 53];
        yield [40, 106];
        yield [41, 212];
        yield [42, 181];
        yield [43, 119];
        yield [44, 238];
        yield [45, 193];
        yield [46, 159];
        yield [47, 35];
        yield [48, 70];
        yield [49, 140];
        yield [50, 5];
        yield [51, 10];
        yield [52, 20];
        yield [53, 40];
        yield [54, 80];
        yield [55, 160];
        yield [56, 93];
        yield [57, 186];
        yield [58, 105];
        yield [59, 210];
        yield [60, 185];
        yield [61, 111];
        yield [62, 222];
        yield [63, 161];
        yield [64, 95];
        yield [65, 190];
        yield [66, 97];
        yield [67, 194];
        yield [68, 153];
        yield [69, 47];
        yield [70, 94];
        yield [71, 188];
        yield [72, 101];
        yield [73, 202];
        yield [74, 137];
        yield [75, 15];
        yield [76, 30];
        yield [77, 60];
        yield [78, 120];
        yield [79, 240];
        yield [80, 253];
        yield [81, 231];
        yield [82, 211];
        yield [83, 187];
        yield [84, 107];
        yield [85, 214];
        yield [86, 177];
        yield [87, 127];
        yield [88, 254];
        yield [89, 225];
        yield [90, 223];
        yield [91, 163];
        yield [92, 91];
        yield [93, 182];
        yield [94, 113];
        yield [95, 226];
        yield [96, 217];
        yield [97, 175];
        yield [98, 67];
        yield [99, 134];
        yield [100, 17];
        yield [101, 34];
        yield [102, 68];
        yield [103, 136];
        yield [104, 13];
        yield [105, 26];
        yield [106, 52];
        yield [107, 104];
        yield [108, 208];
        yield [109, 189];
        yield [110, 103];
        yield [111, 206];
        yield [112, 129];
        yield [113, 31];
        yield [114, 62];
        yield [115, 124];
        yield [116, 248];
        yield [117, 237];
        yield [118, 199];
        yield [119, 147];
        yield [120, 59];
        yield [121, 118];
        yield [122, 236];
        yield [123, 197];
        yield [124, 151];
        yield [125, 51];
        yield [126, 102];
        yield [127, 204];
        yield [128, 133];
        yield [129, 23];
        yield [130, 46];
        yield [131, 92];
        yield [132, 184];
        yield [133, 109];
        yield [134, 218];
        yield [135, 169];
        yield [136, 79];
        yield [137, 158];
        yield [138, 33];
        yield [139, 66];
        yield [140, 132];
        yield [141, 21];
        yield [142, 42];
        yield [143, 84];
        yield [144, 168];
        yield [145, 77];
        yield [146, 154];
        yield [147, 41];
        yield [148, 82];
        yield [149, 164];
        yield [150, 85];
        yield [151, 170];
        yield [152, 73];
        yield [153, 146];
        yield [154, 57];
        yield [155, 114];
        yield [156, 228];
        yield [157, 213];
        yield [158, 183];
        yield [159, 115];
        yield [160, 230];
        yield [161, 209];
        yield [162, 191];
        yield [163, 99];
        yield [164, 198];
        yield [165, 145];
        yield [166, 63];
        yield [167, 126];
        yield [168, 252];
        yield [169, 229];
        yield [170, 215];
        yield [171, 179];
        yield [172, 123];
        yield [173, 246];
        yield [174, 241];
        yield [175, 255];
        yield [176, 227];
        yield [177, 219];
        yield [178, 171];
        yield [179, 75];
        yield [180, 150];
        yield [181, 49];
        yield [182, 98];
        yield [183, 196];
        yield [184, 149];
        yield [185, 55];
        yield [186, 110];
        yield [187, 220];
        yield [188, 165];
        yield [189, 87];
        yield [190, 174];
        yield [191, 65];
        yield [192, 130];
        yield [193, 25];
        yield [194, 50];
        yield [195, 100];
        yield [196, 200];
        yield [197, 141];
        yield [198, 7];
        yield [199, 14];
        yield [200, 28];
        yield [201, 56];
        yield [202, 112];
        yield [203, 224];
        yield [204, 221];
        yield [205, 167];
        yield [206, 83];
        yield [207, 166];
        yield [208, 81];
        yield [209, 162];
        yield [210, 89];
        yield [211, 178];
        yield [212, 121];
        yield [213, 242];
        yield [214, 249];
        yield [215, 239];
        yield [216, 195];
        yield [217, 155];
        yield [218, 43];
        yield [219, 86];
        yield [220, 172];
        yield [221, 69];
        yield [222, 138];
        yield [223, 9];
        yield [224, 18];
        yield [225, 36];
        yield [226, 72];
        yield [227, 144];
        yield [228, 61];
        yield [229, 122];
        yield [230, 244];
        yield [231, 245];
        yield [232, 247];
        yield [233, 243];
        yield [234, 251];
        yield [235, 235];
        yield [236, 203];
        yield [237, 139];
        yield [238, 11];
        yield [239, 22];
        yield [240, 44];
        yield [241, 88];
        yield [242, 176];
        yield [243, 125];
        yield [244, 250];
        yield [245, 233];
        yield [246, 207];
        yield [247, 131];
        yield [248, 27];
        yield [249, 54];
        yield [250, 108];
        yield [251, 216];
        yield [252, 173];
        yield [253, 71];
        yield [254, 142];
    }

    public function testFromAlphaPowerWithNegativePower(): void
    {
        // α^(-1) should equal α^254 in GF(256)
        $this->assertSame(
            $this->gf->fromAlphaPower('α^254'),
            $this->gf->fromAlphaPower('a^-1')
        );
    }

    public function testFromAlphaPowerWithLargePower(): void
    {
        // α^255 = α^0 = 1 (cyclic group of order 255)
        $this->assertSame(1, $this->gf->fromAlphaPower('α^255'));
        $this->assertSame(1, $this->gf->fromAlphaPower('α^0'));

        // α^256 = α^1 = 2
        $this->assertSame(2, $this->gf->fromAlphaPower('α^256'));
        $this->assertSame(2, $this->gf->fromAlphaPower('α^1'));
    }

    public function testToAlphaPowerThrowsForInvalidElement(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->gf->toAlphaPower(256); // Out of range
    }

    public function testPrimeFieldThrowsException(): void
    {
        $gf7 = new GaloisField(7);

        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessage('toAlphaPower() is only available for binary extension fields');

        $gf7->toAlphaPower(3);
    }

    public function testMultiplicationInAlphaPowers(): void
    {
        $elemA = $this->gf->fromAlphaPower("α^10");
        $elemB = $this->gf->fromAlphaPower("α^20");
        $product = $this->gf->multiply($elemA, $elemB);
        $expected = $this->gf->fromAlphaPower("α^30");

        $this->assertSame($expected, $product);
    }

    public function testInverseInAlphaPowers(): void
    {
        $element = $this->gf->fromAlphaPower("α^48");
        $inverse = $this->gf->inverse($element);
        $expectedInverse = $this->gf->fromAlphaPower("α^-48");

        $this->assertSame($expectedInverse, $inverse);
        $this->assertSame(1, $this->gf->multiply($element, $inverse));
    }
}
