<?php

namespace App\Form;

use App\Entity\StudyGroup;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


/**
 * Dedicated class of the StudyGroup's form
 */
class StudyGroupType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('group_name', TextType::class, array('attr' => array('class' => 'form-control')))
            ->add('group_leader', TextType::class, array('attr' => array('class' => 'form-control')))
            ->add('subject',TextType::class, array('attr' => array('class' => 'form-control')))
            ->add('datetime_of_study_group',DateTimeType::class,
                array('attr' => array('class' => 'form-control'),
                    'date_format' => 'yyyy - MMMM - dd',
                    'years' => range(1950,date('Y'))));

        if($options['option'] == 1) {
            $builder
                ->add('save', SubmitType::class, array('label' => 'Create', 'attr' => array('style' => 'background-color: #36ccdd; width: 80px; height: 25px')));
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => StudyGroup::class]);
        $resolver->setRequired(array('option'));
    }

}