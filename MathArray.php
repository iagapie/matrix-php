<?php
/**
 * Copyright © 2019. All rights reserved.
 *
 * @author Igor Agapie <igoragapie@gmail.com>
 */

declare(strict_types=1);

namespace IA;

final class MathArray
{
    /**
     * @param array $matrix
     * @return float|int
     */
    public static function determinant(array $matrix)
    {
        $rows = count($matrix);
        $columns = count($matrix[0]);
        $determinant = 0;

        if (1 === $rows && 1 === $columns) {
            $determinant = $matrix[0][0];
        } else {
            if (2 === $rows && 2 === $columns) {
                $determinant = $matrix[0][0] * $matrix[1][1] - $matrix[0][1] * $matrix[1][0];
            } else {
                for ($i = 0; $i < $rows; ++$i) {
                    $as = array_slice($matrix, 1);
                    $h = count($as);

                    for ($j = 0; $j < $h; ++$j) {
                        $as[$j] = array_merge(array_slice($as[$j], 0, $i), array_slice($as[$j], $i + 1));
                    }

                    $sign = (-1) ** ($i % 2);
                    $sub = static::determinant($as);

                    $determinant += $sign * $matrix[0][$i] * $sub;
                }
            }
        }

        return $determinant;
    }

    /**
     * @param array $matrix
     * @return array
     */
    public static function inverse(array $matrix): array
    {
        $rows = count($matrix);
        $im = static::eye($rows);

        for ($i = 0; $i < $rows; ++$i) {
            $isc = 1 / $matrix[$i][$i];

            for ($j = 0; $j < $rows; ++$j) {
                $matrix[$i][$j] *= $isc;
                $im[$i][$j] *= $isc;
            }

            for ($k = 0; $k < $rows; ++$k) {
                if ($k !== $i) {
                    $ksc = $matrix[$k][$i];

                    for ($m = 0; $m < $rows; ++$m) {
                        $matrix[$k][$m] -= $ksc * $matrix[$i][$m];
                        $im[$k][$m] -= $ksc * $im[$i][$m];
                    }
                }
            }
        }

        return $im;
    }

    public static function map(array $matrix, callable $callable): array
    {
        $rows = count($matrix);

        if (0 === $rows) {
            return $matrix;
        }

        if (false === is_array($matrix[0])) {
            return array_map($callable, $matrix);
        }

        for ($row = 0; $row < $rows; ++$row) {
            $matrix[$row] = static::map($matrix[$row], $callable);
        }

        return $matrix;
    }

    /**
     * @param array $matrix
     * @param int $precision
     * @param int $mode
     * @return array
     */
    public static function round(array $matrix, int $precision = 0, int $mode = PHP_ROUND_HALF_UP): array
    {
        return static::map($matrix, function ($item) use ($precision, $mode) {
            return round($item, $precision, $mode);
        });
    }

    public static function random(int $n, ?int $m = null): array
    {
        $matrix = [];

        if (null === $m) {
            for ($i = 0; $i < $n; ++$i) {
                $matrix[$i] = rand() / getrandmax();
            }
        } else {
            for ($i = 0; $i < $n; ++$i) {
                $matrix[$i] = static::random($m);
            }
        }

        return $matrix;
    }

    public static function eye(int $n, ?int $m = null, int $k = 0): array
    {
        if (null === $m) {
            $m = $n;
        }

        $matrix = static::zeros([$n, $m]);

        for ($i = $k < 0 ? abs($k) : 0, $j = $k < 0 ? 0 : $k; $i < count($matrix) && $j < count($matrix[$i]); ++$i, ++$j) {
            $matrix[$i][$j] = 1;
        }

        return $matrix;
    }

    public static function zeros($shape): array
    {
        list($rows, $columns) = (array) $shape;

        $row = array_fill(0, $columns ?? $rows, 0);

        return $columns ? array_fill(0, $rows, $row) : $row;
    }

    public static function dot(array $a, $b): ?array
    {
        if (is_array($b)) {
            $b = static::transpose($b);

            return static::is2D($a) ? static::multiply2DWithTransposed($a, $b) : static::multiply1DWithTransposed($a, $b);
        }

        if (is_numeric($b)) {
            return static::multiplyByScalar($a, $b);
        }

        return null;
    }

    public static function divide(array $a, $b): ?array
    {
        if (is_array($b)) {
            // TODO
        }

        if (is_scalar($b)) {
            return static::divideByScalar($a, $b);
        }

        return null;
    }

    public static function subtract(array $a, $b): ?array
    {
        if (is_array($b)) {
            // TODO
        }

        if (is_scalar($b)) {
            return static::subtractByScalar($a, $b);
        }

        return null;
    }

    public static function add(array $a, $b): ?array
    {
        if (is_array($b)) {
            // TODO
        }

        if (is_scalar($b)) {
            return static::addByScalar($a, $b);
        }

        return null;
    }

    public static function negative(array $matrix): array
    {
        return static::multiplyByScalar($matrix, -1);
    }

    public static function transpose(array $matrix): array
    {
        $transposed = [];

        if (static::is2D($matrix)) {
            for ($row = 0; $row < count($matrix); ++$row) {
                for ($column = 0; $column < count($matrix[$row]); ++$column) {
                    $transposed[$column][$row] = $matrix[$row][$column];
                }
            }
        } else {
            $transposed = $matrix;
        }

        return $transposed;
    }

    /**
     * @param array $matrix
     * @return array
     */
    public static function flatten(array $matrix): array
    {
        if (false === static::is2D($matrix)) {
            return $matrix;
        }

        $flatten = [];

        for ($i = 0; $i < count($matrix); ++$i) {
            $flatten = array_merge($flatten, $matrix[$i]);
        }

        return $flatten;
    }

    public static function is2D(array $matrix): bool
    {
        return count($matrix) && is_array($matrix[0]);
    }

    public static function toString(array $matrix): string
    {
        if (static::is2D($matrix)) {
            $matrix = array_map(function (array $row) {
                return '['.join(' ', $row).']';
            }, $matrix);

            return '['.join("\n", $matrix).']';
        }

        return '['.join(' ', $matrix).']';
    }

    private static function multiplyByScalar(array $matrix, $scalar): array
    {
        return static::map($matrix, function ($item) use ($scalar) {
            return $item * $scalar;
        });
    }

    private static function divideByScalar(array $matrix, $scalar): array
    {
        return static::map($matrix, function ($item) use ($scalar) {
            return $item / $scalar;
        });
    }

    private static function subtractByScalar(array $matrix, $scalar): array
    {
        return static::map($matrix, function ($item) use ($scalar) {
            return $item - $scalar;
        });
    }

    private static function addByScalar(array $matrix, $scalar): array
    {
        return static::map($matrix, function ($item) use ($scalar) {
            return $item + $scalar;
        });
    }

    private static function multiply2DWithTransposed(array $a, array $b): array
    {
        $multiplied = [];

        for ($i = 0; $i < count($a); ++$i) {
            $multiplied[$i] = static::multiply1DWithTransposed($a[$i], $b);
        }

        return $multiplied;
    }

    private static function multiply1DWithTransposed(array $vector, $matrix): array
    {
        $multiplied = [];

        for ($i = 0; $i < count($matrix); ++$i) {
            $multiplied[$i] = 0;

            for ($j = 0; $j < count($matrix[$i]); ++$j) {
                $multiplied[$i] += $vector[$j] * $matrix[$i][$j];
            }
        }

        return $multiplied;
    }
}
