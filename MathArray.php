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

    /**
     * @param array $matrix
     * @param callable $callable
     * @return array
     */
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

    /**
     * @param int $n
     * @param int|null $m
     * @param int $k
     * @return array
     */
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

    /**
     * @param array|int $shape
     * @return array
     */
    public static function zeros($shape): array
    {
        $shape = (array) $shape;

        $rows = $shape[0] ?? 0;
        $columns = $shape[1] ?? null;

        return static::fill($rows, $columns, 0);
    }

    /**
     * @param array $a1
     * @param mixed $a2
     * @return array|null
     */
    public static function dot(array $a1, $a2): ?array
    {
        if (is_numeric($a2)) {
            return static::m($a1, $a2);
        }

        if (false === is_array($a2)) {
            return null;
        }

        $a2d = static::is2D($a1);
        $b2d = static::is2D($a2);

        if (false === $a2d) {
            $a1 = [$a1];
        }

        if (false === $b2d) {
            $a2 = array_chunk($a2, 1);
        }

        $columnsA = count($a1[0]);

        if ($columnsA !== count($a2)) {
            return null;
        }

        $rowsA = count($a1);
        $columnsB = count($a2[0]);

        $result = [];

        for ($cb = 0; $cb < $columnsB; ++$cb) {
            for ($ra = 0; $ra < $rowsA; ++$ra) {
                $tmp = 0;

                for ($ca = 0; $ca < $columnsA; ++$ca) {
                    $tmp += $a1[$ra][$ca] * $a2[$ca][$cb];
                }

                $result[$ra][$cb] = $tmp;
            }
        }

        if (false === $a2d || false === $b2d) {
            $result = static::flatten($result);
        }

        return $result;
    }

    /**
     * @param array $a1
     * @param mixed $a2
     * @return array|null
     */
    public static function divide(array $a1, $a2): ?array
    {
        if (is_array($a2)) {
            // TODO
        }

        if (is_scalar($a2)) {
            return static::m($a1, 1 / $a2);
        }

        return null;
    }

    /**
     * @param array $a1
     * @param mixed $a2
     * @return array|null
     */
    public static function subtract(array $a1, $a2): ?array
    {
        return static::a($a1, $a2, -1);
    }

    /**
     * @param array $a1
     * @param mixed $a2
     * @return array|null
     */
    public static function add(array $a1, $a2): ?array
    {
        return static::a($a1, $a2, 1);
    }

    /**
     * @param array $matrix
     * @return array
     */
    public static function negative(array $matrix): array
    {
        return static::m($matrix, -1);
    }

    /**
     * @param array $matrix
     * @return array
     */
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
        if (static::is2D($matrix)) {
            return array_merge(... $matrix);
        }

        return $matrix;
    }

    /**
     * @param array $matrix
     * @return string
     */
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

    private static function is2D(array $matrix): bool
    {
        return count($matrix) && is_array($matrix[0]);
    }

    private static function m(array $matrix, $scalar): array
    {
        return static::map($matrix, function ($item) use ($scalar) {
            return $item * $scalar;
        });
    }

    private static function a(array $a1, $a2, int $sign): ?array
    {
        $a1Is2d = static::is2D($a1);

        if (is_numeric($a2)) {
            $a2 = array_fill(0, $a1Is2d ? count($a1[0]) : count($a1), $a2);
        }

        if (false === is_array($a2)) {
            return null;
        }

        $a2Is2d = static::is2D($a2);

        if (false === $a1Is2d && false === $a2Is2d) {
            return static::sum($a1, $a2, $sign);
        }

        if (false === $a1Is2d) {
            $a1 = array_fill(0, count($a2), $a1);
        }

        if (false === $a2Is2d) {
            $a2 = array_fill(0, count($a1), $a2);
        }

        if (count($a1) !== count($a2) || count($a1[0]) !== count($a2[0])) {
            return null;
        }

        $result = [];

        for ($i = 0; $i < count($a1); ++$i) {
            $result[$i] = static::sum($a1[$i], $a2[$i], $sign);
        }

        return $result;
    }

    private static function sum(array $a1, array $a2, int $sign): array
    {
        $result = [];

        for ($i = 0; $i < count($a1); ++$i) {
            $result[$i] = $a1[$i] + $sign * $a2[$i];
        }

        return $result;
    }

    private static function fill(int $n, ?int $m, $value): array
    {
        if ($m) {
            $value = array_fill(0, $m, $value);
        }

        return array_fill(0, $n, $value);
    }
}
