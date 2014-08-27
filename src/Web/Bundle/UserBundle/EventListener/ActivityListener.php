<?php
namespace Web\Bundle\UserBundle\EventListener;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Web\Bundle\UserBundle\Entity\User;

class ActivityListener
{
    /**
     * @var \Symfony\Component\Security\Core\SecurityContextInterface
     */
    protected $context;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * @var string
     */
    protected $interval;

    /**
     * @var SessionInterface
     */
    protected $session;

    public function __construct(SecurityContextInterface $context, EntityManager $manager, $interval, SessionInterface $session)
    {
        $this->context  = $context;
        $this->em       = $manager;
        $this->interval = $interval;
        $this->session  = $session;
    }

    /**
     * Update the user "lastActivity" on each request
     * @param FilterControllerEvent $event
     */
    public function onController(FilterControllerEvent $event)
    {
        if ($event->getRequestType() !== HttpKernel::MASTER_REQUEST) {
            return;
        }

        // Set name of controller in session
        $invoke = $event->getController();
        $controller = new \ReflectionClass(get_class($invoke[0]));
        $controller = strtolower(str_replace('Controller', '', $controller->getShortName()));

        // Token dla jarka
        if (! $this->session->has('user_token')) {
            $this->session->set('user_token', base_convert(sha1(uniqid(mt_rand(), true)), 16, 36));
        }

        $this->session->set('controller', $controller);

        if ($this->context->getToken()) {
            $user = $this->context->getToken()->getUser();

            $delay = new \DateTime();
            $delay->setTimestamp(strtotime($this->interval));

            if ($user instanceof User && $user->getLastActivity() < $delay) {
                $user->isActiveNow();
                $this->em->flush($user);
            }
        }
    }
}