<?php

namespace App\Twig;

use App\Service\NotifierService;
use Twig\TwigFunction;
use Doctrine\ORM\EntityManagerInterface;
use Twig\Extension\AbstractExtension;

class AppExtension extends AbstractExtension
{
    private $notifier;
    public function __construct(NotifierService $notifier){
        $this->notifier = $notifier;
    }
    
    public function getFunctions()
    {
        return [
            new TwigFunction('notifierGlobal', [$this, 'notifier']),
        ];
    }



}