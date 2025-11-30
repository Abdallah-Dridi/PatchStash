<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'user')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string $username;

    #[ORM\Column(length: 255)]
    private string $email;

    #[ORM\Column(length: 255)]
    private string $password;

    #[ORM\Column(length: 50)]
    private string $role;               // e.g. Admin, ProjectManager, Operator, Auditor

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $data = null;

    /** @var Collection<int, Project> */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Project::class)]
    private Collection $projects;

    public function __construct()
    {
        $this->projects = new ArrayCollection();
    }

    // ------------------- Getters / Setters -------------------

    public function getId(): ?int { return $this->id; }

    public function getUsername(): string { return $this->username; }
    public function setUsername(string $username): self { $this->username = $username; return $this; }

    public function getEmail(): string { return $this->email; }
    public function setEmail(string $email): self { $this->email = $email; return $this; }

    public function getPassword(): string { return $this->password; }
    public function setPassword(string $password): self { $this->password = $password; return $this; }

    public function getRole(): string { return $this->role; }
    public function setRole(string $role): self { $this->role = $role; return $this; }

    public function getData(): ?string { return $this->data; }
    public function setData(?string $data): self { $this->data = $data; return $this; }

    /** @return Collection<int, Project> */
    public function getProjects(): Collection { return $this->projects; }

    public function addProject(Project $project): self
    {
        if (!$this->projects->contains($project)) {
            $this->projects->add($project);
            $project->setUser($this);
        }
        return $this;
    }

    public function removeProject(Project $project): self
    {
        if ($this->projects->removeElement($project) && $project->getUser() === $this) {
            $project->setUser(null);
        }
        return $this;
    }

    // ------------------- UserInterface -------------------
    public function getRoles(): array
    {
        $roleValue = $this->role ?? '';
        $storedRole = $roleValue
            ? (str_starts_with($roleValue, 'ROLE_') ? $roleValue : 'ROLE_'.strtoupper($roleValue))
            : 'ROLE_USER';

        return array_values(array_unique(['ROLE_USER', $storedRole]));
    }
    public function eraseCredentials(): void { /* no sensitive data */ }
    public function getUserIdentifier(): string { return $this->username; }

    public function __toString(): string { return $this->username; }
}
