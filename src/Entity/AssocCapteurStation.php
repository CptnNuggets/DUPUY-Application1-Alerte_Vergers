<?php

namespace App\Entity;
use DateTime;

use App\Repository\AssocCapteurStationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=AssocCapteurStationRepository::class)
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(
 *      uniqueConstraints={
 *          @ORM\UniqueConstraint(columns={"station_id", "capteur_id"}),
 *          @ORM\UniqueConstraint(columns={"station_id", "numero_capteur_id"}),
 *          @ORM\UniqueConstraint(columns={"station_id", "code_capteur"})
 *      }
 * )
 * @UniqueEntity(
 *      fields={"station", "capteur"},
 *      errorPath="capteur",
 *      message="Ce capteur est déjà créé pour cette station météo."
 * )
 */
class AssocCapteurStation
{
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $codeCapteur;

    /**
     * @ORM\ManyToOne(targetEntity=NumeroCapteur::class, inversedBy="assocCapteurStations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $numeroCapteur;

    /**
     * @ORM\ManyToOne(targetEntity=Station::class, inversedBy="assocCapteurStations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $station;

    /**
     * @ORM\ManyToOne(targetEntity=Capteur::class, inversedBy="assocCapteurStations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $capteur;

    /**
     * @ORM\OneToMany(targetEntity=Mesure::class, mappedBy="AssoCapteurStation")
     */
    private $mesures;

    public function __construct()
    {
        $this->id = Uuid::v4();
        $this->mesures = new ArrayCollection();
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getCodeCapteur(): ?string
    {
        return $this->codeCapteur;
    }

    public function setCodeCapteur(string $codeCapteur): self
    {
        $this->codeCapteur = $codeCapteur;

        return $this;
    }

    public function getNumeroCapteur(): ?NumeroCapteur
    {
        return $this->numeroCapteur;
    }

    public function setNumeroCapteur(?NumeroCapteur $numeroCapteur): self
    {
        $this->numeroCapteur = $numeroCapteur;

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

    public function getCapteur(): ?Capteur
    {
        return $this->capteur;
    }

    public function setCapteur(?Capteur $capteur): self
    {
        $this->capteur = $capteur;

        return $this;
    }

    /**
     * @return Collection|Mesure[]
     */
    public function getMesures(): Collection
    {
        return $this->mesures;
    }

    public function addMesure(Mesure $mesure): self
    {
        if (!$this->mesures->contains($mesure)) {
            $this->mesures[] = $mesure;
            $mesure->setAssoCapteurStation($this);
        }

        return $this;
    }

    public function removeMesure(Mesure $mesure): self
    {
        if ($this->mesures->removeElement($mesure)) {
            // set the owning side to null (unless already changed)
            if ($mesure->getAssoCapteurStation() === $this) {
                $mesure->setAssoCapteurStation(null);
            }
        }

        return $this;
    }
}
