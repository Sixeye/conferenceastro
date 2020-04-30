<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InitialisationPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('ancienPassword', PasswordType::class,    [ 'attr' => ['placeholder' => 'Saississez votre mot de passe actuel']])
            ->add('nouveauPassword', PasswordType::class,   [ 'attr' => ['placeholder' => 'Nouveau mot de passe']])
            ->add('confirmerPassword', PasswordType::class, [ 'attr' => ['placeholder' => 'Confirmez le nouveau mot de passe']])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}