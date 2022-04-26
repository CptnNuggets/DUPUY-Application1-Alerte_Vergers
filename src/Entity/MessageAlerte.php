<?php

namespace App\Entity;

use App\Entity\Traits\Timestampable;
use DateTime;

use App\Repository\MessageAlerteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=MessageAlerteRepository::class)
 * @ORM\HasLifecycleCallbacks
 * @UniqueEntity(
 *      fields={"alerteCode"},
 *      message="Ce code d'alerte est déjà configuré."
 * )
 */
class MessageAlerte
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
    private $alerteCode;

    /**
     * @ORM\Column(type="text")
     */
    private $alerteMessage;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $alerteLevel;

    /**
     * @ORM\OneToMany(targetEntity=Verger::class, mappedBy="messageAlerte")
     */
    private $vergers;

    public function __construct()
    {
        $this->id = Uuid::v4();
        $this->vergers = new ArrayCollection();
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getAlerteCode(): ?string
    {
        return $this->alerteCode;
    }

    public function setAlerteCode(string $alerteCode): self
    {
        $this->alerteCode = $alerteCode;

        return $this;
    }

    public function getAlerteMessage(): ?string
    {
        return $this->alerteMessage;
    }

    public function setAlerteMessage(string $alerteMessage): self
    {
        $this->alerteMessage = $alerteMessage;

        return $this;
    }

    public function getAlerteLevel(): ?string
    {
        return $this->alerteLevel;
    }

    public function setAlerteLevel(?string $alerteLevel): self
    {
        $this->alerteLevel = $alerteLevel;

        return $this;
    }

    /**
     * @return Collection|Verger[]
     */
    public function getVergers(): Collection
    {
        return $this->vergers;
    }

    public function addVerger(Verger $verger): self
    {
        if (!$this->vergers->contains($verger)) {
            $this->vergers[] = $verger;
            $verger->setMessageAlerte($this);
        }

        return $this;
    }

    public function removeVerger(Verger $verger): self
    {
        if ($this->vergers->removeElement($verger)) {
            // set the owning side to null (unless already changed)
            if ($verger->getMessageAlerte() === $this) {
                $verger->setMessageAlerte(null);
            }
        }

        return $this;
    }
}
