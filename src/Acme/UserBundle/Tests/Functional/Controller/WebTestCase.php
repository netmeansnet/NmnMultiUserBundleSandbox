<?php

namespace Acme\UserBundle\Tests\Functional\Controller;

use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as SymfonyWebTestCase;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

abstract class WebTestCase extends SymfonyWebTestCase
{  
    protected $em;
    
    protected function initKernel()
    {
        $kernel = static::createKernel();
        $kernel->boot();
        
        return $kernel;
    }

    protected function initEntityManager()
    {
        $kernel     = $this->initKernel();
        $this->em   = $kernel->getContainer()->get('doctrine.orm.entity_manager');

        return $this->em;
    }
    
    protected function login($client, $username, $password)
    {
        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('Login')->form();
        
        $crawler = $client->submit(
            $form,
            array(
                '_username' => $username,
                '_password' => $password,
            )
        );
        return $client;
    }
    
    protected function logout($client)
    {
        $client->request('GET', '/logout');
    }
    
    public function getMailCollector($client)
    {
        $profile = $client->getProfile();        
        return $profile->getCollector('swiftmailer');
    }
    
    protected function getCurrentUser($username, $userClass)
    {        
        $user = $this->em->getRepository('AcmeUserBundle:' . $userClass)->findOneBy(array('username' => $username)); 
                
        return $user;
    }
    
    /**
     * @param string $firewallName
     * @param array $options
     * @param array $server
     * @return Symfony\Component\BrowserKit\Client
     */
    protected function createClientWithAuthentication($firewallName, $username, $userClass, array $options = array(), array $server = array())
    {
        /* @var $client \Symfony\Component\BrowserKit\Client */
        $client = $this->createClient($options, $server);
        // has to be set otherwise "hasPreviousSession" in Request returns false.
        $client->getCookieJar()->set(new \Symfony\Component\BrowserKit\Cookie(session_name(), true));
        
        /* @var $user UserInterface */
        $user = $this->getCurrentUser($username, $userClass);
        
        $token = new UsernamePasswordToken($user, null, $firewallName, $user->getRoles());
        self::$kernel->getContainer()->get('session')->set('_security_' . $firewallName, serialize($token));
        
        return $client;
    }
    
    protected function isSecure($path)
    {
        $client = $this->createClient();
        $client->followRedirects(true);
        $crawler = $client->request('GET', $path);
        $this->assertEquals(200, $client->getResponse()->getStatusCode()); 
        $this->assertTrue($crawler->filter('input#username')->count() > 0);
        $this->assertTrue($crawler->filter('input#password')->count() > 0);
        $this->assertTrue($crawler->filter('input#_submit')->count() > 0);
    }
        
}

