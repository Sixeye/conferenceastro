<?php

namespace App\service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;

class CustomMailer
{

    public function automaticAccount($user, $mdp, $mailer)
    {
        $email = (new TemplatedEmail())
            ->from('contact@astronomyconference.com')
            ->to($user->getEmail())
            ->subject('Inscription sur le site Astronomy Conference 🚀 , vos informations personnelles')
            ->htmlTemplate('email/bienvenue_auto.html.twig')
            ->context([
                'user' => $user,
                'mot'  => $mdp
            ]);

        $mailer->send($email);
    }

}