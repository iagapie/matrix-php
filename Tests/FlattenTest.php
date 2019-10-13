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

class FlattenTest extends TestCase
{
    /**
     * @return iterable
     */
    public function provider(): iterable
    {
        yield [
            [[-2, 2, -3], [-1, 1, 3], [2, 0, -1]],
            [-2, 2, -3, -1, 1, 3, 2, 0, -1]
        ];

        yield [
            [-2, 2, -3, -1, 1, 3, 2, 0, -1],
            [-2, 2, -3, -1, 1, 3, 2, 0, -1]
        ];

        yield [[], []];
    }

    /**
     * @dataProvider provider
     *
     * @param array $matrix
     * @param array $expected
     * @throws MatrixException
     */
    public function testMatrix(array $matrix, array $expected): void
    {
        $m = new ImmutableMatrix($matrix);

        $actual = $m->flatten();

        $this->assertEquals($expected, $actual);
    }
}
