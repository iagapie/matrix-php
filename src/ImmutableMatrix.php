<?php

/**
 * Copyright © 2019. All rights reserved.
 *
 * @author Igor Agapie <igoragapie@gmail.com>
 */

declare(strict_types=1);

namespace IA\Matrix;

class ImmutableMatrix implements MatrixInterface
{
    /**
     * @var array
     */
    protected $matrix = [];

    /**
     * @var int
     */
    protected $rows = 0;

    /**
     * @var null|int
     */
    protected $columns = null;

    /**
     * @var null|array
     */
    protected $transposed = null;

    /**
     * @var null|array
     */
    protected $inverted = null;

    /**
     * @var null|float
     */
    protected $determinant = null;

    /**
     * ImmutableMatrix constructor.
     *
     * @param array $matrix
     * @throws MatrixException
     */
    public function __construct(array $matrix = [])
    {
        $this->matrix = $matrix;
        $this->rows = count($matrix);

        if ($this->rows && is_array($matrix[0])) {
            $this->columns = count($matrix[0]);
        } else {
            $this->transposed = $matrix;
        }

        $this->validate();
    }

    /**
     * @param array $matrix
     * @return MatrixInterface
     * @throws MatrixException
     */
    public static function from(array $matrix): MatrixInterface
    {
        return new static($matrix);
    }

    /**
     * @param array $shape
     * @param int $k Index of the diagonal: 0 (the default) refers to the main diagonal,
     *               a positive value refers to an upper diagonal,
     *               and a negative value to a lower diagonal.
     * @return MatrixInterface
     * @throws MatrixException
     */
    public static function eye(array $shape, int $k = 0): MatrixInterface
    {
        if (isset($shape[0]) && false == isset($shape[1])) {
            $shape[1] = $shape[0];
        }

        if ($data = static::arrayByShape($shape, 0)) {
            for ($i = (-$k + abs($k)) / 2, $j = ($k + abs($k)) / 2; $i < $shape[0] && $j < $shape[1]; ++$i, ++$j) {
                $data[$i][$j] = 1;
            }
        }

        return new static($data);
    }

    /**
     * @param array $shape example: [], [2], [2,3]
     * @return MatrixInterface of zeros with the given shape
     * @throws MatrixException
     */
    public static function zeros(array $shape): MatrixInterface
    {
        $data = static::arrayByShape($shape, 0);
        return new static($data);
    }

    /**
     * @param array $shape
     * @return MatrixInterface
     * @throws MatrixException
     */
    public static function rand(array $shape): MatrixInterface
    {
        return static::zeros($shape)->apply(function () {
            return rand() / getrandmax();
        });
    }

    /**
     * @param array $shape
     * @return MatrixInterface
     * @throws MatrixException
     */
    public static function randn(array $shape): MatrixInterface
    {
        return static::zeros($shape)->apply(function () {
            $x = rand() / getrandmax();
            $y = rand() / getrandmax();
            return sqrt(-2 * log($x)) * cos(2 * pi() * $y);
        });
    }

    /**
     * @param float|array|MatrixInterface $b
     * @return MatrixInterface
     * @throws MatrixException
     */
    public function add($b): MatrixInterface
    {
        return $this->sum($b, 1);
    }

    /**
     * @param float|array|MatrixInterface $b
     * @return MatrixInterface
     * @throws MatrixException
     */
    public function sub($b): MatrixInterface
    {
        return $this->sum($b, -1);
    }

    /**
     * @param float|array|MatrixInterface $b
     * @return MatrixInterface
     * @throws MatrixException
     */
    public function mul($b): MatrixInterface
    {
        if (is_numeric($b)) {
            return $this->apply(function (float $item) use ($b) {
                return $item * (float) $b;
            });
        }

        if ($b instanceof MatrixInterface) {
            $b = $b->toArray();
        }

        if (false == is_array($b)) {
            throw new MatrixException('Argument "$b" is not valid.');
        }

        $bIs2d = count($b) && is_array($b[0]);
        $matrix = [];

        if ($this->is1d() && false === $bIs2d) {
            if (count($b) !== $this->rows) {
                throw new MatrixException(sprintf('Shapes (%s,) and (%s,) not aligned.', $this->rows, count($b)));
            }

            foreach ($this->matrix as $i => $item) {
                $matrix[$i] = $item * $b[$i];
            }
        } else {
            $a = $this->matrix;

            $a = $this->to2d(count($b), $a);
            $b = $this->to2d($this->rows, $b);

            $this->validateShapesAligned($a, $b);

            foreach ($a as $i => $d1) {
                foreach ($d1 as $j => $value) {
                    $matrix[$i][$j] = $value * $b[$i][$j];
                }
            }
        }

        return new static($matrix);
    }

    /**
     * @param float|array|MatrixInterface $b
     * @return MatrixInterface
     * @throws MatrixException
     */
    public function div($b): MatrixInterface
    {
        if (is_numeric($b)) {
            return $this->apply(function (float $item) use ($b) {
                return $item / (float) $b;
            });
        }

        if ($b instanceof MatrixInterface) {
            $b = $b->toArray();
        }

        // TODO: Implement div() method.
        throw new MatrixException('DIV not implemented yet.');
    }

    /**
     * @param float|array|MatrixInterface $b
     * @return MatrixInterface
     * @throws MatrixException
     */
    public function dot($b): MatrixInterface
    {
        if (is_numeric($b)) {
            return $this->mul($b);
        }

        if ($b instanceof MatrixInterface) {
            $b = $b->toArray();
        }

        if (false == is_array($b)) {
            throw new MatrixException('Argument "$b" is not valid.');
        }

        $bIs2d = count($b) && is_array($b[0]);
        $a = $this->matrix;

        if ($this->is1d()) {
            $a = [$a];
        }

        if (false === $bIs2d) {
            $b = static::flat2d($b);
        }

        $columnsA = count($a[0]);

        if ($columnsA !== count($b)) {
            throw new MatrixException(sprintf(
                'Shapes (%s,%s) and (%s,%s) not aligned.',
                count($a),
                $columnsA,
                count($b),
                count($b[0])
            ));
        }

        $rowsA = count($a);
        $columnsB = count($b[0]);

        $matrix = [];

        for ($cb = 0; $cb < $columnsB; ++$cb) {
            for ($ra = 0; $ra < $rowsA; ++$ra) {
                $tmp = 0;

                for ($ca = 0; $ca < $columnsA; ++$ca) {
                    $tmp += $a[$ra][$ca] * $b[$ca][$cb];
                }

                $matrix[$ra][$cb] = $tmp;
            }
        }

        if ($this->is1d() || false === $bIs2d) {
            $matrix = array_merge(... $matrix);
        }

        return new static($matrix);
    }

    /**
     * @param callable $callback
     * @return MatrixInterface
     * @throws MatrixException
     */
    public function apply(callable $callback): MatrixInterface
    {
        $matrix = $this->map($this->matrix, $callback);
        return new static($matrix);
    }

    /**
     * @return MatrixInterface
     * @throws MatrixException
     */
    public function negative(): MatrixInterface
    {
        return $this->apply(function ($item) {
            return -$item;
        });
    }

    /**
     * @return MatrixInterface
     * @throws MatrixException
     */
    public function transpose(): MatrixInterface
    {
        if (null === $this->transposed) {
            $this->transposed = [];

            foreach ($this->matrix as $i => $row) {
                foreach ($row as $j => $item) {
                    $this->transposed[$j][$i] = $item;
                }
            }
        }

        return new static($this->transposed);
    }

    /**
     * @return MatrixInterface
     * @throws MatrixException
     */
    public function inverse(): MatrixInterface
    {
        if (null === $this->inverted) {
            $this->validateIsSquare();
            $this->validateIsSingular();

            $matrix = $this->matrix;

            $eye = static::eye($this->shape())->toArray();

            for ($i = 0; $i < $this->rows; ++$i) {
                $isc = 1 / $matrix[$i][$i];

                for ($j = 0; $j < $this->rows; ++$j) {
                    $matrix[$i][$j] *= $isc;
                    $eye[$i][$j] *= $isc;
                }

                for ($k = 0; $k < $this->rows; ++$k) {
                    if ($k !== $i) {
                        $ksc = $matrix[$k][$i];

                        for ($m = 0; $m < $this->rows; ++$m) {
                            $matrix[$k][$m] -= $ksc * $matrix[$i][$m];
                            $eye[$k][$m] -= $ksc * $eye[$i][$m];
                        }
                    }
                }
            }

            $this->inverted = $eye;
        }

        return new static($this->inverted);
    }

    /**
     * @return float
     * @throws MatrixException
     */
    public function determinant(): float
    {
        if (null !== $this->determinant) {
            return $this->determinant;
        }

        if (1 === $this->rows && null === $this->columns) {
            return $this->determinant = (float) $this->matrix[0];
        }

        $this->validateIsSquare();

        $fun = function (array $matrix) use (&$fun): float {
            $n = count($matrix);

            if (2 === $n) {
                return $matrix[0][0] * $matrix[1][1] - $matrix[0][1] * $matrix[1][0];
            }

            $dim = 0.0;
            $sub = [];

            for ($k = 0; $k < $n; ++$k) {
                for ($i = 1, $ti = 0; $i < $n; ++$i, ++$ti) {
                    for ($j = 0, $tj = 0; $j < $n; ++$j) {
                        if ($j === $k) {
                            continue;
                        }

                        $sub[$ti][$tj++] = $matrix[$i][$j];
                    }
                }

                $dim += ((-1) ** ($k % 2)) * $matrix[0][$k] * $fun($sub);
            }

            return $dim;
        };

        return $this->determinant = $fun($this->matrix);
    }

    /**
     * @return float
     * @throws MatrixException
     */
    public function mean(): float
    {
        if ($this->isEmpty()) {
            throw new MatrixException('Matrix is empty.');
        }

        return $this->total($this->matrix) / $this->size();
    }

    /**
     * @return array
     */
    public function flatten(): array
    {
        if ($this->columns) {
            return array_merge(... $this->matrix);
        }

        return $this->matrix;
    }

    /**
     * @param int $precision
     * @param int $mode
     * @return MatrixInterface
     * @throws MatrixException
     */
    public function round(int $precision = 0, int $mode = PHP_ROUND_HALF_UP): MatrixInterface
    {
        return $this->apply(function ($item) use ($precision, $mode) {
            return round($item, $precision, $mode);
        });
    }

    /**
     * @return array
     */
    public function shape(): array
    {
        return [$this->rows, $this->columns];
    }

    /**
     * @return int
     */
    public function size(): int
    {
        return $this->rows * ($this->columns ?: 1);
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return 0 === $this->rows;
    }

    /**
     * @return bool
     */
    public function isSquare(): bool
    {
        return $this->rows === $this->columns;
    }

    /**
     * @return bool
     * @throws MatrixException
     */
    public function isSingular(): bool
    {
        return 0.0 === $this->determinant();
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
        $data = $this->matrix;
        $glue = ' ';

        if ($this->is2d()) {
            foreach ($data as &$item) {
                $item = sprintf('[%s]', join($glue, $item));
            }
            $glue = "\n";
        }

        return sprintf('[%s]', join($glue, $data));
    }

    /**
     * @throws MatrixException
     */
    protected function validate(): void
    {
        if (null === $this->columns) {
            return;
        }

        foreach ($this->matrix as $row) {
            if (count($row) !== $this->columns) {
                throw new MatrixException('Matrix is not valid.');
            }
        }
    }

    /**
     * @throws MatrixException
     */
    protected function validateIsSquare(): void
    {
        if (false === $this->isSquare()) {
            throw new MatrixException('Matrix is not a square matrix.');
        }
    }

    /**
     * @throws MatrixException
     */
    protected function validateIsSingular(): void
    {
        if ($this->isSingular()) {
            throw new MatrixException('Matrix is a singular matrix.');
        }
    }

    /**
     * @param array $a
     * @param array $b
     * @throws MatrixException
     */
    protected function validateShapesAligned(array $a, array $b): void
    {
        if (count($a) !== count($b) || count($a[0]) !== count($b[0])) {
            throw new MatrixException(sprintf(
                'Shapes (%s,%s) and (%s,%s) not aligned.',
                count($a),
                count($a[0]),
                count($b),
                count($b[0])
            ));
        }
    }

    /**
     * @return bool
     */
    protected function is1d(): bool
    {
        return null === $this->columns;
    }

    /**
     * @return bool
     */
    protected function is2d(): bool
    {
        return false === $this->is1d();
    }

    /**
     * @param array $matrix
     * @param callable $callback
     * @param array<int, mixed> $args
     * @return array
     */
    protected function map(array $matrix, callable $callback, array $args = []): array
    {
        $data = [];

        foreach ($matrix as $i => $item) {
            $params = $args;
            $params[] = $i;

            if (is_array($item)) {
                $data[$i] = $this->map($item, $callback, $params);
            } else {
                array_unshift($params, $item);
                $data[$i] = call_user_func_array($callback, $params);
            }
        }

        return $data;
    }

    /**
     * @param mixed $b
     * @param float $sign
     * @return MatrixInterface
     * @throws MatrixException
     */
    protected function sum($b, float $sign): MatrixInterface
    {
        if (is_numeric($b)) {
            return $this->apply(function (float $item) use ($b, $sign) {
                return $item + $sign * (float) $b;
            });
        }

        if ($b instanceof MatrixInterface) {
            $b = $b->toArray();
        }

        if (false === is_array($b) || 0 === count($b)) {
            throw new MatrixException('Argument "$b" is not valid.');
        }

        $bIs2d = is_array($b[0]);
        $a = $this->matrix;

        if ($this->is1d() && false === $bIs2d) {
            $matrix = $this->sum1d1d($a, $b, $sign);
        } else {
            $a = $this->to2d(count($b), $a);
            $b = $this->to2d($this->rows, $b);

            $this->validateShapesAligned($a, $b);

            $matrix = [];

            foreach ($a as $i => $item) {
                $matrix[$i] = $this->sum1d1d($item, $b[$i], $sign);
            }
        }

        return new static($matrix);
    }

    /**
     * @param array $a
     * @param array $b
     * @param float $sign
     * @return array
     */
    protected function sum1d1d(array $a, array $b, float $sign): array
    {
        $matrix = [];

        foreach ($a as $i => $item) {
            $matrix[$i] = $item + $sign * $b[$i];
        }

        return $matrix;
    }

    /**
     * @param array $data
     * @return float
     */
    protected function total(array $data): float
    {
        $sum = 0.0;

        foreach ($data as $value) {
            $sum += is_array($value) ? $this->total($value) : $value;
        }

        return $sum;
    }

    /**
     * @param int $rows
     * @param array $data
     * @return array
     */
    protected function to2d(int $rows, array $data): array
    {
        if (isset($data[0]) && false == is_array($data[0])) {
            return static::arrayOf($rows, $data);
        }

        return $data;
    }

    /**
     * @param int $n
     * @param mixed $value
     * @return array
     */
    protected static function arrayOf(int $n, $value): array
    {
        $data = [];

        for ($i = 0; $i < $n; ++$i) {
            $data[$i] = $value;
        }

        return $data;
    }

    /**
     * @param array $shape
     * @param mixed $value
     * @return array
     */
    protected static function arrayByShape(array $shape, $value): array
    {
        $data = $value;

        for ($i = count($shape) - 1; $i >= 0; --$i) {
            $data = static::arrayOf($shape[$i], $data);
        }

        return $data ?: [];
    }

    /**
     * @param array $flatten
     * @return array
     */
    protected static function flat2d(array $flatten): array
    {
        $matrix = [];

        foreach ($flatten as $value) {
            $matrix[] = [$value];
        }

        return $matrix;
    }
}
