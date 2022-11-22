<?php

namespace App\Form;

use App\Entity\Platform;
use App\Entity\Tag;
use App\Entity\Work;
use App\Repository\TagRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class WorkType extends AbstractType
{
    public function __construct(
        private TagRepository $tagRepository,
    ) {
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('type', ChoiceType::class, [
                'choices' => Work::getTypes(),
                'choice_label' => function ($choice)
                {
                    return ucfirst($choice);
                },
            ])
            ->add('nativeCountry', CountryType::class)
            ->add('originalName')
            ->add('alternativeName', CollectionType::class, [
                'entry_type' => TextType::class,
                'entry_options' => [
                    'label' => false,
                ],
                'allow_add' => true,
                'allow_delete' => true,
            ])
            ->add('pictureFile', VichImageType::class, [
                'required' => false,
                'allow_delete' => false,
            ])
            ->add('tags', EntityType::class, [
                'class' => Tag::class,
                'choices' => $this->tagRepository->findWithCustomOrder(),
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('platforms', EntityType::class, [
                'class' => Platform::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('p')
                        ->orderBy('p.name', 'ASC');
                },
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Work::class,
        ]);
    }
}
