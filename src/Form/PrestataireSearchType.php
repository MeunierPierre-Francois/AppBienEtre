<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use App\Entity\Commune;
use App\Entity\CategorieDeServices;
use App\Entity\Localite;
use App\Entity\CodePostal;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PrestataireSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', SearchType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Rechercher un prestataire...'
                ],

            ])

            ->add('categories', EntityType::class, [
                'class' => CategorieDeServices::class,
                'placeholder' => 'Catégories',
                'required' => false,
                'expanded' => true,
                'multiple' => true,

            ])

            ->add('localite', EntityType::class, [
                'class' => Localite::class,
                'placeholder' => '--Sélectionnez une ville--',
                'required' => false
            ])

            ->add('codePostal', EntityType::class, [
                'class' => CodePostal::class,
                'placeholder' => '--Sélectionnez un code postal--',
                'required' => false
            ])

            ->add('commune', EntityType::class, [
                'class' => Commune::class,
                'placeholder' => '--Sélectionnez une commune--',
                'required' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
            'method' => 'GET'
        ]);
    }
}
