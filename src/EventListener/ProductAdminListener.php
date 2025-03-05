<?php

namespace App\EventListener;

use App\Entity\Product;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ProductAdminListener
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function prePersist(PrePersistEventArgs $args)
    {
        $entity = $args->getObject();

        if (!$entity instanceof Product) {
            return;
        }

        // Perform additional actions before persisting a product
        // For example: generate a unique SKU, process images, etc.
    }

    public function postUpdate(PostUpdateEventArgs $args)
    {
        $entity = $args->getObject();

        if (!$entity instanceof Product) {
            return;
        }

        // Perform actions after a product is updated
        // For example: send notifications, update search index, etc.
    }
}