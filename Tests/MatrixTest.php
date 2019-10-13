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

class MatrixTest extends TestCase
{
    /**
     * @return iterable
     */
    public function provider(): iterable
    {
        yield [[[1, 0, 0], [0, 1, 0], [0, 0, 1]]];
        yield [[[1, 5, 0], [2, 4, 6], [5, 3, 2]]];
    }

    /**
     * @dataProvider provider
     *
     * @param array $data
     * @throws MatrixException
     */
    public function testFrom(array $data): void
    {
        $matrix = ImmutableMatrix::from($data);

        $actual = $matrix->toArray();
        $expected = $data;

        $this->assertEquals($expected, $actual);
    }

    /**
     * @throws MatrixException
     */
    public function testRand(): void
    {
        $matrix = ImmutableMatrix::rand([3, 3]);

        $actual = $matrix->shape();
        $expected = [3, 3];

        $this->assertEquals($expected, $actual);
    }

    /**
     * @throws MatrixException
     */
    public function testRandn(): void
    {
        $matrix = ImmutableMatrix::randn([3, 3]);

        $actual = $matrix->shape();
        $expected = [3, 3];

        $this->assertEquals($expected, $actual);
    }

    /**
     * @throws MatrixException
     */
    public function testIsEmpty(): void
    {
        $matrix = new ImmutableMatrix([]);

        $actual = $matrix->isEmpty();

        $this->assertTrue($actual);
    }

    /**
     * @dataProvider provider
     *
     * @param array $data
     * @throws MatrixException
     */
    public function testSize(array $data): void
    {
        $matrix = new ImmutableMatrix($data);

        $actual = $matrix->size();
        $expected = 9;

        $this->assertEquals($expected, $actual);
    }

    /**
     * @dataProvider provider
     *
     * @param array $data
     * @throws MatrixException
     */
    public function testShape(array $data): void
    {
        $matrix = new ImmutableMatrix($data);

        $actual = $matrix->shape();
        $expected = [3, 3];

        $this->assertEquals($expected, $actual);
    }

    /**
     * @dataProvider provider
     *
     * @param array $data
     * @throws MatrixException
     */
    public function testIsSquare(array $data): void
    {
        $matrix = new ImmutableMatrix($data);

        $actual = $matrix->isSquare();

        $this->assertTrue($actual);
    }

    /**
     * @throws MatrixException
     */
    public function testisSingular(): void
    {
        $data = [[1, 2, 3, 4], [5, 6, 7, 8], [9, 10, 11, 12], [13, 14, 15, 16]];
        $matrix = new ImmutableMatrix($data);

        $actual = $matrix->isSingular();

        $this->assertTrue($actual);
    }

    /**
     * @throws MatrixException
     */
    public function testApply(): void
    {
        $data = [[1, 5, 0], [2, 4, 6], [5, 3, 2]];
        $matrix = new ImmutableMatrix($data);

        $actual = $matrix->apply(function ($item, $i, $j) {
            return $item * $i * $j;
        })->toArray();
        $expected = [[0, 0, 0], [0, 4, 12], [0, 6, 8]];

        $this->assertEquals($expected, $actual);
    }

    /**
     * @dataProvider provider
     *
     * @param array $data
     * @throws MatrixException
     */
    public function testToString(array $data): void
    {
        $matrix = new ImmutableMatrix($data);

        $actual = $matrix->__toString();

        $this->assertNotEmpty($actual);
    }
}
