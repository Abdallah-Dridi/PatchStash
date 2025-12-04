<?php

namespace App\Entity;

use App\Repository\AssetRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AssetRepository::class)]
class Asset
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string $name;

    #[ORM\Column(type: 'text')]
    private string $description;

    #[ORM\Column(length: 100)]
    private string $type;

    #[ORM\Column(length: 100)]
    private string $environment;

    #[ORM\Column(length: 100)]
    private string $status;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $info = null;

    #[ORM\ManyToOne(targetEntity: Module::class, inversedBy: 'assets')]
    #[ORM\JoinColumn(name: 'module_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?Module $module = null;

    /** @var Collection<int, PatchCycle> */
    #[ORM\OneToMany(
        mappedBy: 'asset',
        targetEntity: PatchCycle::class,
        cascade: ['remove'],
        orphanRemoval: true
    )]
    private Collection $patchCycles;

    public function __construct()
    {
        $this->patchCycles = new ArrayCollection();
    }

    // ------------------- Getters / Setters -------------------

    public function getId(): ?int { return $this->id; }

    public function getName(): string { return $this->name; }
    public function setName(string $name): self { $this->name = $name; return $this; }

    public function getDescription(): string { return $this->description; }
    public function setDescription(string $description): self { $this->description = $description; return $this; }

    public function getType(): string { return $this->type; }
    public function setType(string $type): self { $this->type = $type; return $this; }

    public function getEnvironment(): string { return $this->environment; }
    public function setEnvironment(string $environment): self { $this->environment = $environment; return $this; }

    public function getStatus(): string { return $this->status; }
    public function setStatus(string $status): self { $this->status = $status; return $this; }

    public function getInfo(): ?string { return $this->info; }
    public function setInfo(?string $info): self { $this->info = $info; return $this; }

    public function getModule(): ?Module { return $this->module; }
    public function setModule(?Module $module): self { $this->module = $module; return $this; }

    /** @return Collection<int, PatchCycle> */
    public function getPatchCycles(): Collection { return $this->patchCycles; }

    public function addPatchCycle(PatchCycle $patchCycle): self
    {
        if (!$this->patchCycles->contains($patchCycle)) {
            $this->patchCycles->add($patchCycle);
            $patchCycle->setAsset($this);
        }
        return $this;
    }

    public function removePatchCycle(PatchCycle $patchCycle): self
    {
        if ($this->patchCycles->removeElement($patchCycle) && $patchCycle->getAsset() === $this) {
            $patchCycle->setAsset(null);
        }
        return $this;
    }

    public function __toString(): string { return $this->name; }
}
