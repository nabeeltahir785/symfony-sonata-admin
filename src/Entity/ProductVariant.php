<?php

namespace App\Entity;

use App\Repository\ProductVariantRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductVariantRepository::class)]
class ProductVariant
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private $id;

    #[ORM\Column(type: "string", length: 255)]
    private $name;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private $sku;

    #[ORM\Column(type: "decimal", precision: 10, scale: 2, nullable: true)]
    private $price;

    #[ORM\Column(type: "integer", nullable: true)]
    private $stock;

    #[ORM\ManyToOne(targetEntity: "App\Entity\Product", inversedBy: "variants")]
    #[ORM\JoinColumn(nullable: false)]
    private $product;

    #[ORM\Column(type: "json", nullable: true)]
    private $attributes = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getSku(): ?string
    {
        return $this->sku;
    }

    public function setSku(?string $sku): self
    {
        $this->sku = $sku;

        return $this;
    }

    public function getPrice()
    {
        // If variant price is null, return the product price
        if ($this->price === null && $this->product) {
            return $this->product->getPrice();
        }

        return $this->price;
    }

    public function setPrice($price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getStock(): ?int
    {
        return $this->stock;
    }

    public function setStock(?int $stock): self
    {
        $this->stock = $stock;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function getAttributes(): ?array
    {
        return $this->attributes;
    }

    public function setAttributes(?array $attributes): self
    {
        $this->attributes = $attributes;

        return $this;
    }

    public function isInStock(): bool
    {
        return $this->stock === null || $this->stock > 0;
    }
}