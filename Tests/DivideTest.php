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

class DivideTest extends TestCase
{
    /**
     * @return iterable
     */
    public function provider(): iterable
    {
        yield [
            [[-2, 2, -3], [-1, 1, 3], [2, 0, -1]],
            3,
            [
                [-0.66666667, 0.66666667, -1.],
                [-0.33333333, 0.33333333, 1.],
                [0.66666667, 0, -0.33333333],
            ],
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
        $actual = MathArray::divide($matrix, $b);

        $actual = $this->round($actual);
        $expected = $this->round($expected);

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

        $actual = $m->divide($b)->toArray();

        $actual = $this->round($actual);
        $expected = $this->round($expected);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @param array $data
     * @return array
     */
    private function round(array $data): array
    {
        return array_map(function ($item) {
            return is_array($item) ? $this->round($item) : round($item, 3);
        }, $data);
    }
}
