<?php

namespace App\Entity;

use App\Entity\Traits\Timestampable;
use DateTime;

use App\Repository\CapteurPourMathsRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=CapteurPourMathsRepository::class)
 * @ORM\HasLifecycleCallbacks
 * @UniqueEntity(
 *      fields={"nomRaccourci"},
 *      message="Cet identifiant capteur a déjà été utilisé."
 * )
 * @UniqueEntity(
 *      fields={"capteur"},
 *      message="Capteur déjà enregistré comme valeur de suivi."
 * )
 */
class CapteurPourMaths
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
    private $nomRaccourci;

    /**
     * @ORM\ManyToOne(targetEntity=Capteur::class, inversedBy="capteurPourMaths")
     * @ORM\JoinColumn(nullable=false, unique=true)
     */
    private $capteur;

    public function __construct()
    {
        $this->id = Uuid::v4();
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getNomRaccourci(): ?string
    {
        return $this->nomRaccourci;
    }

    public function setNomRaccourci(string $nomRaccourci): self
    {
        $this->nomRaccourci = $nomRaccourci;

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
}
