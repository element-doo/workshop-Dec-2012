<?php
namespace NGS\ModelBundle\Listener;

use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class SecurityListener
{
    public function __construct(SecurityContext $security, Session $session)
    {
        $this->security = $security;
        $this->session = $session;
    }

    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        $user = $this->security->getToken()->getUser();
    }
}
