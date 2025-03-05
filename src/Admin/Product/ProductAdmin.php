<?php

namespace App\Admin\Product;

use App\Entity\Product;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\AdminBundle\Admin\TemplateRegistryInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Sonata\AdminBundle\Form\Type\ModelType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class ProductAdmin extends AbstractAdmin
{
    protected $datagridValues = [
        '_page' => 1,
        '_sort_order' => 'DESC',
        '_sort_by' => 'createdAt',
    ];

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->tab('Basic Information')
            ->with('General', ['class' => 'col-md-8'])
            ->add('name', TextType::class, [
                'attr' => ['placeholder' => 'Enter product name']
            ])
            ->add('description', TextareaType::class, [
                'attr' => ['rows' => 5]
            ])
            ->add('status', null, [
                'field_type' => 'choice',
                'field_options' => [
                    'choices' => [
                        'Draft' => 0,
                        'Published' => 1,
                        'Archived' => 2,
                    ],
                ]
            ])
            ->end()
            ->with('Pricing', ['class' => 'col-md-4'])
            ->add('price', MoneyType::class, [
                'currency' => 'USD'
            ])
            ->add('category', ModelType::class, [
                'required' => false
            ])
            ->end()
            ->end()
            ->tab('Media')
            ->with('Images')
            ->add('images', CollectionType::class, [
                'entry_type' => ProductImageType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ])
            ->end()
            ->end()
            ->tab('Variants')
            ->with('Product Variants')
            ->add('variants', CollectionType::class, [
                'entry_type' => ProductVariantType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ])
            ->end()
            ->end()
            ->tab('Metadata')
            ->with('Timestamps')
            ->add('createdAt', DateTimeType::class, [
                'widget' => 'single_text',
                'disabled' => true
            ])
            ->add('updatedAt', DateTimeType::class, [
                'widget' => 'single_text',
                'required' => false,
                'disabled' => true
            ])
            ->end()
            ->end();
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('name')
            ->add('price')
            ->add('status', null, [
                'field_type' => 'Symfony\Component\Form\Extension\Core\Type\ChoiceType',
                'field_options' => [
                    'choices' => [
                        'Draft' => 0,
                        'Published' => 1,
                        'Archived' => 2,
                    ],
                ]
            ])
            ->add('category');
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->addIdentifier('name')
            ->add('thumbnailImage', null, [
                'template' => 'admin/product/list_thumbnail.html.twig',
                'label' => 'Image'
            ])
            ->add('price', 'currency', [
                'currency' => 'USD'
            ])
            ->add('status', 'choice', [
                'template' => '@SonataAdmin/CRUD/list_choice.html.twig',
                'editable' => false,
                'choices' => [
                    0 => 'Draft',
                    1 => 'Published',
                    2 => 'Archived',
                ]
            ])
            ->add('category.name')
            ->add('createdAt', null, [
                'format' => 'd/m/Y H:i'
            ])
            ->add('_action', null, [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'delete' => [],
                    'clone' => [
                        'template' => 'admin/product/list__action_clone.html.twig',
                    ],
                    'generate_variants' => [
                        'template' => 'admin/product/list__action_generate_variants.html.twig',
                    ],
                ]
            ]);
    }

    protected function configureShowFields(ShowMapper $showMapper): void
    {
        $showMapper
            ->tab('Basic Information')
            ->with('General', ['class' => 'col-md-8'])
            ->add('name')
            ->add('description')
            ->add('status', 'choice', [
                'template' => '@SonataAdmin/CRUD/show_choice.html.twig',
                'choices' => [
                    0 => 'Draft',
                    1 => 'Published',
                    2 => 'Archived'
                ]
            ])
            ->end()
            ->with('Pricing', ['class' => 'col-md-4'])
            ->add('price', 'currency', [
                'currency' => 'USD'
            ])
            ->add('category.name')
            ->end()
            ->end()
            ->tab('Media')
            ->with('Gallery')
            ->add('images', null, [
                'template' => 'admin/product/show_images.html.twig'
            ])
            ->end()
            ->end()
            ->tab('Variants')
            ->with('Product Variants')
            ->add('variants', null, [
                'template' => 'admin/product/show_variants.html.twig'
            ])
            ->end()
            ->end()
            ->tab('Metadata')
            ->with('Timestamps')
            ->add('createdAt')
            ->add('updatedAt')
            ->end()
            ->end();
    }

    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection
            ->add('clone', $this->getRouterIdParameter().'/clone')
            ->add('generateVariants', $this->getRouterIdParameter().'/generate-variants');
    }

    protected function configureTemplates(TemplateRegistryInterface $templateRegistry): void
    {
        parent::configureTemplates($templateRegistry);

        $templateRegistry->add('list', 'admin/product/list.html.twig');
        $templateRegistry->add('edit', 'admin/product/edit.html.twig');
    }

    protected function configureBatchActions(array $actions): array
    {
        $actions['publish'] = [
            'label' => 'Publish',
            'ask_confirmation' => true
        ];

        $actions['archive'] = [
            'label' => 'Archive',
            'ask_confirmation' => true
        ];

        return $actions;
    }

    public function getExportFormats(): array
    {
        return ['json', 'xml', 'csv', 'xls'];
    }

    protected function configureExportFields(): array
    {
        return [
            'id',
            'name',
            'description',
            'price',
            'category.name',
            'createdAt',
        ];
    }

    public function configureActionButtons(array $buttonList, string $action, ?object $object = null): array
    {
        $list = parent::configureActionButtons($buttonList, $action, $object);

        if ($action === 'list' && $this->isGranted('ROLE_EXPORT')) {
            $list['export'] = [
                'template' => '@SonataAdmin/Button/export_button.html.twig'
            ];
        }

        return $list;
    }

    public function configureTabs(): array
    {
        $tabs = [];
        $tabs['Product'] = [
            'label' => 'Product Details',
            'icon' => '<i class="fa fa-info-circle"></i>',
            'template' => 'admin/product/tab_info.html.twig',
        ];
        $tabs['Variants'] = [
            'label' => 'Variants',
            'icon' => '<i class="fa fa-list"></i>',
            'template' => 'admin/product/tab_variants.html.twig',
        ];
        $tabs['Reviews'] = [
            'label' => 'Customer Reviews',
            'icon' => '<i class="fa fa-comments"></i>',
            'template' => 'admin/product/tab_reviews.html.twig',
        ];

        return $tabs;
    }

    protected function configureQuery(ProxyQueryInterface $query): ProxyQueryInterface
    {
        $query
            ->leftJoin($query->getRootAlias() . '.category', 'c')
            ->leftJoin($query->getRootAlias() . '.tags', 't')
            ->addSelect('c')
            ->addSelect('t');

        return $query;
    }

    public function prePersist($product): void
    {
        $this->preUpdate($product);
    }

    public function preUpdate($product): void
    {
        $product->setUpdatedAt(new \DateTime());
    }

}