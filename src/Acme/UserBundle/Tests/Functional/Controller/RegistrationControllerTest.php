<?php
//phpunit -c app/ --filter=RegistrationController
//$client->getResponse()->getContent();

namespace Acme\UserBundle\Tests\Functional\Controller;

use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * 
*
* @author leonardo proietti <leonardo@netmeans.net>
*/
class RegistrationControllerTest extends WebTestCase
{
    public function setUp()
    {
        $this->initEntityManager();
    }
        
    public function userRegistrationProvider()
    {
        return array(
            array(
              "/register/user-one",
              "newuserone",
              "userone",              
              "newuserone@netmeans.net",
              "UserOne",
            ),
            array(
              "/register/user-two",
              "newusertwo",
              "usertwo",
              "newusertwo@netmeans.net",
              "UserTwo",
            )
        );
    } 
    
    public function userConfirmProvider()
    {
        return array(
            array(           
              "useronenotconfirmed@netmeans.net",
              "UserOne",
            ),
            array(
              "usertwonotconfirmed@netmeans.net",
              "UserTwo",
            )
        );
    } 
    
    /**
     * @dataProvider userRegistrationProvider
     */
    public function testRegistration($path, $username, $password, $email, $entity)
    {
        $client     = static::createClient();
        $client->followRedirects(true);
                
        $crawler    = $client->request('GET', $path);
        
        $client->followRedirects(false);
        
        $form = $crawler->selectButton('Register')->form();
        
        $crawler = $client->submit(
            $form,
            array(
                'fos_user_registration_form[username]'                  => $username,
                'fos_user_registration_form[email]'                     => $email,
                'fos_user_registration_form[plainPassword][first]'      => $password,
                'fos_user_registration_form[plainPassword][second]'     => $password,
            )
        );
        
        $mailCollector = $this->getMailCollector($client);
        $this->assertEquals(1, $mailCollector->getMessageCount());
        
        $user = $this->em->getRepository('AcmeUserBundle:'.$entity)->findOneByEmail($email);  
        
        $this->assertEquals($username, $user->getUsername());
        $this->assertEquals('Acme\UserBundle\Entity\\' . $entity, get_class($user));
        
        $this->em->remove($user);
        $this->em->flush();        
    }
    
    /**
     * @dataProvider userConfirmProvider
     */
    public function testConfirmRegistration($email, $entity)
    {
        $client     = static::createClient();
        $client->followRedirects(true);
        
        $crawler    = $client->request('GET', '/register/confirm/abcdefg');
        $user = $this->em->getRepository('AcmeUserBundle:'. $entity)->findOneByEmail($email);
        
        $user->setEnabled(false);
        $user->setConfirmationToken('abcdefg');
        $this->em->persist($user);
        $this->em->flush();
    }
}