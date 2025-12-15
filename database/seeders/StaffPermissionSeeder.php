<?php

namespace Database\Seeders;

use App\Models\StaffPermission;
use Illuminate\Database\Seeder;

class StaffPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    // php artisan db:seed --class=PermissionSeeder
    public function run(): void
    {
        $permissions = [
            "sale"             => [
                "view sale",
                "add sale",
                "edit sale",
                "print sale invoice",
                "print pos sale invoice",
                "download sale invoice",
                "view sale payment",
            ],
            "purchase"         => [
                "view purchase",
                "add purchase",
                "edit purchase",
                "update purchase status",
                "print purchase invoice",
                "download purchase invoice",
                "add purchase payment",
                "view purchase payment",
            ],
            "expense"          => [
                "view expense",
                "add expense",
                "edit expense",
                "trash expense",
            ],
            "expense category" => [
                "view expense category",
                "add expense category",
                "edit expense category",
                "trash expense category",
            ],
            "product"          => [
                "view product",
                "add product",
                "edit product",
                "print product barcode",
                "trash product",
            ],
            "category"         => [
                "view category",
                "add category",
                "edit category",
                "trash category",
            ],
            "brand"            => [
                "view brand",
                "add brand",
                "edit brand",
                "trash brand",
            ],
            "unit"             => [
                "view unit",
                "add unit",
                "edit unit",
                "trash unit",
            ],
            "attribute"        => [
                "view attribute",
                "add attribute",
                "edit attribute",
                "trash attribute",
            ],
            "variant"          => [
                "view variant",
                "add variant",
                "edit variant",
                "trash variant",
            ],
            "stock_transfer"   => [
                "view stock transfer",
                "add stock transfer",
                "edit stock transfer",
            ],
            "report"           => [
                "view sale report",
                "view purchase report",
                "view expense report",
                "view stock report",
                "view profit loss report",
            ],
            "warehouse"        => [
                "view warehouse",
                "add warehouse",
                "edit warehouse",
                "trash warehouse",
            ],
            "tax"              => [
                "view tax",
                "add tax",
                "edit tax",
                "trash tax",
            ],
            "coupon"           => [
                "view coupon",
                "add coupon",
                "edit coupon",
                "trash coupon",
            ],
            "payment type"     => [
                "view payment type",
                "add payment type",
                "edit payment type",
                "trash payment type",
            ],
            "payment account"  => [
                "view payment account",
                "add payment account",
                "edit payment account",
                "adjust payment account balance",
                "trash payment account",
            ],
            "customer"         => [
                "view customer",
                "add customer",
                "edit customer",
                "trash customer",
            ],
            "supplier"         => [
                "view supplier",
                "add supplier",
                "edit supplier",
                "trash supplier",
            ],
            "admin"            => [
                "view admin",
                "add admin",
                "edit admin",
            ],
            "role"             => [
                "view role",
                "add role",
                "edit role",
                "assign permission",
            ],
            "setting"          => [
                "general setting",
                "prefix setting",
                "company setting",
                "brand setting",
                "system configuration",
                "notification setting",
            ],
            "other"            => [
                "view dashboard",
                "view transaction",
                "manage extension",
                "manage language",
                "application information",
            ],
            "company"          => [
                "view company",
                "add company",
                "edit company",
                "trash company",
            ],
            "department"       => [
                "view department",
                "add department",
                "edit department",
                "trash department",
            ],
            "designation"      => [
                "view designation",
                "add designation",
                "edit designation",
                "trash designation",
            ],
            "shift"            => [
                "view shift",
                "add shift",
                "edit shift",
                "trash shift",
            ],
            "employee"         => [
                "view employee",
                "add employee",
                "edit employee",
                "trash employee",
            ],
            "attendance"       => [
                "view attendance",
                "add attendance",
                "edit attendance",
                "trash attendance",
            ],
            "leave request"    => [
                "view leave request",
                "add leave request",
                "edit leave request",
                "trash leave request",
            ],
            "leave type"       => [
                "view leave type",
                "add leave type",
                "edit leave type",
                "trash leave type",
            ],
            "holiday"          => [
                "view holiday",
                "add holiday",
                "edit holiday",
                "trash holiday",
            ],
            "payroll"          => [
                "view payroll",
                "add payroll",
                "edit payroll",
                "trash payroll",
            ],
            "user"             => [
                "view user",
                "update user",
            ],
        ];

        foreach ($permissions as $k => $permission) {
            foreach ($permission as $item) {
                $exists = StaffPermission::where("name", $item)->where('group_name', $k)->exists();
                if ($exists) {
                    continue;
                }

                $permission             = new StaffPermission();
                $permission->name       = $item;
                $permission->group_name = $k;
                $permission->guard_name = "web";
                $permission->save();
            }
        }

    }
}
