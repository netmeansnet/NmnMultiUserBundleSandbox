<?php

//phpunit -c app/ --filter=ChangePasswordController
//$client->getResponse()->getContent();

namespace Acme\UserBundle\Tests\Functional\Controller;

/**
 * 
*
* @author leonardo proietti <leonardo@netmeans.net>
*/
class ChangePasswordControllerTest extends WebTestCase
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
    public function testChangePassword($username, $password, $entity)
    {
        $this->isSecure('/profile/change-password');              
        $client  = static::createClient();
        $client->followRedirects(true);
        
        $client = $this->login($client, $username, $password);
        $crawler    = $client->request('GET', '/profile/change-password');
                
        $button = $crawler->selectButton('Change password');
        $form = $button->form();
        
        $crawler = $client->submit(
            $form,
            array(
                'fos_user_change_password_form[current]' => $password,
                'fos_user_change_password_form[new][first]' => $password . '_new',
                'fos_user_change_password_form[new][second]' => $password . '_new',
            )
        );
        
        $this->assertTrue($client->getRequest()->getPathInfo() == '/profile/');        
        $crawler    = $client->request('GET', '/logout');
        
        $client = $this->login($client, $username, $password. '_new');        
        $crawler    = $client->request('GET', '/profile/change-password');       
        $button = $crawler->selectButton('Change password');
        $form = $button->form();
        
        $crawler = $client->submit(
            $form,
            array(
                'fos_user_change_password_form[current]' => $password . '_new',
                'fos_user_change_password_form[new][first]' => $password,
                'fos_user_change_password_form[new][second]' => $password,
            )
        );
        
        
    }    
}