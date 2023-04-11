<?php

namespace App\Entity;

use App\Repository\PromocodeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PromocodeRepository::class)
 */
class Promocode
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $percent;

    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $promoCodeTitle;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $promoCodeName;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isActive;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $endDate;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $useNumber;

    /**
     * @ORM\OneToMany(targetEntity=Category::class, mappedBy="promocode")
     */
    private $allowedCategories;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $sellerId;

    public function __construct()
    {
        $this->allowedCategories = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPercent(): ?float
    {
        return $this->percent;
    }

    public function setPercent(?float $percent): self
    {
        $this->percent = $percent;

        return $this;
    }

    public function getPromoCodeTitle(): ?string
    {
        return $this->promoCodeTitle;
    }

    public function setPromoCodeTitle(?string $promoCodeTitle): self
    {
        $this->promoCodeTitle = $promoCodeTitle;

        return $this;
    }

    public function getPromoCodeName(): ?string
    {
        return $this->promoCodeName;
    }

    public function setPromoCodeName(?string $promoCodeName): self
    {
        $this->promoCodeName = $promoCodeName;

        return $this;
    }

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(?bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(?\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getUseNumber(): ?int
    {
        return $this->useNumber;
    }

    public function setUseNumber(?int $useNumber): self
    {
        $this->useNumber = $useNumber;

        return $this;
    }

    /**
     * @return Collection<int, Category>
     */
    public function getAllowedCategories(): Collection
    {
        return $this->allowedCategories;
    }

    public function addAllowedCategory(Category $allowedCategory): self
    {
        if (!$this->allowedCategories->contains($allowedCategory)) {
            $this->allowedCategories[] = $allowedCategory;
            $allowedCategory->setPromocode($this);
        }

        return $this;
    }

    public function removeAllowedCategory(Category $allowedCategory): self
    {
        if ($this->allowedCategories->removeElement($allowedCategory)) {
            // set the owning side to null (unless already changed)
            if ($allowedCategory->getPromocode() === $this) {
                $allowedCategory->setPromocode(null);
            }
        }

        return $this;
    }

    public function getSellerId(): ?int
    {
        return $this->sellerId;
    }

    public function setSellerId(?int $sellerId): self
    {
        $this->sellerId = $sellerId;

        return $this;
    }

    
}
