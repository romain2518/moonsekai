<?php

namespace App\Form;

use App\Entity\LightNovel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class LightNovelType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('author')
            ->add('editor')
            ->add('releaseYear')
            ->add('pictureFile', VichImageType::class, [
                'required' => false,
                'allow_delete' => false,
            ])
            ->add('addCalendarEvent', CheckboxType::class, [
                'required' => false,
                'mapped' => false,
            ])
            ->add('eventDate', DateTimeType::class, [
                'required' => false,
                'mapped' => false,
                'widget' => 'single_text',
                'html5' => 'false',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => LightNovel::class,
        ]);
    }
}
