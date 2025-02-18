<?php

namespace App\Form;

use App\Entity\Groupe;
use App\Entity\Habit;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class HabitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class)
            ->add('description',TextType::class)
            ->add('difficulty',IntegerType::class)
            ->add('color',TextType::class)
            ->add('periodicity',TextType::class)
            ->add('creator_id', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
            ])
            ->add('group_id', EntityType::class, [
                'class' => Groupe::class,
                'choice_label' => 'id',
            ])
            ->add('valider', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Habit::class,
        ]);
    }
}
