<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cline\CodingStandard\Rector\Factory;
use Rector\DeadCode\Rector\Stmt\RemoveUnreachableStatementRector;
use Rector\Php85\Rector\FuncCall\OrdSingleByteRector;

return Factory::create(
    paths: [__DIR__.'/src', __DIR__.'/tests'],
    skip: [
        RemoveUnreachableStatementRector::class => [__DIR__.'/tests'],
        OrdSingleByteRector::class,
    ],
);
