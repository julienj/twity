<?php

namespace App\DataFixtures\Processor;

use App\Document\User;
use Fidry\AliceDataFixtures\ProcessorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserProcessor implements ProcessorInterface
{
    protected $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function preProcess(string $id, $object): void
    {
        if (!$object instanceof User) {
            return;
        }

        if ($object->getPlainPassword()) {
            $password = $this->encoder->encodePassword($object, $object->getPlainPassword());
            $object->setPassword($password);
        }
    }

    public function postProcess(string $id, $object): void
    {
    }
}
