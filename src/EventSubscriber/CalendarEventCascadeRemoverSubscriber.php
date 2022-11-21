<?php

namespace App\EventSubscriber;

use App\Entity\CalendarEvent;
use App\Entity\Chapter;
use App\Entity\Episode;
use App\Entity\LightNovel;
use App\Entity\Movie;
use App\Entity\News;
use App\Entity\WorkNews;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class CalendarEventCascadeRemoverSubscriber implements EventSubscriberInterface
{
    public function getSubscribedEvents(): array
    {
        return [
            Events::preRemove,
        ];
    }

    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        $em = $args->getObjectManager();

        if (!$entity instanceof Chapter && !$entity instanceof Episode
            && !$entity instanceof LightNovel && !$entity instanceof Movie
            && !$entity instanceof News && !$entity instanceof WorkNews) {
            return;
        }

        foreach ($em->getRepository(CalendarEvent::class)->findBy(['targetTable' => $entity::class, 'targetId' => $entity->getId()]) as $event) {
            $em->remove($event);
        }

        $em->flush();
    }
}
