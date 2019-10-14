<?php
/**
 * Copyright © 2019. All rights reserved.
 *
 * @author Igor Agapie <igoragapie@gmail.com>
 */

declare(strict_types=1);

namespace IA\Matrix;

interface MatrixInterface
{
    /**
     * @param float|array|MatrixInterface $b
     * @return MatrixInterface
     * @throws MatrixException
     */
    public function add($b): self;

    /**
     * @param float|array|MatrixInterface $b
     * @return MatrixInterface
     * @throws MatrixException
     */
    public function sub($b): self;

    /**
     * @param float|array|MatrixInterface $b
     * @return MatrixInterface
     * @throws MatrixException
     */
    public function mul($b): self;

    /**
     * @param float|array|MatrixInterface $b
     * @return MatrixInterface
     * @throws MatrixException
     */
    public function div($b): self;

    /**
     * @param float|array|MatrixInterface $b
     * @return MatrixInterface
     * @throws MatrixException
     */
    public function dot($b): self;

    /**
     * @param callable $callback
     * @return MatrixInterface
     * @throws MatrixException
     */
    public function apply(callable $callback): self;

    /**
     * @return MatrixInterface
     * @throws MatrixException
     */
    public function negative(): self;

    /**
     * @return MatrixInterface
     * @throws MatrixException
     */
    public function transpose(): self;

    /**
     * @return MatrixInterface
     * @throws MatrixException
     */
    public function inverse(): self;

    /**
     * @return float
     * @throws MatrixException
     */
    public function determinant(): float;

    /**
     * @return float
     * @throws MatrixException
     */
    public function mean(): float;

    /**
     * @return array
     */
    public function flatten(): array;

    /**
     * @param int $precision
     * @param int $mode
     * @return MatrixInterface
     * @throws MatrixException
     */
    public function round(int $precision = 0, int $mode = PHP_ROUND_HALF_UP): self;

    /**
     * @return array
     */
    public function shape(): array;

    /**
     * @return int
     */
    public function size(): int;

    /**
     * @return bool
     */
    public function isEmpty(): bool;

    /**
     * @return bool
     */
    public function isSquare(): bool;

    /**
     * @return bool
     */
    public function isSingular(): bool;

    /**
     * @return array
     */
    public function toArray(): array;

    /**
     * @return string
     */
    public function __toString(): string;
}
