<?php

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$finder = Finder::create()
    ->in(
        [
            'src',
            'bin',
            'tests',
        ]
    );

$config = (new Config)
    ->setRiskyAllowed(true)
    ->setRules([
        '@Symfony' => true,
        '@PSR2' => true,
        'declare_strict_types' => true,
        'align_multiline_comment' => true,
        'array_syntax' => ['syntax' => 'short'],
        'blank_line_after_opening_tag' => true,
        'blank_line_before_statement' => true,
        'combine_consecutive_unsets' => true,
        'cast_spaces' => true,
        'encoding' => true,
        'list_syntax' => ['syntax' => 'long'],
        'no_null_property_initialization' => true,
        'no_unreachable_default_argument_value' => true,
        'no_useless_else' => true,
        'no_closing_tag' => true,
        'no_useless_return' => true,
        'no_leading_import_slash' => true,
        'ordered_class_elements' => true,
        'ordered_imports' => true,
        'phpdoc_add_missing_param_annotation' => true,
        'phpdoc_order' => true,
        'phpdoc_align' => true,
        'phpdoc_types_order' => true,
        'protected_to_private' => true,
        'semicolon_after_instruction' => true,
        'single_blank_line_at_eof' => true,
        'single_blank_line_before_namespace' => true,
        'single_line_comment_style' => true,
        'visibility_required' => true,
    ])
    ->setFinder($finder);

return $config;
