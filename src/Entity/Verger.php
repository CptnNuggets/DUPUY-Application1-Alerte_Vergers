<?php

namespace App\Entity;

use App\Entity\Traits\Timestampable;
use DateTime;

use App\Repository\VergerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=VergerRepository::class)
 * @ORM\HasLifecycleCallbacks
 * @UniqueEntity(
 *      fields={"idVerger"},
 *      message="Ce nom de verger est déjà utilisé."
 * )
 */
class Verger
{
    use Timestampable;
    
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity=AssocStationVerger::class, mappedBy="verger")
     */
    private $assocStationVergers;

    /**
     * @ORM\Column(type="string", length=255, nullable=false, unique=true)
     */
    private $idVerger;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $contact;

    /**
     * @ORM\ManyToOne(targetEntity=MessageAlerte::class, inversedBy="vergers")
     */
    private $messageAlerte;

    public function __construct()
    {
        $this->id = Uuid::v4();
        $this->assocStationVergers = new ArrayCollection();
    }

    public function getId(): Uuid
    {
        return $this->id;
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
            $assocStationVerger->setVerger($this);
        }

        return $this;
    }

    public function removeAssocStationVerger(AssocStationVerger $assocStationVerger): self
    {
        if ($this->assocStationVergers->removeElement($assocStationVerger)) {
            // set the owning side to null (unless already changed)
            if ($assocStationVerger->getVerger() === $this) {
                $assocStationVerger->setVerger(null);
            }
        }

        return $this;
    }

    public function getIdVerger(): ?string
    {
        return $this->idVerger;
    }

    public function setIdVerger(?string $idVerger): self
    {
        $this->idVerger = $idVerger;

        return $this;
    }

    public function getContact(): ?string
    {
        return $this->contact;
    }

    public function setContact(?string $contact): self
    {
        $this->contact = $contact;

        return $this;
    }

    public function getMessageAlerte(): ?MessageAlerte
    {
        return $this->messageAlerte;
    }

    public function setMessageAlerte(?MessageAlerte $messageAlerte): self
    {
        $this->messageAlerte = $messageAlerte;

        return $this;
    }
}
