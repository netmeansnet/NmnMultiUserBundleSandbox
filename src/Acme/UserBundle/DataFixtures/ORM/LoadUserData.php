<?php

namespace Acme\UserBundle\DataFixtures\ORM;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

use Acme\UserBundle\Entity\UserOne;
use Acme\UserBundle\Entity\UserTwo;

/**
 * Loads the user fixtures
 *
 * @author Leonardo Proietti
 */
class LoadUserData extends AbstractFixture implements OrderedFixtureInterface
{

    public function load(\Doctrine\Common\Persistence\ObjectManager $manager)
    {
        $userOne = new UserOne();
        $userOne->setUsername('userone');
        $userOne->setPlainPassword('userone');
        $userOne->setEmail('userone@netmeans.net');
        $userOne->setEnabled(true);        
        $manager->persist($userOne);  
        
        $userTwo = new UserTwo();
        $userTwo->setUsername('usertwo');
        $userTwo->setPlainPassword('usertwo');
        $userTwo->setEmail('usertwo@netmeans.net');
        $userTwo->setEnabled(true);        
        $manager->persist($userTwo);  
        
        $userOneNotConfirmed = new UserOne();
        $userOneNotConfirmed->setUsername('userone-not-confirmed');
        $userOneNotConfirmed->setPlainPassword('userone-not-confirmed');
        $userOneNotConfirmed->setEmail('useronenotconfirmed@netmeans.net');
        $userOneNotConfirmed->setConfirmationToken('abcdefg');
        $userOneNotConfirmed->setEnabled(false);
        $manager->persist($userOneNotConfirmed);  
        
        $userTwoNotConfirmed = new UserTwo();
        $userTwoNotConfirmed->setUsername('usertwo-not-confirmed');
        $userTwoNotConfirmed->setPlainPassword('usertwo-not-confirmed');
        $userTwoNotConfirmed->setEmail('usertwonotconfirmed@netmeans.net');
        $userTwoNotConfirmed->setConfirmationToken('abcdefg');
        $userTwoNotConfirmed->setEnabled(false);
        $manager->persist($userTwoNotConfirmed); 
        
        $manager->flush();
        
        $this->addReference('user.one', $userOne);
        $this->addReference('user.two', $userTwo);    
    }
    
    public function getOrder()
    {
        return 1;
    }
}
