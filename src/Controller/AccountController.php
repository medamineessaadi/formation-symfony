<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AccountType;
use App\Entity\PasswordUpdate;
use App\Form\RegistrationType;
use App\Form\PasswordUpdateType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AccountController extends AbstractController
{
    /**
     * Permet d'afficher et de gerer le formulaire de connexion
     * 
     * @Route("/login", name="account_login")
     * 
     * @return Response
     */
    public function login(AuthenticationUtils $utils)
    {
        $error=$utils->getLastAuthenticationError();
        $username=$utils->getLastUsername();
       
        return $this->render('account/login.html.twig',[
            'hasError'=>$error !== null,
            'userName'=>$username
        ]);
    }
    /**
     * Permet de se deconnecter
     * 
     *@Route("/logout", name="account_logout")

     * @return void
     */
    public function logout()
    {  
        //Rien..!
    }
    /**
     * Permet d'afficher le formulaire d'inscription
     *
     * @Route("/register",name="account_register")
     * 
     * @return Response
     */
    public function register(Request $request , ObjectManager $manager,UserPasswordEncoderInterface $encoder)
    {
        $user = new User();
        $form = $this->createForm(RegistrationType::class,$user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) 
        {   
               $hash=$encoder->encodePassword($user, $user->getHash());
                $user->setHash($hash);
               $manager->persist($user);
               $manager->flush();
               $this->addFlash(
                   'success',
                   "Votre compte a bien ete cree! Vous pouvez maintenent vous connecter !"
               );
            return $this->redirectToRoute('account_login');
         }
            
           
        return $this->render('account/registration.html.twig',
        [
            'form'=>$form->createView()
        ]);
    }

    /**
     * Permet d'afficher et de traiter le formulaire de modification de profil
     *
     * @Route("/account/profile",name="account_profile")
     * 
     * @IsGranted("ROLE_USER")
     * 
     * @return Response
     */
    public function profile(Request $request,ObjectManager $manager)
    {
        $user = $this->getUser();

        $form = $this->createForm(AccountType::class,$user);
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) 
        {   
             
               $manager->persist($user);
               $manager->flush();
               $this->addFlash(
                   'success',
                   "Les donnees du profil ont ete enregistrees avec succes !"
               );
            return $this->redirectToRoute('account_login');
         }

        return $this->render('account/profile.html.twig',
        [
            'form' => $form->createView()
        ]);
    } 
    
    /**
    * Permet de modifier le mot de passe
    *
    * @Route("/account/password_update",name="account_password")
    *
    * @IsGranted("ROLE_USER")
    * 
    * @return Response
    */
   public function updatePassword(Request $request,UserPasswordEncoderInterface $encoder,ObjectManager $manager)
   {
       $passwordUpdate= new PasswordUpdate();

       $user=$this->getUser();

       $form = $this->createForm(PasswordUpdateType::class,$passwordUpdate);

       $form->handleRequest($request);

       if ($form->isSubmitted() && $form->isValid()) 
       {   
        // 1. verifier que le old password de formulaire soit le meme que le password de l'user
            if (!password_verify($passwordUpdate->getOldPassword(),$user->getHash())) 
            { # gerer l'erreur
                $form->get('oldPassword')->addError(new FormError("Le mot de passe que vous avez tape n'est pas 
                 votre mot de passe actuel !"));

            }
            else {  # l'ancien mot de passe et correcte !

                $newPassword=$passwordUpdate->getNewPassword();
                $hash=$encoder->encodePassword($user,$newPassword);

                $user->setHash($hash);

                $manager->persist($user);
                $manager->flush();

                $this->addFlash(
                  'success',
                  "Votre mot de passe a bien ete modifie !"
              );
              return $this->redirectToRoute('homepage'); 
            }
          
        }

       return $this->render('account/password.html.twig',
       [
           'form' => $form->createView()
       ]);
   }
   /**
    * Permet d'afficher le profil de l'utilisateur connecte
    *
    * @Route("/account", name="account_index")
    *
    * @IsGranted("ROLE_USER")
    * @return Response
    */
    public function myAccount()
    {
        return $this->render('user/index.html.twig',
       [
           'user' => $this->getUser()
       ]);
    }
    /**
     * Permer d'affcher la liste des reservation faites par l'utilisateur
     *
     * @Route("/account/bookings", name="account_bookings")
     * @return Response
     */
    public function bookings()
    {
        return $this->render('account/bookings.html.twig');
    }

}
