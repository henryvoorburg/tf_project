<?php


namespace App\Form\Type;

use App\Entity\Person;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class PersonType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', TextType::Class, ['attr' => ['class' => "form-control col-"], 'label' => 'Voornaam'])
            ->add('preprovision', TextType::Class, ['attr' => ['class' => "form-control"], 'label' => 'Voortzetels'])
            ->add('lastname', TextType::Class, ['attr' => ['class' => "form-control"], 'label' => 'Achternaam'])
            ->add('dateofbirth', BirthdayType::Class, ['label' => 'Geboortedatum'])
            ->add('loginname', TextType::Class, ['attr' => ['class' => "form-control"], 'label' => 'Gebruikersnaam'])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'De wachtwoorden velden moeten met elkaar overeenkomen.',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options' => ['label' => 'Wachtwoord'],
                'second_options' => ['label' => 'Herhaling'],
            ])
            ->add('gender', ChoiceType::Class, ['choices' => ['Man' => 'Man', 'Vrouw' => "Vrouw"], 'expanded' => true])
            ->add('street', TextType::Class, ['attr' => ['class' => "form-control"], 'mapped' => false,'label' => 'Straat', 'constraints' => [
                new Assert\NotBlank(['message' => 'Vul een geldige straat in.']),
            ]])
            ->add('place', TextType::Class, ['attr' => ['class' => "form-control"], 'mapped' => false,'label' => 'Plaats', 'constraints' => [
                new Assert\NotBlank(['message' => 'Vul een geldige stad in.']),
            ]])
            ->add('postal_code', TextType::Class, ['attr' => ['class' => "form-control"],'label' => 'Postcode','mapped' => false, 'constraints' => [
                new Assert\NotBlank(['message' => 'Vul een geldige postcode in.']),
            ]])
            ->add('emailadres', EmailType::Class, ['attr' => ['class' => "form-control"]])
            ->add('Submit', SubmitType::Class, ['attr' => ['class' => "btn btn-primary", "style" => "
            margin-top: 2%;
    text-align: center;
    margin-left: 50%;
    margin-bottom: 2%;
            "]]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Person::Class
        ]);
    }
}
