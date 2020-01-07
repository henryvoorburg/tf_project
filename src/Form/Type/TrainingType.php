<?php


namespace App\Form\Type;


use App\Entity\Training;
use App\Form\Type\DataTransformer\StringToFileTransformer;
use App\Repository\UserRepository;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TrainingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
//            ->addModelTransformer(new StringToFileTransformer($this->userRepository))
            ->add("naam", TextType::class, ['label' => 'Naam'])
            ->add("description", TextType::class, ['label' => 'Beschrijving'])
            ->add("duration", TimeType::class, ['label' => 'Duratie'])
            ->add("costs", MoneyType::class, ['label' => 'Kost'])
            ->add('image_name', FileType::class, ['label'=> "Foto", 'mapped' => false, 'required' => false])
            ->add('button', SubmitType::class, ['attr' => ['class' => "btn btn-primary", "style" => "
            margin-top: 2%;
            text-align: center;
            margin-left: 50%;
            margin-bottom: 2%;"], 'label' => 'Opslaan']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Training::class,
        ]);
    }

}