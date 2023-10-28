<?php

namespace Layerok\TgMall\Services;

interface HydratorInterface {
    public function hydrate(string $type, array $data);
    public function extract(object $obj): array;
}
