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
    private $empty_fixture;

    /**
     * @value null
     * @value ""
     */
    private $null_fixture;

    /**
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
     * @bar test@example.com @toto @tata @number 2.1
     */
    private $identifier_parsing_fixture;

    /**
     * @value ["x", "y"]
     * @value {"x": {"y": "z"}}
     * @value {"x": {"y": ["z", "p"]}}
     */
    private $json_fixture;

    /**
     * @Minime\Annotations\Fixtures\AnnotationConstructInjection  -> { "__construct" : ["bar"] }
     * @\Minime\Annotations\Fixtures\AnnotationConstructInjection -> { "__construct" : ["bar"] }
     *
     * @Minime\Annotations\Fixtures\AnnotationSetterInjection    -> { "setFoo" : ["bar"] }
     * @\Minime\Annotations\Fixtures\AnnotationSetterInjection   -> { "setFoo" : ["bar"] }
     */
    private $concrete_fixture;

    /**
     * @SomeUndefinedClass -> {}
     */
    private $bad_concrete_fixture;

    /**
     * @Minime\Annotations\Fixtures\AnnotationSetterInjection -> []
     */
    private $bad_concrete_fixture_root_schema;

    /**
     * @Minime\Annotations\Fixtures\AnnotationSetterInjection -> { "setFoo" : "bar" }
     */
    private $bad_concrete_fixture_method_schema;

    /**
     * @value json {"x" : ["y"}
     */
    private $bad_json_fixture;

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
     * @value_with_trailing_space string  
     * @value_with_trailing_space  integer  
     * @value_with_trailing_space float  
     * @value_with_trailing_space  json  
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
     * @foo . ' " \' \" ` { } \{ \} ( ) \( \) ; : \ áḉõ²¬¢£³°§
     */
    private $serialize_fixture;

    /**
     * @type stringed
     * @type integers
     * @type floated
     * @type jsonable
     */
    private $i32_fixture;

    /**
     * A valid URL is:
     * scheme://user:pass@domain.com **we had a false positive here**
     *
     * The getReturnType() method will get the return type of the method,
     * by parsing its @return tag in the comment block. **we had a false positive here**
     *
     * @return void
     */
    private $i49_fixture;

    /**
     * @name gsouf
     *
     */
    private $i55_fixture;

    /**
     * @get @post @ajax
     * @postParam x
     * @postParam y
     * @postParam z
     */
    private function method_fixture() {}

    /**
     * Related to issue #56
     * @fix 56
     * @foo
     */
    const CONSTANT_FIXTURE = "someValue";

    /**
     * Related to issue #56
     * @fix 56
     * @foo
     */
    const
        /**
         * @value foo
         */
        CONSTANT_MANY1 = "foo",
        /**
         * @value bar
         * @type constant
         */
        CONSTANT_MANY2 = "bar";


    const CONSTANT_EMPTY = "empty";

    const
        CONSTANT_EMPTY_MANY1 = "foo",
        CONSTANT_EMPTY_MANY2 = "bar";

}
