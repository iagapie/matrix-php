<?php

/**
 * Copyright © 2019. All rights reserved.
 *
 * @author Igor Agapie <igoragapie@gmail.com>
 */

declare(strict_types=1);

namespace IA\Matrix\Tests;

use IA\Matrix\ImmutableMatrix;
use IA\Matrix\MatrixException;
use PHPUnit\Framework\TestCase;

class EyeTest extends TestCase
{
    /**
     * @return iterable
     */
    public function provider(): iterable
    {
        yield [[1], 0, [[1]]];
        yield [[1, null], 0, [[1]]];
        yield [[1, 0], 0, [[]]];
        yield [[1, 3], 1, [[0, 1, 0]]];
        yield [[3, 3], -1, [[0, 0, 0], [1, 0, 0], [0, 1, 0]]];
        yield [[3, 3], 0, [[1, 0, 0], [0, 1, 0], [0, 0, 1]]];
        yield [[4, 4], -6, [[0, 0, 0, 0], [0, 0, 0, 0], [0, 0, 0, 0], [0, 0, 0, 0]]];
    }

    /**
     * @dataProvider provider
     *
     * @param array $shape
     * @param int $k
     * @param array $expected
     * @throws MatrixException
     */
    public function testMatrix(array $shape, int $k, array $expected): void
    {
        $actual = ImmutableMatrix::eye($shape, $k)->toArray();

        $this->assertEquals($expected, $actual);
    }
}
