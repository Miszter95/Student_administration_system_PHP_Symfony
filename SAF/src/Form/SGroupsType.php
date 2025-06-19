<?php

namespace App\Form;

use App\Entity\Student;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Dedicated class for embedding collection of the Study Group forms and for edit
 */
class SGroupsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add("study_groups", CollectionType::class, array(
                'entry_type' => StudyGroupType::class,
                'entry_options' => ['option' => 2,'label' => false],
                'label' => false
            ))
            ->add('save', SubmitType::class, array('label' => 'Edit', 'attr' => array('style' => 'background-color: #36ccdd; width: 80px; height: 25px')));

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => Student::class]);
        $resolver->setRequired(array('option'));
    }

}