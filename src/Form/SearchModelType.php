<?php

namespace App\Form;

use App\Model\SearchModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchModelType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('value', SearchType::class, [
                'required' => true,
                'label' => false,
                'attr' => [
                    'class' => 'form-control mr-sm-2'
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'generic.form.submit.label',
                'attr' => [
                    'class' => 'btn btn-outline-light my-2 my-sm-0'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SearchModel::class,
            'attr' => ['class' => 'form-inline',],
        ]);
    }
}