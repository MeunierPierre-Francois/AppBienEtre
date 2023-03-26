<?php

namespace App\Form;


use App\Entity\Prestataire;
use App\Entity\CategorieDeServices;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class PrestataireFormType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom de l\'entreprise',
            ])
            ->add('site_internet', TextType::class, [
                'label' => 'Site internet',
                'required' => false,
            ])
            ->add('num_tel', TelType::class, [
                'label' => 'Numéro de téléphone',
                'required' => false,
            ])
            ->add('num_tva', TextType::class, [
                'label' => 'Numéro de TVA',
                'required' => false,
            ])
            ->add('categories', EntityType::class, [
                'class' => CategorieDeServices::class,
                'choice_label' => 'nom',
                'expanded' => true,
                'multiple' => true,
            ])
            ->add('images', FileType::class, [
                'label' => 'Ajouter des images',
                'multiple' => true,
                'mapped' => false,
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Prestataire::class,
        ]);
    }
}
