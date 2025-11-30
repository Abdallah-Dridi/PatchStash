<?php

namespace App\Entity;

use App\Repository\ReportRepository;
use Doctrine\ORM\Mapping as ORM;
use DateTimeInterface;

#[ORM\Entity(repositoryClass: ReportRepository::class)]
class Report
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'report_id')]
    private ?int $reportId = null;

    #[ORM\Column(length: 255)]
    private string $title;

    #[ORM\Column(type: 'text')]
    private string $content;

    #[ORM\Column(type: 'date')]
    private DateTimeInterface $generatedDate;

    #[ORM\ManyToOne(targetEntity: Project::class, inversedBy: 'reports')]
    #[ORM\JoinColumn(name: 'project_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?Project $project = null;

    // ------------------- Getters / Setters -------------------

    public function getReportId(): ?int { return $this->reportId; }

    public function getTitle(): string { return $this->title; }
    public function setTitle(string $title): self { $this->title = $title; return $this; }

    public function getContent(): string { return $this->content; }
    public function setContent(string $content): self { $this->content = $content; return $this; }

    public function getGeneratedDate(): DateTimeInterface { return $this->generatedDate; }
    public function setGeneratedDate(DateTimeInterface $generatedDate): self { $this->generatedDate = $generatedDate; return $this; }

    public function getProject(): ?Project { return $this->project; }
    public function setProject(?Project $project): self { $this->project = $project; return $this; }

    public function __toString(): string { return $this->title; }
}
