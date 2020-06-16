<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class,       ['attr' => ['placeholder' => 'votre e-mail']])
            ->add('nom', TextType::class,          ['attr' => ['placeholder' => 'votre nom']])
            ->add('prenom', TextType::class,       ['attr' => ['placeholder' => 'votre prénom']])
            ->add('adresse', TextType::class,      ['attr' => ['placeholder' => 'votre adresse']])
            ->add('ville', TextType::class,        ['attr' => ['placeholder' => 'votre ville']])
            ->add('code_postal', TextType::class,  ['attr' => ['placeholder' => 'votre code postal']])
            ->add('password', PasswordType::class, ['attr' => ['placeholder' => 'votre mot de passe'],
                                                                'constraints' => [
                                                                    new NotBlank(),
                                                                    new Length(['min' => 5])]])
            ->add('passwordConfirm', PasswordType::class, ['attr' => ['placeholder' => 'Confirmez']])

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
