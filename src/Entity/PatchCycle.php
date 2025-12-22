<?php

namespace App\Entity;

use App\Repository\PatchCycleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use DateTimeInterface;

#[ORM\Entity(repositoryClass: PatchCycleRepository::class)]
class PatchCycle
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50, unique: true)]
    private string $cycleId;

    #[ORM\Column(length: 100)]
    private string $status;

    #[ORM\Column(type: 'text')]
    private string $description;

    #[ORM\Column(type: 'date')]
    private DateTimeInterface $deadline;

    #[ORM\Column(type: 'date', nullable: true)]
    private ?DateTimeInterface $appliedDate = null;

    #[ORM\Column(type: 'float')]
    private float $cvss;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $info = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $createdAt = null;

    /** @var Collection<int, Vulnerability> */
    #[ORM\OneToMany(
        mappedBy: 'patchCycle',
        targetEntity: Vulnerability::class,
        cascade: ['persist', 'remove'],
        orphanRemoval: true
    )]
    private Collection $vulnerabilities;

    #[ORM\ManyToOne(targetEntity: Asset::class, inversedBy: 'patchCycles')]
    #[ORM\JoinColumn(name: 'asset_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?Asset $asset = null;

    public function __construct()
    {
        $this->vulnerabilities = new ArrayCollection();
        $this->createdAt = new \DateTime();
    }

    // ------------------- Getters / Setters -------------------

    public function getId(): ?int 
    { 
        return $this->id; 
    }

    public function getCycleId(): string 
    { 
        return $this->cycleId; 
    }

    public function setCycleId(string $cycleId): self 
    { 
        $this->cycleId = $cycleId; 
        return $this; 
    }

    public function getStatus(): string 
    { 
        return $this->status; 
    }

    public function setStatus(string $status): self 
    { 
        $this->status = $status; 
        return $this; 
    }

    public function getDescription(): string 
    { 
        return $this->description; 
    }

    public function setDescription(string $description): self 
    { 
        $this->description = $description; 
        return $this; 
    }

    public function getDeadline(): DateTimeInterface 
    { 
        return $this->deadline; 
    }

    public function setDeadline(DateTimeInterface $deadline): self 
    { 
        $this->deadline = $deadline; 
        return $this; 
    }

    public function getAppliedDate(): ?DateTimeInterface 
    { 
        return $this->appliedDate; 
    }

    public function setAppliedDate(?DateTimeInterface $appliedDate): self 
    { 
        $this->appliedDate = $appliedDate; 
        return $this; 
    }

    public function getCvss(): float 
    { 
        return $this->cvss; 
    }

    public function setCvss(float $cvss): self 
    { 
        $this->cvss = $cvss; 
        return $this; 
    }

    public function getInfo(): ?string 
    { 
        return $this->info; 
    }

    public function setInfo(?string $info): self 
    { 
        $this->info = $info; 
        return $this; 
    }

    /**
     * @return Collection<int, Vulnerability>
     */
    public function getVulnerabilities(): Collection
    {
        return $this->vulnerabilities;
    }

    public function addVulnerability(Vulnerability $vulnerability): self
    {
        if (!$this->vulnerabilities->contains($vulnerability)) {
            $this->vulnerabilities->add($vulnerability);
            $vulnerability->setPatchCycle($this);
        }

        return $this;
    }

    public function removeVulnerability(Vulnerability $vulnerability): self
    {
        if ($this->vulnerabilities->removeElement($vulnerability)) {
            if ($vulnerability->getPatchCycle() === $this) {
                $vulnerability->setPatchCycle(null);
            }
        }

        return $this;
    }

    public function getAsset(): ?Asset 
    { 
        return $this->asset; 
    }

    public function setAsset(?Asset $asset): self 
    { 
        $this->asset = $asset; 
        return $this; 
    }

    public function getCreatedAt(): ?\DateTimeInterface { return $this->createdAt; }
    public function setCreatedAt(?\DateTimeInterface $createdAt): self { $this->createdAt = $createdAt; return $this; }

    public function __toString(): string 
    { 
        return $this->cycleId; 
    }
}
