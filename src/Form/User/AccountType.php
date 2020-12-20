<?php
/**
 * ToDoAndCo Project
 * Copyright (c) 2020 BigBoss 2020.  BigBoss Oualid
 * mailto: <bigboss@it-bigboss.de>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 * Inc., Munich, Germany.
 */

namespace App\Form\User;

use App\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AccountType extends AbstractUserType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class)
            ->add('email', EmailType::class)
        ;
        if ($this->isRoleEditable($options['logged_user'], $builder->getData())) {
            $builder->add(
                'roles',
                ChoiceType::class,
                [
                    'choices' => parent::getRolesOptions(),
                ]
            );
            parent::transformRolesType($builder);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'translation_domain' => 'forms',
            'logged_user' => User::class,
        ]);
    }

    /**
     * Add select input if user has admin role.
     *
     * @param User $loggedUser
     * @param User $editedUser
     *
     * @return bool
     */
    private function isRoleEditable(User $loggedUser, User $editedUser): bool
    {
        return in_array('ROLE_ADMIN', $loggedUser->getRoles()) && $loggedUser !== $editedUser;
    }
}
