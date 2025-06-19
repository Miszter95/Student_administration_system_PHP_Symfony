<?php

namespace App\Form;

use App\Entity\StudyGroup;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Dedicated class for embedding collection of the Student forms and for edit
 */
class StudentsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add("enrolled_students", CollectionType::class, array(
                'entry_type' => StudentType::class,
                'entry_options' => ['label' => false,'option' => 2],
                'label' => false
            ))
            ->add('save', SubmitType::class, array('label' => 'Edit', 'attr' => array('style' => 'background-color: #36ccdd; width: 80px; height: 25px')));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => StudyGroup::class]);
        $resolver->setRequired(array('option'));
    }

}