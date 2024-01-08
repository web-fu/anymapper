<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__.'/src');

return (new PhpCsFixer\Config())
    ->setRules([
        '@DoctrineAnnotation'                              => true,
        '@PHP81Migration'                                  => true,
        '@Symfony'                                         => true,
        'array_syntax'                                     => ['syntax' => 'short'],
        'backtick_to_shell_exec'                           => true,
        'binary_operator_spaces'                           => [
            'default'   => 'align_single_space_minimal',
            'operators' => [
                '-'  => 'single_space',
                '||' => null,
                '|'  => null,
                '&&' => null,
            ],
        ],
        'combine_consecutive_issets'                       => false,
        'combine_consecutive_unsets'                       => true,
        'combine_nested_dirname'                           => true,
        'comment_to_phpdoc'                                => true,
        'compact_nullable_type_declaration"'               => true,
        'declare_strict_types'                             => true,
        'dir_constant'                                     => true,
        'doctrine_annotation_array_assignment'             => ['operator' => '='],
        'ereg_to_preg'                                     => true,
        'escape_implicit_backslashes'                      => true,
        'explicit_indirect_variable'                       => true,
        'explicit_string_variable'                         => false,
        'self_static_accessor'                             => true,
        'fully_qualified_strict_types'                     => true,
        'general_phpdoc_annotation_remove'                 => ['annotations'=>['author']],
        'global_namespace_import'                          => ['import_classes' => true],
        'header_comment'                                   => [
            'comment_type' => 'PHPDoc',
            'header'       => "This file is part of web-fu/anymapper\n\n@copyright Web-Fu <info@web-fu.it>\n\nFor the full copyright and license information, please view the LICENSE\nfile that was distributed with this source code.",
        ],
        'heredoc_indentation'                              => true,
        'heredoc_to_nowdoc'                                => true,
        'implode_call'                                     => true,
        'is_null'                                          => true,
        'linebreak_after_opening_tag'                      => true,
        'list_syntax'                                      => ['syntax' => 'short'],
        'method_chaining_indentation'                      => true,
        'modernize_types_casting'                          => true,
        'multiline_comment_opening_closing'                => true,
        'multiline_whitespace_before_semicolons'           => true,
        'no_alias_functions'                               => true,
        'no_alternative_syntax'                            => true,
        'no_binary_string'                                 => true,
        'no_homoglyph_names'                               => true,
        'no_php4_constructor'                              => true,
        'echo_tag_syntax'                                  => ['format' => 'long'],
        'no_superfluous_elseif'                            => true,
        'no_superfluous_phpdoc_tags'                       => false,
        'no_unreachable_default_argument_value'            => true,
        'no_unset_cast'                                    => true,
        'no_unset_on_property'                             => true,
        'no_useless_else'                                  => true,
        'no_useless_return'                                => true,
        'non_printable_character'                          => ['use_escape_sequences_in_strings' => true],
        'nullable_type_declaration_for_default_null_value' => true,
        'ordered_class_elements'                           => true,
        'ordered_imports'                                  => true,
        'php_unit_construct'                               => true,
        'php_unit_dedicate_assert'                         => ['target' => 'newest'],
        'php_unit_dedicate_assert_internal_type'           => ['target' => 'newest'],
        'php_unit_expectation'                             => ['target' => 'newest'],
        'php_unit_method_casing'                           => true,
        'php_unit_mock'                                    => true,
        'php_unit_mock_short_will_return'                  => true,
        'php_unit_namespaced'                              => ['target' => 'newest'],
        'php_unit_no_expectation_annotation'               => true,
        'php_unit_set_up_tear_down_visibility'             => true,
        'php_unit_test_class_requires_covers'              => true,
        'phpdoc_add_missing_param_annotation'              => true,
        'phpdoc_annotation_without_dot'                    => true,
        'phpdoc_line_span'                                 => true,
        'phpdoc_order'                                     => true,
        'phpdoc_no_empty_return'                           => true,
        'phpdoc_return_self_reference'                     => true,
        'phpdoc_types'                                     => false,
        'phpdoc_var_annotation_correct_order'              => true,
        'pow_to_exponentiation'                            => true,
        'protected_to_private'                             => true,
        'psr_autoloading'                                  => true,
        'random_api_migration'                             => true,
        'return_assignment'                                => true,
        'self_accessor'                                    => true,
        'set_type_to_cast'                                 => true,
        'simple_to_complex_string_variable'                => true,
        'simplified_null_return'                           => false,
        'strict_param'                                     => true,
        'ternary_to_null_coalescing'                       => true,
        'visibility_required'                              => true,
        'void_return'                                      => true,
    ])
    ->setFinder($finder);
