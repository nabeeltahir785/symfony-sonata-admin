<?php

namespace App\Controller\Product;

class ProductAdminController extends CRUDController
{
    public function cloneAction()
    {
        $object = $this->admin->getSubject();

        if (!$object) {
            throw new NotFoundHttpException(sprintf('Unable to find %s object', $this->admin->getClassnameLabel()));
        }

        // Create clone
        $clonedObject = clone $object;
        $clonedObject->setName($object->getName() . ' (Clone)');
        $clonedObject->setCreatedAt(new \DateTime());
        $clonedObject->setUpdatedAt(null);

        $this->admin->create($clonedObject);

        $this->addFlash('success', 'Product cloned successfully');

        return new RedirectResponse($this->admin->generateUrl('list'));
    }

    public function generateVariantsAction()
    {
        $object = $this->admin->getSubject();

        if (!$object) {
            throw new NotFoundHttpException(sprintf('Unable to find %s object', $this->admin->getClassnameLabel()));
        }

        // Example: Generate variants based on size
        $sizes = ['S', 'M', 'L', 'XL'];

        foreach ($sizes as $size) {
            $variant = new ProductVariant();
            $variant->setProduct($object);
            $variant->setName($object->getName() . ' - ' . $size);
            $variant->setSku(strtoupper(substr(str_replace(' ', '', $object->getName()), 0, 3)) . '-' . $size);
            $variant->setPrice($object->getPrice());

            $object->addVariant($variant);
        }

        $this->admin->update($object);

        $this->addFlash('success', 'Product variants generated successfully');

        return new RedirectResponse($this->admin->generateUrl('edit', ['id' => $object->getId()]));
    }

    public function batchActionPublish()
    {
        $query = $this->admin->getDatagrid()->getQuery();

        $this->admin->checkAccess('edit');

        $selectedModels = $query->execute();
        $modelManager = $this->admin->getModelManager();

        foreach ($selectedModels as $selectedModel) {
            $selectedModel->setStatus(1); // Published
        }

        $modelManager->update($selectedModel);

        $this->addFlash('success', 'Selected products have been published.');

        return new RedirectResponse($this->admin->generateUrl('list'));
    }

    public function batchActionArchive()
    {
        $query = $this->admin->getDatagrid()->getQuery();

        $this->admin->checkAccess('edit');

        $selectedModels = $query->execute();
        $modelManager = $this->admin->getModelManager();

        foreach ($selectedModels as $selectedModel) {
            $selectedModel->setStatus(2); // Archived
        }

        $modelManager->update($selectedModel);

        $this->addFlash('success', 'Selected products have been archived.');

        return new RedirectResponse($this->admin->generateUrl('list'));
    }
}