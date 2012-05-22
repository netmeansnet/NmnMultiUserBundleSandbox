<?php

namespace Acme\UserBundle\Controller;

use Nmn\MultiUserBundle\Controller\RegistrationController as BaseController;
use Symfony\Component\HttpFoundation\RedirectResponse;

class RegistrationController extends BaseController
{
    public function registerAction()
    {
        $url = $this->container->get('router')->generate('user_one_registration');
        return new RedirectResponse($url);
    }
}