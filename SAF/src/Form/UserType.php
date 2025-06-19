<?php

namespace App\Form;

use App\Entity\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;


/**
 * Dedicated class of the User's form
 */
class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, array('attr' => array('class' => 'form-control')))
            ->add('email_address', EmailType::class, array('attr' => array('class' => 'form-control')))
            ->add('password',PasswordType::class, array('attr' => array('class' => 'form-control')));

        if($options['option'] == 1) {
            $builder
                ->add('imageFile',FileType::class,
                    array('label' => 'User image (.bmp/ .png/ .jp2)', 'mapped' => false,
                        'constraints' => [ new File([
                            'maxSize' => '1024k',
                            'mimeTypes' => ['image/bmp', 'image/png', 'image/jp2'],
                            //'mimeTypesMessage' => 'Please upload an image with an allowed extension!',
                        ]),],))
                ->add('save', SubmitType::class, array('label' => 'Create', 'attr' => array('style' => 'background-color: #36ccdd; width: 80px; height: 25px')));
        }
        elseif ($options['option'] == 2){
            $builder
                ->add('imageFile',FileType::class,
                    array('label' => 'User image (.bmp/ .png/ .jp2)', 'mapped' => false, 'required'   => false,
                        'constraints' => [ new File([
                            'maxSize' => '1024k',
                            'mimeTypes' => ['image/bmp', 'image/png', 'image/jp2'],
                            //'mimeTypesMessage' => 'Please upload an image with an allowed extension!',
                        ]),],))
                ->add('save', SubmitType::class, array('label' => 'Edit', 'attr' => array('style' => 'background-color: #36ccdd; width: 80px; height: 25px')));
        }

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => User::class]);
        $resolver->setRequired(array('option'));
    }

}