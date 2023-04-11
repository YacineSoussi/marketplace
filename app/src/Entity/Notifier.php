<?php

namespace App\Entity;

use App\Repository\NotifierRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=NotifierRepository::class)
 */
class Notifier
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isViewed;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $content;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $sellerId;

  

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIsViewed(): ?bool
    {
        return $this->isViewed;
    }

    public function setIsViewed(?bool $isViewed): self
    {
        $this->isViewed = $isViewed;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

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
