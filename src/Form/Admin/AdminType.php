<?php

namespace App\Form\Admin;

use App\Entity\Admin;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints\File;

class AdminType extends AbstractType
{

    private $security;

    public function __construct(Security $security)
    {
        // Avoid calling getUser() in the constructor: auth may not
        // be complete yet. Instead, store the entire Security object.
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $roles['ROLE_ADMIN'] = 'ROLE_ADMIN';

        //SI ES UN SUPER ADMIN PUEDE ASIGNAR EL MISMO ROL A OTRO ADMINISTRADOR:
        if($this->security->isGranted('ROLE_SUPERADMIN'))
            $roles['ROLE_SUPERADMIN'] = 'ROLE_SUPERADMIN';

        $builder
            ->add('email', EmailType::class)
            ->add('name', TextType::class)
            /* ->add('roles', ChoiceType::class, [
                'choices' => $roles,
                'choice_attr' => function($key, $val, $index) {
                    return $val == 'ROLE_ADMIN' ? ['checked' => 'checked'] : [];
                },
                'mapped'=> false,
                'multiple' => true,
                'expanded' => true,

            ]) */
            ->add('image', FileType::class, [
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png'
                        ],
                        'mimeTypesMessage' => 'La archivo a subir debe ser una imágen.',
                    ])
                ],
            ])
            ->add('password', PasswordType::class, [
                'empty_data' => '',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Admin::class,
        ]);
    }
}
