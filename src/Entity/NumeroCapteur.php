<?php

namespace App\Entity;

use App\Entity\Traits\Timestampable;
use DateTime;

use App\Repository\NumeroCapteurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=NumeroCapteurRepository::class)
 * @ORM\HasLifecycleCallbacks
 * @UniqueEntity(
 *      fields={"numero"},
 *      message="Numéro déjà existant."
 * )
 */
class NumeroCapteur
{
    use Timestampable;

    /**
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     */
    private $id;

    /**
     * @ORM\Column(type="integer", nullable=false, unique=true)
     */
    private $numero;

    /**
     * @ORM\OneToMany(targetEntity=AssocCapteurStation::class, mappedBy="numeroCapteur")
     */
    private $assocCapteurStations;

    public function __construct()
    {
        $this->id = Uuid::v4();
        $this->mesures = new ArrayCollection();
        $this->assocCapteurStations = new ArrayCollection();
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getNumero(): ?int
    {
        return $this->numero;
    }

    public function setNumero(?int $numero): self
    {
        $this->numero = $numero;

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
            $assocCapteurStation->setNumeroCapteur($this);
        }

        return $this;
    }

    public function removeAssocCapteurStation(AssocCapteurStation $assocCapteurStation): self
    {
        if ($this->assocCapteurStations->removeElement($assocCapteurStation)) {
            // set the owning side to null (unless already changed)
            if ($assocCapteurStation->getNumeroCapteur() === $this) {
                $assocCapteurStation->setNumeroCapteur(null);
            }
        }

        return $this;
    }
}
