<?php
/**
 * User: zjkiza
 * Date: 5/7/19
 * Time: 7:10 PM
 */

namespace Converter\Service;

class ObjectToArray extends Converter
{
    /**
     * @param array $arrayOfObjects
     * @param array $arrays
     * @return array
     * @throws \Exception
     */
    public function multi(array $arrayOfObjects, array $arrays = []): array
    {
        $this->checkInputData($arrayOfObjects);

        $listOfMethods = $this->getListOfClassMethods(self::GET);

        foreach ($arrayOfObjects as $theObject) {
            $arrays[] = $this->getArrayFromObject($theObject, $listOfMethods);
        }

        return $arrays;
    }

    /**
     * @param object $object
     * @return array
     * @throws \Exception
     */
    public function single(object $object): array
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
     * @throws \Exception
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
}