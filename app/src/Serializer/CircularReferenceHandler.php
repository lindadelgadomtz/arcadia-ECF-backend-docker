<?php

namespace App\Serializer;

class CircularReferenceHandler
{
    public function __invoke($object)
    {
        echo("hola");
        // Return the ID if the object has one, otherwise return null
        return method_exists($object, 'getId') ? $object->getId() : null;
    }
}
