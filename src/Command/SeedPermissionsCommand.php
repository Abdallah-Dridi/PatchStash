<?php

namespace App\Command;

use App\Entity\Permission;
use App\Repository\PermissionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:seed:permissions',
    description: 'Seed the database with default permissions',
)]
final class SeedPermissionsCommand extends Command
{
    public function __construct(
        private PermissionRepository $permissionRepository,
        private EntityManagerInterface $entityManager,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $permissions = [
            // Users
            ['name' => 'create_user', 'description' => 'Create new users', 'category' => 'users'],
            ['name' => 'edit_user', 'description' => 'Edit user details', 'category' => 'users'],
            ['name' => 'delete_user', 'description' => 'Delete users', 'category' => 'users'],
            ['name' => 'view_users', 'description' => 'View user list', 'category' => 'users'],
            ['name' => 'manage_roles', 'description' => 'Manage user roles and permissions', 'category' => 'users'],

            // Projects
            ['name' => 'create_project', 'description' => 'Create new projects', 'category' => 'projects'],
            ['name' => 'edit_project', 'description' => 'Edit project details', 'category' => 'projects'],
            ['name' => 'delete_project', 'description' => 'Delete projects', 'category' => 'projects'],
            ['name' => 'view_projects', 'description' => 'View project list', 'category' => 'projects'],
            ['name' => 'assign_project', 'description' => 'Assign projects to users', 'category' => 'projects'],

            // Modules
            ['name' => 'create_module', 'description' => 'Create new modules', 'category' => 'modules'],
            ['name' => 'edit_module', 'description' => 'Edit module details', 'category' => 'modules'],
            ['name' => 'delete_module', 'description' => 'Delete modules', 'category' => 'modules'],
            ['name' => 'view_modules', 'description' => 'View module list', 'category' => 'modules'],

            // Assets
            ['name' => 'create_asset', 'description' => 'Create new assets', 'category' => 'assets'],
            ['name' => 'edit_asset', 'description' => 'Edit asset details', 'category' => 'assets'],
            ['name' => 'delete_asset', 'description' => 'Delete assets', 'category' => 'assets'],
            ['name' => 'view_assets', 'description' => 'View asset list', 'category' => 'assets'],

            // Patch Cycles
            ['name' => 'create_patch_cycle', 'description' => 'Create patch cycles', 'category' => 'patch_cycles'],
            ['name' => 'edit_patch_cycle', 'description' => 'Edit patch cycles', 'category' => 'patch_cycles'],
            ['name' => 'delete_patch_cycle', 'description' => 'Delete patch cycles', 'category' => 'patch_cycles'],
            ['name' => 'view_patch_cycles', 'description' => 'View patch cycles', 'category' => 'patch_cycles'],
            ['name' => 'approve_patch_cycle', 'description' => 'Approve patch cycles', 'category' => 'patch_cycles'],

            // Vulnerabilities
            ['name' => 'create_vulnerability', 'description' => 'Create vulnerabilities', 'category' => 'vulnerabilities'],
            ['name' => 'edit_vulnerability', 'description' => 'Edit vulnerabilities', 'category' => 'vulnerabilities'],
            ['name' => 'delete_vulnerability', 'description' => 'Delete vulnerabilities', 'category' => 'vulnerabilities'],
            ['name' => 'view_vulnerabilities', 'description' => 'View vulnerabilities', 'category' => 'vulnerabilities'],
            ['name' => 'assign_vulnerability', 'description' => 'Assign vulnerabilities', 'category' => 'vulnerabilities'],

            // Reports
            ['name' => 'create_report', 'description' => 'Create reports', 'category' => 'reports'],
            ['name' => 'view_reports', 'description' => 'View reports', 'category' => 'reports'],
            ['name' => 'delete_report', 'description' => 'Delete reports', 'category' => 'reports'],
            ['name' => 'export_reports', 'description' => 'Export reports', 'category' => 'reports'],

            // System
            ['name' => 'export_data', 'description' => 'Export system data', 'category' => 'system'],
            ['name' => 'view_audit_logs', 'description' => 'View audit logs', 'category' => 'system'],
            ['name' => 'manage_integrations', 'description' => 'Manage integrations', 'category' => 'system'],
            ['name' => 'system_settings', 'description' => 'Access system settings', 'category' => 'system'],
        ];

        $created = 0;
        $skipped = 0;

        foreach ($permissions as $permData) {
            $existing = $this->permissionRepository->findOneBy(['name' => $permData['name']]);
            if ($existing) {
                $skipped++;
                continue;
            }

            $permission = new Permission();
            $permission->setName($permData['name']);
            $permission->setDescription($permData['description']);
            $permission->setCategory($permData['category']);

            $this->entityManager->persist($permission);
            $created++;
        }

        $this->entityManager->flush();

        $io->success(sprintf('Created %d permissions, skipped %d existing', $created, $skipped));

        return Command::SUCCESS;
    }
}
