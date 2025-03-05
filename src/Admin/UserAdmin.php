<?php

namespace App\Admin;

use App\Entity\User;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;

class UserAdmin extends AbstractAdmin
{
    private $passwordEncoder;

    public function __construct(
        $code,
        $class,
        $baseControllerName,
        PasswordHasherFactoryInterface $passwordEncoder
    ) {
        parent::__construct($code, $class, $baseControllerName);
        $this->passwordEncoder = $passwordEncoder;
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $passwordOptions = [
            'required' => (!$this->getSubject() || is_null($this->getSubject()->getId()))
        ];

        $formMapper
            ->add('email')
            ->add('firstName')
            ->add('lastName')
            ->add('plainPassword', PasswordType::class, [
                'label' => 'Password',
                'required' => (!$this->getSubject() || is_null($this->getSubject()->getId())),
                'mapped' => false
            ])
            ->add('roles', ChoiceType::class, [
                'choices' => [
                    'User' => 'ROLE_USER',
                    'Admin' => 'ROLE_ADMIN',
                    'Super Admin' => 'ROLE_SUPER_ADMIN'
                ],
                'multiple' => true,
                'expanded' => true
            ]);
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('email')
            ->add('firstName')
            ->add('lastName');
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->addIdentifier('email')
            ->add('firstName')
            ->add('lastName')
            ->add('lastLogin')
            ->add('roles', 'choice', [
                'choices' => [
                    'ROLE_USER' => 'User',
                    'ROLE_ADMIN' => 'Admin',
                    'ROLE_SUPER_ADMIN' => 'Super Admin'
                ],
                'multiple' => true
            ])
            ->add('_action', null, [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'delete' => []
                ]
            ]);
    }

    protected function configureShowFields(ShowMapper $showMapper): void
    {
        $showMapper
            ->add('email')
            ->add('firstName')
            ->add('lastName')
            ->add('lastLogin')
            ->add('roles');
    }

    /**
     * {@inheritdoc}
     */
    public function prePersist($user): void
    {
        $this->updatePassword($user);
    }

    /**
     * {@inheritdoc}
     */
    public function preUpdate($user): void
    {
        $this->updatePassword($user);
    }

    /**
     * Update password if specified
     */
    private function updatePassword($user)
    {
        if ($plainPassword = $this->getForm()->get('plainPassword')->getData()) {
            $encodedPassword = $this->passwordEncoder->encodePassword($user, $plainPassword);
            $user->setPassword($encodedPassword);
        }
    }
}