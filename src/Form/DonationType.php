<?php

namespace App\Form;

use App\Entity\Donation;
use App\Form\AddressType;
use App\Form\ProductType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class DonationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre du don',
                'attr' => [
                    'placeholder' => 'Saisir ici un nom pour le don'
                ]
            ])
            ->add('picture', TextType::class, [
                'label' => 'Image illustrant le don'
            ])
            ->add('address', AddressType::class, [
                
            ])
            ->add('products', CollectionType::class, [
                'label' => 'Ajouter un produit au don',
                'entry_type' => ProductType::class,
                'entry_options' => [
                    'label' => false,
                ],
                'by_reference' => false,
                'allow_add' => true,
                'allow_delete' => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Donation::class,
        ]);
    }
}
