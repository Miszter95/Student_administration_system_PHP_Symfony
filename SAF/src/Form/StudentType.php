<?php

namespace App\Form;

use App\Entity\StudyGroup;
use App\Entity\Student;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

/**
 * Dedicated class of the Student's form
 */
class StudentType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('name', TextType::class, array('attr' => array('class' => 'form-control')))
            ->add('sex', ChoiceType::class, array('attr' => array('class' => 'form-control'),
                'choices'  => [
                    'female' => 'female',
                    'male' => 'male',
                    ]
                ))
            ->add('email_address',EmailType::class, array('attr' => array('class' => 'form-control')))
            ->add('place_of_birth',TextType::class, array('attr' => array('class' => 'form-control')))
            ->add('date_of_birth',DateType::class,
                array('attr' => array('class' => 'form-control'),
                    'format' => 'yyyy - MMMM - dd',
                    'years' => range(1950,date('Y'))))
            ->add('study_groups', EntityType::class,
                array('label' => 'Study groups (maximum 4 groups at once)',
                    'class' => StudyGroup::class,
                    'choice_label' => 'group_name',
                    'query_builder' => function (EntityRepository $enRep){
                        return $enRep->createQueryBuilder('g')
                            ->orderBy('g.group_name','ASC');
                    },
                    'multiple' => true,
                    'expanded' => true))
            ->add('oldValue', HiddenType::class, [
                'label'         => false,
                'mapped'        => false,
            ]);

        if($options['option'] == 1){
            $builder
                ->add('imageFile',FileType::class,
                    array('label' => 'Student image (.bmp/ .png/ .jp2)', 'mapped' => false,
                        'constraints' => [ new File([
                            'maxSize' => '1024k',
                            'mimeTypes' => ['image/bmp', 'image/png', 'image/jp2'],
                        ]),],
                        ))
                ->add('save', SubmitType::class, array('label' => 'Create', 'attr' => array('style' => 'background-color: #36ccdd; width: 80px; height: 25px')));
        }
        elseif ($options['option'] == 2) {
            $builder
                ->add('imageFile', FileType::class,
                    array('label' => 'Student image (.bmp/ .png/ .jp2)', 'mapped' => false, 'required' => false,
                        'constraints' => [new File([
                            'maxSize' => '1024k',
                            'mimeTypes' => ['image/bmp', 'image/png', 'image/jp2'],
                        ]),],));
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => Student::class]);
        $resolver->setRequired(array('option'));
    }

}