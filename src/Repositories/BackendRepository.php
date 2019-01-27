<?php

namespace App\Repositories;

use Doctrine\MongoDB\Query\Builder;
use Doctrine\ODM\MongoDB\DocumentRepository;

class BackendRepository extends DocumentRepository
{
    public function search(int $page = 1, string $query = null, int $size = 20)
    {
        $skip = ($page * $size) - $size;
        $skip = $skip < 0 ? 0 : $skip;

        $qb = $this->createQueryBuilder()
            ->limit($size)
            ->skip($skip);

        $this->addSearch($qb, $query);

        return $qb->getQuery()->execute();
    }

    public function count(string $query = null)
    {
        $qb = $this->createQueryBuilder()->count();
        $this->addSearch($qb, $query);

        return $qb->getQuery()->execute();
    }

    private function addSearch(Builder $qb, string $query = null, string $type = null): void
    {
        if ($query) {
            $qb->field('domain')->equals(new \MongoRegex(sprintf('/%s/', $query)));
        }
    }
}
