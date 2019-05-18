<?php

namespace App\Repositories;

use App\Document\Provider;
use Doctrine\MongoDB\Query\Builder;
use Doctrine\ODM\MongoDB\DocumentRepository;

class ProviderRepository extends DocumentRepository
{
    public function search(int $page = 1, string $query = null, string $type = null, int $size = 20)
    {
        $skip = ($page * $size) - $size;
        $skip = $skip < 0 ? 0 : $skip;

        $qb = $this->createQueryBuilder()
            ->select('name', 'type', 'vcsUri', 'lastUpdate', 'description', 'updateInProgress', 'downloads')
            ->limit($size)
            ->skip($skip);

        $this->addSearch($qb, $query, $type);

        return $qb->getQuery()->execute();
    }

    public function count(string $query = null)
    {
        $qb = $this->createQueryBuilder()->count();
        $this->addSearch($qb, $query);

        return $qb->getQuery()->execute();
    }

    public function facets(string $query = null)
    {
        $qb = $this->createAggregationBuilder()
            ->match()
            ->field('name')->equals(new \MongoRegex(sprintf('/%s/', $query)))
            ->group()
            ->field('_id')
            ->expression('$type')
            ->field('count')
            ->sum(1);

        $data = $qb->execute();

        $rs = [];

        foreach ($data as $item) {
            $rs[$item['_id']] = $item['count'];
        }

        return $rs;
    }

    public function getProvidersAndSha256(): array
    {
        $providers = $this->createQueryBuilder()
            ->select('sha256')
            ->hydrate(false)
            ->getQuery()
            ->execute()
            ->toArray();

        return array_map(function ($item) {
            unset($item['_id']);

            return $item;
        }, $providers);
    }

    public function findUpdateInProgress(): array
    {
        $providers = $this->createQueryBuilder()
            ->select('name')
            ->field('updateInProgress')->equals(true)
            ->hydrate(false)
            ->getQuery()
            ->execute()
            ->toArray();

        return array_keys($providers);
    }

    public function getReplacements(Provider $provider)
    {
        return $this->createQueryBuilder()
            ->field('replace')->equals($provider->getName())
            ->getQuery()
            ->execute();
    }

    private function addSearch(Builder $qb, string $query = null, string $type = null): void
    {
        if ($query) {
            $qb->field('name')->equals(new \MongoRegex(sprintf('/%s/', $query)));
        }

        if ($type) {
            $qb->field('type')->equals($type);
        }
    }
}
