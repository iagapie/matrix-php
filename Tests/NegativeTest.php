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

class NegativeTest extends TestCase
{
    /**
     * @return iterable
     */
    public function provider(): iterable
    {
        yield [
            [[-2, 2, -3], [-1, 1, 3], [2, 0, -1]],
            [[2, -2, 3], [1, -1, -3], [-2, 0, 1]]
        ];

        yield [
            [-2, 2, -3, -1, 1, 3, 2, 0, -1],
            [2, -2, 3, 1, -1, -3, -2, 0, 1]
        ];

        yield [[], []];
    }

    /**
     * @dataProvider provider
     *
     * @param array $matrix
     * @param array $expected
     */
    public function testMathArray(array $matrix, array $expected): void
    {
        $actual = MathArray::negative($matrix);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @dataProvider provider
     *
     * @param array $matrix
     * @param array $expected
     * @throws \IA\MatrixException
     */
    public function testM(array $matrix, array $expected): void
    {
        $m = new M($matrix);

        $actual = $m->negative()->toArray();

        $this->assertEquals($expected, $actual);
    }
}
