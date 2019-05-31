<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom du produit',
                'attr' => [
                    'placeholder' => 'Saisir ici le nom du produit'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le nom ne peut pas être vide'
                    ])
                ]
            ])
            ->add('quantity', TextType::class, [
                'label' => 'Nombre de produits',
                'attr' => [
                    'placeholder' => 'Saisir ici le nombre de produit'
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description du produit',
                'attr' => [
                    'placeholder' => 'Saisir ici une description du/des produits'
                ]
            ])
            ->add('expiry_date', DateType::class, [
                'label' => 'Date d\'expiration du produit'
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'label' => 'Catégorie'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
