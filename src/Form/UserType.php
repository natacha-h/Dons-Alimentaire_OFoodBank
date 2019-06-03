<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        //fonction anonyme a passer au addEventListener() dans le form
	    $listener = function (FormEvent $event) {

        //ceci est l'objet user passé en parametre lors du createform effectué dans le controller
        $currentUser = $event->getData();

        //ceci est le formulaire en cours de creation , il beneficie donc deja des input créé avec la methode add() tel que role , username, email
        $currentForm = $event->getForm();

        //si mon utilisateur à un id c'est qu'il existe deja en BDD => MODIFICATION / update
        if($currentUser->getId()){

            $currentForm->add('password', RepeatedType::class, [

                'type' => PasswordType::class,
                'invalid_message' => 'Les deux mots de passe doivent être identiques.',
                'options' => ['attr' => ['class' => 'password-field']],
                'empty_data' => '',
                'required' => true,
                'first_options'  => [
                    'label' => 'Password',
                    'empty_data' => '',
                    'attr' => [
                        'placeholder' => 'Laisser vide si inchangé'
                    ]
                ],
                'second_options' => [
                    'label' => 'Repeat Password', 
                    'empty_data' => '',
                    'attr' => [
                        'placeholder' => 'Laisser vide si inchangé'
                    ]
                ],
                'empty_data' => array(),
            ]);

        } else { //sinon je suis en création

            $currentForm->add('password', RepeatedType::class, [

                'type' => PasswordType::class,
                'invalid_message' => 'The password fields must match.',
                'options' => ['attr' => ['class' => 'password-field']],
                'empty_data' => '',
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                ],
                'first_options'  => [
                    'label' => 'Password',
                    'empty_data' => '',
                ],
                'second_options' => [
                    'label' => 'Repeat Password', 
                    'empty_data' => '',
                ],
                'empty_data' => array(),
            ]);
        }
    };

        $builder
            ->add('firstname')
            ->add('lastname')
            //je rajoute un ecouteur d'evenement sur PRE_SET_DATA qui se declenche a la construction du formulaire 
            ->addEventListener(FormEvents::PRE_SET_DATA, $listener)
            ->add('email')
            ->add('company')
            ->add('phone_number')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'attr' => ['novalidate' => 'novalidate'] # desactive (a faire de facon temporaire) la validation html5
        ]);
    }
}
