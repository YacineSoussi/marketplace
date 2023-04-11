<?php

namespace App\Entity;

use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\ProductRepository;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;


/**
 * @ORM\Entity(repositoryClass=ProductRepository::class)
 */
class Product
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
    private $title;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $subtitle;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isBest;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="products",cascade={"persist"})
     */
    private $category;

    /**
     * @ORM\OneToMany(targetEntity=Specification::class, mappedBy="product",cascade={"persist"})
     */
    private $specifications;

    /**
     * @Gedmo\Slug(fields={"title"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $slug;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $price;

    /**
     * @ORM\Column(type="string", length=155, nullable="true")
     */
    private $coverPhoto;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="product", orphanRemoval=true)
     */
    private $comments;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $weight;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $promo;

    /**
     * @ORM\ManyToOne(targetEntity=Seller::class, inversedBy="products")
     */
    private $seller;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $sold;

    /**
     * @ORM\OneToMany(targetEntity=ProductLike::class, mappedBy="product")
     */
    private $likes;

    public function __construct()
    {
        $this->specifications = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->likes = new ArrayCollection();
    }

    public function getAvgRatings() {
        // Calculer la somme des notations
        $sum = array_reduce($this->comments->toArray(), function($total, $comment) {
            return $total + $comment->getRating();
        }, 0);

        // Faire la division pour avoir la moyenne
       if(count($this->comments) > 0) return $moyenne = $sum / count($this->comments);

       return 0;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getSubtitle(): ?string
    {
        return $this->subtitle;
    }

    public function setSubtitle(?string $subtitle): self
    {
        $this->subtitle = $subtitle;

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

    public function getIsBest(): ?bool
    {
        return $this->isBest;
    }

    public function setIsBest(?bool $isBest): self
    {
        $this->isBest = $isBest;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection|Specification[]
     */
    public function getSpecifications(): Collection
    {
        return $this->specifications;
    }

    public function addSpecification(Specification $specification): self
    {
        if (!$this->specifications->contains($specification)) {
            $this->specifications[] = $specification;
            $specification->setProduct($this);
        }

        return $this;
    }

    public function removeSpecification(Specification $specification): self
    {
        if ($this->specifications->removeElement($specification)) {
            // set the owning side to null (unless already changed)
            if ($specification->getProduct() === $this) {
                $specification->setProduct(null);
            }
        }

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(?float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getCoverPhoto(): ?string
    {
        return $this->coverPhoto;
    }

    public function setCoverPhoto(?string $coverPhoto): self
    {
        $this->coverPhoto = $coverPhoto;

        return $this;
    }


    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setProduct($this);
        }

        return $this;
    }


    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getProduct() === $this) {
                $comment->setProduct(null);
            }
        }

        return $this;
    }

    public function getDetails() {
        
        return [
            'title' => $this->getTitle(),
            'coverPhoto' => $this->getCoverPhoto(),
            'category' => $this->getCategory(),
            'price' => $this->getPrice(),
            'weight' => $this->getWeight(),
            'sellerId' => $this->seller->getId(),
            
        ];
    }

    public function getWeight(): ?float
    {
        return $this->weight;
    }

    public function setWeight(?float $weight): self
    {
        $this->weight = $weight;

        return $this;
    }

    public function getPromo(): ?bool
    {
        return $this->promo;
    }

    public function setPromo(?bool $promo): self
    {
        $this->promo = $promo;

        return $this;
    }

    public function getSeller(): ?Seller
    {
        return $this->seller;
    }

    public function setSeller(?Seller $seller): self
    {
        $this->seller = $seller;

        return $this;
    }

    public function getsold(): ?int
    {
        return $this->sold;
    }

    public function setsold(?int $sold): self
    {
        $this->sold = $sold;

        return $this;
    }

    /**
     * @return Collection<int, ProductLike>
     */
    public function getLikes(): Collection
    {
        return $this->likes;
    }

    public function addLike(ProductLike $like): self
    {
        if (!$this->likes->contains($like)) {
            $this->likes[] = $like;
            $like->setProduct($this);
        }

        return $this;
    }

    public function removeLike(ProductLike $like): self
    {
        if ($this->likes->removeElement($like)) {
            // set the owning side to null (unless already changed)
            if ($like->getProduct() === $this) {
                $like->setProduct(null);
            }
        }

        return $this;
    }

    /**
     * Permet de savoir si ce produit est liké par l'utilisateur courant
     * @return Bool
     */
    public function isLikedByUser(User $user): bool
    {
        foreach($this->likes as $like){
            if($like->getUser() === $user) return true;
        }

        return false;
    }
}
