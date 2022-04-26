<?php

namespace App\Entity;

use App\Entity\Traits\Timestampable;
use DateTime;

use App\Repository\MesureRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=MesureRepository::class)
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(
 *      uniqueConstraints={
 *          @ORM\UniqueConstraint(columns={"assoc_capteur_station_id", "date_time"})
 *      }
 * )
 * @UniqueEntity(
 *      fields={"assocCapteurStation", "dateTime"},
 *      errorPath="station",
 *      message="Mesures déjà entrées dans la base de données."
 * )
 */
class Mesure
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
    private $valeur;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateTime;

    /**
     * @ORM\ManyToOne(targetEntity=AssocCapteurStation::class, inversedBy="mesures")
     */
    private $assocCapteurStation;

    public function __construct()
    {
        $this->id = Uuid::v4();
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getValeur(): ?float
    {
        return $this->valeur;
    }

    public function setValeur(?float $valeur): self
    {
        $this->valeur = $valeur;

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

    public function getAssocCapteurStation(): ?AssocCapteurStation
    {
        return $this->assocCapteurStation;
    }

    public function setAssocCapteurStation(?AssocCapteurStation $assocCapteurStation): self
    {
        $this->assocCapteurStation = $assocCapteurStation;

        return $this;
    }
}
