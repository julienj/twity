<?php

namespace App\Repositories;

use App\Document\User;
use Doctrine\MongoDB\Query\Builder;
use Doctrine\ODM\MongoDB\DocumentRepository;
use MongoDB\BSON\ObjectId;

class ApplicationRepository extends DocumentRepository
{
    public function search(User $user, int $page = 1, string $query = null, int $size = 20)
    {
        $skip = ($page * $size) - $size;
        $skip = $skip < 0 ? 0 : $skip;

        $qb = $this->createQueryBuilder()
            ->limit($size)
            ->skip($skip);

        $this->addSearch($qb, $user, $query);

        return $qb->getQuery()->execute();
    }

    public function count(User $user, string $query = null)
    {
        $qb = $this->createQueryBuilder()->count();
        $this->addSearch($qb, $user, $query);

        return $qb->getQuery()->execute();
    }

    private function addSearch(Builder $qb, User $user, string $query = null, string $type = null): void
    {
        if (User::ROLE_USER === $user->getRole()) {
            //$qb->field('accesses')->elemMatch($qb->expr()->field('user')->references($user));
            $qb->field('accesses')->elemMatch(['user.$id' => new ObjectId($user->getId())]);
        }

        if ($query) {
            $qb->field('name')->equals(new \MongoRegex(sprintf('/%s/', $query)));
        }
    }
}
