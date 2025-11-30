<?php

namespace App\Entity;

use App\Repository\PatchCycleRepository;
use Doctrine\ORM\Mapping as ORM;
use DateTimeInterface;

#[ORM\Entity(repositoryClass: PatchCycleRepository::class)]
class PatchCycle
{
    #[ORM\Id]
    #[ORM\Column(length: 50)]
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

    #[ORM\ManyToOne(targetEntity: Vulnerability::class, inversedBy: 'patchCycles')]
    #[ORM\JoinColumn(
        name: 'vulnerability_cve_id',
        referencedColumnName: 'cve_id',  // ← Now matches
        nullable: true,
        onDelete: 'SET NULL'
    )]
    private ?Vulnerability $vulnerability = null;

    #[ORM\ManyToOne(targetEntity: Asset::class, inversedBy: 'patchCycles')]
    #[ORM\JoinColumn(
        name: 'asset_id',
        referencedColumnName: 'id',
        nullable: true,
        onDelete: 'SET NULL'
    )]
    private ?Asset $asset = null;

    // ------------------- Getters / Setters -------------------

    public function getCycleId(): string { return $this->cycleId; }
    public function setCycleId(string $cycleId): self { $this->cycleId = $cycleId; return $this; }

    public function getStatus(): string { return $this->status; }
    public function setStatus(string $status): self { $this->status = $status; return $this; }

    public function getDescription(): string { return $this->description; }
    public function setDescription(string $description): self { $this->description = $description; return $this; }

    public function getDeadline(): DateTimeInterface { return $this->deadline; }
    public function setDeadline(DateTimeInterface $deadline): self { $this->deadline = $deadline; return $this; }

    public function getAppliedDate(): ?DateTimeInterface { return $this->appliedDate; }
    public function setAppliedDate(?DateTimeInterface $appliedDate): self { $this->appliedDate = $appliedDate; return $this; }

    public function getCvss(): float { return $this->cvss; }
    public function setCvss(float $cvss): self { $this->cvss = $cvss; return $this; }

    public function getInfo(): ?string { return $this->info; }
    public function setInfo(?string $info): self { $this->info = $info; return $this; }

    public function getVulnerability(): ?Vulnerability { return $this->vulnerability; }
    public function setVulnerability(?Vulnerability $vulnerability): self { $this->vulnerability = $vulnerability; return $this; }

    public function getAsset(): ?Asset { return $this->asset; }
    public function setAsset(?Asset $asset): self { $this->asset = $asset; return $this; }

    public function __toString(): string { return $this->cycleId; }
}
