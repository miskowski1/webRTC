<?php
namespace Web\Bundle\UserBundle\EventListener;

use Doctrine\ORM\EntityManager;
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

    public function __construct(SecurityContextInterface $context, EntityManager $manager, $interval)
    {
        $this->context  = $context;
        $this->em       = $manager;
        $this->interval = $interval;
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