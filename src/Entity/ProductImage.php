<?php

namespace App\Entity;

use App\Repository\ProductImageRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductImageRepository::class)]
class ProductImage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private $id;

    #[ORM\Column(type: "string", length: 255)]
    private $path;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private $alt;

    #[ORM\Column(type: "boolean")]
    private $isThumbnail = false;

    #[ORM\Column(type: "integer")]
    private $position = 0;

    #[ORM\ManyToOne(targetEntity: "App\Entity\Product", inversedBy: "images")]
    #[ORM\JoinColumn(nullable: false)]
    private $product;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function getAlt(): ?string
    {
        return $this->alt;
    }

    public function setAlt(?string $alt): self
    {
        $this->alt = $alt;

        return $this;
    }

    public function isThumbnail(): ?bool
    {
        return $this->isThumbnail;
    }

    public function setIsThumbnail(bool $isThumbnail): self
    {
        $this->isThumbnail = $isThumbnail;

        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): self
    {
        $this->position = $position;

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
}