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

class EyeTest extends TestCase
{
    /**
     * @return iterable
     */
    public function provider(): iterable
    {
        yield [1, null, 0, [[1]]];
        yield [1, 3, 1, [[0, 1, 0]]];
        yield [3, 3, -1, [[0, 0, 0], [1, 0, 0], [0, 1, 0]]];
        yield [3, 3, 0, [[1, 0, 0], [0, 1, 0], [0, 0, 1]]];
        yield [4, 4, -6, [[0, 0, 0, 0], [0, 0, 0, 0], [0, 0, 0, 0], [0, 0, 0, 0]]];
    }

    /**
     * @dataProvider provider
     *
     * @param int $n
     * @param int|null $m
     * @param int $k
     * @param array $expected
     */
    public function testMathArray(int $n, ?int $m, int $k, array $expected): void
    {
        $actual = MathArray::eye($n, $m, $k);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @dataProvider provider
     *
     * @param int $n
     * @param int|null $m
     * @param int $k
     * @param array $expected
     * @throws \IA\MatrixException
     */
    public function testM(int $n, ?int $m, int $k, array $expected): void
    {
        $actual = M::eye($n, $m, $k)->toArray();

        $this->assertEquals($expected, $actual);
    }
}
