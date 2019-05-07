<?php
/**
 * User: zjkiza
 * Date: 5/7/19
 * Time: 7:11 PM
 */

namespace Converter\Service;

class ArrayToObject extends Converter
{
    /**
     * @param array $arrays
     * @param array $arrayOfObjects
     * @return array
     */
    public function multi(array $arrays, array $arrayOfObjects = []): array
    {
        $this->checkInputData($arrays);

        $listOfMethods = $this->getListOfClassMethods();

        foreach ($arrays as $array) {
            $arrayOfObjects[] = $this->getObjectFromArray($array, $listOfMethods);
        }

        return $arrayOfObjects;
    }

    /**
     * @param array $array
     * @return object
     */
    public function single(array $array): object
    {
        $this->checkInputData($array);
        $this->checkIfArraySingle($array);

        return $this->getObjectFromArray(
            $array,
            $this->getListOfClassMethods()
        );
    }

    /**
     * @param array $array
     * @param array $listOfMethods
     * @return object
     * @throws \BadMethodCallException
     */
    private function getObjectFromArray(array $array, array $listOfMethods): object
    {
        $objectName = get_class($this->getObject());
        $newObject = new $objectName();

        foreach ($array as $key => $value) {
            $methodName = self::SET . ucfirst($key);
            $this->checkIfMethodExistInClass($methodName, $listOfMethods);
            $newObject->$methodName($value);
        }

        return $newObject;
    }
}