<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class Cache
{
    public function __construct(private TagAwareCacheInterface $myApplicationCache)
    {
    }

    public function get(string $key, $values): mixed
    {
        return $this->myApplicationCache->get($key, function (ItemInterface $item) use ($key, $values) {
            $item->tag([$key]);

            return $values;
        });
    }

    public function invalidate(array $keys): void
    {
        $this->myApplicationCache->invalidateTags($keys);
    }
}
