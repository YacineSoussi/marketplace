<?php

namespace App\Controller;

use App\Classe\Mail;
use App\Entity\User;
use App\Form\UserType;
use App\Form\RegisterType;
use Symfony\Component\Mime\Email;
use App\Repository\UserRepository;
use Symfony\Component\Mime\Address;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    
    /**
     * @Route("/register", name="register")
     */
    public function register(MailerInterface $mailer, Request $request, UserPasswordHasherInterface $encoder)
    {
        $user = new User();
        $notification = null;

        $form = $this->createForm(RegisterType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid() ) {
            
            $user = $form->getData();
            $search_email = $this->entityManager->getRepository(User::class)->findOneByEmail($user->getEmail());

            if (!$search_email) {
                $password = $encoder->hashPassword($user,$user->getPassword());
                $user->setPassword($password);
                $user->setActivationToken(md5(uniqid()));
                $user->setRoles(["ROLE_USER"]);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();
                $notification = "Votre inscription s'est correctement déroulée. Veuillez vérifier votre adresse email.";
                
                $email = (new TemplatedEmail())
                    ->from(new Address('no-reply@moon-factory.fr', 'Marketplace'))
                    ->to($user->getEmail())
                    ->subject('Vous avez presque terminé !')
                    ->htmlTemplate('mail/activation.html.twig')
                    ->context([
                        'user' => $user,
                        'token' => $user->getActivationToken()
                    ])
                    ;
                    
                $mailer->send($email);
 
            } else {
            
                $notification = "L'email que vous avez renseigné existe déjà.";
            }
        }

        return $this->render('user/form.html.twig', [
            'form' => $form->createView(),
            'notification' => $notification,
        ]);
    }

    /**
     * @Route("/activation/{token}", name="activation")
     *
     * @param MailerInterface $mailer
     * @param [type] $token
     * @param UserRepository $users
     * @return void
     */
    public function activation(MailerInterface $mailer, $token, UserRepository $users) {

        // On recherche si un utilisateur avec ce token existe dans la base de données
        $user = $users->findOneBy(['activationToken' => $token]);
      
            // Si aucun utilisateur n'est associé à ce token
        if (!$user) {
            // On renvoie une erreur 404
            $this->addFlash("expire", "Jeton invalide ou expiré");
        } else {
            // On supprime le token
            $user->setIsActif(true);
            $user->setActivationToken(null);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            // On génère l'e-mail
            $email = (new TemplatedEmail())
                        ->from(new Address('no-reply@moon-factory.fr', 'Marketplace'))
                        ->to($user->getEmail())
                        
                        ->subject('Bienvenue chez ESGI-Marketplace')
                        ->htmlTemplate('mail/register.mail.html.twig')
                        ->context([
                            'user' => $user,
                            
                        ])
                        ;
        // On envoie l'e-mail
            $mailer->send($email);

            // On génère un message
            $this->addFlash('message', 'Votre compte est maintenant actif.');
        }
         // On retourne à l'accueil
         return $this->redirectToRoute('app_login');
        
    }

    /**
     * @Route("/admin/edit/user/{id}",name="edit_user")
    */

    public function editUserAction(Request $request, User $user,UserPasswordHasherInterface $encoder){

        $user = $this->getUser();

        if(!$user) {
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(UserType::class,$user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
         
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('list_users');


        }
       
        return $this->render('user/edit.html.twig',[
            'form' => $form->createView()
        ]);

    } 

    /**
    * @Route("/admin/user/delete/{id}", name="delete_user")
    */

    public function deleteUserAction($id){

        $entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager->getRepository(User::class)->findOneById($id);
        $entityManager->remove($user);
        $entityManager->flush();
        return $this->redirectToRoute('list_users');
    }

    /**
    * @Route("/admin/users",name="list_users")
    */
    public function ListUserAction(){

        $user = $this->getUser();

        if(!$user) {
            return $this->redirectToRoute('app_login');
        }

        $users = $this->getDoctrine()->getRepository(User::class)->findAll();

        return $this->render('user/list.html.twig',[
            'users' => $users 
        ]);

    }

    /**
     * Permet de bloquer un utilisateur
     * 
     * @Route("/admin/users/autorize/{id}", name="admin_autorize_user")
     * @param User $user
     * @param EntityManagerInterface $em
     * @return void
     */
    public function AutorizationUser(User $user, EntityManagerInterface $em) {

        $actif = $user->getIsActif();
  
            if ($actif === true) {
                 $user->setIsActif(false);
        
                 $em->persist($user);
                 $em->flush();
               
                return $this->json([
                    'code'=> 200,
                    'message' => 'Utilisateur bien desactivé',
                    'statut' => $user->getIsActif()
                   ], 200);
                  
            } else {
                $user->setIsActif(true);
                $em->persist($user);
                $em->flush();
            return $this->json([
                'code'=> 200,
                'message' => 'Utilisateur bien activé',
                'statut' => $user->getIsActif()
               ], 200);
            }
    
               return $this->json(['code' => 200, 'message' => 'Ça marche bien'], 200);

    }
    
    

}