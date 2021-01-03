<?php

/**
 * ToDoAndCo Project
 * Copyright (c) 2020 BigBoss 2020.  BigBoss Oualid
 * mailto: <bigboss@it-bigboss.de>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 * Inc., Munich, Germany.
 */

namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\User;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Yaml\Yaml;

/**
 * Class AppFixtures.
 */
final class AppFixtures extends Fixture
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager): void
    {
        $users = $this->getDataFixture('User');
        $tasks = $this->getDataFixture('Task');
        // Add user => 'ROLE_ADMIN', user => 'ROLE_USER'
        $this->addUsers($users, $manager);
        // Add 6 tasks
        $this->addTasks($tasks, $manager);

        $manager->flush();
        echo "\n Loading fixtures is terminated!\n";
    }

    /**
     * Retrieve fixtures from file and transform them to array.
     *
     * @param string $entityName
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     *
     * @return array[]|null
     */
    private function getDataFixture(string $entityName): ?array
    {
        $input = file_get_contents(__DIR__.'/Fixtures/'.$entityName.'s.yaml', true);
        if (!$input) {
            return null;
        }

        return Yaml::parse($input);
    }

    /**
     * Create users.
     *
     * @param array[] | null $users
     * @param ObjectManager  $manager
     */
    private function addUsers(?array $users, ObjectManager $manager): void
    {
        if (!$users) {
            return;
        }

        foreach ($users as $name => $user) {
            /** @var User $userEntity */
            $userEntity = new User();

            $userEntity->setUsername($user['Username'])
                ->setPassword($user['Password'])
                ->setEmail($user['Email'])
                ->setRoles($user['Roles']);

            $manager->persist($userEntity);
            $this->addReference($name, $userEntity);
        }
    }

    /**
     * Create tasks.
     *
     * @param array[] | null $tasks
     * @param ObjectManager  $manager
     */
    private function addTasks(?array $tasks, ObjectManager $manager): void
    {
        if (!$tasks) {
            return;
        }

        foreach ($tasks as $task) {
            /** @var Task $taskEntity */
            $taskEntity = new Task();

            $taskEntity->setTitle($task['Title'])
                ->setContent($task['Content'])
                ->setCreatedAt(new DateTime());

            if (isset($task['Reference']['Author'])) {
                /** @var User $author */
                $author = $this->getReference($task['Reference']['Author']);
                $taskEntity->setUser($author);
            }

            $manager->persist($taskEntity);
        }
    }
}
