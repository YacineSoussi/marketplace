<?php

namespace App\Service;


use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

class ShipmentFeesService
{

    public function automated($shipmentWeight) {
        $shipmentPrice = 0;
        switch($shipmentWeight) {
            case ($shipmentWeight <= 250 ) :
                $shipmentPrice = 4.95;
                return $shipmentPrice;
                break;

            case ($shipmentWeight <= 500 ) :
                $shipmentPrice = 6.55;
                return $shipmentPrice;
                break;
            
            case ($shipmentWeight <= 750 ) :
                $shipmentPrice = 7.45;
                return $shipmentPrice;
                break;

            case ($shipmentWeight <= 1000 ) :
                $shipmentPrice = 8.10;
                return $shipmentPrice;
                break;
            
            case ($shipmentWeight <= 2000 ) :
                
                $shipmentPrice = 9.35;
                return $shipmentPrice;
                break;
            
            case ($shipmentWeight <= 5000 ) :
    
                $shipmentPrice = 14.35;
                return $shipmentPrice;
                break;

            case ($shipmentWeight <= 10000 ) :
            
                $shipmentPrice = 20.85;
                return $shipmentPrice;
                break;
            
            case ($shipmentWeight <= 15000 ) :
                
                $shipmentPrice = 26.40;
                return $shipmentPrice;
                break;

            case ($shipmentWeight <= 20000 ) :
                $shipmentPrice = 32.7;
                return $shipmentPrice;
                break;
            
            default:
                $shipmentPrice = 5;
                return $shipmentPrice;
        }
    }
}