<?php

namespace App\Tests\Controller;

use App\DataFixtures\InitFixtures;
use App\Repository\UserRepository;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Test\kernelTestCase;
use App\DataFixtures\UserFixtures;
use Doctrine\ORM\EntityManagerInterface;


class RegistrationControllerTest extends WebTestCase
{
    use FixturesTrait;

    public function testRegistration()
    {
        // Create User
        $client = static::createClient();

        $this->loadFixtures([
            UserFixtures::class
        ]);

        $client->request('GET', '/register');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Register');

        $client->submitForm('Register', [
            'registration_form[email]' => 'newuser@mail.com',
            'registration_form[username]' => 'newuser',
            'registration_form[plainPassword][first]' => 'password',
            'registration_form[plainPassword][second]' => 'password',
            'registration_form[agreeTerms]' => 1,
        ]);
        $this->assertResponseRedirects();
        $client->followRedirect();

        // Find User
        self::bootkernel();
        $usersCount = self::$container->get(UserRepository::class)->count([]);
        $this->assertEquals(4, $usersCount);
        $user = self::$container->get(UserRepository::class)->findOneBy([
          'email' => 'newuser@mail.com'
        ]);
        $this->assertTrue(!empty($user));
    }
}