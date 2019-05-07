<?php
/**
 * User: zjkiza
 * Date: 5/7/19
 * Time: 7:11 PM
 */

namespace Converter\Service;

abstract class Converter
{
    protected const GET = 'get';
    protected const SET = 'set';

    /**
     * @var object
     */
    private $object;

    /**
     * ConverterService constructor.
     * @param object $object
     * @throws \Exception
     */
    public function __construct(object $object)
    {
        $this->object = $object;
    }

    /**
     * @return object
     */
    protected function getObject(): object
    {
        return $this->object;
    }

    /**
     * @param string $typeOfMethod
     * @return array
     */
    protected function getListOfClassMethods(string $typeOfMethod = self::SET): array
    {
        $listOfMethods = get_class_methods($this->getObject());

        $filterListOfMethods = array_filter($listOfMethods, function ($methodName) use ($typeOfMethod) {

            return stripos($methodName, $typeOfMethod) !== false ?: false;
        });

        return $filterListOfMethods;
    }

    /**
     * @param string $methodName
     * @param array $listOfMethods
     */
    protected function checkIfMethodExistInClass(string $methodName, array $listOfMethods): void
    {
        if (!$this->methodExistInClass($methodName, $listOfMethods)) {
            throw new \BadMethodCallException(
                sprintf('Method %s does not exist in object %s', $methodName, get_class($this->getObject()))
            );
        }
    }

    /**
     * @param array $array
     */
    protected function checkInputData(array $array): void
    {
        if (!$array) {
            throw new \InvalidArgumentException('Input data does not exist');
        }
    }

    /**
     * @param array $array
     */
    protected function checkIfArraySingle(array $array): void
    {
        if (is_array(current($array))) {
            throw new \InvalidArgumentException('Input array is not single');
        }
    }

    /**
     * @param object $object
     * @throws \Exception
     */
    protected function checkOfEquality2Object(object $object): void
    {
        $inputObject = get_class_methods($object);
        $defineObject = get_class_methods($this->getObject());
        sort($inputObject);
        sort($defineObject);

        if ($inputObject !== $defineObject) {
            throw new \InvalidArgumentException (
                sprintf(
                    'Input object %s and define object %s does not equal',
                    get_class($object),
                    get_class($this->getObject())
                )
            );
        }
    }

    /**
     * @param string $methodName
     * @param array $filterListOfMethods
     * @return bool
     */
    private function methodExistInClass(string $methodName, array $filterListOfMethods): bool
    {
        return in_array($methodName, $filterListOfMethods, false);
    }
}
