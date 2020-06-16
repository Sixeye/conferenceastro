<?php

namespace App\Controller;

use App\Entity\InitialisationPassword;
use App\Entity\User;
use App\Form\InitialisationPasswordType;
use App\Form\RegistrationType;
use App\Form\ResetPasswordType;
use App\Repository\UserRepository;
use App\Security\LoginFormAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
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
     * @param EntityManagerInterface $em
     * @param GuardAuthenticatorHandler $guardHandler
     * @param LoginFormAuthenticator $authenticator
     * @param MailerInterface $mailer
     * @return Response|null
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $em, GuardAuthenticatorHandler $guardHandler, LoginFormAuthenticator $authenticator,
                             MailerInterface $mailer)
    {
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);

//error exists

        if($form->isSubmitted() && $form->isValid())
        {
            $password = $passwordEncoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);

            // On génère un activation_token
            $user->setActivationToken(md5(uniqid()));

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

//            $em->persist($user);
//            $em->flush();

            // Activation email avec le token
            $email = (new TemplatedEmail())
                ->from('contact@astronomyconference.com')
                ->to($user->getEmail())
                ->subject('Inscription sur le site Astronomy Conference 🚀 , confirmation de votre compte')
                ->htmlTemplate('email/activation.html.twig')
                ->context([
                    'token' => $user->getActivationToken(),
                ]);

            $mailer->send($email);

            // Message Flashbag qui indique que tout s'est bien passé

            $this->addFlash(
                'success',
                "Le compte a bien été créé! Bienvenue ! Veuillez confirmer le compte à la réception du mail."
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
     * @Route("/initialisation_password", name="initialisation_password")
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
                return $this->redirectToRoute('home');
            }
        }
        return $this->render('account/initialisation_password.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/activation/{token}", name="activation")
     */
    public function activation($token, UserRepository $userRepository)
    {
        // Un user a-t-il ce token?
        $user = $userRepository->findOneBy(['activation_token' => $token]);

        // Si aucun utilisateur n'est associé à ce token
        if(!$user){
            // On renvoie une erreur 404
            throw $this->createNotFoundException('Cet utilisateur n\'existe pas');
        }

        // On supprime le token
        $user->setActivationToken(null);
        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        // On génère un message flash bag
        $this->addFlash('message', 'Votre compte est activé avec succès');

        // On retourne à l'accueil
        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/oubli-mot-de-passe", name="app_forgotten_password")
     * @param Request $request
     * @param UserRepository $userRepository
     * @param MailerInterface $mailer
     * @param TokenGeneratorInterface $tokenGenerator
     */
    public function forgottenPassword(Request $request, UserRepository $userRepository, MailerInterface $mailer, TokenGeneratorInterface $tokenGenerator)
    {
        // On créé le formulaire
        $form = $this->createForm(ResetPasswordType::class);

        // On traite le formulaire
        $form->handleRequest($request);

        // Si le formulaire est valide
        if ($form->isSubmitted() && $form->isValid()) {
            // On récupère les données
            $datas = $form->getData();

            // On cherche un utilisateur ayant cet e-mail
            $user = $userRepository->findOneByEmail($datas['email']);

            // Si l'utilisateur n'existe pas
            if ($user === null) {
                // On envoie une alerte disant que l'adresse e-mail est inconnue
                $this->addFlash('danger', 'Cette adresse email nous est malheureusement inconnue');

                // On retourne sur la page de connexion
                return $this->redirectToRoute('app_login');
            }

            // On génère un token
            $token = $tokenGenerator->generateToken();

            // On essaie d'écrire le token en base de données
            try{
                $user->setResetToken($token);
                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();
            } catch (Exception $e) {
                $this->addFlash('warning', $e->getMessage());
                return $this->redirectToRoute('app_login');
            }

            // On génère l'URL de réinitialisation de mot de passe
            $url = $this->generateUrl('app_reset_password', array('token' => $token), UrlGeneratorInterface::ABSOLUTE_URL);

            $email = (new TemplatedEmail())
                ->from('contact@astronomyconference.com')
                ->to($user->getEmail())
                ->subject('Compte sur le site Astronomy Conference 🚀 , oubli du mot de passe de votre compte')
                ->htmlTemplate('email/mot_de_passe_oublie.html.twig')
                ->context([
                    'url' => $url,
                ]);

            $mailer->send($email);

            // On crée le message flash de confirmation
            $this->addFlash('success', 'Email de réinitialisation du mot de passe envoyé !');

            // On redirige vers la page de login
            return $this->redirectToRoute('app_login');

        }

        // On envoie le formulaire à la vue
        return $this->render('security/forgotten_password.html.twig',['form' => $form->createView()]);
    }

    /**
     * @Route("/reset_password/{token}", name="app_reset_password")
     * @param Request $request
     * @param string $token
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function resetPassword(Request $request, string $token, UserPasswordEncoderInterface $passwordEncoder)
    {

        // On cherche un utilisateur avec le token donné
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['reset_token' => $token]);

        // Si l'utilisateur n'existe pas
        if ($user === null) {
            // On affiche une erreur
            $this->addFlash('danger', 'Token Inconnu');
            return $this->redirectToRoute('app_login');
        }

        // Si le formulaire est envoyé en méthode post
        if ($request->isMethod('POST')) {
            // On supprime le token
            $user->setResetToken(null);

            // On chiffre le mot de passe
            $user->setPassword($passwordEncoder->encodePassword($user, $request->request->get('password')));

            // On enregistre dans la base de données
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            // On crée le message flash
            $this->addFlash('message', 'Votre Mot de passe est mis à jour');

            // On redirige vers la page de connexion
            return $this->redirectToRoute('app_login');
        }else {
            // Si on n'a pas reçu les données, on affiche le formulaire
            return $this->render('security/reset_password.html.twig', ['token' => $token]);
        }

    }

}

