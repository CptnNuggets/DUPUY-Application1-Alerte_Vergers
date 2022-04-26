<?php

namespace App\Entity;

use App\Entity\Traits\Timestampable;
use DateTime;

use App\Repository\AssocStationVergerRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


/**
 * @ORM\Entity(repositoryClass=AssocStationVergerRepository::class)
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(
 *      uniqueConstraints={@ORM\UniqueConstraint(columns={"station_id","verger_id"})}
 * )
 * @UniqueEntity(
 *      fields={"station", "verger"},
 *      errorPath="verger",
 *      message="Cette station et ce verger ont déjà été associés."
 * )
 */
class AssocStationVerger
{
    use Timestampable;

    /**
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Station::class, inversedBy="assocStationVergers")
     * @ORM\JoinColumn(nullable=false)
     */
    private $station;

    /**
     * @ORM\ManyToOne(targetEntity=Verger::class, inversedBy="assocStationVergers")
     * @ORM\JoinColumn(nullable=false)
     */
    private $verger;

    public function __construct()
    {
        $this->id = Uuid::v4();
    }

    public function getId(): Uuid
    {
        return $this->id;
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

    public function getVerger(): ?Verger
    {
        return $this->verger;
    }

    public function setVerger(?Verger $verger): self
    {
        $this->verger = $verger;

        return $this;
    }
}
