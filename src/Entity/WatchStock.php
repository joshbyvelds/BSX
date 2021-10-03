<?php

namespace App\Entity;

use App\Repository\WatchStockRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=WatchStockRepository::class)
 */
class WatchStock
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
    private $profitpoint;

    /**
     * @ORM\Column(type="float")
     */
    private $target;

    /**
     * @ORM\Column(type="float")
     */
    private $golden;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $url;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $ticker;

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

    public function getProfitpoint(): ?float
    {
        return $this->profitpoint;
    }

    public function setProfitpoint(float $profitpoint): self
    {
        $this->profitpoint = $profitpoint;

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

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

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
