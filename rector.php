<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/examples',
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    // uncomment to reach your current PHP version
    ->withPhpSets(php82: true)
    ->withPreparedSets(typeDeclarations: true, codeQuality: true, deadCode: true, phpunitCodeQuality: true)
    ->withSkip([
        Rector\CodeQuality\Rector\Assign\CombinedAssignRector::class,
        Rector\PHPUnit\CodeQuality\Rector\Class_\YieldDataProviderRector::class,
    ])
;
