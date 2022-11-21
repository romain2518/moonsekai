<?php

namespace App\Form;

use App\Entity\CalendarEvent;
use App\Entity\Chapter;
use App\Entity\Episode;
use App\Entity\LightNovel;
use App\Entity\Movie;
use App\Entity\News;
use App\Entity\Work;
use App\Entity\WorkNews;
use App\Repository\NewsRepository;
use App\Repository\WorkRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CalendarEventType extends AbstractType
{
    public function __construct(
        private WorkRepository $workRepository,
        private NewsRepository $newsRepository,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'empty_data' => '',
            ])
            ->add('start', DateTimeType::class, [
                'empty_data' => (new \DateTime())->format('d/m/Y h:i'),
                'widget' => 'single_text',
                'html5' => 'false',
            ])
            ->add('targetTable', ChoiceType::class, [
                'placeholder' => 'Choose a target',
                'choices' => CalendarEvent::getTargetTables(),
            ])
            ->add('work', EntityType::class, [
                'class' => Work::class,
                'placeholder' => 'Choose a target',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('w')
                        ->orderBy('w.name', 'ASC');
                },
                'choice_label' => 'name',
                'data' => $options['work'],
                'mapped' => false
            ])
        ;

        $formModifier = function (FormInterface $form, ?string $targetTable, ?Work $work) {
            $choices = null === $targetTable || (News::class !== $targetTable && null === $work) ? [] : $this->getChoicesByTarget($targetTable, $work);
            
            $form->add('targetId', ChoiceType::class, [
                'choices' => $choices,
                'placeholder' => 'Choose an item',
                'empty_data' => 0,
            ]);
        };

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($formModifier, $options) {
                $data = $event->getData();
                
                $formModifier($event->getForm(), $data->getTargetTable(), $options['work']);
                
                $event->getForm()->get('targetId')->setData($options['targetId']);
            }
        );

        $builder->addEventListener(
            FormEvents::SUBMIT,
            function (FormEvent $event) use ($formModifier) {
                $targetTable = $event->getForm()->get('targetTable')->getData();
                $work = $event->getForm()->get('work')->getData();

                $formModifier($event->getForm(), $targetTable, $work);
            }
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CalendarEvent::class,
            'work' => null,
            'targetId' => null,
        ]);
    }

    public function getChoicesByTarget(string $targetTable, ?Work $work)
    {
        $choices = [];
        switch ($targetTable) {
            case News::class:
                foreach ($this->newsRepository->findBy([], ['title' => 'ASC']) as $news) {
                    $choices[$news->getTitle()] = $news->getId();
                }
                break;
            case Movie::class:
                foreach ($work->getMovies() as $movie) {
                    $choices[$movie->getName()] = $movie->getId();
                }
                break;
            case LightNovel::class:
                foreach ($work->getLightNovels() as $lightNovel) {
                    $choices[$lightNovel->getName()] = $lightNovel->getId();
                }
                break;
            case WorkNews::class:
                foreach ($work->getWorkNews() as $workNews) {
                    $choices[$workNews->getTitle()] = $workNews->getId();
                }           
                break;
            case Chapter::class:
                $qb = $this->workRepository->createQueryBuilder('w')
                    ->addSelect('m, v, c')
                    ->innerJoin('w.mangas', 'm')
                    ->innerJoin('m.volumes', 'v' )
                    ->innerJoin('v.chapters', 'c' )
                    ->where('w = :work')
                    ->setParameter('work', $work)
                    ->getQuery()
                ;

                /** @var Work $work */
                $work = $qb->getOneOrNullResult();

                foreach ($work->getMangas() as $manga) {
                    foreach ($manga->getVolumes() as $volume) {
                        foreach ($volume->getChapters() as $chapter) {
                            $choices[
                               $manga->getName()][
                                    $volume->getNumber() . ' ' . $volume->getName()][
                                        $chapter->getNumber() . ' ' . $chapter->getName()
                                        ] = $chapter->getId();
                        }
                    }
                }
                break;

            case Episode::class:
                $qb = $this->workRepository->createQueryBuilder('w')
                    ->addSelect('a, s, e')
                    ->innerJoin('w.animes', 'a')
                    ->innerJoin('a.seasons', 's' )
                    ->innerJoin('s.episodes', 'e' )
                    ->where('w = :work')
                    ->setParameter('work', $work)
                    ->getQuery()
                ;

                /** @var Work $work */
                $work = $qb->getOneOrNullResult();

                foreach ($work->getAnimes() as $anime) {
                    foreach ($anime->getSeasons() as $season) {
                        foreach ($season->getEpisodes() as $episode) {
                            $choices[
                               $anime->getName()][
                                    $season->getNumber() . ' ' . $season->getName()][
                                        $episode->getNumber() . ' ' . $episode->getName()
                                        ] = $episode->getId();
                        }
                    }
                }
                break;
        }

        return $choices;
    }
}
