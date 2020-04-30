<?php

namespace App\Controller;

use App\Entity\InitialisationPassword;
use App\Entity\User;
use App\Form\InitialisationPasswordType;
use App\Form\RegistrationType;
use App\Security\LoginFormAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     * @param AuthenticationUtils $authenticationUtils
     * @param GuardAuthenticatorHandler $guardHandler
     * @param User $user
     * @param Request $request
     * @param LoginFormAuthenticator $formAuthenticator
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils, GuardAuthenticatorHandler $guardHandler, Request $request, LoginFormAuthenticator $formAuthenticator): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        // Renvoi à la page qu'on souhaitait consulter


        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * @Route("/register", name="app_register")
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param GuardAuthenticatorHandler $guardHandler
     * @param LoginFormAuthenticator $formAuthenticator
     * @return Response|null
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $em, GuardAuthenticatorHandler $guardHandler, LoginFormAuthenticator $authenticator)
    {
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $password = $passwordEncoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);
            $em->persist($user);
            $em->flush();

            // Message Flashbag qui indique que tout s'est bien passé

            $this->addFlash(
                'success',
                "Le compte a bien été créé! Bienvenue !"
            );

            $guardHandler->authenticateUserAndHandleSuccess(
                $user,          // the User object you just created
                $request,
                $authenticator, // authenticator whose onAuthenticationSuccess you want to use
                'main'          // the name of your firewall in security.yaml
            );

            return $this->redirectToRoute('home');
        }

        return $this->render('security/register.html.twig', [
        'form' => $form->createView(),
        ]);
    }

    /**
     * Allows to change the password
     * @Route("/account/initialisation_password", name="initialisation_password")
     * @return Response
     */
    public function initialisationPassword(Request $request, UserPasswordEncoderInterface $encoder, EntityManagerInterface $em)
    {
        $initialisationPassword = new InitialisationPassword();

        $user = $this->getUser();

        $form = $this->createForm(InitialisationPasswordType::class, $initialisationPassword);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            if(!password_verify($initialisationPassword->getAncienPassword(), $user->getPassword()))
            {
                $form->get('ancienPassword')->addError(new FormError("Il ne s'agit pas de votre mot de passe actuel"));

            } else
            {
                $nouveauPassword = $initialisationPassword->getNouveauPassword();
                $password = $encoder->encodePassword($user, $nouveauPassword);

                $user->setPassword($password);
                $em->flush();
                $this->addFlash(
                    'success',
                    "Votre mot de passe a bien été modifié."
                );
                return $this->redirectToRoute('accueil');
            }
        }
        return $this->render('account/initialisation_password.html.twig', [
            'form' => $form->createView()
        ]);
    }
}

