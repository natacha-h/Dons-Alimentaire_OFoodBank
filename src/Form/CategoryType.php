<?php

namespace App\Form;

use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class CategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->setMethod('GET')
            ->add('name', EntityType::class, [
                'label' => false,
                'required' => false,
                // la classe à charger
                'class' => Category::class,
                // le champ de l'entité à utilser dans la liste déroulante
                'choice_label' => 'name',
            ])
            ->add('Choisir', SubmitType::class,  [
                'label' => 'Afficher',
                'attr' => [
                    'class' => 'btn dark-green'
                ]
            ])
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Category::class,
        ]);
    }
}
