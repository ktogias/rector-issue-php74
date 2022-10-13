<?php

declare(strict_types=1);

use Rector\CodingStyle\Rector\ArrowFunction\StaticArrowFunctionRector;
use Rector\CodingStyle\Rector\ClassConst\VarConstantCommentRector;
use Rector\CodingStyle\Rector\ClassMethod\UnSpreadOperatorRector;
use Rector\CodingStyle\Rector\Closure\StaticClosureRector;
use Rector\CodingStyle\Rector\Encapsed\EncapsedStringsToSprintfRector;
use Rector\CodingStyle\Rector\Encapsed\WrapEncapsedVariableInCurlyBracesRector;
use Rector\CodingStyle\Rector\FuncCall\ConsistentPregDelimiterRector;
use Rector\CodingStyle\Rector\Plus\UseIncrementAssignRector;
use Rector\CodingStyle\Rector\PostInc\PostIncDecToPreIncDecRector;
use Rector\CodingStyle\Rector\String_\UseClassKeywordForClassNameResolutionRector;
use Rector\Config\RectorConfig;
use Rector\Php73\Rector\FuncCall\JsonThrowOnErrorRector;
use Rector\Php81\Rector\Property\ReadOnlyPropertyRector;
use Rector\Privatization\Rector\MethodCall\PrivatizeLocalGetterToPropertyRector;
use Rector\RemovingStatic\Rector\ClassMethod\LocallyCalledStaticMethodToNonStaticRector;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;
use Rector\Visibility\Rector\ClassMethod\ExplicitPublicClassMethodRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->disableParallel(); // TODO enable after tweaking

    $rectorConfig->paths([
        __DIR__ . '/src'
    ]);

    $rectorConfig->phpstanConfig(__DIR__ . '/phpstan-rector.neon');

    // define sets of rules
    $rectorConfig->sets([
        LevelSetList::UP_TO_PHP_81,
        SetList::CODE_QUALITY,
        SetList::CODING_STYLE,
        SetList::DEAD_CODE,
        SetList::EARLY_RETURN,
        SetList::TYPE_DECLARATION_STRICT,
    ]);

    // skip rules
    $rectorConfig->skip([
        EncapsedStringsToSprintfRector::class,
        JsonThrowOnErrorRector::class,
        PostIncDecToPreIncDecRector::class,
        ReadOnlyPropertyRector::class,
        StaticArrowFunctionRector::class,
        StaticClosureRector::class,
        UnSpreadOperatorRector::class,
        UseClassKeywordForClassNameResolutionRector::class,
        UseIncrementAssignRector::class,
        VarConstantCommentRector::class,
        WrapEncapsedVariableInCurlyBracesRector::class
    ]);

    $rectorConfig->rule(ExplicitPublicClassMethodRector::class);
    $rectorConfig->rule(PrivatizeLocalGetterToPropertyRector::class);
    $rectorConfig->rule(LocallyCalledStaticMethodToNonStaticRector::class);

    $rectorConfig->ruleWithConfiguration(ConsistentPregDelimiterRector::class, [
        ConsistentPregDelimiterRector::DELIMITER => '/',
    ]);
};
