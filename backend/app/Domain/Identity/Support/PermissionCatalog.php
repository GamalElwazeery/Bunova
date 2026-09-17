<?php

namespace App\Domain\Identity\Support;

use App\Domain\Identity\Models\Organization;
use App\Domain\Identity\Models\Permission;
use App\Domain\Identity\Models\Role;

class PermissionCatalog
{
    // Orders
    public const ORDERS_VIEW = 'orders.view';
    public const ORDERS_CREATE = 'orders.create';
    public const ORDERS_UPDATE = 'orders.update';
    public const ORDERS_CANCEL = 'orders.cancel';
    public const ORDERS_DISCOUNT = 'orders.discount';
    public const ORDERS_VOID_ITEM = 'orders.void_item';

    // Billing & Payments
    public const BILLS_VIEW = 'bills.view';
    public const BILLS_CREATE = 'bills.create';
    public const BILLS_FINALIZE = 'bills.finalize';
    public const BILLS_VOID = 'bills.void';
    public const BILLS_REOPEN = 'bills.reopen';
    public const BILLS_DISCOUNT = 'bills.discount';
    public const PAYMENTS_PROCESS = 'payments.process';
    public const PAYMENTS_REFUND = 'payments.refund';

    // Cash Shifts & Drawer
    public const CASH_OPEN_SHIFT = 'cash.open_shift';
    public const CASH_CLOSE_SHIFT = 'cash.close_shift';
    public const CASH_DROP = 'cash.drop';
    public const CASH_AUDIT = 'cash.audit';

    // Catalog & Inventory
    public const CATALOG_VIEW = 'catalog.view';
    public const CATALOG_MANAGE = 'catalog.manage';

    // Tables & Venue
    public const TABLES_VIEW = 'tables.view';
    public const TABLES_MANAGE = 'tables.manage';
    public const TABLES_OVERRIDE = 'tables.override';

    // Staff & Reports
    public const STAFF_VIEW = 'staff.view';
    public const STAFF_MANAGE = 'staff.manage';
    public const REPORTS_VIEW = 'reports.view';

    // Platform (Restricted to platform operators only)
    public const PLATFORM_ADMIN = 'platform.admin';
    public const PLATFORM_TENANTS = 'platform.tenants';
    public const PLATFORM_BILLING = 'platform.billing';

    /**
     * All permission definitions with their module and human-readable name.
     *
     * @return array<string, array{name: string, module: string}>
     */
    public static function definitions(): array
    {
        return [
            self::ORDERS_VIEW => ['name' => 'View Orders', 'module' => 'orders'],
            self::ORDERS_CREATE => ['name' => 'Create Orders', 'module' => 'orders'],
            self::ORDERS_UPDATE => ['name' => 'Update Orders', 'module' => 'orders'],
            self::ORDERS_CANCEL => ['name' => 'Cancel Orders', 'module' => 'orders'],
            self::ORDERS_DISCOUNT => ['name' => 'Apply Order Discount', 'module' => 'orders'],
            self::ORDERS_VOID_ITEM => ['name' => 'Void Order Item', 'module' => 'orders'],

            self::BILLS_VIEW => ['name' => 'View Bills', 'module' => 'billing'],
            self::BILLS_CREATE => ['name' => 'Create Bills', 'module' => 'billing'],
            self::BILLS_FINALIZE => ['name' => 'Finalize Bills', 'module' => 'billing'],
            self::BILLS_VOID => ['name' => 'Void Bills', 'module' => 'billing'],
            self::BILLS_REOPEN => ['name' => 'Reopen Bills', 'module' => 'billing'],
            self::BILLS_DISCOUNT => ['name' => 'Apply Bill Discount', 'module' => 'billing'],
            self::PAYMENTS_PROCESS => ['name' => 'Process Payments', 'module' => 'payments'],
            self::PAYMENTS_REFUND => ['name' => 'Refund Payments', 'module' => 'payments'],

            self::CASH_OPEN_SHIFT => ['name' => 'Open Cash Shift', 'module' => 'cash'],
            self::CASH_CLOSE_SHIFT => ['name' => 'Close Cash Shift', 'module' => 'cash'],
            self::CASH_DROP => ['name' => 'Cash Drop / Pay-out', 'module' => 'cash'],
            self::CASH_AUDIT => ['name' => 'Audit Cash Drawer', 'module' => 'cash'],

            self::CATALOG_VIEW => ['name' => 'View Catalog', 'module' => 'catalog'],
            self::CATALOG_MANAGE => ['name' => 'Manage Catalog', 'module' => 'catalog'],

            self::TABLES_VIEW => ['name' => 'View Tables', 'module' => 'tables'],
            self::TABLES_MANAGE => ['name' => 'Manage Tables', 'module' => 'tables'],
            self::TABLES_OVERRIDE => ['name' => 'Override Table State', 'module' => 'tables'],

            self::STAFF_VIEW => ['name' => 'View Staff', 'module' => 'staff'],
            self::STAFF_MANAGE => ['name' => 'Manage Staff & Roles', 'module' => 'staff'],
            self::REPORTS_VIEW => ['name' => 'View Reports', 'module' => 'reports'],

            self::PLATFORM_ADMIN => ['name' => 'Platform Administration', 'module' => 'platform'],
            self::PLATFORM_TENANTS => ['name' => 'Manage Platform Tenants', 'module' => 'platform'],
            self::PLATFORM_BILLING => ['name' => 'Platform Commercial Billing', 'module' => 'platform'],
        ];
    }

    /**
     * Seed all defined permissions into database.
     */
    public static function seedPermissions(): void
    {
        foreach (self::definitions() as $id => $meta) {
            Permission::updateOrCreate(
                ['id' => $id],
                ['name' => $meta['name'], 'module' => $meta['module']]
            );
        }
    }

    /**
     * Baseline role templates for a café organization.
     *
     * @return array<string, array{display_name: string, scope_type: string, permissions: list<string>}>
     */
    public static function roleTemplates(): array
    {
        return [
            'owner' => [
                'display_name' => 'Café Owner',
                'scope_type' => 'organization',
                'permissions' => [
                    self::ORDERS_VIEW, self::ORDERS_CREATE, self::ORDERS_UPDATE, self::ORDERS_CANCEL, self::ORDERS_DISCOUNT, self::ORDERS_VOID_ITEM,
                    self::BILLS_VIEW, self::BILLS_CREATE, self::BILLS_FINALIZE, self::BILLS_VOID, self::BILLS_REOPEN, self::BILLS_DISCOUNT,
                    self::PAYMENTS_PROCESS, self::PAYMENTS_REFUND,
                    self::CASH_OPEN_SHIFT, self::CASH_CLOSE_SHIFT, self::CASH_DROP, self::CASH_AUDIT,
                    self::CATALOG_VIEW, self::CATALOG_MANAGE,
                    self::TABLES_VIEW, self::TABLES_MANAGE, self::TABLES_OVERRIDE,
                    self::STAFF_VIEW, self::STAFF_MANAGE, self::REPORTS_VIEW,
                ],
            ],
            'branch_manager' => [
                'display_name' => 'Branch Manager',
                'scope_type' => 'branch',
                'permissions' => [
                    self::ORDERS_VIEW, self::ORDERS_CREATE, self::ORDERS_UPDATE, self::ORDERS_CANCEL, self::ORDERS_DISCOUNT, self::ORDERS_VOID_ITEM,
                    self::BILLS_VIEW, self::BILLS_CREATE, self::BILLS_FINALIZE, self::BILLS_VOID, self::BILLS_REOPEN, self::BILLS_DISCOUNT,
                    self::PAYMENTS_PROCESS, self::PAYMENTS_REFUND,
                    self::CASH_OPEN_SHIFT, self::CASH_CLOSE_SHIFT, self::CASH_DROP, self::CASH_AUDIT,
                    self::CATALOG_VIEW,
                    self::TABLES_VIEW, self::TABLES_MANAGE, self::TABLES_OVERRIDE,
                    self::STAFF_VIEW, self::REPORTS_VIEW,
                ],
            ],
            'cashier' => [
                'display_name' => 'Cashier',
                'scope_type' => 'branch',
                'permissions' => [
                    self::ORDERS_VIEW, self::ORDERS_CREATE, self::ORDERS_UPDATE,
                    self::BILLS_VIEW, self::BILLS_CREATE, self::BILLS_FINALIZE,
                    self::PAYMENTS_PROCESS,
                    self::CASH_OPEN_SHIFT, self::CASH_CLOSE_SHIFT,
                    self::CATALOG_VIEW,
                    self::TABLES_VIEW,
                ],
            ],
            'waiter' => [
                'display_name' => 'Waiter / Service Staff',
                'scope_type' => 'branch',
                'permissions' => [
                    self::ORDERS_VIEW, self::ORDERS_CREATE, self::ORDERS_UPDATE,
                    self::BILLS_VIEW,
                    self::CATALOG_VIEW,
                    self::TABLES_VIEW,
                ],
            ],
        ];
    }

    /**
     * Create baseline roles for an organization.
     *
     * @return array<string, Role>
     */
    public static function seedOrganizationRoles(Organization $org): array
    {
        self::seedPermissions();
        $created = [];

        foreach (self::roleTemplates() as $roleName => $data) {
            $role = Role::updateOrCreate(
                [
                    'organization_id' => $org->id,
                    'name' => $roleName,
                ],
                [
                    'display_name' => $data['display_name'],
                    'scope_type' => $data['scope_type'],
                    'is_system' => true,
                ]
            );

            // Sync permissions
            $role->permissions()->sync($data['permissions']);
            $created[$roleName] = $role;
        }

        return $created;
    }
}
