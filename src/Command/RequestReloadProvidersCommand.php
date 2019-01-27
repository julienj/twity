<?php

namespace App\Command;

use App\Document\Provider;
use App\Messenger\Message\ProviderImportation;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\MessageBusInterface;

class RequestReloadProvidersCommand extends Command
{
    protected $bus;

    private $manager;

    public function __construct(DocumentManager $manager, MessageBusInterface $bus)
    {
        $this->manager = $manager;
        $this->bus = $bus;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('app:request-reload-providers')
            ->setDescription('Reload all providers.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Request update of all providers');

        // do not use findAll for memory usage
        $providers = $this->manager->getRepository('App:Provider')
            ->createQueryBuilder()
            ->getQuery()
            ->execute();

        /** @var Provider $provider */
        foreach ($providers as $provider) {
            $io->text('Request for '.$provider->getName());
            $provider->setUpdateInProgress(true);
            $this->manager->flush();
            $this->bus->dispatch(new ProviderImportation($provider->getName()));

            $this->manager->detach($provider);
        }
    }
}
