<?php
/**
 * Copyright © 2019. All rights reserved.
 *
 * @author Igor Agapie <igoragapie@gmail.com>
 */

declare(strict_types=1);

use IA\M;
use IA\MathArray;
use PHPUnit\Framework\TestCase;

class ZerosTest extends TestCase
{
    /**
     * @return iterable
     */
    public function provider(): iterable
    {
        yield [
            [3, 3],
            [[0, 0, 0], [0, 0, 0], [0, 0, 0]],
        ];

        yield [
            [3, 2],
            [[0, 0], [0, 0], [0, 0]],
        ];

        yield [
            [1, 3],
            [[0, 0, 0]],
        ];

        yield [
            [3],
            [0, 0, 0],
        ];

        yield [
            3,
            [0, 0, 0],
        ];

        yield [[], []];
    }

    /**
     * @dataProvider provider
     *
     * @param mixed $shape
     * @param array $expected
     */
    public function testMathArray($shape, array $expected): void
    {
        $actual = MathArray::zeros($shape);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @dataProvider provider
     *
     * @param mixed $shape
     * @param array $expected
     * @throws \IA\MatrixException
     */
    public function testM($shape, array $expected): void
    {
        $actual = M::zeros($shape)->toArray();

        $this->assertEquals($expected, $actual);
    }
}
