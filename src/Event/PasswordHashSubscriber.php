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

use App\Entity\User;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Encode users password just before persisting the entity.
 */
class PasswordHashSubscriber implements EventSubscriber
{
    private UserPasswordEncoderInterface $encoder;

    /**
     * PasswordHashSubscriber constructor.
     *
     * @param UserPasswordEncoderInterface $encoder
     */
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
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
            Events::preUpdate,
        ];
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args): void
    {
        $this->hashPassword($args->getObject());
    }

    /**
     * @param PreUpdateEventArgs $args
     */
    public function preUpdate(PreUpdateEventArgs $args): void
    {
        // End the event if the password doesn't change
        if (!array_key_exists('password', $args->getEntityChangeSet())) {
            return;
        }

        $this->hashPassword($args->getObject());
    }

    /**
     * @param object $entity
     */
    private function hashPassword(object $entity): void
    {
        if (!$entity instanceof User) {
            return;
        }

        $plainText = $entity->getPassword();
        if ($plainText) {
            $entity->setPassword(
                $this->encoder->encodePassword($entity, $plainText)
            );
        }
    }
}
