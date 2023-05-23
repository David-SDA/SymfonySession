<?php

namespace App\Entity;

use App\Repository\ModuleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ModuleRepository::class)]
class Module
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 70)]
    private ?string $libelle = null;

    #[ORM\ManyToOne(inversedBy: 'modules')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Categorie $categorie = null;

    #[ORM\OneToMany(mappedBy: 'module', targetEntity: SessionModule::class)]
    private Collection $sessionModules;

    public function __construct()
    {
        $this->sessionModules = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function getCategorie(): ?Categorie
    {
        return $this->categorie;
    }

    public function setCategorie(?Categorie $categorie): self
    {
        $this->categorie = $categorie;

        return $this;
    }

    /**
     * @return Collection<int, SessionModule>
     */
    public function getSessionModules(): Collection
    {
        return $this->sessionModules;
    }

    public function addSessionModule(SessionModule $sessionModule): self
    {
        if (!$this->sessionModules->contains($sessionModule)) {
            $this->sessionModules->add($sessionModule);
            $sessionModule->setModule($this);
        }

        return $this;
    }

    public function removeSessionModule(SessionModule $sessionModule): self
    {
        if ($this->sessionModules->removeElement($sessionModule)) {
            // set the owning side to null (unless already changed)
            if ($sessionModule->getModule() === $this) {
                $sessionModule->setModule(null);
            }
        }

        return $this;
    }

    public function __toString(): string{
        return $this->libelle;
    }
}
