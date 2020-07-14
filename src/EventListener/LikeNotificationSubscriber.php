<?php


namespace App\EventListener;


use App\Entity\LikeNotification;
use App\Entity\MicroPost;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Events;

class LikeNotificationSubscriber implements EventSubscriber
{

    public function getSubscribedEvents()
    {
        return [
            Events::onFlush
        ];
    }

    public function onFlush(OnFlushEventArgs $args)
    {
        $entityManager = $args->getEntityManager();
        $unitOfWork = $entityManager->getUnitOfWork();

        foreach($unitOfWork->getScheduledCollectionUpdates() as $collectionUpdate) {
            $insertDiff = $collectionUpdate->getInsertDiff();
            if(
                $collectionUpdate->getOwner() instanceof MicroPost &&
                'likedBy' === $collectionUpdate->getMapping()['fieldName'] &&
                count($insertDiff) > 0
            ) {
                /** @var MicroPost $microPost */
                $microPost = $collectionUpdate->getOwner();

                $notification = new LikeNotification();
                $notification->setUser($microPost->getUser())
                    ->setMicroPost($microPost)
                    ->setLikedBy(reset($insertDiff));

                $entityManager->persist($notification);

                $unitOfWork->computeChangeSet($entityManager->getClassMetadata(LikeNotification::class), $notification);
            }
        }
    }
}