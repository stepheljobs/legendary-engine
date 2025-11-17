<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permissions
        $permissions = [
            // Member management permissions
            [
                'name' => 'view-members',
                'display_name' => 'View Team Members',
                'description' => 'View the list of team members',
                'category' => 'members',
            ],
            [
                'name' => 'invite-members',
                'display_name' => 'Invite Members',
                'description' => 'Send invitations to new team members',
                'category' => 'members',
            ],
            [
                'name' => 'manage-members',
                'display_name' => 'Manage Members',
                'description' => 'Edit and remove team members',
                'category' => 'members',
            ],
            // Settings permissions
            [
                'name' => 'view-settings',
                'display_name' => 'View Settings',
                'description' => 'View organization settings',
                'category' => 'settings',
            ],
            [
                'name' => 'manage-settings',
                'display_name' => 'Manage Settings',
                'description' => 'Edit organization settings',
                'category' => 'settings',
            ],
            // Billing permissions
            [
                'name' => 'view-billing',
                'display_name' => 'View Billing',
                'description' => 'View billing information and invoices',
                'category' => 'billing',
            ],
            [
                'name' => 'manage-billing',
                'display_name' => 'Manage Billing',
                'description' => 'Manage billing, subscriptions, and payment methods',
                'category' => 'billing',
            ],
        ];

        $createdPermissions = [];
        foreach ($permissions as $permissionData) {
            $createdPermissions[$permissionData['name']] = Permission::create($permissionData);
        }

        // Create roles
        $roles = [
            [
                'name' => 'owner',
                'display_name' => 'Owner',
                'description' => 'Full access to all features and settings',
                'permissions' => [
                    'view-members',
                    'invite-members',
                    'manage-members',
                    'view-settings',
                    'manage-settings',
                    'view-billing',
                    'manage-billing',
                ],
            ],
            [
                'name' => 'admin',
                'display_name' => 'Administrator',
                'description' => 'Manage team members and most settings',
                'permissions' => [
                    'view-members',
                    'invite-members',
                    'manage-members',
                    'view-settings',
                    'manage-settings',
                    'view-billing',
                ],
            ],
            [
                'name' => 'member',
                'display_name' => 'Member',
                'description' => 'Basic access with limited permissions',
                'permissions' => [
                    'view-members',
                    'view-settings',
                ],
            ],
        ];

        foreach ($roles as $roleData) {
            $permissions = $roleData['permissions'];
            unset($roleData['permissions']);

            $role = Role::create($roleData);

            // Attach permissions to role
            foreach ($permissions as $permissionName) {
                if (isset($createdPermissions[$permissionName])) {
                    $role->givePermission($createdPermissions[$permissionName]);
                }
            }
        }

        if ($this->command) {
            $this->command->info('Roles and permissions seeded successfully!');
        }
    }
}
