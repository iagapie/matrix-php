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

class SubtractTest extends TestCase
{
    /**
     * @return iterable
     */
    public function provider(): iterable
    {
        yield [
            [[-2, 2, -3], [-1, 1, 3], [2, 0, -1]],
            3,
            [[-5, -1, -6], [-4, -2, 0], [-1, -3, -4]],
        ];

        yield [
            [[-2, 2, -3], [-1, 1, 3], [2, 0, -1]],
            [1, 2, 1],
            [[-3, 0, -4], [-2, -1, 2], [1, -2, -2]],
        ];

        yield [
            [1, 2, 1],
            [[-2, 2, -3], [-1, 1, 3], [2, 0, -1]],
            [[3, 0, 4], [2, 1, -2], [-1, 2, 2]],
        ];

        yield [
            [1, 2, 1],
            [2, 3, 4],
            [-1, -1, -3],
        ];

        yield [
            [1, 2, 1],
            6,
            [-5, -4, -5],
        ];

        yield [
            [[-2, 2, -3], [-1, 1, 3], [2, 0, -1]],
            [[3, 4, 5], [12, 65, 2], [0, 1, 0]],
            [[-5, -2, -8], [-13, -64, 1], [2, -1, -1]],
        ];
    }

    /**
     * @dataProvider provider
     *
     * @param array $matrix
     * @param mixed $b
     * @param mixed $expected
     */
    public function testMathArray(array $matrix, $b, $expected): void
    {
        $actual = MathArray::subtract($matrix, $b);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @dataProvider provider
     *
     * @param array $matrix
     * @param mixed $b
     * @param mixed $expected
     * @throws \IA\MatrixException
     */
    public function testM(array $matrix, $b, $expected): void
    {
        $m = new M($matrix);

        $actual = $m->subtract($b)->toArray();

        $this->assertEquals($expected, $actual);
    }
}
