<?php

namespace App\Form;

use App\Entity\Address;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class AddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('number', TextType::class, [
                'label' => 'Numéro de la rue'
            ])
            ->add('street', TextType::class, [
                'label' => 'Nom de la rue'
            ])
            ->add('zip_code', TextType::class, [
                'label' => 'Code postal'
            ])
            ->add('city', TextType::class, [
                'label' => 'Ville'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Address::class,
        ]);
    }
}
