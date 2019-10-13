<?php
/**
 * Copyright © 2019. All rights reserved.
 *
 * @author Igor Agapie <igoragapie@gmail.com>
 */

declare(strict_types=1);

namespace IA;

class M
{
    /**
     * @var array
     */
    private $matrix;

    /**
     * @var array
     */
    private $transposed;

    /**
     * @var int
     */
    private $rows;

    /**
     * @var int
     */
    private $columns;

    /**
     * @var int|float|null
     */
    private $det;

    /**
     * @var null|array
     */
    private $inv;

    /**
     * Matrix constructor.
     *
     * @param array $matrix
     * @throws \IA\MatrixException
     */
    public function __construct(array $matrix = [])
    {
        $this->rows = count($matrix);

        if ($this->rows && is_array($matrix[0])) {
            $this->columns = count($matrix[0]);

            for ($row = 0; $row < $this->rows; ++$row) {
                if (count($matrix[$row]) !== $this->columns) {
                    throw new MatrixException();
                }
            }
        }

        $this->matrix = $matrix;

        $this->transposed = MathArray::transpose($matrix);
    }

    /**
     * @param int $n Number of rows in the output
     * @param int|null $m Number of the columns in the output. If NULL, defaults to $n.
     * @return \IA\M
     * @throws \IA\MatrixException
     */
    public static function random(int $n, ?int $m = null): self
    {
        if ($n < 0 || $m < 0) {
            throw new MatrixException("Negative dimensions are not allowed.");
        }

        return new self(MathArray::random($n, $m));
    }

    /**
     * @param array $matrix
     * @return \IA\M
     * @throws \IA\MatrixException
     */
    public static function from(array $matrix): self
    {
        return new self($matrix);
    }

    /**
     * @param array $flatten 1D
     * @return \IA\M
     * @throws \IA\MatrixException
     */
    public static function fromFlat(array $flatten): self
    {
        return new self(array_map(function ($item) {
            return [$item];
        }, $flatten));
    }

    /**
     * @param int $n Number of rows in the output
     * @param int|null $m Number of the columns in the output. If NULL, defaults to $n.
     * @param int $k Index of the diagonal: 0 (the default) refers to the main diagonal,
     *               a positive value refers to an upper diagonal,
     *               and a negative value to a lower diagonal.
     * @return \IA\M
     * @throws \IA\MatrixException
     */
    public static function eye(int $n, ?int $m = null, int $k = 0): self
    {
        if ($n < 0 || $m < 0) {
            throw new MatrixException("Negative dimensions are not allowed.");
        }

        return new self(MathArray::eye($n, $m, $k));
    }

    /**
     * @param int|array $shape example: 2, [], [2], [2,3]
     * @return \IA\M of zeros with the given shape
     * @throws \IA\MatrixException
     */
    public static function zeros($shape): self
    {
        return new self(MathArray::zeros($shape));
    }

    /**
     * @param int|float|array|\IA\M $b
     * @return \IA\M
     * @throws \IA\MatrixException
     */
    public function dot($b): self
    {
        if ($b instanceof self) {
            $b = $b->toArray();
        }

        $result = MathArray::dot($this->matrix, $b);

        if (null === $result) {
            throw new MatrixException();
        }

        return new self($result);
    }

    /**
     * @param int|float|array|\IA\M $b
     * @return \IA\M
     * @throws \IA\MatrixException
     */
    public function divide($b): self
    {
        if ($b instanceof self) {
            $b = $b->toArray();
        }

        if ($matrix = MathArray::divide($this->matrix, $b)) {
            return new self($matrix);
        }

        throw new MatrixException();
    }

    /**
     * @param int|float|array|\IA\M $b
     * @return \IA\M
     * @throws \IA\MatrixException
     */
    public function subtract($b): self
    {
        if ($b instanceof self) {
            $b = $b->toArray();
        }

        if ($matrix = MathArray::subtract($this->matrix, $b)) {
            return new self($matrix);
        }

        throw new MatrixException();
    }

    /**
     * @param int|float|array|\IA\M $b
     * @return \IA\M
     * @throws \IA\MatrixException
     */
    public function add($b): self
    {
        if ($b instanceof self) {
            $b = $b->toArray();
        }

        if ($matrix = MathArray::add($this->matrix, $b)) {
            return new self($matrix);
        }

        throw new MatrixException();
    }

    /**
     * @return \IA\M
     * @throws \IA\MatrixException
     */
    public function negative(): self
    {
        return new self(MathArray::negative($this->matrix));
    }

    /**
     * @return \IA\M
     * @throws \IA\MatrixException
     */
    public function transpose(): self
    {
        return new self($this->transposed);
    }

    /**
     * @return \IA\M
     * @throws \IA\MatrixException
     */
    public function inverse(): self
    {
        if (null === $this->inv) {
            if (false === $this->isSquare()) {
                throw new MatrixException();
            }

            $this->inv = MathArray::inverse($this->matrix);
        }

        return new self($this->inv);
    }

    public function flatten(): array
    {
        return MathArray::flatten($this->matrix);
    }

    /**
     * @return float|int
     * @throws \IA\MatrixException
     */
    public function determinant()
    {
        if ($this->det) {
            return $this->det;
        }

        if (false === $this->isSquare()) {
            throw new MatrixException();
        }

        return $this->det = MathArray::determinant($this->matrix);
    }

    /**
     * @param int $precision
     * @param int $mode
     * @return \IA\M
     * @throws \IA\MatrixException
     */
    public function round(int $precision = 0, int $mode = PHP_ROUND_HALF_UP): self
    {
        return new self(MathArray::round($this->matrix, $precision, $mode));
    }

    /**
     * @return array
     */
    public function shape(): array
    {
        return [$this->rows, $this->columns];
    }

    /**
     * @param int $row
     * @return int|float|array
     */
    public function getRow(int $row)
    {
        return $this->matrix[$row];
    }

    /**
     * @param int $column
     * @return int|float|array
     */
    public function getColumn(int $column)
    {
        return $this->transposed[$column];
    }

    /**
     * @return int
     */
    public function size(): int
    {
        return $this->rows * (int) $this->columns;
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return 0 === $this->size();
    }

    public function isSquare(): bool
    {
        return $this->rows === $this->columns;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return $this->matrix;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return MathArray::toString($this->matrix);
    }
}
