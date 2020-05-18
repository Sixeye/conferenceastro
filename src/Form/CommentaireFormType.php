<?php

namespace App\Form;

use App\Entity\Commentaire;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;

class CommentaireFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('texte', TextareaType::class, [ 'attr' => ['name' => 'commentaire',
                                                             'placeholder' =>'Saisissez votre commentaire',
                                                              'rows' => '2',
                                                               'class' => 'form-control' ]
            ])
            ->add('photo', FileType::class, [
                'required'    => false,
                'mapped'      => false,
                'attr'        => ['name'        => 'photo',
                                  'placeholder' => 'Téléchargez votre image',
                                  'class'       => 'btn'],
                'constraints' => [
                    new Image(['maxSize' => '1024k']),
            ]]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Commentaire::class,
        ]);
    }
}
