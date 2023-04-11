<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Seller;
use App\Form\SellerType;
use App\Repository\UserRepository;
use Symfony\Component\Mime\Address;
use App\Repository\SellerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AddSellerController extends AbstractController
{
    private $entityManager;
	public function __construct(
        EntityManagerInterface $entityManager
    ) {
		$this->entityManager = $entityManager;	
	}
    
    /**
     * @Route("/add/seller", name="app_seller_new")
    */
    public function new(Request $request,MailerInterface $mailer): Response
    {
            if ($this->getUser() !== null && $this->getUser()->getSeller() !== null && $this->getUser()->getSeller()->getIsActif() == true) {
                return $this->redirectToRoute('app_seller_dashboard');
            } else {
                $notification = null;
                $entityManager = $this->getDoctrine()->getManager();

                $seller = new Seller();
                $form = $this->createForm(SellerType::class, $seller);
                $form->handleRequest($request);
        
                if ($form->isSubmitted() && $form->isValid()) {
                
                    $search_email = $this->entityManager->getRepository(User::class)->findOneByEmail($form['contactMail']->getData());
                    if ($search_email && $search_email->getRoles() !== ["ROLE_SELLER"] && $search_email->getSeller() == null) {
                        $seller->setIsRequested(true);
                        $search_email->setSeller($seller);
                        $search_email->setActivationToken(md5(uniqid()));
                        $entityManager->persist($search_email);
                        $entityManager->flush();
                        $email = (new TemplatedEmail())
                            ->from(new Address('no-reply@moon-factory.fr', 'Marketplace'))
                            ->to('moncefsaiki@gmail.com')
                            ->subject('Nouvelle demande vendeur')
                            ->htmlTemplate('mail/sellerActivation.mail.html.twig')
                            ->context([
                                'user' => $search_email,
                                'token' => $search_email->getActivationToken()
                                
                            ])
                            ;
                        $mailer->send($email);
                        $notification = "Votre demande a bien été prise en compte et sera traité par nos équipes dans un délais de 24h";
                    }
                    elseif (!$search_email)  {
                        $notification = "Votre demande a bien été prise en compte et sera traité par nos équipes dans un délais de 24h";
                        $user = new User();
                        $user->setFirstname($form['firstnameCEO']->getData());
                        $user->setLastname($form['lastnameCEO']->getData());
                        $user->setEmail($form['contactMail']->getData());
                        $user->setActivationToken(md5(uniqid()));
                        $seller->setIsRequested(true);
                        $seller->setUser($user);
                        $entityManager->persist($user);
                        $entityManager->flush();

                        $email = (new TemplatedEmail())
                            ->from(new Address('no-reply@moon-factory.fr', 'Marketplace'))
                            ->to('moncefsaiki@gmail.com')
                            
                            ->subject('Nouvelle demande vendeur')
                            ->htmlTemplate('mail/sellerActivation.mail.html.twig')
                            ->context([
                                'user' => $user,
                                'token' => $user->getActivationToken()
                                
                            ])
                            ;
                        $mailer->send($email);
                    } elseif ($search_email->getSeller() !== null && $search_email->getSeller()->getIsRequested() == true) {
                
                        $notification = "Une demande concernant cette boutique est en cours de traitement";
                    }
                    
                }
                
                return $this->renderForm('seller/new.html.twig', [
                    'seller' => $seller,
                    'form' => $form,
                    'notification' => $notification,
                ]);

            }
            
        }

    /**
     * @Route("/seller/activation/{token}", name="seller_activation")
     *
     * @param MailerInterface $mailer
     * @param [type] $token
     * @param UserRepository $users
     * @return void
     */
    public function activation(MailerInterface $mailer, $token, UserRepository $users,UserPasswordHasherInterface $encoder) {
        $message = null;
        $user = $users->findOneBy(['activationToken' => $token]);
        if (!$user) {
            $message="Jeton expiré";
            $this->addFlash("expire", "Jeton invalide ou expiré");
        } else {
            $message = "Le compte vendeur ".$user->getSeller()->getLegalBrand()." a été activé avec succès";
            $password = null;
            $user->setIsActif(true);
            $user->setRoles(["ROLE_SELLER"]);
            
            if ($user->getPassword() == null){
                $password = uniqid();
                $user->setPassword($encoder->hashPassword($user,$password));
            }
            $user->getSeller()->setIsActif(true);
            $user->setActivationToken(null);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $email = (new TemplatedEmail())
                        ->from(new Address('no-reply@moon-factory.fr', 'Marketplace'))
                        ->to($user->getEmail())
                        ->subject('Activiation de votre compte vendeur')
                        ->htmlTemplate('mail/accept-seller.mail.html.twig')
                        ->context([
                            'user' => $user,
                            'password' => $password
                        ])
                        ;
            $mailer->send($email);

            $this->addFlash('message', 'Votre compte vendeur est maintenant actif.');
        }
    
        return $this->render('response.html.twig', [
            'message' => $message,
        ]);
     
        
    }

    /**
     * @Route("/seller/refuse/{token}", name="seller_refuse")
     *
     * @param MailerInterface $mailer
     * @param [type] $token
     * @param UserRepository $users
     * @return void
     */
    public function refuse(MailerInterface $mailer, $token, UserRepository $users) {
        $message = null;
        $user = $users->findOneBy(['activationToken' => $token]);
        if (!$user) {
            $message = "Jeton expiré";
        } else {
           
            $message = "Le compte vendeur ".$user->getSeller()->getLegalBrand()." a été refusé de la marketplace";
            $user->setIsActif(false);
            $user->getSeller()->setIsActif(false);
            $user->setActivationToken(null);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $email = (new TemplatedEmail())
                        ->from(new Address('no-reply@moon-factory.fr', 'Marketplace'))
                        ->to($user->getEmail())
                        
                        ->subject('Activiation de votre compte vendeur')
                        
                        ->htmlTemplate('mail/refuse-seller.mail.html.twig')
                        ->context([
                            'user' => $user,
                            
                        ])
                        ;
            $mailer->send($email);

        }
        
        
        return $this->render('response.html.twig', [
            'message' => $message,
        ]);
    }

       
    
}
