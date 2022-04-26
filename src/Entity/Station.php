<?php

namespace App\Entity;

use App\Entity\Traits\Timestampable;
use DateTime;

use App\Repository\StationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=StationRepository::class)
 * @ORM\HasLifecycleCallbacks
 * @UniqueEntity(
 *      fields={"stationName"},
 *      message="Ce nom de station est déjà utilisé."
 * )
 * @UniqueEntity(
 *      fields={"stationCode"},
 *      message="Ce code API est déjà utilisé."
 * )
 */
class Station
{
    use Timestampable;
    
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=false, unique=true)
     */
    private $stationName;

    /**
     * @ORM\OneToMany(targetEntity=AssocStationVerger::class, mappedBy="station")
     */
    private $assocStationVergers;

    /**
     * @ORM\Column(type="string", length=255, nullable=false, unique=true)
     */
    private $stationCode;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $listeCapteurs = [];

    /**
     * @ORM\OneToMany(targetEntity=AssocCapteurStation::class, mappedBy="station")
     */
    private $assocCapteurStations;

    /**
     * @ORM\ManyToOne(targetEntity=Constructeur::class, inversedBy="station")
     */
    private $constructeur;

    /**
     * @ORM\OneToMany(targetEntity=ValeursCumulees::class, mappedBy="station")
     */
    private $valeursCumulees;

    public function __construct()
    {
        $this->id = Uuid::v4();
        $this->assocStationVergers = new ArrayCollection();
        $this->mesures = new ArrayCollection();
        $this->assocCapteurStations = new ArrayCollection();
        $this->valeursCumulees = new ArrayCollection();
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getStationName(): ?string
    {
        return $this->stationName;
    }

    public function setStationName(string $stationName): self
    {
        $this->stationName = $stationName;

        return $this;
    }

    /**
     * @return Collection|AssocStationVerger[]
     */
    public function getAssocStationVergers(): Collection
    {
        return $this->assocStationVergers;
    }

    public function addAssocStationVerger(AssocStationVerger $assocStationVerger): self
    {
        if (!$this->assocStationVergers->contains($assocStationVerger)) {
            $this->assocStationVergers[] = $assocStationVerger;
            $assocStationVerger->setStation($this);
        }

        return $this;
    }

    public function removeAssocStationVerger(AssocStationVerger $assocStationVerger): self
    {
        if ($this->assocStationVergers->removeElement($assocStationVerger)) {
            // set the owning side to null (unless already changed)
            if ($assocStationVerger->getStation() === $this) {
                $assocStationVerger->setStation(null);
            }
        }

        return $this;
    }

    public function getStationCode(): ?string
    {
        return $this->stationCode;
    }

    public function setStationCode(?string $stationCode): self
    {
        $this->stationCode = $stationCode;

        return $this;
    }

    public function getListeCapteurs(): ?array
    {
        return $this->listeCapteurs;
    }

    public function setListeCapteurs(?array $listeCapteurs): self
    {
        $this->listeCapteurs = $listeCapteurs;

        return $this;
    }

    /**
     * @return Collection|AssocCapteurStation[]
     */
    public function getAssocCapteurStations(): Collection
    {
        return $this->assocCapteurStations;
    }

    public function addAssocCapteurStation(AssocCapteurStation $assocCapteurStation): self
    {
        if (!$this->assocCapteurStations->contains($assocCapteurStation)) {
            $this->assocCapteurStations[] = $assocCapteurStation;
            $assocCapteurStation->setStation($this);
        }

        return $this;
    }

    public function removeAssocCapteurStation(AssocCapteurStation $assocCapteurStation): self
    {
        if ($this->assocCapteurStations->removeElement($assocCapteurStation)) {
            // set the owning side to null (unless already changed)
            if ($assocCapteurStation->getStation() === $this) {
                $assocCapteurStation->setStation(null);
            }
        }

        return $this;
    }

    public function getConstructeur(): ?Constructeur
    {
        return $this->constructeur;
    }

    public function setConstructeur(?Constructeur $constructeur): self
    {
        $this->constructeur = $constructeur;

        return $this;
    }

    /**
     * @return Collection|ValeursCumulees[]
     */
    public function getValeursCumulees(): Collection
    {
        return $this->valeursCumulees;
    }

    public function addValeursCumulee(ValeursCumulees $valeursCumulee): self
    {
        if (!$this->valeursCumulees->contains($valeursCumulee)) {
            $this->valeursCumulees[] = $valeursCumulee;
            $valeursCumulee->setStation($this);
        }

        return $this;
    }

    public function removeValeursCumulee(ValeursCumulees $valeursCumulee): self
    {
        if ($this->valeursCumulees->removeElement($valeursCumulee)) {
            // set the owning side to null (unless already changed)
            if ($valeursCumulee->getStation() === $this) {
                $valeursCumulee->setStation(null);
            }
        }

        return $this;
    }
}
