<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private $id;

    #[ORM\Column(type: "string", length: 255)]
    private $name;

    #[ORM\Column(type: "text", nullable: true)]
    private $description;

    #[ORM\Column(type: "decimal", precision: 10, scale: 2)]
    private $price;

    #[ORM\Column(type: "integer")]
    private $status = 0; // 0 = Draft, 1 = Published, 2 = Archived

    #[ORM\ManyToOne(targetEntity: "App\Entity\Category", inversedBy: "products")]
    private $category;

    #[ORM\OneToMany(targetEntity: "App\Entity\ProductImage", mappedBy: "product", cascade: ["persist", "remove"], orphanRemoval: true)]
    private $images;

    #[ORM\OneToMany(targetEntity: "App\Entity\ProductVariant", mappedBy: "product", cascade: ["persist", "remove"], orphanRemoval: true)]
    private $variants;

    #[ORM\ManyToMany(targetEntity: "App\Entity\Tag", inversedBy: "products")]
    private $tags;

    #[ORM\Column(type: "datetime")]
    private $createdAt;

    #[ORM\Column(type: "datetime", nullable: true)]
    private $updatedAt;

    public function __construct()
    {
        $this->images = new ArrayCollection();
        $this->variants = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->createdAt = new \DateTime();
    }

    #[ORM\PreUpdate]
    public function setUpdatedAtValue(): void
    {
        $this->updatedAt = new \DateTime();
    }

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function setPrice($price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getCategory()
    {
        return $this->category;
    }

    public function setCategory($category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage($image): self
    {
        if (!$this->images->contains($image)) {
            $this->images[] = $image;
            $image->setProduct($this);
        }

        return $this;
    }

    public function removeImage($image): self
    {
        if ($this->images->contains($image)) {
            $this->images->removeElement($image);
            if ($image->getProduct() === $this) {
                $image->setProduct(null);
            }
        }

        return $this;
    }

    public function getVariants(): Collection
    {
        return $this->variants;
    }

    public function addVariant($variant): self
    {
        if (!$this->variants->contains($variant)) {
            $this->variants[] = $variant;
            $variant->setProduct($this);
        }

        return $this;
    }

    public function removeVariant($variant): self
    {
        if ($this->variants->contains($variant)) {
            $this->variants->removeElement($variant);
            if ($variant->getProduct() === $this) {
                $variant->setProduct(null);
            }
        }

        return $this;
    }

    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag($tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags[] = $tag;
        }

        return $this;
    }

    public function removeTag($tag): self
    {
        if ($this->tags->contains($tag)) {
            $this->tags->removeElement($tag);
        }

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getThumbnailImage()
    {
        foreach ($this->images as $image) {
            if ($image->isThumbnail()) {
                return $image;
            }
        }

        return $this->images->isEmpty() ? null : $this->images->first();
    }
}