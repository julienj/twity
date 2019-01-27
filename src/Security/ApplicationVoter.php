<?php

namespace App\Security;

use App\Document\Access;
use App\Document\Application;
use App\Document\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class ApplicationVoter extends Voter
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports($attribute, $subject)
    {
        if (!in_array($attribute, [Access::ACCESS_USER, Access::ACCESS_MASTER, Access::ACCESS_OWNER])) {
            return false;
        }

        if (!$subject instanceof Application) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        if ($this->security->isGranted(User::ROLE_MANAGER)) {
            return true;
        }

        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        /** @var Application $application */
        $application = $subject;

        $access = null;

        foreach ($application->getAccesses() as $item) {
            if ($item->getUser() === $user) {
                $access = $item->getAccess();
            }
        }

        if (!$access) {
            return false;
        }

        switch ($attribute) {
            case Access::ACCESS_USER:
                return true;
            case Access::ACCESS_MASTER:
                return in_array($access, [Access::ACCESS_MASTER, Access::ACCESS_OWNER]);
            case Access::ACCESS_OWNER:
                return Access::ACCESS_OWNER === $access;
        }

        throw new \LogicException('This code should not be reached!');
    }
}
