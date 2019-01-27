<?php

use Behat\Behat\Context\Context;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelInterface;
use Doctrine\ODM\MongoDB\DocumentManager;
use Fidry\AliceDataFixtures\LoaderInterface;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behatch\Context\RestContext;
/**
 * @see http://behat.org/en/latest/quick_start.html
 */
class FeatureContext implements Context
{
    private $kernel;

    private $dm;

    private $schemaManager;

    private $fixtureLoader;

    /**
     * @var RestContext
     */
    private $restContext;

    public function __construct(KernelInterface $kernel, DocumentManager $dm,  LoaderInterface $fixtureLoader)
    {
        $this->kernel = $kernel;
        $this->dm = $dm;
        $this->schemaManager = $dm->getSchemaManager();
        $this->fixtureLoader = $fixtureLoader;
    }


    /**
     * @BeforeScenario
     */
    public function gatherContexts(BeforeScenarioScope $scope)
    {
        $environment = $scope->getEnvironment();

        $this->restContext = $environment->getContext(RestContext::class);
    }

    /**
     * @BeforeScenario @createSchema
     */
    public function createDatabase()
    {
        $this->schemaManager->dropDatabases();
        $this->schemaManager->ensureIndexes();

        $this->fixtureLoader->load([
            __DIR__ . '/fixtures/users.yml'
        ]);
    }

    /**
     * @Given I am an administrator
     */
    public function IAmAnAdministrateur()
    {
        $this->restContext->iAddHeaderEqualTo('PHP_AUTH_USER', 'admin');
        $this->restContext->iAddHeaderEqualTo('PHP_AUTH_PW', 'admin');
    }

    /**
     * @Given I am a user
     */
    public function IAmAUser()
    {
        $this->restContext->iAddHeaderEqualTo('PHP_AUTH_USER', 'user');
        $this->restContext->iAddHeaderEqualTo('PHP_AUTH_PW', 'user');
    }

    /**
     * @Given I have a :username token
     */
    public function iHaveAUserToken($username)
    {
        /** @var \App\Document\User $user */
        $user = $this->dm->getRepository('App:User')->findOneBy(['username' => $username]);

        $this->restContext->iAddHeaderEqualTo('PHP_AUTH_USER', $username);
        $this->restContext->iAddHeaderEqualTo('PHP_AUTH_PW', $user->getToken());
    }

    /**
     * @Given I have a invalid user token
     */
    public function iHaveAInvalidUserToken()
    {
        $this->restContext->iAddHeaderEqualTo('PHP_AUTH_USER', 'guest');
        $this->restContext->iAddHeaderEqualTo('PHP_AUTH_PW', 'bad password');
    }

    /**
     * @Given I load :file fixture
     */
    public function iLoadFixture($file)
    {
        $this->fixtureLoader->load([
            __DIR__ . '/fixtures/' . $file
        ], [], [], \Fidry\AliceDataFixtures\Persistence\PurgeMode::createNoPurgeMode());
    }


}
