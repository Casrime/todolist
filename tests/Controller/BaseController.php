<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BaseController extends WebTestCase
{
    final public const ADMIN_EMAIL = 'admin@todolist.com';
    final public const USER_EMAIL = 'user@todolist.com';

    protected function login($email)
    {
        $client = static::createClient();

        // get or create the user somehow (e.g. creating some users only
        // for tests while loading the test fixtures)
        $userRepository = $this->getIdentifier(UserRepository::class);
        $testUser = $userRepository->findOneByEmail($email);

        $client->loginUser($testUser);

        return $client;
    }

    protected function getIdentifier(string $containerIdentifier)
    {
        return static::getContainer()->get($containerIdentifier);
    }

    protected function trans(string $identifier, array $parameters = [], string $domain = 'messages')
    {
        return $this->getIdentifier('translator')->trans($identifier, $parameters, $domain);
    }
}
