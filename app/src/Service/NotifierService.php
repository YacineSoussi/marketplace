<?php

namespace App\Service;

use App\Entity\Notifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;


class NotifierService
{
   
    private $entityManager;
    private $security;

    public function __construct(EntityManagerInterface $entityManager,Security $security) {
        $this->entityManager = $entityManager;
        $this->security = $security;
    }

    public function notifySeller($seller,$content) {
       
       
        $notification = new Notifier();
        $notification->setContent($content);
        $notification->setSellerId($seller);
        $this->entityManager->persist($notification);
        $this->entityManager->flush();
        
        return $notification;

    }

    public function notify($seller,$content) {
       
       
        $notification = new Notifier();
        $notification->setContent($content);
        $notification->setSellerId($seller);
        $this->entityManager->persist($notification);
        $this->entityManager->flush();
        
        return $notification;

    }

    public function allNotifyBySeller() {
        if( $this->security->getUser()){
            
            if(($this->security->getUser()->getSeller() !== null) && $this->security->getUser()->getSeller()->getIsActif() == true) {

                $notifications = $this->entityManager->getRepository(Notifier::class)->findBy([
                    "sellerId" => $this->security->getUser()->getSeller()->getId(),
                ]);
        
                return array_slice($notifications, -5);
            }
        }    

    }

   
    

}