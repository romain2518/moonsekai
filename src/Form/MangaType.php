<?php

namespace App\Form;

use App\Entity\Manga;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class MangaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('state', ChoiceType::class, [
                'choices' => Manga::getStates(),
                'choice_label' => function ($choice)
                {
                    return ucfirst($choice);
                },
            ])
            ->add('releaseRegularity', ChoiceType::class, [
                'choices' => Manga::getReleaseRegularities(),
                'choice_label' => function ($choice)
                {
                    return ucfirst($choice);
                },
            ])
            ->add('author')
            ->add('designer')
            ->add('editor')
            ->add('releaseYear')
            ->add('pictureFile', VichImageType::class, [
                'required' => false,
                'allow_delete' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Manga::class,
        ]);
    }
}
