<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superAdmin = 'super-admin';
        $admin = 'admin';
        $support = 'support';
        $user = 'user';
        $marketer = 'marketer';
        $manager = 'manager';
        $artist = 'artist';

        $globalPermissions = [
            'update-profile',
            'delete-account',
            'upload-avatar',
            'index-portfolio',
            'view-portfolio',
            'index-profile',
            'view-profile',
        ];

        $userPermissions = [
            'like-portfolio',
            'save-portfolio',
            'view-artist-portfolio',
        ];

        $artistPermissions = [
            'buy-plan',
            'buy-coin',
            'update-auth-info',
            'update-business-info',
            'upload-business-logo',
            'choose-business-location',
            'create-portfolio',
            'update-portfolio',
            'delete-portfolio',
            'activate-portfolio',
            'deactivate-portfolio',
            'index-portfolio-statistics',
            'laddering-portfolio',
            'populate-profile',
            'laddering-profile',
        ];

        $marketerPermissions = [
            'index-refer-link',
            'index-refer-banner',
            'index-financial-report',
            'index-referred-user',
            'index-top-marketer',
            'view-marketing-rate',
            'update-bank-details',
            'withdrawal',
            'can-request-manager',
        ];

        $managerPermissions = [
            'index-referred-user-financial-report',
        ];

        $adminPermissions = [
            'index-service',
            'create-service',
            'update-service',
            'view-service',
            'delete-service',
            'activate-service',
            'deactivate-service',
            'index-main-slider',
            'create-main-slider',
            'update-main-slider',
            'view-main-slider',
            'delete-main-slider',
            'activate-main-slider',
            'deactivate-main-slider',
            'index-portfolio',
            'create-portfolio',
            'update-portfolio',
            'view-portfolio',
            'delete-portfolio',
            'activate-portfolio',
            'deactivate-portfolio',
            'accept-portfolio',
            'reject-portfolio',
            'index-user',
            'create-user',
            'update-user',
            'view-user',
            'delete-user',
            'activate-user',
            'deactivate-user',
            'change-user-role-permission',
            'accept-withdrawal',
            'reject-withdrawal',
            'increase-marketer-wallet-balance',
            'decrease-marketer-wallet-balance',
            'index-top-marketer',
            'index-marketing-banner',
            'upload-marketing-banner',
            'update-marketing-banner',
            'delete-marketing-banner',
            'view-marketing-banner',
            'index-plan',
            'update-plan',
        ];

        $supportPermissions = [
            'index-ticket',
            'create-ticket',
            'update-ticket',
            'view-ticket',
            'delete-ticket',
            'answer-ticket',
            'close-ticket',
        ];

        $superAdminPermissions = [
            'index-admin',
            'create-admin',
            'update-admin',
            'view-admin',
            'delete-admin',
            'activate-admin',
            'deactivate-admin',
        ];

        foreach ([...$globalPermissions, ...$userPermissions,
                     ...$artistPermissions, ...$marketerPermissions,
                     ...$managerPermissions, ...$adminPermissions,
                     ...$supportPermissions, ...$superAdminPermissions] as $p) {
            if (Permission::query()->where('name', $p)->count() == 0) {
                Permission::create(['name' => $p]);
            }
        }

        /** @var Role $superAdminRole */
        $superAdminRole = Role::create(['name' => $superAdmin]);
        /** @var Role $adminRole */
        $adminRole = Role::create(['name' => $admin]);
        /** @var Role $supportRole */
        $supportRole = Role::create(['name' => $support]);
        /** @var Role $userRole */
        $userRole = Role::create(['name' => $user]);
        /** @var Role $marketerRole */
        $marketerRole = Role::create(['name' => $marketer]);
        /** @var Role $managerRole */
        $managerRole = Role::create(['name' => $manager]);
        /** @var Role $artistRole */
        $artistRole = Role::create(['name' => $artist]);

        $superAdminRole->syncPermissions(
            ...$superAdminPermissions
        );

        $adminRole->syncPermissions(
            ...$adminPermissions,
        );

        $supportRole->syncPermissions(
            ...$supportPermissions,
        );

        $userRole->syncPermissions(
            ...$globalPermissions,
            ...$userPermissions,
        );

        $marketerRole->syncPermissions(
            ...$marketerPermissions,
        );

        $managerRole->syncPermissions(
            ...$managerPermissions,
        );

        $artistRole->syncPermissions(
            ...$artistPermissions,
        );
    }
}
