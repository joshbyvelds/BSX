<?php

namespace App\Entity;

use App\Repository\StockRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    /**
     * @ORM\OneToMany(targetEntity=Dividend::class, mappedBy="stock")
     */
    private $dividends;

    /**
     * @ORM\Column(type="float")
     */
    private $opening_price;

    /**
     * @ORM\Column(type="float")
     */
    private $closing_price;

    /**
     * @ORM\Column(type="integer")
     */
    private $buys;

    public function __construct()
    {
        $this->dividends = new ArrayCollection();
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

    /**
     * @return Collection|Dividend[]
     */
    public function getDividends(): Collection
    {
        return $this->dividends;
    }

    public function addDividend(Dividend $dividend): self
    {
        if (!$this->dividends->contains($dividend)) {
            $this->dividends[] = $dividend;
            $dividend->setStock($this);
        }

        return $this;
    }

    public function removeDividend(Dividend $dividend): self
    {
        if ($this->dividends->removeElement($dividend)) {
            // set the owning side to null (unless already changed)
            if ($dividend->getStock() === $this) {
                $dividend->setStock(null);
            }
        }

        return $this;
    }

    public function getOpeningPrice(): ?float
    {
        return $this->opening_price;
    }

    public function setOpeningPrice(float $opening_price): self
    {
        $this->opening_price = $opening_price;

        return $this;
    }

    public function getClosingPrice(): ?float
    {
        return $this->closing_price;
    }

    public function setClosingPrice(float $closing_price): self
    {
        $this->closing_price = $closing_price;

        return $this;
    }

    public function getBuys(): ?int
    {
        return $this->buys;
    }

    public function setBuys(int $buys): self
    {
        $this->buys = $buys;

        return $this;
    }
}
