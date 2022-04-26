<?php

namespace App\Entity;

use App\Entity\Traits\Timestampable;
use DateTime;

use App\Repository\ValeursCumuleesRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=ValeursCumuleesRepository::class)
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(
 *      uniqueConstraints={
 *          @ORM\UniqueConstraint(columns={"station_id", "date_time"})
 *      }
 * )
 */
class ValeursCumulees
{
    use Timestampable;

    /**
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     */
    private $id;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $humectationCumulee;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $temperatureCumulee;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateTime;

    /**
     * @ORM\ManyToOne(targetEntity=Station::class, inversedBy="valeursCumulees")
     * @ORM\JoinColumn(nullable=false)
     */
    private $station;

    public function __construct()
    {
        $this->id = Uuid::v4();
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getHumectationCumulee(): ?float
    {
        return $this->humectationCumulee;
    }

    public function setHumectationCumulee(?float $humectationCumulee): self
    {
        $this->humectationCumulee = $humectationCumulee;

        return $this;
    }

    public function getTemperatureCumulee(): ?float
    {
        return $this->temperatureCumulee;
    }

    public function setTemperatureCumulee(?float $temperatureCumulee): self
    {
        $this->temperatureCumulee = $temperatureCumulee;

        return $this;
    }

    public function getDateTime(): ?\DateTimeInterface
    {
        return $this->dateTime;
    }

    public function setDateTime(?\DateTimeInterface $dateTime): self
    {
        $this->dateTime = $dateTime;

        return $this;
    }

    public function getStation(): ?Station
    {
        return $this->station;
    }

    public function setStation(?Station $station): self
    {
        $this->station = $station;

        return $this;
    }
}
