<?php

namespace App\Service;

use Symfony\Component\Serializer\SerializerInterface;

class CitiesService
{
    private $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    public function getCities(): array
    {
        $json = file_get_contents('../public/json/zipcode.json');
        return $this->serializer->deserialize($json, 'array', 'json');
    }
}
