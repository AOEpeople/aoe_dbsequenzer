<?php

declare(strict_types=1);

use Rector\Arguments\Rector\ClassMethod\ArgumentAdderRector;
use Rector\CodeQuality\Rector\Equal\UseIdenticalOverEqualWithSameTypeRector;
use Rector\CodeQuality\Rector\Identical\FlipTypeControlToUseExclusiveTypeRector;
use Rector\CodeQuality\Rector\Isset_\IssetOnPropertyObjectToPropertyExistsRector;
use Rector\CodingStyle\Rector\Encapsed\EncapsedStringsToSprintfRector;
use Rector\CodingStyle\Rector\Encapsed\WrapEncapsedVariableInCurlyBracesRector;
use Rector\Config\RectorConfig;
use Rector\DeadCode\Rector\Cast\RecastingRemovalRector;
use Rector\DeadCode\Rector\Property\RemoveUnusedPrivatePropertyRector;
use Rector\Php71\Rector\FuncCall\RemoveExtraParametersRector;
use Rector\Php80\Rector\Class_\ClassPropertyAssignToConstructorPromotionRector;
use Rector\Php81\Rector\FuncCall\NullToStrictStringFuncCallArgRector;
use Rector\Php81\Rector\Property\ReadOnlyPropertyRector;
use Rector\Php83\Rector\ClassMethod\AddOverrideAttributeToOverriddenMethodsRector;
use Rector\PHPUnit\Set\PHPUnitSetList;
use Rector\Set\ValueObject\SetList;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/../Classes',
        __DIR__ . '/../Tests',
        __DIR__ . '/../code-quality',
    ])
    ->withPhpSets(
        true,
        false
    )
    ->withSets([
        SetList::CODE_QUALITY,
        SetList::CODING_STYLE,
        SetList::DEAD_CODE,
        SetList::EARLY_RETURN,
        SetList::PRIVATIZATION,
        SetList::TYPE_DECLARATION,
        SetList::PHP_80,
        SetList::PHP_81,
        SetList::PHP_82,
        SetList::PHP_83,
        PHPUnitSetList::PHPUNIT_CODE_QUALITY,
    ])
    ->withRules([
        RemoveUnusedPrivatePropertyRector::class,
    ])
    ->withSkip([
        RecastingRemovalRector::class,
        IssetOnPropertyObjectToPropertyExistsRector::class,
        FlipTypeControlToUseExclusiveTypeRector::class,

        ClassPropertyAssignToConstructorPromotionRector::class => [
            __DIR__ . '/../Classes/Service/Typo3Service.php',
        ],

        // @todo strict php
        ArgumentAdderRector::class,
        RemoveExtraParametersRector::class,
        EncapsedStringsToSprintfRector::class,
        WrapEncapsedVariableInCurlyBracesRector::class,
        UseIdenticalOverEqualWithSameTypeRector::class,
        NullToStrictStringFuncCallArgRector::class,
        ReadOnlyPropertyRector::class,
        AddOverrideAttributeToOverriddenMethodsRector::class,
    ])
    ->withAutoloadPaths([__DIR__ . '/../Classes'])
    ->withCache('.cache/rector/default')
    ->withImportNames(false);
