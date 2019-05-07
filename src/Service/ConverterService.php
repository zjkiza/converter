<?php
/**
 * Author: zjkiza
 * Date: 5/7/19
 * Time: 1:09 PM
 */

namespace Converter\Service;

use BadMethodCallException;
use Exception;

class ConverterService
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
     * @throws Exception
     */
    public function __construct(object $object)
    {
        $this->object = $object;
    }

    /**
     * @param array $arrays
     * @param array $arrayOfObjects
     * @return array
     */
    public function arrayToArrayOfObject(array $arrays, array $arrayOfObjects = []): array
    {
        $this->checkInputData($arrays);

        $listOfMethods = $this->getListOfClassMethods();

        foreach ($arrays as $array) {
            $arrayOfObjects[] = $this->getObjectFromArray($array, $listOfMethods);
        }

        return $arrayOfObjects;
    }

    /**
     * @param array $arrayOfObjects
     * @param array $arrays
     * @return array
     * @throws Exception
     */
    public function arrayOfObjectToArrays(array $arrayOfObjects, array $arrays = []): array
    {
        $this->checkInputData($arrayOfObjects);

        $listOfMethods = $this->getListOfClassMethods(self::GET);

        foreach ($arrayOfObjects as $theObject) {
            $arrays[] = $this->getArrayFromObject($theObject, $listOfMethods);
        }

        return $arrays;
    }

    /**
     * @param array $array
     * @return object
     */
    public function arrayToObject(array $array): object
    {
        $this->checkInputData($array);

        return $this->getObjectFromArray(
            $array,
            $this->getListOfClassMethods()
        );
    }

    /**
     * @param object $object
     * @return array
     * @throws Exception
     */
    public function objectToArray(object $object): array
    {
        return $this->getArrayFromObject(
            $object,
            $this->getListOfClassMethods(self::GET)
        );
    }

    /**
     * @param object $object
     * @param array $listOfMethods
     * @param array $array
     * @return array
     * @throws Exception
     */
    private function getArrayFromObject(object $object, array $listOfMethods, array $array = []): array
    {
        $this->checkOfEquality2Object($object);

        foreach ($listOfMethods as $listOfMethod) {
            $key = strtolower(
                str_replace(self::GET, '', $listOfMethod)
            );
            $array[$key] = $object->$listOfMethod();
        }

        return $array;
    }

    /**
     * @param array $array
     * @param array $listOfMethods
     * @return object
     * @throws \BadMethodCallException
     */
    private function getObjectFromArray(array $array, array $listOfMethods): object
    {
        $objectName = get_class($this->object);
        $newObject = new $objectName();

        foreach ($array as $key => $value) {
            $methodName = self::SET . ucfirst($key);
            $this->checkIfMethodExistInClass($methodName, $listOfMethods);
            $newObject->$methodName($value);
        }

        return $newObject;
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

    /**
     * @param string $typeOfMethod
     * @return array
     */
    private function getListOfClassMethods(string $typeOfMethod = self::SET): array
    {
        $listOfMethods = get_class_methods($this->object);

        $filterListOfMethods = array_filter($listOfMethods, function ($methodName) use ($typeOfMethod) {

            return stripos($methodName, $typeOfMethod) !== false ?: false;
        });

        return $filterListOfMethods;
    }

    /**
     * @param string $methodName
     * @param array $listOfMethods
     */
    private function checkIfMethodExistInClass(string $methodName, array $listOfMethods): void
    {
        if (!$this->methodExistInClass($methodName, $listOfMethods)) {
            throw new BadMethodCallException(
                sprintf('Method %s does not exist in object %s', $methodName, get_class($this->object))
            );
        }
    }

    /**
     * @param array $array
     */
    private function checkInputData(array $array): void
    {
        if (!$array) {
            throw new \InvalidArgumentException('Input data does not exist');
        }
    }

    /**
     * @param object $object
     * @throws Exception
     */
    private function checkOfEquality2Object(object $object): void
    {
        $inputObject = get_class_methods($object);
        $defineObject = get_class_methods($this->object);
        sort($inputObject);
        sort($defineObject);

        if ($inputObject !== $defineObject) {
            throw new Exception (
                sprintf(
                    'Input object %s and define object %s does not equal',
                    get_class($object),
                    get_class($this->object)
                )
            );
        }
    }
}