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
use App\Repository\ChapterRepository;
use App\Repository\EpisodeRepository;
use App\Repository\LightNovelRepository;
use App\Repository\MovieRepository;
use App\Repository\NewsRepository;
use App\Repository\WorkNewsRepository;
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
        private NewsRepository $newsRepository,
        private EpisodeRepository $episodeRepository,
        private ChapterRepository $chapterRepository,
        private LightNovelRepository $lightNovelRepository,
        private MovieRepository $movieRepository,
        private WorkNewsRepository $workNewsRepository,
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
                foreach ($this->movieRepository->findBy(['work' => $work], ['name' => 'ASC']) as $movie) {
                    $choices[$movie->getName()] = $movie->getId();
                }
                break;
            case LightNovel::class:
                foreach ($this->lightNovelRepository->findBy(['work' => $work], ['name' => 'ASC']) as $lightNovel) {
                    $choices[$lightNovel->getName()] = $lightNovel->getId();
                }
                break;
            case WorkNews::class:
                foreach ($this->workNewsRepository->findBy(['work' => $work], ['title' => 'ASC']) as $workNews) {
                    $choices[$workNews->getTitle()] = $workNews->getId();
                }           
                break;
            case Chapter::class:
                $qb = $this->chapterRepository->createQueryBuilder('c')
                    ->addSelect('v, m')
                    ->innerJoin('c.volume', 'v' )
                    ->innerJoin('v.manga', 'm')
                    ->where('m.work = :work')
                    ->addOrderBy('m.name', 'ASC')
                    ->addOrderBy('CAST(v.number AS decimal)', 'ASC')
                    ->addOrderBy('CAST(c.number AS decimal)', 'ASC')
                    ->setParameter('work', $work)
                    ->getQuery()
                ;
                $chapters = $qb->getResult();

                foreach ($chapters as $chapter) {
                    $choices[
                        $chapter->getVolume()->getManga()->getName()][
                            $chapter->getVolume()->getNumber() . ' ' . $chapter->getVolume()->getName()][
                                $chapter->getNumber() . ' ' . $chapter->getName()
                                ] = $chapter->getId();
                }
                break;
            case Episode::class:
                $qb = $this->episodeRepository->createQueryBuilder('e')
                    ->addSelect('s, a')
                    ->innerJoin('e.season', 's' )
                    ->innerJoin('s.anime', 'a')
                    ->where('a.work = :work')
                    ->addOrderBy('a.name', 'ASC')
                    ->addOrderBy('CAST(s.number AS decimal)', 'ASC')
                    ->addOrderBy('CAST(e.number AS decimal)', 'ASC')
                    ->setParameter('work', $work)
                    ->getQuery()
                ;
                $episodes = $qb->getResult();

                foreach ($episodes as $episode) {
                    $choices[
                        $episode->getSeason()->getAnime()->getName()][
                            $episode->getSeason()->getNumber() . ' ' . $episode->getSeason()->getName()][
                                $episode->getNumber() . ' ' . $episode->getName()
                                ] = $episode->getId();
                }
                break;
        }

        return $choices;
    }
}
