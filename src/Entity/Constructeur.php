<?php

namespace App\Entity;

use App\Entity\Traits\Timestampable;
use DateTime;

use App\Repository\ConstructeurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=ConstructeurRepository::class)
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(
 *      uniqueConstraints={
 *          @ORM\UniqueConstraint(columns={"constructeur_name", "version_api"})
 *      }
 * )
 * @UniqueEntity(
 *      fields={"constructeurName", "versionAPI"},
 *      errorPath="constructeurName",
 *      message="Cette version d'API est déjà renseignée pour ce constructeur."
 * )
 */
class Constructeur
{
    use Timestampable;

    /**
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $constructeurName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $versionAPI;

    /**
     * @ORM\OneToMany(targetEntity=Station::class, mappedBy="constructeur")
     */
    private $station;

    public function __construct()
    {
        $this->id = Uuid::v4();
        $this->station = new ArrayCollection();
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getConstructeurName(): ?string
    {
        return $this->constructeurName;
    }

    public function setConstructeurName(string $constructeurName): self
    {
        $this->constructeurName = $constructeurName;

        return $this;
    }

    public function getVersionAPI(): ?string
    {
        return $this->versionAPI;
    }

    public function setVersionAPI(?string $versionAPI): self
    {
        $this->versionAPI = $versionAPI;

        return $this;
    }

    /**
     * @return Collection|Station[]
     */
    public function getStation(): Collection
    {
        return $this->station;
    }

    public function addStation(Station $station): self
    {
        if (!$this->station->contains($station)) {
            $this->station[] = $station;
            $station->setConstructeur($this);
        }

        return $this;
    }

    public function removeStation(Station $station): self
    {
        if ($this->station->removeElement($station)) {
            // set the owning side to null (unless already changed)
            if ($station->getConstructeur() === $this) {
                $station->setConstructeur(null);
            }
        }

        return $this;
    }
}
