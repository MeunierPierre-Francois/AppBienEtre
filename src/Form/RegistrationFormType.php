<?php

namespace App\Form;

use App\Entity\Utilisateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

use App\Service\CitiesService;

class RegistrationFormType extends AbstractType
{
    private $citiesService;

    public function __construct(CitiesService $citiesService)
    {
        $this->citiesService = $citiesService;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $cities = $this->citiesService->getCities();

        $builder
            ->add('email', EmailType::class)
            ->add('plainPassword', PasswordType::class, [

                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',

                        'max' => 4096,
                    ]),
                ],
            ])
            ->add('ville', ChoiceType::class, [
                'choices' => $cities,
                'choice_label' => function ($city) {
                    return $city['ville'];
                },
                'placeholder' => 'Choose a city',
                'mapped' => false,
                'required' => true,
            ])
            ->add('adresse_num')
            ->add('adresse_rue')
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'You should agree to our terms.',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
        ]);
    }
}
