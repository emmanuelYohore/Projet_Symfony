<?php

namespace App\Form;

use App\Document\Habit;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class HabitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class)
            ->add('description', TextType::class)
            ->add('difficulty', ChoiceType::class, [
                'choices' => [
                    'Very Easy' => 0,
                    'Easy' => 1,
                    'Medium' => 2,
                    'Hard' => 3,
                ],
            ])
            ->add('color', ChoiceType::class, [
                'choices' => [
                    'Blue' => "#0000FF",
                    'Red' => "#FF0000",
                    'Green' => "#00FF00",
                    "Magenta" => "#FF00FF",
                    "Cyan" => "#00FFFF",
                    "Yellow" => "#FFFF00",
                ]
            ])
            ->add('periodicity', ChoiceType::class, [
                'choices' => [
                    'Daily' => 'daily',
                    'Weekly' => 'weekly',
                    'Monthly' => 'monthly',
                ],
            ])
            ->add('save', SubmitType::class, [
                'label' => "Ajouter une tache",
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Habit::class,
        ]);
    }
}