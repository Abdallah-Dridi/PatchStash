<?php

namespace App\Entity;

use App\Repository\ProjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProjectRepository::class)]
class Project
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
    private string $status;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $info = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'projects')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?User $user = null;

    /** @var Collection<int, Module> */
    #[ORM\OneToMany(
        mappedBy: 'project',
        targetEntity: Module::class,
        cascade: ['remove'],
        orphanRemoval: true
    )]
    private Collection $modules;

    /** @var Collection<int, Report> */
    #[ORM\OneToMany(
        mappedBy: 'project',
        targetEntity: Report::class,
        cascade: ['remove'],
        orphanRemoval: true
    )]
    private Collection $reports;

    public function __construct()
    {
        $this->modules = new ArrayCollection();
        $this->reports = new ArrayCollection();
    }

    // ------------------- Getters / Setters -------------------

    public function getId(): ?int 
    { 
        return $this->id; 
    }

    public function getName(): string 
    { 
        return $this->name; 
    }

    public function setName(string $name): self 
    { 
        $this->name = $name;
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

    public function getStatus(): string 
    { 
        return $this->status; 
    }

    public function setStatus(string $status): self 
    { 
        $this->status = $status;
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

    public function getUser(): ?User 
    { 
        return $this->user; 
    }

    public function setUser(?User $user): self 
    { 
        $this->user = $user;
        return $this; 
    }

    /** @return Collection<int, Module> */
    public function getModules(): Collection 
    { 
        return $this->modules; 
    }

    public function addModule(Module $module): self
    {
        if (!$this->modules->contains($module)) {
            $this->modules->add($module);
            $module->setProject($this);
        }
        return $this;
    }

    public function removeModule(Module $module): self
    {
        if ($this->modules->removeElement($module) && $module->getProject() === $this) {
            $module->setProject(null);
        }
        return $this;
    }

    /**
     * Helper to get all assets across all modules in this project
     * @return Collection<int, Asset>
     */
    public function getAssets(): Collection
    {
        $assets = new ArrayCollection();
        foreach ($this->modules as $module) {
            foreach ($module->getAssets() as $asset) {
                if (!$assets->contains($asset)) {
                    $assets->add($asset);
                }
            }
        }
        return $assets;
    }

    /** @return Collection<int, Report> */
    public function getReports(): Collection 
    { 
        return $this->reports; 
    }

    public function addReport(Report $report): self
    {
        if (!$this->reports->contains($report)) {
            $this->reports->add($report);
            $report->setProject($this);
        }
        return $this;
    }

    public function removeReport(Report $report): self
    {
        if ($this->reports->removeElement($report) && $report->getProject() === $this) {
            $report->setProject(null);
        }
        return $this;
    }

    public function __toString(): string 
    { 
        return $this->name; 
    }
}
