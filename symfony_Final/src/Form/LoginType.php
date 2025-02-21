<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LoginType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
<<<<<<< HEAD
            ->add('username', TextType::class, [
                'label' => 'Nom d\'utilisateur',
                'attr' => ['placeholder' => 'Entrez votre nom d\'utilisateur']
=======
            ->add('identifier', TextType::class, [
                'label' => 'Nom d\'utilisateur ou e-mail',
                'attr' => ['placeholder' => 'Entrez votre nom d\'utilisateur ou votre e-mail']
>>>>>>> origin/emmanuel
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Mot de passe',
                'attr' => ['placeholder' => 'Entrez votre mot de passe']
            ])
            ->add('login', SubmitType::class, [
                'label' => 'Se connecter'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
<<<<<<< HEAD
}
=======
}
>>>>>>> origin/emmanuel
