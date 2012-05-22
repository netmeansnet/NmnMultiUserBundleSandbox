<?php

//phpunit -c app/ --filter=ResettingController
//$client->getResponse()->getContent();

namespace Acme\UserBundle\Tests\Functional\Controller;

/**
 * 
*
* @author leonardo proietti <leonardo@netmeans.net>
*/
class ResettingControllerTest extends WebTestCase
{
    public function setUp()
    {
        $this->initEntityManager();
    }
        
    public function userProvider()
    {
        return array(
            array(           
              "userone@netmeans.net",
            ),
            array(
              "usertwo@netmeans.net",
            )
        );
    } 
    
    /**
     * @dataProvider userProvider
     */
    public function testReset($email)
    {
        $client     = static::createClient();
        $client->followRedirects(false);
                
        $crawler    = $client->request('GET', '/resetting/request');
        
        $form = $crawler->selectButton('Reset password')->form();
            
        $crawler = $client->submit(
            $form,
            array(
                'username' => $email,
            )
        );
                
        $mailCollector = $this->getMailCollector($client);
        $this->assertEquals(1, $mailCollector->getMessageCount());
    }
    
}