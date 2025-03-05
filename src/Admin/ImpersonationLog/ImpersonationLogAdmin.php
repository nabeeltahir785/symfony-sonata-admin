<?php

namespace App\Admin\ImpersonationLog;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\AdminBundle\Show\ShowMapper;

class ImpersonationLogAdmin extends AbstractAdmin
{
    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        // Remove create, edit and delete actions
        $collection->remove('create');
        $collection->remove('edit');
        $collection->remove('delete');
    }
    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('impersonator.email', null, [
                'label' => 'Impersonator'
            ])
            ->add('impersonated.email', null, [
                'label' => 'Impersonated User'
            ])
            ->add('action', null, [
                'label' => 'Action'
            ])
            ->add('ipAddress', null, [
                'label' => 'IP Address'
            ])
            ->add('createdAt', null, [
                'label' => 'Date'
            ]);
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->add('createdAt', 'datetime', [
                'format' => 'd/m/Y H:i:s',
                'label' => 'Date/Time'
            ])
            ->add('impersonator.fullName', null, [
                'label' => 'Impersonator'
            ])
            ->add('impersonated.fullName', null, [
                'label' => 'Impersonated User'
            ])
            ->add('action', 'choice', [
                'choices' => [
                    'enter' => 'Started Impersonation',
                    'exit' => 'Ended Impersonation'
                ],
                'label' => 'Action'
            ])
            ->add('ipAddress', null, [
                'label' => 'IP Address'
            ])
            ->add('userAgent', null, [
                'template' => 'admin/impersonation_log/list_user_agent.html.twig',
                'label' => 'User Agent'
            ])
            ->add('_action', null, [
                'actions' => [
                    'show' => [],
                ]
            ]);
    }

    protected function configureShowFields(ShowMapper $showMapper): void
    {
        $showMapper
            ->add('createdAt', 'datetime', [
                'format' => 'd/m/Y H:i:s',
                'label' => 'Date/Time'
            ])
            ->add('impersonator.fullName', null, [
                'label' => 'Impersonator'
            ])
            ->add('impersonated.fullName', null, [
                'label' => 'Impersonated User'
            ])
            ->add('action', 'choice', [
                'choices' => [
                    'enter' => 'Started Impersonation',
                    'exit' => 'Ended Impersonation'
                ],
                'label' => 'Action'
            ])
            ->add('ipAddress', null, [
                'label' => 'IP Address'
            ])
            ->add('userAgent', null, [
                'label' => 'User Agent'
            ]);
    }
}