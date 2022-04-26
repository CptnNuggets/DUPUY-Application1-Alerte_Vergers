<?php

namespace App\Entity;

use App\Entity\Traits\Timestampable;
use DateTime;

use App\Repository\CapteurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=CapteurRepository::class)
 * @ORM\HasLifecycleCallbacks
 * @UniqueEntity(
 *      fields={"capteurName"},
 *      message="Ce capteur a déjà été créé."
 * )
 */
class Capteur
{
    use Timestampable;

    /**
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $capteurName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $unite;

    /**
     * @ORM\OneToMany(targetEntity=AssocCapteurStation::class, mappedBy="capteur")
     */
    private $assocCapteurStations;

    /**
     * @ORM\OneToMany(targetEntity=CapteurPourMaths::class, mappedBy="capteur")
     */
    private $capteurPourMaths;

    public function __construct()
    {
        $this->id = Uuid::v4();
        $this->assocCapteurStations = new ArrayCollection();
        $this->capteurPourMaths = new ArrayCollection();
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getCapteurName(): ?string
    {
        return $this->capteurName;
    }

    public function setCapteurName(string $capteurName): self
    {
        $this->capteurName = $capteurName;

        return $this;
    }

    public function getUnite(): ?string
    {
        return $this->unite;
    }

    public function setUnite(?string $unite): self
    {
        $this->unite = $unite;

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
            $assocCapteurStation->setCapteur($this);
        }

        return $this;
    }

    public function removeAssocCapteurStation(AssocCapteurStation $assocCapteurStation): self
    {
        if ($this->assocCapteurStations->removeElement($assocCapteurStation)) {
            // set the owning side to null (unless already changed)
            if ($assocCapteurStation->getCapteur() === $this) {
                $assocCapteurStation->setCapteur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|CapteurPourMaths[]
     */
    public function getCapteurPourMaths(): Collection
    {
        return $this->capteurPourMaths;
    }

    public function addCapteurPourMath(CapteurPourMaths $capteurPourMath): self
    {
        if (!$this->capteurPourMaths->contains($capteurPourMath)) {
            $this->capteurPourMaths[] = $capteurPourMath;
            $capteurPourMath->setCapteur($this);
        }

        return $this;
    }

    public function removeCapteurPourMath(CapteurPourMaths $capteurPourMath): self
    {
        if ($this->capteurPourMaths->removeElement($capteurPourMath)) {
            // set the owning side to null (unless already changed)
            if ($capteurPourMath->getCapteur() === $this) {
                $capteurPourMath->setCapteur(null);
            }
        }

        return $this;
    }

}
