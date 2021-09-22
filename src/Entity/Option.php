<?php

namespace App\Entity;

use App\Repository\OptionRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OptionRepository::class)
 * @ORM\Table(name="`option`")
 */
class Option
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
     * @ORM\Column(type="string", length=12)
     */
    private $ticker;

    /**
     * @ORM\Column(type="date")
     */
    private $first_bought;

    /**
     * @ORM\Column(type="date")
     */
    private $last_bought;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $last_sold;

    /**
     * @ORM\Column(type="integer")
     */
    private $currency;

    /**
     * @ORM\Column(type="integer")
     */
    private $contracts;

    /**
     * @ORM\Column(type="float")
     */
    private $current_price;

    /**
     * @ORM\Column(type="float")
     */
    private $strike_price;

    /**
     * @ORM\Column(type="float")
     */
    private $average;

    /**
     * @ORM\Column(type="float")
     */
    private $cost;

    /**
     * @ORM\Column(type="date")
     */
    private $expires;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $profitCalcUrl;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $type;

    /**
     * @ORM\Column(type="float")
     */
    private $profit_can;

    /**
     * @ORM\Column(type="float")
     */
    private $profit_usd;

    /**
     * @ORM\Column(type="integer")
     */
    private $buy_currency;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $notes;

    /**
     * @ORM\Column(type="float")
     */
    private $stock_price;

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

    public function getFirstBought(): ?\DateTimeInterface
    {
        return $this->first_bought;
    }

    public function setFirstBought(\DateTimeInterface $first_bought): self
    {
        $this->first_bought = $first_bought;

        return $this;
    }

    public function getLastBought(): ?\DateTimeInterface
    {
        return $this->last_bought;
    }

    public function setLastBought(\DateTimeInterface $last_bought): self
    {
        $this->last_bought = $last_bought;

        return $this;
    }

    public function getLastSold(): ?\DateTimeInterface
    {
        return $this->last_sold;
    }

    public function setLastSold(\DateTimeInterface $last_sold): self
    {
        $this->last_sold = $last_sold;

        return $this;
    }

    public function getCurrency(): ?int
    {
        return $this->currency;
    }

    public function setCurrency(int $currency): self
    {
        $this->currency = $currency;

        return $this;
    }

    public function getContracts(): ?int
    {
        return $this->contracts;
    }

    public function setContracts(int $contracts): self
    {
        $this->contracts = $contracts;

        return $this;
    }

    public function getCurrentPrice(): ?float
    {
        return $this->current_price;
    }

    public function setCurrentPrice(float $current_price): self
    {
        $this->current_price = $current_price;

        return $this;
    }

    public function getStrikePrice(): ?float
    {
        return $this->strike_price;
    }

    public function setStrikePrice(float $strike_price): self
    {
        $this->strike_price = $strike_price;

        return $this;
    }

    public function getAverage(): ?float
    {
        return $this->average;
    }

    public function setAverage(float $average): self
    {
        $this->average = $average;

        return $this;
    }

    public function getCost(): ?float
    {
        return $this->cost;
    }

    public function setCost(float $cost): self
    {
        $this->cost = $cost;

        return $this;
    }

    public function getExpires(): ?\DateTimeInterface
    {
        return $this->expires;
    }

    public function setExpires(\DateTimeInterface $expires): self
    {
        $this->expires = $expires;

        return $this;
    }

    public function getProfitCalcUrl(): ?string
    {
        return $this->profitCalcUrl;
    }

    public function setProfitCalcUrl(string $profitCalcUrl): self
    {
        $this->profitCalcUrl = $profitCalcUrl;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getProfitCan(): ?float
    {
        return $this->profit_can;
    }

    public function setProfitCan(float $profit_can): self
    {
        $this->profit_can = $profit_can;

        return $this;
    }

    public function getProfitUsd(): ?float
    {
        return $this->profit_usd;
    }

    public function setProfitUsd(float $profit_usd): self
    {
        $this->profit_usd = $profit_usd;

        return $this;
    }

    public function getBuyCurrency(): ?int
    {
        return $this->buy_currency;
    }

    public function setBuyCurrency(int $buy_currency): self
    {
        $this->buy_currency = $buy_currency;

        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): self
    {
        $this->notes = $notes;

        return $this;
    }

    public function getStockPrice(): ?float
    {
        return $this->stock_price;
    }

    public function setStockPrice(float $stock_price): self
    {
        $this->stock_price = $stock_price;

        return $this;
    }
}
