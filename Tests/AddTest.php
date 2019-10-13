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

class AddTest extends TestCase
{
    /**
     * @return iterable
     */
    public function provider(): iterable
    {
        yield [
            [[-2, 2, -3], [-1, 1, 3], [2, 0, -1]],
            3,
            [[1, 5, 0], [2, 4, 6], [5, 3, 2]],
        ];

        yield [
            [[-2, 2, -3], [-1, 1, 3], [2, 0, -1]],
            [1, 2, 1],
            [[-1, 4, -2], [0, 3, 4], [3, 2, 0]],
        ];

        yield [
            [1, 2, 1],
            [[-2, 2, -3], [-1, 1, 3], [2, 0, -1]],
            [[-1, 4, -2], [0, 3, 4], [3, 2, 0]],
        ];

        yield [
            [1, 2, 1],
            [2, 3, 4],
            [3, 5, 5],
        ];

        yield [
            [1, 2, 1],
            6,
            [7, 8, 7],
        ];

        yield [
            [[-2, 2, -3], [-1, 1, 3], [2, 0, -1]],
            [[3, 4, 5], [12, 65, 2], [0, 1, 0]],
            [[1, 6, 2], [11, 66, 5], [2, 1, -1]],
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
        $actual = MathArray::add($matrix, $b);

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

        $actual = $m->add($b)->toArray();

        $this->assertEquals($expected, $actual);
    }
}
