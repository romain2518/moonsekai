<?php

namespace App\Form;

use App\Entity\Comment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('message')
            ->add('targetTable', HiddenType::class, [
                'mapped' => false,
                'data' => $options['targetTable'],
            ])
            ->add('targetId', HiddenType::class, [
                'mapped' => false,
                'data' => $options['targetId'],
            ])
            ->add('parentId', HiddenType::class, [
                'mapped' => false
                
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
            'targetTable' => null,
            'targetId' => null,
        ]);
    }
}
