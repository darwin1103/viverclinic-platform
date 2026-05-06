<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {


        Role::firstOrCreate(['name' => 'SUPER_ADMIN']);
        $ownerRole = Role::firstOrCreate(['name' => 'OWNER']);
        $adminRole = Role::firstOrCreate(['name' => 'ADMIN']);
        $employeeRole = Role::firstOrCreate(['name' => 'EMPLOYEE']);
        $patientRole = Role::firstOrCreate(['name' => 'PATIENT']);
        $salesRole = Role::firstOrCreate(['name' => 'SALES']);

        $salesPermissions = [
            'admin_dashboard',
            'ver_pacientes',
            'crear_citas',
        ];

        foreach ($salesPermissions as $permission) {
            $salesRole->givePermissionTo(Permission::firstOrCreate(['name' => $permission]));
        }

        $ownerPermissions = [
            'admin_dashboard',
            'ver_pacientes',
            'crear_citas',
            'ver_inventario',
            'ver_reportes',
            'ver_contabilidad',
            'ver_referidos',
            'ver_promociones',
            'admin_dashboard_role_management',
            'admin_dashboard_user_management',
            'admin_dashboard_branch_management',
            'admin_dashboard_treatment_management',
        ];

        foreach ($ownerPermissions as $permission) {
            $ownerRole->givePermissionTo(Permission::firstOrCreate(['name' => $permission]));
        }

        $adminPermissions = [
            'admin_dashboard',
            'ver_pacientes',
            'crear_citas',
            'ver_inventario',
            'admin_dashboard_role_management',
            'admin_dashboard_user_management',
            'admin_dashboard_branch_management',
            'admin_dashboard_treatment_management',
        ];

        foreach ($adminPermissions as $permission) {
            $adminRole->givePermissionTo(Permission::firstOrCreate(['name' => $permission]));
        }

        $employeeRole->givePermissionTo(Permission::firstOrCreate(['name' => 'employee_dashboard']));
        $employeeRole->givePermissionTo(Permission::firstOrCreate(['name' => 'employee_agenda_day_home_btn']));
        $employeeRole->givePermissionTo(Permission::firstOrCreate(['name' => 'employee_agenda_new_home_btn']));
        $employeeRole->givePermissionTo(Permission::firstOrCreate(['name' => 'employee_job_training_home_btn']));
        $employeeRole->givePermissionTo(Permission::firstOrCreate(['name' => 'employee_promotions_home_btn']));

        $patientRole->givePermissionTo(Permission::firstOrCreate(['name' => 'patient_dashboard']));
        $patientRole->givePermissionTo(Permission::firstOrCreate(['name' => 'patient_medical_record_home_btn']));
        $patientRole->givePermissionTo(Permission::firstOrCreate(['name' => 'patient_qualify_staff_home_btn']));
        $patientRole->givePermissionTo(Permission::firstOrCreate(['name' => 'patient_treatment_home_btn']));
        $patientRole->givePermissionTo(Permission::firstOrCreate(['name' => 'patient_care_tips_home_btn']));
        $patientRole->givePermissionTo(Permission::firstOrCreate(['name' => 'patient_buy_package_home_btn']));
        $patientRole->givePermissionTo(Permission::firstOrCreate(['name' => 'patient_virtual_wallet_home_btn']));
        $patientRole->givePermissionTo(Permission::firstOrCreate(['name' => 'patient_promotions_home_btn']));
        $patientRole->givePermissionTo(Permission::firstOrCreate(['name' => 'patient_recomentations_home_btn']));
        $patientRole->givePermissionTo(Permission::firstOrCreate(['name' => 'patient_referrals_home_btn']));
        $patientRole->givePermissionTo(Permission::firstOrCreate(['name' => 'patient_schedule_appointment_home_btn']));
        $patientRole->givePermissionTo(Permission::firstOrCreate(['name' => 'patient_cancel_appointment_home_btn']));

    }

}
