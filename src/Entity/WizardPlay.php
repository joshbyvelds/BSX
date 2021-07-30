<?php

namespace App\Entity;

use App\Repository\WizardPlayRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=WizardPlayRepository::class)
 */
class WizardPlay
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $ticker;

    /**
     * @ORM\Column(type="float")
     */
    private $currentPrice;

    /**
     * @ORM\Column(type="float")
     */
    private $entryPrice;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $targets;

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

    public function getTicker(): ?string
    {
        return $this->ticker;
    }

    public function setTicker(string $ticker): self
    {
        $this->ticker = $ticker;

        return $this;
    }

    public function getCurrentPrice(): ?float
    {
        return $this->currentPrice;
    }

    public function setCurrentPrice(float $currentPrice): self
    {
        $this->currentPrice = $currentPrice;

        return $this;
    }

    public function getEntryPrice(): ?float
    {
        return $this->entryPrice;
    }

    public function setEntryPrice(float $entryPrice): self
    {
        $this->entryPrice = $entryPrice;

        return $this;
    }

    public function getTargets(): ?string
    {
        return $this->targets;
    }

    public function setTargets(string $targets): self
    {
        $this->targets = $targets;

        return $this;
    }
}
