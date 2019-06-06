<?php

namespace App\Form;

use App\Entity\Donation;
use App\Form\AddressType;
use App\Form\ProductType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class DonationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre du don*',
                'attr' => [
                    'placeholder' => 'Saisir ici un nom pour le don (exemple: "Don de produits frais")'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez renseigner un nom pour votre don'
                    ])
                ]
            ])
            ->add('picture', FileType::class, [
                'label' => 'Image illustrant le don (jpg, png, gif)'
            ])
            ->add('address', AddressType::class, [
                'label' => false
            ])
            ->add('products', CollectionType::class, [
                'label' => false,
                'entry_type' => ProductType::class,
                'entry_options' => [
                    'label' => false,
                ],
                'by_reference' => false,
                'allow_add' => true,
                'allow_delete' => true,
            ])
            ->add('Publier', SubmitType::class, [
                'label' => 'Publier le don',
                'attr' => [
                    'class' => 'btn dark-green'
                ]
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
