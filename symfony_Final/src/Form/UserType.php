<?php

namespace App\Form;

use App\Document\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('first_name', TextType::class, ['label' => 'First Name'])
            ->add('last_name', TextType::class, ['label' => 'Last Name'])
            ->add('username', TextType::class, ['label' => 'Username'])
            ->add('email', EmailType::class, ['label' => 'Email'])
            ->add('password', PasswordType::class, ['label' => 'Password'])
            ->add('profile_picture', FileType::class, [
                'label' => 'Profile Picture (PNG, JPG, JPEG)',
                'mapped' => false, // Indique que ce champ ne correspond pas à une propriété de l'entité User
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '10M',
                        'mimeTypes' => ['image/jpeg', 'image/png', 'image/jpg'],
                        'mimeTypesMessage' => 'Veuillez télécharger une image valide (PNG, JPG, JPEG)',
                    ]),
                ],
            ])
            ->add('save', SubmitType::class, ['label' => 'Create User']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
