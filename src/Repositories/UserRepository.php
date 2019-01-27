<?php

namespace App\Repositories;

use App\Document\User;
use Doctrine\MongoDB\Query\Builder;
use Doctrine\ODM\MongoDB\DocumentRepository;
use Omines\OAuth2\Client\Provider\GitlabResourceOwner;

class UserRepository extends DocumentRepository
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

    public function createFromGitlab(GitlabResourceOwner $gitlabUser, string $role): User
    {
        $user = new User();
        $user
            ->setRole($role)
            ->setUsername($gitlabUser->getUsername())
            ->setEmail($gitlabUser->getEmail())
            ->setType(User::TYPE_GITLAB)
            ->setFullName($gitlabUser->getName())
            ->setAvatarUrl($gitlabUser->getAvatarUrl());

        $this->dm->persist($user);
        $this->dm->flush();

        return $user;
    }

    private function addSearch(Builder $qb, string $query = null): void
    {
        if ($query) {
            $qb->addOr($qb->expr()->field('username')->equals(new \MongoRegex(sprintf('/%s/', $query))));
            $qb->addOr($qb->expr()->field('fullName')->equals(new \MongoRegex(sprintf('/%s/', $query))));
        }
    }
}
