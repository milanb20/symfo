<?php

namespace App\Form;

use App\Entity\Trip;
use App\Entity\Country;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;

// import du type nous permettant de gérer le choix d'un pays
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class TripType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date_start')
            ->add('date_end')
            ->add('title', TextType::class)
            ->add('description')
            ->add('price')
            ->add('country', EntityType::class, array(
              'class' => Country::class,
              'choice_label' => 'name',
              'choice_value' => 'id'
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Trip::class,
        ]);
    }
}
