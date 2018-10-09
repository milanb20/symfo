<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TeamRepository")
 */
class Team
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(
     *  min=3,
     *  max=20,
     *  minMessage="Le nom doit comporter entre 3 et 20 caractères"
     * )
     * @Assert\NotIdenticalTo(
     *  value="Real Madrid",
     *  message="Pas de Real dans ma base !"
     * )
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $coach;

    /**
     * @ORM\Column(type="integer")
     * @Assert\Range(
     *  min=1900,
     *  max=2018,
     *  minMessage="L'année doit être comprise entre 1900 et 2018",
     *  maxMessage="L'année doit être comprise entre 1900 et 2018"
     * )
     */
    private $foundationYear;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Player", mappedBy="team")
     */
    private $players;


    public function __construct($name, $coach, $yearFoundation)
    {
      $this->setName($name);
      $this->setCoach($coach);
      $this->setFoundationYear($yearFoundation);
      $this->players = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getCoach(): ?string
    {
        return $this->coach;
    }

    public function setCoach(string $coach): self
    {
        $this->coach = $coach;

        return $this;
    }

    public function getFoundationYear(): ?int
    {
        return $this->foundationYear;
    }

    public function setFoundationYear(int $foundationYear): self
    {
        $this->foundationYear = $foundationYear;

        return $this;
    }

    /**
     * @return Collection|Player[]
     */
    public function getPlayers(): Collection
    {
        return $this->players;
    }

    public function addPlayer(Player $player): self
    {
        if (!$this->players->contains($player)) {
            $this->players[] = $player;
            $player->setTeam($this);
        }

        return $this;
    }

    public function removePlayer(Player $player): self
    {
        if ($this->players->contains($player)) {
            $this->players->removeElement($player);
            // set the owning side to null (unless already changed)
            if ($player->getTeam() === $this) {
                $player->setTeam(null);
            }
        }

        return $this;
    }
}
