<?php

namespace App\Entity;

use App\Repository\SellerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SellerRepository::class)
 */
class Seller
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $address;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $contactMail;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $label;

    /**
     * @ORM\OneToMany(targetEntity=Product::class, mappedBy="seller", cascade={"persist", "remove"})
     */
    private $products;

    /**
     * @ORM\OneToOne(targetEntity=User::class, mappedBy="seller", cascade={"persist"})
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity=OrderDetails::class, mappedBy="seller", cascade={"persist", "remove"})
     */
    private $orderDetails;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $legalBrand;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $type;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $offerDescription;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $phoneContact;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $firstnameCEO;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $lastnameCEO;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isActif;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isRequested;

    
    public function __construct()
    {
        $this->products = new ArrayCollection();
        $this->orderDetails = new ArrayCollection();
    }

    public function __toString()
    {
       return $this->getLegalBrand();
    }
    
    

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getContactMail(): ?string
    {
        return $this->contactMail;
    }

    public function setContactMail(?string $contactMail): self
    {
        $this->contactMail = $contactMail;

        return $this;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @return Collection<int, Product>
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): self
    {
        if (!$this->products->contains($product)) {
            $this->products[] = $product;
            $product->setSeller($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): self
    {
        if ($this->products->removeElement($product)) {
            // set the owning side to null (unless already changed)
            if ($product->getSeller() === $this) {
                $product->setSeller(null);
            }
        }

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        // unset the owning side of the relation if necessary
        if ($user === null && $this->user !== null) {
            $this->user->setSeller(null);
        }

        // set the owning side of the relation if necessary
        if ($user !== null && $user->getSeller() !== $this) {
            $user->setSeller($this);
        }

        $this->user = $user;

        return $this;
    }

    public function getLegalBrand(): ?string
    {
        return $this->legalBrand;
    }

    public function setLegalBrand(?string $legalBrand): self
    {
        $this->legalBrand = $legalBrand;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getOfferDescription(): ?string
    {
        return $this->offerDescription;
    }

    public function setOfferDescription(?string $offerDescription): self
    {
        $this->offerDescription = $offerDescription;

        return $this;
    }

    public function getPhoneContact(): ?string
    {
        return $this->phoneContact;
    }

    public function setPhoneContact(?string $phoneContact): self
    {
        $this->phoneContact = $phoneContact;

        return $this;
    }

    public function getFirstnameCEO(): ?string
    {
        return $this->firstnameCEO;
    }

    public function setFirstnameCEO(?string $firstnameCEO): self
    {
        $this->firstnameCEO = $firstnameCEO;

        return $this;
    }

    public function getLastnameCEO(): ?string
    {
        return $this->lastnameCEO;
    }

    public function setLastnameCEO(?string $lastnameCEO): self
    {
        $this->lastnameCEO = $lastnameCEO;

        return $this;
    }

    public function getIsActif(): ?bool
    {
        return $this->isActif;
    }

    public function setIsActif(?bool $isActif): self
    {
        $this->isActif = $isActif;

        return $this;
    }

    public function getIsRequested(): ?bool
    {
        return $this->isRequested;
    }

    public function setIsRequested(?bool $isRequested): self
    {
        $this->isRequested = $isRequested;

        return $this;
    }
    
}
