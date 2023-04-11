<?php

namespace App\Controller;

use Dompdf\Dompdf;

// Include Dompdf required namespaces
use Dompdf\Options;
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PdfController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    
    /**
     * @Route("/account/my-order/invoice/pdf/{reference}", name="invoice_pdf")
    */
    public function index($reference)
    {
       

        $order = $this->entityManager->getRepository(Order::class)->findOneByReference($reference);
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        
        $dompdf = new Dompdf($pdfOptions);
        
        $html = $this->renderView('pdf/index.html.twig', [
            'order' => $order
        ]);
        
        $dompdf->loadHtml($html);
        
        $dompdf->setPaper('A4', 'portrait');

       
        $dompdf->render();

        $dompdf->stream("Facture commande-".$order->getReference().".pdf", [
            "Attachment" => true
        ]);
        
      
    }
}