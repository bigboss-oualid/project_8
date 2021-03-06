<?php
/**
 * ToDoAndCo Project
 * Copyright (c) 2020 BigBoss 2020.  BigBoss Oualid
 * mailto: <bigboss@it-bigboss.de>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 * Inc., Munich, Germany.
 */

namespace App\Event;

use App\Entity\Task;
use App\Entity\User;
use DateTime;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Security;

/**
 * Add datetime value of the property createdAt.
 */
class TaskSubscriber implements EventSubscriber
{
    /**
     * @var Security
     */
    private $security;

    /**
     * TaskSubscriber constructor.
     *
     * @param Security $security
     */
    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return string[]
     */
    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
        ];
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();
        /** @var User|null $loggedUser */
        $loggedUser = $this->security->getUser();

        if (!$entity instanceof Task || !$loggedUser) {
            return;
        }

        $entity->setUser($loggedUser)->setCreatedAt(new DateTime());
    }
}
