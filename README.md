# Matrix PHP

[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%207.2-8892BF.svg)](https://php.net/)
[![Latest Version](https://img.shields.io/packagist/v/iagapie/matrix-php.svg)](https://packagist.org/packages/iagapie/matrix-php)
[![Build Status](https://travis-ci.org/iagapie/matrix-php.svg?branch=master)](https://travis-ci.org/iagapie/matrix-php)
[![License](https://poser.pugx.org/iagapie/matrix-php/license)](https://packagist.org/packages/iagapie/matrix-php)
[![Coverage Status](https://coveralls.io/repos/github/iagapie/matrix-php/badge.svg?branch=master)](https://coveralls.io/github/iagapie/matrix-php?branch=master)

Matrix PHP requires PHP >= 7.2.

## Installation

```
composer require iagapie/matrix-php:dev-master
```

## Simple example

```php
require_once __DIR__ . '/vendor/autoload.php';

use IA\Matrix\ImmutableMatrix as M;
use IA\Matrix\MatrixInterface;

class NeuralNetwork
{
    private $w1;
    private $w2;
    private $z2;
    private $z3;

    public function __construct($inputSize = 2, $hiddenSize = 3, $outputSize = 1)
    {
        $this->w1 = M::randn([$inputSize, $hiddenSize]);
        $this->w2 = M::randn([$hiddenSize, $outputSize]);
    }

    public function predict(MatrixInterface $x): MatrixInterface
    {
        return $this->forward($x);
    }

    public function train(MatrixInterface $x, MatrixInterface $y, int $epochs = 15000, bool $verbose = true): void
    {
        if ($verbose) {
            printf("Training Input (scaled): \n%s\n", $x);
            printf("Training Output: \n%s\n", $y);
        }

        for ($i = 0; $i < $epochs; ++$i) {
            $o = $this->forward($x);
            $this->backward($x, $y, $o);

            if ($verbose) {
                printf("\n# %s\n", $i);
                printf("Predicted Output: \n%s\n", $o->__toString());
                $loss = $y->sub($o)->apply(function ($value) { return $value * $value; })->mean();
                printf("Loss: \n%s\n", $loss);
            }
        }
    }

    private function forward(MatrixInterface $x): MatrixInterface
    {
        $z = $x->dot($this->w1);
        $this->z2 = $this->sigmoid($z);
        $this->z3 = $this->z2->dot($this->w2);
        $o = $this->sigmoid($this->z3);
        return $o;
    }

    private function backward(MatrixInterface $x, MatrixInterface $y, MatrixInterface $o): void
    {
        $error = $y->sub($o);
        $delta = $error->mul($this->sigmoidPrime($o));
        $z2Error = $delta->dot($this->w2->transpose());
        $z2Delta = $z2Error->mul($this->sigmoidPrime($this->z2));
        $this->w1 = $this->w1->add($x->transpose()->dot($z2Delta));
        $this->w2 = $this->w2->add($this->z2->transpose()->dot($delta));
    }

    private function sigmoid(MatrixInterface $s): MatrixInterface
    {
        return $s->apply(function ($value) {
            return 1 / (1 + exp(-$value));
        });
    }

    private function sigmoidPrime(MatrixInterface $s): MatrixInterface
    {
        return $s->apply(function ($value) {
            return $value * (1 -$value);
        });
    }
}

$x = M::from([[0.4, 0.9], [0.2, 0.5], [0.6, 0.6]]);
$y = M::from([[92], [86], [89]])->div(100);

$nn = new NeuralNetwork();
$nn->train($x, $y);

$xp = M::from([[1., 1.]]);
$predicted = $nn->predict($xp);

printf("\nPredicted data based on trained weights:\n");
printf("Input (scaled):\n%s\n", $xp->__toString());
printf("Output:\n%s\n", $predicted->__toString());
```

<!---
# docker-compose run --rm php-cli composer install
# docker-compose run --rm php-cli vendor/bin/phpunit
# docker-compose run --rm php-cli vendor/bin/php-coveralls -v
-->
