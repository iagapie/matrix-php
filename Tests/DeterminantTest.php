<?php
/**
 * Copyright © 2019. All rights reserved.
 *
 * @author Igor Agapie <igoragapie@gmail.com>
 */

declare(strict_types=1);

use IA\M;
use IA\MatrixException;
use PHPUnit\Framework\TestCase;
use IA\MathArray as MA;

class DeterminantTest extends TestCase
{
    /**
     * @return iterable
     */
    public function provider(): iterable
    {
        yield [[[-2, 2, -3], [-1, 1, 3], [2, 0, -1]], 18];
        yield [[[1, 2, 3, 4], [5, 6, 7, 8], [9, 10, 11, 12], [13, 14, 15, 16]], 0];
        yield [[[1, 2, 3, 4], [8, 5, 6, 7], [9, 12, 10, 11], [13, 14, 16, 15]], -348];
        yield [[[1, 2, 3, 4, 1], [8, 5, 6, 7, 2], [9, 12, 10, 11, 3], [13, 14, 16, 15, 4], [10, 8, 6, 4, 2]], -240];
    }

    /**
     * @dataProvider provider
     *
     * @param array $matrix
     * @param $expected
     */
    public function testMathArray(array $matrix, int $expected): void
    {
        $actual = MA::determinant($matrix);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @dataProvider provider
     *
     * @param array $matrix
     * @param $expected
     * @throws \IA\MatrixException
     */
    public function testM(array $matrix, int $expected): void
    {
        $m = new M($matrix);

        $actual = $m->determinant();

        $this->assertEquals($expected, $actual);
    }

    /**
     * @throws \IA\MatrixException
     */
    public function testMException(): void
    {
        $m = new M([1, 2, 3]);

        $this->expectException(MatrixException::class);

        $m->determinant();
    }
}
