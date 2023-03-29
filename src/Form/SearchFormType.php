<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use App\Entity\Commune;
use App\Entity\CategorieDeServices;
use App\Entity\Localite;
use App\Entity\CodePostal;
use App\Model\SearchData;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('prestataire', TextType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Rechercher un prestataire...'
                ],
                'empty_data' => ''
            ])

            ->add('categorie_service', EntityType::class, [
                'class' => CategorieDeServices::class,
                'placeholder' => 'Catégories',
                'required' => false,
                'expanded' => true,
                'multiple' => true,

            ]);
        /* ->add('localite', EntityType::class, [
                'class' => Localite::class,
                'placeholder' => 'Localité',
                'required' => false
            ])
            ->add('code_postal', EntityType::class, [
                'class' => CodePostal::class,
                'placeholder' => 'Code Postal',
                'required' => false
            ])
            ->add('commune', EntityType::class, [
                'class' => Commune::class,
                'placeholder' => 'Commune',
                'required' => false
            ]);*/
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SearchData::class,
            'method' => 'GET'
        ]);
    }
}
