<?php


namespace App\EventSubscriber;

use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;
use ApiPlatform\Core\EventListener\EventPriorities;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\KernelEvent;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class PasswordHashSubscriber implements  EventSubscriberInterface
{

    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder){
       $this->passwordEncoder = $passwordEncoder;
    }
    public static function getSubscribedEvents()
    {
        return[
            KernelEvent::class => ['hashPassword', EventPriorities::PRE_WRITE]
        ];
    }

    public function hasPassword(ControllerArgumentsEvent $event){

        $user = $event->getController();
        $method = $event->getRequest()->getMethod();

        if(!$user instanceof User || Request::METHOD_POST !== $method ){
            return;
        }

        $user->setPassword(
            $this->passwordEncoder->encodePassword($user, $user->getPassword())
        );
    }
}