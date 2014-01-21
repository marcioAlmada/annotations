<?php

namespace Minime\Annotations\Fixtures;

/**
 * A Common DockBlock
 *
 * @get @post @ajax
 * @postParam x
 * @postParam y
 * @postParam z
 */
class AnnotationsFixture
{

    use \Minime\Annotations\Traits\Reader;

    private $empty_fixture;

    /**
     * @value null
     * @value NULL
     * @value ""
     */
    private $null_fixture;

    /**
     * @value TRUE
     * @value FALSE
     * @value true
     * @value false
     * @value "true"
     * @value "false"
     */
    private $boolean_fixture;

    /**
     * @alpha
     * @beta
     * @gamma
     */
    private $implicit_boolean_fixture;

    /**
     * @value abc   
     * @value "abc"  
     * @value "abc "  
     * @value "123"
     */
    private $string_fixture;

    /**
     * @value 123
     * @value 023
     * @value -023
     */
    private $integer_fixture;

    /**
     * @value .45
     * @value 0.45
     * @value 45.
     * @value -4.5
     */
    private $float_fixture;

    /**
     * @bar test@example.com @toto @tata @number float 2.1
     */
    private $identifier_parsing_fixture;

    /**
     * @value ["x", "y"]
     * @value {"x": {"y": "z"}}
     * @value {"x": {"y": ["z", "p"]}}
     */
    private $json_fixture;

    /**
     * @Minime\Annotations\Fixtures\AnnotationConstructInjection -> [ "bar" ]
     * @Minime\Annotations\Fixtures\AnnotationSetterInjection    -> { "foo" : "bar" }
     */
    private $concrete_fixture;

    /**
     * @SomeUndefinedClass -> []
     */
    private $bad_concrete_fixture;


    /**
     * @value json {"x" : ["y"}
     */
    private $bad_json_fixture;

    /**
     * @value eval (1000 * 24 * 60 * 60)
     * @value eval range(1, 3)
     * @value eval base_convert('A37334', 16, 2)
     */
    private $eval_fixture;

    /**
     * @value eval $
     */
    private $bad_eval_fixture;

    /**
     * @param_a foo
     * @param_b bar
     */
    private $single_values_fixture;

    /**
     * @value x
     * @value y
     * @value z
     */
    private $multiple_values_fixture;

    /**
     * @get @post @ajax
     * @postParam x
     * @postParam y
     * @postParam z
     * @alpha @beta @gamma
     */
    private $same_line_fixture;

    /**
     * @value string abc
     * @value string  45  
     * @value integer 45
     * @value integer  -45  
     * @value float   .45
     * @value float  0.45
     * @value float  45.
     * @value float -4.5
     * @value float  4  
     * @json_value json ["x", "y"]
     * @json_value json {"x": {"y": "z"}}
     * @json_value json   {"x": {"y": ["z", "p"]}}  
     */
    private $strong_typed_fixture;

    /**
     * @multiline_string Lorem ipsum dolor sit amet, consectetur adipiscing elit.
     * Etiam malesuada mauris justo, at sodales nisi accumsan sit amet.
     * 
     * Morbi imperdiet lacus non purus suscipit convallis.
     * Suspendisse egestas orci a felis imperdiet, non consectetur est suscipit.
     *
     * @multiline_indented_string
     * ------
     * < moo >
     * ------ 
     *         \   ^__^
     *          \  (oo)\_______
     *             (__)\       )\/\
     *                 ||----w |
     *                 ||     ||
     *
     * 
     * @multiline_json json {
     *     "x": {
     *         "y": [
     *             "z", "p"
     *         ]
     *     }
     * }
     */
    private $multiline_value_fixture;

    /**
     * @value string
     * @value integer
     * @value float
     * @value json
     * @value eval
     * @value_with_trailing_space string  
     * @value_with_trailing_space  integer  
     * @value_with_trailing_space float  
     * @value_with_trailing_space  json  
     * @value_with_trailing_space eval  
     */
    private $reserved_words_as_value_fixture;

    /**
     * @value footype Tolerate me. DockBlocks can't be evaluated rigidly.
     */
    private $non_recognized_type_fixture;

    /**
     * @value integer 045
     */
    private $bad_integer_fixture;

    /**
     * @value float 2.1.3
     */
    private $bad_float_fixture;

    /**
     * @path.to.the.treasure "cheers!"
     * @path.to.the.cake "the cake is a lie"
     * @another.path.to.cake "foo"
     */
    private $namespaced_fixture;

    /** @value foo */
    private $inline_docblock_fixture;

    /** @alpha */
    private $inline_docblock_implicit_boolean_fixture;

    /** @alpha @beta @gama */
    private $inline_docblock_multiple_implicit_boolean_fixture;

    /**
     * @get @post @ajax
     * @postParam x
     * @postParam y
     * @postParam z
     */
    private function method_fixture() {}

}
