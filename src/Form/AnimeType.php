<?php

namespace App\Form;

use App\Entity\Anime;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class AnimeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('state', ChoiceType::class, [
                'choices' => Anime::getStates(),
                'choice_label' => function ($choice)
                {
                    return ucfirst($choice);
                },
            ])
            ->add('author')
            ->add('animationStudio')
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
            'data_class' => Anime::class,
        ]);
    }
}
