<?php

namespace App\Form;

use App\Entity\Course;
use App\Entity\Lesson;
use App\Entity\UserTracking;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LessonType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('content')
            ->add('lessonNumber')
            ->add('type')
            ->add('createdAt')
            ->add('updatedAt')
            ->add('userTrackings', EntityType::class, [
                'class' => UserTracking::class,
'choice_label' => 'id',
'multiple' => true,
            ])
            ->add('course', EntityType::class, [
                'class' => Course::class,
'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Lesson::class,
        ]);
    }
}
