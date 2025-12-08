<?php

namespace App\Entity;

use App\Repository\RolePermissionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RolePermissionRepository::class)]
#[ORM\Table(name: 'role_permissions')]
class RolePermission
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'role_permission_id')]
    private ?int $rolePermissionId = null;

    #[ORM\Column(length: 50, unique: true)]
    private string $role;

    #[ORM\ManyToMany(targetEntity: Permission::class)]
    #[ORM\JoinTable(name: 'role_permission_mappings')]
    #[ORM\JoinColumn(name: 'role_permission_id', referencedColumnName: 'role_permission_id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'permission_id', referencedColumnName: 'permission_id', onDelete: 'CASCADE')]
    private Collection $permissions;

    public function __construct()
    {
        $this->permissions = new ArrayCollection();
    }

    public function getRolePermissionId(): ?int
    {
        return $this->rolePermissionId;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function setRole(string $role): self
    {
        $this->role = $role;
        return $this;
    }

    public function getPermissions(): Collection
    {
        return $this->permissions;
    }

    public function addPermission(Permission $permission): self
    {
        if (!$this->permissions->contains($permission)) {
            $this->permissions->add($permission);
        }
        return $this;
    }

    public function removePermission(Permission $permission): self
    {
        $this->permissions->removeElement($permission);
        return $this;
    }

    public function __toString(): string
    {
        return $this->role;
    }
}
