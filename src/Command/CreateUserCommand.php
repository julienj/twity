<?php

namespace App\Command;

use App\Document\User;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CreateUserCommand extends Command
{
    protected $validator;

    private $passwordEncoder;
    private $manager;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder, DocumentManager $manager, ValidatorInterface $validator)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->manager = $manager;
        $this->validator = $validator;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('app:create-user')
            ->setDescription('Creates a new user.')
            ->setHelp('This command allows you to create a user');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Create a new user');

        $username = $io->ask('Please enter the username');
        $email = $io->ask('Please enter the email');
        $fullName = $io->ask('Please enter the full name');
        $password = $io->askHidden('What is the password?');

        $helper = $this->getHelper('question');
        $question = new ChoiceQuestion(
            'Select User role',
            [User::ROLE_USER, User::ROLE_MANAGER, User::ROLE_ADMIN],
            User::ROLE_USER
        );
        $question->setErrorMessage('Role %s is invalid.');

        $role = $helper->ask($input, $output, $question);

        $user = new User();
        $user
            ->setUsername($username)
            ->setEmail($email)
            ->setFullName($fullName)
            ->setRole($role)
            ->setPassword($this->passwordEncoder->encodePassword($user, $password));

        if ($io->confirm('Create this user ?')) {
            $this->manager->persist($user);
            $this->manager->flush();
            $io->success('User created');
        }
    }
}
