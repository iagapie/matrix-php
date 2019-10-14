<?php
/**
 * Copyright © 2019. All rights reserved.
 *
 * @author Igor Agapie <igoragapie@gmail.com>
 */

declare(strict_types=1);

use IA\Matrix\ImmutableMatrix;
use IA\Matrix\MatrixException;
use PHPUnit\Framework\TestCase;

class MeanTest extends TestCase
{
    /**
     * @return iterable
     */
    public function provider(): iterable
    {
        yield [
            [[-2, 2, -3], [-1, 1, 3], [2, 0, -1]],
            0.11111
        ];

        yield [
            [[-1, 4, -2], [0, 3, 4], [3, 2, 0]],
            1.44444
        ];

        yield [
            [1, 2, 1],
            1.33333
        ];

        yield [
            [2],
            2.0
        ];

        yield [
            [[2, 2], [2, 2]],
            2.0
        ];
    }

    /**
     * @dataProvider provider
     *
     * @param array $matrix
     * @param float $expected
     * @throws MatrixException
     */
    public function testMatrix(array $matrix, float $expected): void
    {
        $m = new ImmutableMatrix($matrix);

        $actual = $m->mean();
        $actual = round($actual, 5);

        $this->assertEquals($expected, $actual);
    }
}
