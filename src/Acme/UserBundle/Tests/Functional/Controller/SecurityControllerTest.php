<?php

//phpunit -c app/ --filter=SecurityController
//$client->getResponse()->getContent();

namespace Acme\UserBundle\Tests\Functional\Controller;

use Nmn\MultiUserBundle\Manager\UserDiscriminator;

/**
 * 
*
* @author leonardo proietti <leonardo@netmeans.net>
*/
class SecurityControllerTest extends WebTestCase
{
            
    public function setUp()
    {
        $this->initEntityManager();
    }
        
    public function userProvider()
    {
        return array(
            array(           
              "userone",
              "userone",
              "UserOne",
            ),
            array(
              "usertwo",
              "usertwo",
              "UserTwo",
            )
        );
    } 
    
    /**
     * @dataProvider userProvider
     */
    public function testUserLogin($username, $password, $entity)
    {
        $client  = static::createClient();
               
        $crawler = $client->request('GET', '/login');
                
        $button = $crawler->selectButton('_submit');
        $form = $button->form();
        
        $crawler = $client->submit(
            $form,
            array(
                '_username' => $username,
                '_password' => $password,
            )
        );
        
        $this->assertTrue($client->getRequest()->getPathInfo() == '/login_check');  
        
        $crawler = $client->followRedirect();
        
        $this->assertTrue($client->getRequest()->getPathInfo() == '/');
        
        $session = $client->getContainer()->get("session");
        $this->assertEquals('Acme\UserBundle\Entity\\' . $entity, $session->get(UserDiscriminator::SESSION_NAME)); 
    }

}