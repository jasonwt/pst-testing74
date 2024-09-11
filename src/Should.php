<?php
/*__FILEDOCBLOCK__*/

declare(strict_types=1);

namespace Pst\Testing;

use Pst\Testing\Exceptions\ShouldException;

use function Pst\Core\enum_exists;
use function Pst\Core\is_enum;

use Closure;
use Exception;
use Throwable;

/**
 * A collection of assertion methods
 * 
 * @package PST\Testing
 * 
 */
class Should {
    private function __construct() {}

    
    public static function dumpException(ShouldException $e): void {
        echo "\n" . $e->getMessage() . "\n";
    }

    public static function executeTests(Closure $tests): void {
        try {
            $tests();

        } catch (ShouldException $e) {
            self::dumpException($e);

        } catch (Throwable $e) {
            throw $e;
        }
    }


    /******************************* be *******************************/

    /**
     * Asserts that the value is strictly equal to the given value
     * 
     * @param mixed $expectedValue 
     * @param mixed[] $values 
     * 
     * @return void 
     * 
     * @throws ShouldException 
     * 
     */
    public static function be($expectedValue, ...$values): void {
        foreach ($values as $k => $v)
            if ($expectedValue !== $v)
                throw new ShouldException("[$k] expected value: '" . gettype($expectedValue) . "' is not " . gettype($v));
    }

    /**
     * Asserts that the value is not strictly equal to the given value
     * 
     * @param mixed $expectedValue 
     * @param mixed[] $values 
     * 
     * @return void 
     * 
     * @throws ShouldException 
     * 
     */
    public static function notBe($expectedValue, ...$values): void {
        foreach ($values as $k => $v)
            if ($expectedValue === $v)
                throw new ShouldException("[$k] " . gettype($expectedValue) . " is " . gettype($v));
    }

    /******************************* equal *******************************/

    /**
     * Asserts that the value is equal to the given value
     * 
     * @param mixed $expectedValue 
     * @param mixed[] $values 
     * 
     * @return void 
     * 
     * @throws ShouldException 
     * 
     */
    public static function equal($expectedValue, ...$values): void {
        foreach ($values as $k => $v) {
            if ($expectedValue != $v) {
                $valueOutput = is_object($expectedValue) ? (method_exists($expectedValue, "__toString") ? $expectedValue : print_r($expectedValue, true)) : $expectedValue;
                $vOutput = is_object($v) ? (method_exists($v, "__toString") ? $v : print_r($v, true)) : $v;

                throw new ShouldException("[$k] expected: " . gettype($expectedValue) . ": '$valueOutput' does not equal actual: " .  gettype($v) . ": '$vOutput'");
            }
        }
    }

    /**
     * Asserts that the value is not equal to the given value
     * 
     * @param mixed $expectedValue 
     * @param mixed[] $values 
     * 
     * @return void 
     * 
     * @throws ShouldException 
     * 
     */
    public static function notEqual($expectedValue, ...$values): void {
        foreach ($values as $k => $v) {
            if ($expectedValue == $v) {
                throw new ShouldException("[$k] " . gettype($expectedValue) . ": '" . print_r($expectedValue, true) . "' is equal to " . gettype($v) . ": '" . print_r($v, true) . "'");
            }
        }
    }

    /******************************* beTrue *******************************/

    /**
     * Asserts that the value is true
     * 
     * @param bool[] $values 
     * 
     * @return void 
     * 
     * @throws ShouldException 
     * 
     */
    public static function beTrue(bool ...$values): void {
        foreach ($values as $k => $value)
            if ($value !== true)
                throw new ShouldException("[$k] " . gettype($value) . " is not true");
    }

    /**
     * Asserts that the value is not true
     * 
     * @param bool[] $values 
     * 
     * @return void 
     * 
     * @throws ShouldException 
     * 
     */
    public static function notBeTrue(bool ...$values): void {
        foreach ($values as $k => $value)
            if ($value === true)
                throw new ShouldException("[$k] " . gettype($value) . " is true");
    }

    /******************************* beFalse *******************************/

    /**
     * Asserts that the value is false
     * 
     * @param bool[] $values 
     * 
     * @return void 
     * 
     * @throws ShouldException 
     * 
     */
    public static function beFalse(bool ...$values): void {
        foreach ($values as $k => $value)
            if ($value !== false)
                throw new ShouldException("[$k] " . gettype($value) . " is not false");
    }

    /**
     * Asserts that the value is not false
     * 
     * @param bool[] $values 
     * 
     * @return void 
     * 
     * @throws ShouldException 
     * 
     */
    public static function notBeFalse(bool ...$values): void {
        foreach ($values as $k => $value)
            if ($value === false)
                throw new ShouldException("[$k] " . gettype($value) . " is false");
    }

    /******************************* beNull *******************************/

    /**
     * Asserts that the value is null
     * 
     * @param mixed[] $values 
     * 
     * @return void 
     * 
     * @throws ShouldException 
     * 
     */
    public static function beNull(...$values): void {
        foreach ($values as $k => $value)
            if ($value !== null)
                throw new ShouldException("[$k] " . gettype($value) . " is not null");
    }

    /**
     * Asserts that the value is not null
     * 
     * @param mixed[] $values 
     * 
     * @return void 
     * 
     * @throws ShouldException 
     * 
     */
    public static function notBeNull(...$values): void {
        foreach ($values as $k => $value)
            if ($value === null)
                throw new ShouldException("[$k] " . gettype($value) . " is null");
    }

    /******************************* haveMethod *******************************/

    /**
     * Asserts that the object has the given method
     * 
     * @param string|object $object 
     * @param string[] $expectedMethods 
     * 
     * @return void 
     * 
     * @throws Exception 
     * @throws ShouldException 
     * 
     */
    public static function haveMethods(/*PHP8 string|object*/ $object, string ...$expectedMethods): void {
        if (is_object($object))
            $object = get_class($object);
        else if (!is_string($object))
            throw new Exception("Object must be a string or an object");

        foreach ($expectedMethods as $k => $method) {
/* 
            NOTE: not sure why my IDE thinks $method is not a string. I tried renaming it to other variable names with the same problem
            Have to do this unneeded cast to get rid of the warning.  Repeating problem in other methods. seems to have something to do
            with the docblock type hinting 
*/

            if (!method_exists($object, (string) $method))
                throw new ShouldException("[$k] Method '$method' does not exist");
        }
    }

    /**
     * Asserts that the object does not have the given method
     * 
     * @param string|object $object 
     * @param string[] $expectedMethods 
     * 
     * @return void 
     * 
     * @throws Exception 
     * @throws ShouldException 
     * 
     */
    public static function notHaveMethods(/*PHP8 string|object*/ $object, string ...$expectedMethods): void {
        if (is_object($object))
            $object = get_class($object);
        else if (!is_string($object))
            throw new Exception("Object must be a string or an object");

        foreach ($expectedMethods as $k => $method)
            if (method_exists($object, (string) $method))
                throw new ShouldException("[$k] Method '$method' exists");
    }

    /******************************* beA *******************************/

    /**
     * Asserts that the object is an instance of the given class or interface
     * 
     * @param string|object $object 
     * @param string[] $expectedClasses 
     * 
     * @return void 
     * 
     * @throws ShouldException 
     * 
     */
    public static function beA(/*PHP8 string|object*/ $object, string ...$expectedClasses): void {
        if (is_object($object))
            $object = get_class($object);
        else if (!is_string($object))
            throw new Exception("Object must be a string or an object");

        foreach ($expectedClasses as $k => $class)
            if (!is_a($object, (string) $class, true))
                throw new ShouldException("[$k] Object '$object' is not an instance of '$class'");
    }

    /**
     * Asserts that the object is not an instance of the given class or interface
     * 
     * @param string|object $object 
     * @param string[] $expectedClasses 
     * 
     * @return void 
     * 
     * @throws ShouldException 
     * 
     */
    public static function notBeA(/*PHP8 string|object*/ $object, string ...$expectedClasses): void {
        if (is_object($object))
            $object = get_class($object);
        else if (!is_string($object))
            throw new Exception("Object must be a string or an object");

        foreach ($expectedClasses as $k => $class)
            if (is_a($object, (string) $class, true))
                throw new ShouldException("[$k] Object '$object' is an instance of '$class'");
    }

    /******************************* beAClass *******************************/

    /**
     * Asserts that the class exists
     * 
     * @param string[] $classes 
     * 
     * @return void 
     * 
     * @throws ShouldException 
     * 
     */
    public static function beAClass(string ...$classes): void {
        foreach ($classes as $k => $class)
            if (!class_exists((string) $class))
                throw new ShouldException("[$k] Class '$class' does not exist");
    }

    /**
     * Asserts that the class does not exist
     * 
     * @param string[] $classes 
     * 
     * @return void 
     * 
     * @throws ShouldException 
     * 
     */
    public static function notBeAClass(string ...$classes): void {
        foreach ($classes as $k => $class)
            if (class_exists((string) $class))
                throw new ShouldException("[$k] Class '$class' exists");
    }

    /******************************* beAnInterface *******************************/

    /**
     * Asserts that the interface exists
     * 
     * @param string[] $interfaces 
     * 
     * @return void 
     * 
     * @throws ShouldException 
     * 
     */
    public static function beAnInterface(string ...$interfaces): void {
        foreach ($interfaces as $k => $interface)
            if (!interface_exists((string) $interface))
                throw new ShouldException("[$k] Interface '$interface' does not exist");
    }

    /**
     * Asserts that the interface does not exist
     * 
     * @param string[] $interfaces 
     * 
     * @return void 
     * 
     * @throws ShouldException 
     * 
     */
    public static function notBeAnInterface(string ...$interfaces): void {
        foreach ($interfaces as $k => $interface)
            if (interface_exists((string) $interface))
                throw new ShouldException("[$k] Interface '$interface' exists");
    }

    /******************************* beATrait *******************************/

    /**
     * Asserts that the trait exists
     * 
     * @param string[] $traits 
     * 
     * @return void 
     * 
     * @throws ShouldException 
     * 
     */
    public static function beATrait(string ...$traits): void {
        foreach ($traits as $k => $trait)
            if (!trait_exists((string) $trait))
                throw new ShouldException("[$k] Trait '$trait' does not exist");
    }

    /**
     * Asserts that the trait does not exist
     * 
     * @param string[] $traits 
     * 
     * @return void 
     * 
     * @throws ShouldException 
     * 
     */
    public static function notBeATrait(string ...$traits): void {
        foreach ($traits as $k => $trait)
            if (trait_exists((string) $trait))
                throw new ShouldException("[$k] Trait '$trait' exists");
    }

    /******************************* beAnEnum *******************************/

    /**
     * Asserts that the enum exists
     * 
     * @param string[] $enums 
     * 
     * @return void 
     * 
     * @throws Exception 
     * @throws ShouldException 
     * 
     */
    public static function beAnEnum(string ...$enums): void {
        if (PHP_VERSION_ID < 80100)
            throw new Exception("Enums are only supported in PHP 8.1 and later");

        foreach ($enums as $k => $enum)
            if (!enum_exists((string) $enum))
                throw new ShouldException("[$k] Enum '$enum' does not exist");
    }

    /**
     * Asserts that the enum does not exist
     * 
     * @param string[] $enums 
     * 
     * @return void 
     * 
     * @throws Exception 
     * @throws ShouldException 
     * 
     */
    public static function notBeAnEnum(string ...$enums): void {
        if (PHP_VERSION_ID < 80100)
            throw new Exception("Enums are only supported in PHP 8.1 and later");

        foreach ($enums as $k => $enum)
            if (enum_exists((string) $enum))
                throw new ShouldException("[$k] Enum '$enum' exists");
    }

    /******************************* haveTrait *******************************/

    /**
     * Asserts that the object uses the given trait
     * 
     * @param string|object $object 
     * @param string[] $traits 
     * 
     * @return void 
     * 
     * @throws Exception 
     * @throws ShouldException 
     */
    public static function haveTrait(/*PHP8 string|object*/ $object, string ...$traits): void {
        if (is_object($object))
            $object = get_class($object);
        else if (!is_string($object))
            throw new Exception("Object must be a string or an object");

        foreach ($traits as $k => $trait)
            if (!in_array($trait, class_uses($object), true))
                throw new ShouldException("[$k] Object '$object' does not use trait '$trait'");
    }

    /**
     * Asserts that the object does not use the given trait
     * 
     * @param string|object $object 
     * @param string[] $traits 
     * 
     * @return void 
     * 
     * @throws Exception 
     * @throws ShouldException 
     */
    public static function notHaveTrait(/*PHP8 string|object*/ $object, string ...$traits): void {
        if (is_object($object))
            $object = get_Class($object);
        else if (!is_string($object))
            throw new Exception("Object must be a string or an object");

        foreach ($traits as $k => $trait)
            if (in_array($trait, class_uses($object), true))
                throw new ShouldException("[$k] Object '$object' uses trait '$trait'");
    }

    /******************************* throw *******************************/

    /**
     * Asserts that the given exception is thrown
     * 
     * @param string $exception 
     * @param callable[] $callables 
     * 
     * @return void 
     * 
     * @throws ShouldException 
     * 
     */
    public static function throw(string $exception, callable ...$callables): void {
        foreach ($callables as $k => $callable) {
            try {
                $callable();

            } catch (Throwable $e) {

                $exceptionClass = get_class($e);

                if ($exceptionClass !== $exception && !is_subclass_of($e, $exception)) {
                    throw new ShouldException("[$k] Exception '$exception' was not thrown, '$exceptionClass' was thrown instead: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
                }

                continue;
            } 

            throw new ShouldException("[$k] Exception '$exception' was not thrown");
        }
    }

    /**
     * Asserts that the given exception is not thrown
     * 
     * @param string $exception 
     * @param callable[] $callables 
     * 
     * @return array 
     * 
     * @throws ShouldException 
     * 
     */
    public static function notThrow(string $exception, callable ...$callables): array {
        $results = [];
        foreach ($callables as $k => $callable) {
            try {
                $results[] = $callable();

            } catch (Throwable $e) {
                if (get_class($e) === $exception || is_subclass_of($e, $exception))
                    throw new ShouldException("[$k] Exception '$exception' was thrown: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
            } 
        }

        return $results;
    }
}