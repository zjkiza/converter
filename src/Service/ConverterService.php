<?php
/**
 * Author: zjkiza
 * Date: 5/7/19
 * Time: 1:09 PM
 */

namespace Converter\Service;

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
     */
    public function arrayOfObjectToArrays(array $arrayOfObjects, array $arrays = []): array
    {
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
        return $this->getObjectFromArray(
            $array,
            $this->getListOfClassMethods()
        );
    }

    /**
     * @param object $object
     * @return array
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
     */
    private function getArrayFromObject(object $object, array $listOfMethods, array $array = []): array
    {
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
     */
    private function getObjectFromArray(array $array, array $listOfMethods): object
    {
        $objectName = get_class($this->object);
        $newObject = new $objectName();

        foreach ($array as $key => $value) {
            $methodName = self::SET . ucfirst($key);
            !$this->methodExistInClass($methodName, $listOfMethods) ?: $newObject->$methodName($value);
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
}