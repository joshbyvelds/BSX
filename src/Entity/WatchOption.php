<?php

namespace App\Entity;

use App\Repository\WatchOptionRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=WatchOptionRepository::class)
 */
class WatchOption
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
     * @ORM\Column(type="string", length=255)
     */
    private $url;

    /**
     * @ORM\Column(type="integer")
     */
    private $type;

    /**
     * @ORM\Column(type="float")
     */
    private $current;

    /**
     * @ORM\Column(type="float")
     */
    private $deadstop;

        /**
     * @ORM\Column(type="float")
     */
    private $buyin;

    /**
     * @ORM\Column(type="float")
     */
    private $profit;

    /**
     * @ORM\Column(type="float")
     */
    private $strike;

    /**
     * @ORM\Column(type="float")
     */
    private $target;

    /**
     * @ORM\Column(type="float")
     */
    private $golden;

    /**
     * @ORM\Column(type="integer")
     */
    private $status;

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

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

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

    public function getCurrent(): ?float
    {
        return $this->current;
    }

    public function setCurrent(float $current): self
    {
        $this->current = $current;

        return $this;
    }

    public function getDeadstop(): ?float
    {
        return $this->deadstop;
    }

    public function setDeadstop(float $deadstop): self
    {
        $this->deadstop = $deadstop;

        return $this;
    }

    public function getBuyin(): ?float
    {
        return $this->buyin;
    }

    public function setBuyin(float $buyin): self
    {
        $this->buyin = $buyin;

        return $this;
    }

    public function getProfit(): ?float
    {
        return $this->profit;
    }

    public function setProfit(float $profit): self
    {
        $this->profit = $profit;

        return $this;
    }

    public function getStrike(): ?float
    {
        return $this->strike;
    }

    public function setStrike(float $strike): self
    {
        $this->strike = $strike;

        return $this;
    }

    public function getTarget(): ?float
    {
        return $this->target;
    }

    public function setTarget(float $target): self
    {
        $this->target = $target;

        return $this;
    }

    public function getGolden(): ?float
    {
        return $this->golden;
    }

    public function setGolden(float $golden): self
    {
        $this->golden = $golden;

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
}
