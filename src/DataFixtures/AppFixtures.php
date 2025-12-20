<?php

namespace App\DataFixtures;

use App\Entity\Permission;
use App\Entity\Project;
use App\Entity\RolePermission;
use App\Entity\User;
use App\Enum\UserRole;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    public function load(ObjectManager $manager): void
    {
        // 1. Define Permissions
        $permissionsData = [
            // Report Permissions
            ['name' => 'create_report', 'category' => 'Reporting', 'description' => 'Create new security reports'],
            ['name' => 'view_reports', 'category' => 'Reporting', 'description' => 'View existing reports'],
            
            // Project Permissions
            ['name' => 'manage_projects', 'category' => 'Project', 'description' => 'Create and edit projects'],
            ['name' => 'view_projects', 'category' => 'Project', 'description' => 'View project details'],
            
            // Asset Permissions
            ['name' => 'manage_assets', 'category' => 'Asset', 'description' => 'Add or remove assets'],
            ['name' => 'view_assets', 'category' => 'Asset', 'description' => 'View asset inventory'],
            
            // Vulnerability/Scan Permissions
            ['name' => 'scan_vulnerabilities', 'category' => 'Security', 'description' => 'Run vulnerability scans'],
            ['name' => 'manage_vulnerabilities', 'category' => 'Security', 'description' => 'Update vulnerability status'],
            ['name' => 'view_vulnerabilities', 'category' => 'Security', 'description' => 'View vulnerability list'],
            
            // Dashboard Permissions
            ['name' => 'view_dashboard', 'category' => 'General', 'description' => 'Access the main dashboard'],
        ];

        $permissionEntities = [];
        foreach ($permissionsData as $data) {
            $permission = new Permission();
            $permission->setName($data['name']);
            $permission->setCategory($data['category']);
            $permission->setDescription($data['description']);
            $manager->persist($permission);
            $permissionEntities[$data['name']] = $permission;
        }

        // 2. Define Role Mappings
        $rolesData = [
            UserRole::PROJECT_MANAGER->value => [
                'manage_projects', 'view_projects', 
                'manage_assets', 'view_assets', 
                'create_report', 'view_reports',
                'view_dashboard'
            ],
            UserRole::OPERATOR->value => [
                'scan_vulnerabilities', 'manage_vulnerabilities', 'view_vulnerabilities',
                'view_assets', 'view_projects',
                'view_dashboard'
            ],
            UserRole::AUDITOR->value => [
                'view_reports',
                'view_projects',
                'view_assets',
                'view_vulnerabilities',
                'view_dashboard'
            ],
            // Admin typically bypasses checks or has all, but we can explicitly add if needed.
            // For now, PermissionChecker bypasses for ROLE_ADMIN.
        ];

        foreach ($rolesData as $roleName => $perms) {
            $rolePermission = new RolePermission();
            $rolePermission->setRole($roleName); // Matches Enum string value
            
            foreach ($perms as $permName) {
                if (isset($permissionEntities[$permName])) {
                    $rolePermission->addPermission($permissionEntities[$permName]);
                }
            }
            $manager->persist($rolePermission);
        }

        // 3. Create Users
        $users = [
            [
                'username' => 'admin',
                'email' => 'admin@patchstash.com',
                'role' => UserRole::ADMIN->value,
                'password' => 'password'
            ],
            [
                'username' => 'manager',
                'email' => 'manager@patchstash.com',
                'role' => UserRole::PROJECT_MANAGER->value,
                'password' => 'password'
            ],
            [
                'username' => 'operator',
                'email' => 'operator@patchstash.com',
                'role' => UserRole::OPERATOR->value,
                'password' => 'password'
            ],
            [
                'username' => 'auditor',
                'email' => 'auditor@patchstash.com',
                'role' => UserRole::AUDITOR->value,
                'password' => 'password'
            ]
        ];

        foreach ($users as $userData) {
            $user = new User();
            $user->setUsername($userData['username']);
            $user->setEmail($userData['email']);
            $user->setRole($userData['role']);
            $user->setIsVerified(true);
            $user->setPassword($this->passwordHasher->hashPassword($user, $userData['password']));
            $manager->persist($user);

            // Create a sample project for the manager to see data immediately
            if ($userData['username'] === 'manager') {
                $project = new Project();
                $project->setName('Alpha Sector Grid');
                $project->setDescription('Main power grid control systems for Sector 7.');
                $project->setStatus('active');
                $project->setUser($user);
                $manager->persist($project);
            }
        }

        $manager->flush();
    }
}
