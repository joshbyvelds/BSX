<?php

namespace App\Entity;

use App\Repository\StockRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=StockRepository::class)
 */
class Stock
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
     * @ORM\Column(type="date")
     */
    private $first_bought;

    /**
     * @ORM\Column(type="date")
     */
    private $last_bought;

    /**
     * @ORM\Column(type="integer")
     */
    private $shares;

    /**
     * @ORM\Column(type="float")
     */
    private $average_price;

    /**
     * @ORM\Column(type="float")
     */
    private $dividends;

    /**
     * @ORM\Column(type="float")
     */
    private $sold;

    /**
     * @ORM\Column(type="integer")
     */
    private $type;

    /**
     * @ORM\Column(type="integer")
     */
    private $currency;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $last_sold;

    /**
     * @ORM\Column(type="integer")
     */
    private $buy_currency;

    /**
     * @ORM\Column(type="float")
     */
    private $profit_can;

    /**
     * @ORM\Column(type="float")
     */
    private $profit_usd;

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

    public function getShares(): ?int
    {
        return $this->shares;
    }

    public function addShares(int $shares): self
    {
        $this->shares += $shares;
        return $this;
    }

    public function sellShares(int $shares): self
    {
        $this->shares -= $shares;
        return $this;
    }

    public function setShares(int $shares): self
    {
        $this->shares = $shares;

        return $this;
    }

    public function getAveragePrice(): ?float
    {
        return $this->average_price;
    }

    public function setAveragePrice(float $average_price): self
    {
        $this->average_price = $average_price;

        return $this;
    }

    public function getDividends(): ?float
    {
        return $this->dividends;
    }

    public function setDividends(float $dividends): self
    {
        $this->dividends = $dividends;

        return $this;
    }

    public function getSold(): ?float
    {
        return $this->sold;
    }

    public function setSold(float $sold): self
    {
        $this->sold = $sold;

        return $this;
    }

    public function addSold(float $sold): self
    {
        $this->sold += $sold;

        return $this;
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(int $type): self
    {
        $this->type = $type;

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

    public function getLastSold(): ?\DateTimeInterface
    {
        return $this->last_sold;
    }

    public function setLastSold(?\DateTimeInterface $last_sold): self
    {
        $this->last_sold = $last_sold;

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

    public function getProfitCan(): ?float
    {
        return $this->profit_can;
    }

    public function setProfitCan(float $profit_can): self
    {
        $this->profit_can = $profit_can;

        return $this;
    }

    public function addProfitCan(float $profit_can): self
    {
        $this->profit_can += $profit_can;

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

    public function addProfitUsd(float $profit_usd): self
    {
        $this->profit_usd += $profit_usd;

        return $this;
    }
}
