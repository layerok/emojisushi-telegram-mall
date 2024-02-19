<?php

namespace Layerok\TgMall\Services;

use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\PropertyNormalizer;
use Symfony\Component\Serializer\Serializer;

class Hydrator implements HydratorInterface
{
    public Serializer $serializer;

    public function __construct() {
        $encoders = [new JsonEncoder()];
        $normalizers = [new ArrayDenormalizer(), new PropertyNormalizer(null, null, new PhpDocExtractor())];
        $this->serializer = new Serializer($normalizers, $encoders);
    }

    public function hydrate(string $type, $data)
    {
        return $this->serializer->deserialize(json_encode($data), $type, JsonEncoder::FORMAT);
    }

    public function extract($obj): array
    {
        return json_decode($this->serializer->serialize($obj, JsonEncoder::FORMAT,  [
            AbstractObjectNormalizer::SKIP_NULL_VALUES => true
        ]), true);
    }
}
