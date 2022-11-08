<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            ->add('roles')
            ->add('password')
            ->add('pseudo')
            ->add('picturePath')
            ->add('bannerPath')
            ->add('biography')
            ->add('notificationSetting')
            ->add('isNotificationRedirectionEnabled')
            ->add('isMuted')
            ->add('isSubscribedNewsletter')
            ->add('isVerified')
            ->add('createdAt')
            ->add('updatedAt')
            ->add('followedWorks')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
