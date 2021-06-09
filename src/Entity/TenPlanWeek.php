<?php

namespace App\Entity;

use App\Repository\TenPlanWeekRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TenPlanWeekRepository::class)
 */
class TenPlanWeek
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $week;

    /**
     * @ORM\Column(type="date")
     */
    private $enddate;

    /**
     * @ORM\Column(type="float")
     */
    private $increase;

    public function getWeek(): ?int
    {
        return $this->week;
    }

    public function getEnddate(): ?\DateTimeInterface
    {
        return $this->enddate;
    }

    public function setEnddate(\DateTimeInterface $enddate): self
    {
        $this->enddate = $enddate;

        return $this;
    }

    public function getIncrease(): ?float
    {
        return $this->increase;
    }

    public function setIncrease(float $increase): self
    {
        $this->increase = $increase;

        return $this;
    }
}
