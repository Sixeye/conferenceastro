<?php


namespace App\service;

use App\Entity\User;

class UserAuto
{
    private $user;
    private $mdp;

    public function userAuto($request, $encoder, $guardHandler, $authenticator, $manager, $formUser){
        $user = new User();
        $data = '1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZabcefghijklmnopqrstuvwxyz';
        $mdp = substr(str_shuffle($data), 0, 18);
        $password = $encoder->encodePassword($user, $mdp);
        $user->setPassword($password);
        $user->setPrenom($formUser['prenom']->getData());
        $user->setNom($formUser['nom']->getData());
        $user->setEmail($formUser['email']->getData());
        $manager->persist($user);
        $manager->flush();
        $this->setUser($user);
        $this->setMdp($mdp);

        //If a new user is made, connect him
        $guardHandler->authenticateUserAndHandleSuccess(
            $user,          // the User object you just created
            $request,
            $authenticator, // authenticator whose onAuthenticationSuccess you want to use
            'main' );         // the name of your firewall in security.yaml
}

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user): void
    {
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getMdp()
    {
        return $this->mdp;
    }

    /**
     * @param mixed $mdp
     */
    public function setMdp($mdp): void
    {
        $this->mdp = $mdp;
    }



}