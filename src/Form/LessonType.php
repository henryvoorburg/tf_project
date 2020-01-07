<?php

namespace App\Form;

use App\Entity\Instructor;
use App\Entity\Lesson;
use App\Entity\Training;
use Doctrine\DBAL\Types\IntegerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LessonType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('time', TimeType::class)
            ->add('date', BirthdayType::class, [
                'format' => 'dd-MM-yyyy',
            ])
            ->add('location', TextType::class)
            ->add('max_persons', NumberType::class, ['label' => 'Max aantal deelnemers'])
             ->add('instructor', EntityType::class, [
                 'class' => Instructor::class,
                 'choice_label' => "getPerson.fullname"
             ])
            ->add('training', EntityType::class, [
                'class' => Training::class,
                'choice_label' => 'naam'
            ])->add('Submit', SubmitType::Class, ['attr' => ['class' => "btn btn-primary", "style" => "
            margin-top: 2%;
    text-align: center;
    margin-left: 50%;
    margin-bottom: 2%;
            "]]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Lesson::class,
            'entityManager' => null
        ]);
    }
}
