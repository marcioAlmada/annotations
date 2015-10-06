<?php

namespace Minime\Annotations\Reflector;

use PhpParser\Error;
use PhpParser\Lexer;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassConst;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Parser as PHPParser;

/**
 * We need this class in the annotation reader because there is no builtin constant reflector
 * We only implemented getDocComment because that's the only feature we need
 * @see \Minime\Annotations\Reader::getConstantAnnotations()
 */
class ReflectionConst implements \Reflector
{

    protected $classConstNode = null;
    protected $constNode = null;

    protected $docComment = null;

    /**
     * @param string|object $class        fully qualified name or instance of the class
     * @param string $constName           name of the constant
     * @throws \ReflectionException
     */
    public function __construct($class, $constName)
    {

        $classReflection = new \ReflectionClass($class);
        $className = $classReflection->getName();
        $fileName = $classReflection->getFileName();

        $parser = new PHPParser(new Lexer());
        try {
            $stmts = $parser->parse(file_get_contents($fileName));
        } catch (Error $e) {
            throw new \ReflectionException("Cannot parse the class ${fileName}", 0, $e);
        }

        // Class can be in a namespace or at the root of the statement
        $classNode = $this->findClassNode($stmts);
        if (!$classNode) {
            throw new \ReflectionException("Class ${className} not found in file ${fileName}");
        }


        // Find the constant we are looking for
        foreach ($classNode->stmts as $classSubNode) {
            if ($classSubNode instanceof ClassConst) {
                foreach ($classSubNode->consts as $constNode) {
                    if ($constNode->name == $constName) {
                        $this->classConstNode = $classSubNode;
                        $this->constNode = $constNode;
                        break 2;
                    }
                }
            }
        }

        if (!$this->constNode) {
            throw new \ReflectionException("Class constant ${constName} does not exist in class ${className}");
        }

    }

    /**
     * @param $stmts
     * @return Class_
     */
    private function findClassNode($stmts)
    {
        foreach ($stmts as $node) {
            if ($node instanceof Namespace_) {
                return $this->findClassNode($node->stmts);
            } else {
                if ($node instanceof Class_) {
                    return $node;
                }
            }
        }
        return null;
    }

    /**
     * @return string
     */
    public function getDocComment()
    {

        if (null == $this->docComment) {
            /* more than one means we have this case :
             *  const
             *     /**
             *      * @annotation
             *      * /
             *      FOO = 'foo',
             *      /**
             *      * @annotation
             *      * /
             *      BAR = 'bar'
             * ;
             *
             * Else we have this case
             *
             * /**
             *  * @annotation
             *  * /
             *  const Foo = 'foo';
             *
             */
            if (count($this->classConstNode->consts) > 1) {
                $comments = $this->constNode->getAttribute('comments', []);
            } else {
                $comments = $this->classConstNode->getAttribute('comments', []);
            }

            if (count($comments) > 0) {
                // we can have many doc comment for one statement
                // We only take the closest one
                $docComment = end($comments);

                if (substr($docComment, 0, 3) == '/**') {
                    $this->docComment = $docComment;
                } else {
                    $this->docComment = "";
                }

            } else {
                $this->docComment = "";
            }

        }

        return $this->docComment;


    }

    /**
     * No need to implement it, we only need the getDocComment
     */
    public static function export()
    {

    }

    /**
     * No need to implement it, we only need the getDocComment
     */
    public function __toString()
    {
        // TODO: Implement __toString() method.
    }
}
