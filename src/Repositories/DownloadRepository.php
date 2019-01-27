<?php

namespace App\Repositories;

use App\Document\Provider;
use Doctrine\ODM\MongoDB\DocumentRepository;

class DownloadRepository extends DocumentRepository
{
    public function getProviderStats(Provider $provider, $version = null)
    {
        $builder = $this->createAggregationBuilder();
        $builder
            ->addFields()
            ->field('stringdate')
            ->dateToString('%Y-%m-%d', '$date')
            ->match()
            ->field('provider')
            ->equals($provider->getName())
            ->sort('date', -1);

        if ($version) {
            $builder->match()
                ->field('version')
                ->equals($version);
        }

        $builder
            ->group()
            ->field('id')
            ->expression('$stringdate')
            ->field('downloads')
            ->sum(1)

            ->project()
            ->excludeFields(['_id'])
            ->includeFields(['date', 'downloads'])
            ->field('date')
            ->expression('$_id');

        return $builder->execute();
    }

    public function countLast7Days()
    {
        $qb = $this->createQueryBuilder()->count();

        $date = new \DateTime();
        $date->sub(new \DateInterval('P7D'));
        $qb->field('date')->gte($date);

        return $qb->getQuery()->execute();
    }
}
