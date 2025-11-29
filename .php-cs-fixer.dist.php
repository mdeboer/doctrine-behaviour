<?php

declare(strict_types=1);

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__ . '/src')
    ->in(__DIR__ . '/test')
    ->in(__DIR__ . '/tests');

return (new PhpCsFixer\Config())
    ->setRules([
        '@PER-CS2x0' => true,
        'phpdoc_align' => true,
        'no_unused_imports' => true,
        'method_chaining_indentation' => true,
        'no_unneeded_import_alias' => true,
        'declare_strict_types' => true,
        'global_namespace_import' => [
            'import_classes' => false,
            'import_constants' => false,
            'import_functions' => false,
        ],
    ])
    ->setRiskyAllowed(true)
    ->setFinder($finder);
