<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\WEB\RolesModel;
use App\Models\WEB\PermissionModel;
use App\Models\WEB\MenusModel;
use App\Models\WEB\UserHasRolesModel;
use App\Models\WEB\RoleHasPermissionsModel;
use Illuminate\Support\Str;
use DB;
use Carbon\Carbon;

class InitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $random =  Str::random(18);
        // dd($random);

        // Users
        DB::table('users')->insert(
            [
                "name" => "Web API",
                "email" => "webapi2@samoragroup.co.id",
                "remember_token" => $random,
                "password" => bcrypt($random),
                "username" => "web-api",
                "user_uid" => "36aa6ebb-4f3a-4a42-a656-5ed6ace7aa63",
                "created_at" => Carbon::now()
            ]
        );

        //Roles
        DB::table('roles')->insert(
            [
                "role_uid" => "933d8952-d4cd-4597-acb9-d8429b41f394",
                "role_name" => "SYS",
                "created_at" => Carbon::now()
            ]
        );

        // User Has Roles
        DB::table('user_has_roles')->insert(
            [
                "user_uid" => "36aa6ebb-4f3a-4a42-a656-5ed6ace7aa63",
                "role_uid" => "933d8952-d4cd-4597-acb9-d8429b41f394",
                "created_at" => Carbon::now()
            ]
        );

        // Menus
        DB::table('menus')->insert([
                  [
                    "created_at" =>  Carbon::now(),
                    "menus_uid" =>  "da274730-8289-4206-b6f8-088402e47b8c",
                    "menus_type" =>  "Pages",
                    "menus_name" =>  "Users",
                    "url" =>  "/users",
                    "acl_action" =>  "manage",
                    "acl_subject" =>  "users",
                    "icon" =>  "ph:user-circle-plus",
                    "level" =>  "1",
                    "parent_id" =>  "6a14e547-0d2d-4365-9541-99877b204956",
                    "order_by" =>  999
                  ],
                  [
                    "created_at" => Carbon::now(),
                    "menus_uid" =>  "6023dc9b-72dc-4fc6-85e5-5d21112f78e5",
                    "menus_type" =>  "Pages",
                    "menus_name" =>  "Menus",
                    "url" =>  "/menus",
                    "acl_action" =>  "manage",
                    "acl_subject" =>  "menus",
                    "icon" =>  "icon-park-outline:difference-set",
                    "level" =>  "1",
                    "parent_id" =>  "6a14e547-0d2d-4365-9541-99877b204956",
                    "order_by" =>  998
                  ],
                  [
                    "created_at" => Carbon::now(),
                    "menus_uid" =>  "b2610b53-8f8a-4598-af28-182d6f677814",
                    "menus_type" =>  "Pages",
                    "menus_name" =>  "Roles",
                    "url" =>  "/roles",
                    "acl_action" =>  "manage",
                    "acl_subject" =>  "roles",
                    "icon" =>  "eos-icons:role-binding-outlined",
                    "level" =>  "1",
                    "parent_id" =>  "6a14e547-0d2d-4365-9541-99877b204956",
                    "order_by" =>  997
                  ],
                  [
                    "created_at" => Carbon::now(),
                    "menus_uid" =>  "ec73346b-61c8-4b9f-a4a1-4bc5361aca0c",
                    "menus_type" =>  "Pages",
                    "menus_name" =>  "Permissions",
                    "url" =>  "/permissions",
                    "acl_action" =>  "manage",
                    "acl_subject" =>  "permissions",
                    "icon" =>  "gala:settings",
                    "level" =>  "1",
                    "parent_id" =>  "6a14e547-0d2d-4365-9541-99877b204956",
                    "order_by" =>  996
                  ],
                  [
                    "created_at" => Carbon::now(),
                    "menus_uid" =>  "1d861388-1c47-457b-8b8d-5fa67d842bce",
                    "menus_type" =>  "Pages",
                    "menus_name" =>  "Second Page",
                    "url" =>  "/second-page",
                    "acl_action" =>  "manage",
                    "acl_subject" =>  "second-page",
                    "icon" =>  "mdi:book-open-page-variant-outline",
                    "level" =>  "1",
                    "parent_id" =>  "b3fca053-a4ba-42fe-8b1d-fcf30f96ed72",
                    "order_by" =>  202
                  ],
                  [
                    "created_at" => Carbon::now(),
                    "menus_uid" =>  "0dd80df3-bcca-4d36-b11f-cac36b442c33",
                    "menus_type" =>  "Pages",
                    "menus_name" =>  "ACL Page",
                    "url" =>  "/acl",
                    "acl_action" =>  "manage",
                    "acl_subject" =>  "acl-page",
                    "icon" =>  "carbon:ibm-security-services",
                    "level" =>  "1",
                    "parent_id" =>  "b3fca053-a4ba-42fe-8b1d-fcf30f96ed72",
                    "order_by" =>  201
                  ],
                  [
                    "created_at" => Carbon::now(),
                    "menus_uid" =>  "b3fca053-a4ba-42fe-8b1d-fcf30f96ed72",
                    "menus_type" =>  "sectionTitle",
                    "menus_name" =>  "Apps & Page",
                    "url" =>  "#",
                    "acl_action" =>  "manage",
                    "acl_subject" =>  "apps-page",
                    "icon" => null,
                    "level" =>  "1",
                    "parent_id" => null,
                    "order_by" =>  200
                  ],
                  [
                    "created_at" => Carbon::now(),
                    "menus_uid" =>  "6a14e547-0d2d-4365-9541-99877b204956",
                    "menus_type" =>  "sectionTitle",
                    "menus_name" =>  "Configuration",
                    "url" =>  "#",
                    "acl_action" =>  "manage",
                    "acl_subject" =>  "configuration",
                    "icon" => null,
                    "level" =>  "1",
                    "parent_id" => null,
                    "order_by" =>  900
                  ],
                  [
                    "created_at" => Carbon::now(),
                    "menus_uid" =>  "e1da14eb-0554-4abc-ae78-7b32b5a3aaab",
                    "menus_type" =>  "sectionTitle",
                    "menus_name" =>  "Reports",
                    "url" =>  "#",
                    "acl_action" =>  "manage",
                    "acl_subject" =>  "reports",
                    "icon" => null,
                    "level" =>  "1",
                    "parent_id" => null,
                    "order_by" =>  300
                  ],
                  [
                    "created_at" =>  Carbon::now(),
                    "menus_uid" =>  "cc45ac17-c76f-4141-8a59-a6ce336eb7b9",
                    "menus_type" =>  "sectionTitle",
                    "menus_name" =>  "Dashboard",
                    "url" =>  "#",
                    "acl_action" =>  "manage",
                    "acl_subject" =>  "dashboard-root",
                    "icon" =>  "#",
                    "level" =>  "1",
                    "parent_id" => null,
                    "order_by" =>  2
                  ],
                  [
                    "created_at" =>  Carbon::now(),
                    "menus_uid" =>  "1c4bb083-7b0d-4ea1-8782-340673df07a1",
                    "menus_type" =>  "Pages",
                    "menus_name" =>  "Home",
                    "url" =>  "/home",
                    "acl_action" =>  "manage",
                    "acl_subject" =>  "home",
                    "icon" =>  "clarity:home-solid",
                    "level" =>  "1",
                    "parent_id" => null,
                    "order_by" =>  1
                  ]
        ]);

        //
        DB::table('permissions')->insert([
                  [
                    "permissions_uid" => "56da9d11-29ad-452e-9abe-207e67008861",
                    "permissions_name" => "apps-page",
                    "acl_action" => "manage",
                    "type" => "menu",
                    "parent_id" => null,
                    "created_at" =>  Carbon::now(),
                  ],
                  [
                    "permissions_uid" => "ebeb648a-d386-44a3-812b-e7125a3b0b22",
                    "permissions_name" => "acl-page",
                    "acl_action" => "manage",
                    "type" => "menu",
                    "parent_id" => null,
                    "created_at" =>  Carbon::now(),
                  ],
                  [
                    "permissions_uid" => "f1863945-9bd2-49a4-86ae-e24a52beb9fe",
                    "permissions_name" => "second-page",
                    "acl_action" => "manage",
                    "type" => "menu",
                    "parent_id" => null,
                    "created_at" =>  Carbon::now(),
                  ],
                  [
                    "permissions_uid" => "b521e9c7-e2e2-4ca8-8211-72dc3f5ec222",
                    "permissions_name" => "reports",
                    "acl_action" => "manage",
                    "type" => "menu",
                    "parent_id" => null,
                    "created_at" =>  Carbon::now(),
                  ],
                  [
                    "permissions_uid" => "d0c73159-88f8-4da3-b6a5-0c2e8f655fa3",
                    "permissions_name" => "configuration",
                    "acl_action" => "manage",
                    "type" => "menu",
                    "parent_id" => null,
                    "created_at" =>  Carbon::now(),
                  ],
                  [
                    "permissions_uid" => "23f1734f-1ddb-4dd8-9c0c-99131a26485b",
                    "permissions_name" => "permissions",
                    "acl_action" => "manage",
                    "type" => "menu",
                    "parent_id" => null,
                    "created_at" =>  Carbon::now(),
                  ],
                  [
                    "permissions_uid" => "8ba11fd9-3f68-4e6b-b0be-15e09565b301",
                    "permissions_name" => "roles",
                    "acl_action" => "manage",
                    "type" => "menu",
                    "parent_id" => null,
                    "created_at" =>  Carbon::now(),
                  ],
                  [
                    "permissions_uid" => "45cc3a46-4bc4-4e7b-83d6-e8778d0a40c9",
                    "permissions_name" => "menus",
                    "acl_action" => "manage",
                    "type" => "menu",
                    "parent_id" => null,
                    "created_at" =>  Carbon::now(),
                  ],
                  [
                    "permissions_uid" => "053f95d2-8713-4232-ba65-275dbd625233",
                    "permissions_name" => "users",
                    "acl_action" => "manage",
                    "type" => "menu",
                    "parent_id" => null,
                    "created_at" =>  Carbon::now(),
                  ],
                  [
                    "permissions_uid" => "4b74b593-f901-45a9-87f2-f4ff478029b1",
                    "permissions_name" => "home",
                    "acl_action" => "manage",
                    "type" => "menu",
                    "parent_id" => null,
                    "created_at" =>  Carbon::now(),
                  ],
                  [
                    "permissions_uid" => "e9d06d83-73da-4005-822a-9d4cb30f1540",
                    "permissions_name" => "menus-create",
                    "acl_action" => "manage",
                    "type" => null,
                    "parent_id" => "45cc3a46-4bc4-4e7b-83d6-e8778d0a40c9",
                    "created_at" =>  Carbon::now(),
                  ],
                  [
                    "permissions_uid" => "7c52128a-4c92-4f26-896f-19328ffb0a7a",
                    "permissions_name" => "role-set-permission",
                    "acl_action" => "manage",
                    "type" => null,
                    "parent_id" => "8ba11fd9-3f68-4e6b-b0be-15e09565b301",
                    "created_at" =>  Carbon::now(),
                  ],
                  [
                    "permissions_uid" => "8344e034-7279-4f30-b77a-d7c4fd84b3a5",
                    "permissions_name" => "user-edit",
                    "acl_action" => "manage",
                    "type" => null,
                    "parent_id" => "053f95d2-8713-4232-ba65-275dbd625233",
                    "created_at" =>  Carbon::now(),
                  ],
                  [
                    "permissions_uid" => "3be6d61b-9ae4-48f0-882f-feecebaed737",
                    "permissions_name" => "dashboard-root",
                    "acl_action" => "manage",
                    "type" => "menu",
                    "parent_id" => null,
                    "created_at" =>  Carbon::now(),
                  ],
                  [
                    "permissions_uid" => "a26044ad-8e43-4eba-bff1-8c8c11358795",
                    "permissions_name" => "user-create",
                    "acl_action" => "manage",
                    "type" => null,
                    "parent_id" => "053f95d2-8713-4232-ba65-275dbd625233",
                    "created_at" =>  Carbon::now(),
                  ]
        ]);

        // Roles Has Permissions
        DB::table('role_has_permissions')->insert([
                  [
                    "role_uid" => "933d8952-d4cd-4597-acb9-d8429b41f394",
                    "permission_uid" => "4b74b593-f901-45a9-87f2-f4ff478029b1",
                    "created_at" =>  Carbon::now(),
                  ],
                  [
                    "role_uid" => "933d8952-d4cd-4597-acb9-d8429b41f394",
                    "permission_uid" => "8344e034-7279-4f30-b77a-d7c4fd84b3a5",
                    "created_at" =>  Carbon::now(),
                  ],
                  [
                    "role_uid" => "933d8952-d4cd-4597-acb9-d8429b41f394",
                    "permission_uid" => "3be6d61b-9ae4-48f0-882f-feecebaed737",
                    "created_at" =>  Carbon::now(),
                  ],
                  [
                    "role_uid" => "933d8952-d4cd-4597-acb9-d8429b41f394",
                    "permission_uid" => "56da9d11-29ad-452e-9abe-207e67008861",
                    "created_at" =>  Carbon::now(),
                  ],
                  [
                    "role_uid" => "933d8952-d4cd-4597-acb9-d8429b41f394",
                    "permission_uid" => "ebeb648a-d386-44a3-812b-e7125a3b0b22",
                    "created_at" =>  Carbon::now(),
                  ],
                  [
                    "role_uid" => "933d8952-d4cd-4597-acb9-d8429b41f394",
                    "permission_uid" => "b521e9c7-e2e2-4ca8-8211-72dc3f5ec222",
                    "created_at" =>  Carbon::now(),
                  ],
                  [
                    "role_uid" => "933d8952-d4cd-4597-acb9-d8429b41f394",
                    "permission_uid" => "23f1734f-1ddb-4dd8-9c0c-99131a26485b",
                    "created_at" =>  Carbon::now(),
                  ],
                  [
                    "role_uid" => "933d8952-d4cd-4597-acb9-d8429b41f394",
                    "permission_uid" => "d0c73159-88f8-4da3-b6a5-0c2e8f655fa3",
                    "created_at" =>  Carbon::now(),
                  ],
                  [
                    "role_uid" => "933d8952-d4cd-4597-acb9-d8429b41f394",
                    "permission_uid" => "053f95d2-8713-4232-ba65-275dbd625233",
                    "created_at" =>  Carbon::now(),
                  ],
                  [
                    "role_uid" => "933d8952-d4cd-4597-acb9-d8429b41f394",
                    "permission_uid" => "45cc3a46-4bc4-4e7b-83d6-e8778d0a40c9",
                    "created_at" =>  Carbon::now(),
                  ],
                  [
                    "role_uid" => "933d8952-d4cd-4597-acb9-d8429b41f394",
                    "permission_uid" => "8ba11fd9-3f68-4e6b-b0be-15e09565b301",
                    "created_at" =>  Carbon::now(),
                  ],
                  [
                    "role_uid" => "933d8952-d4cd-4597-acb9-d8429b41f394",
                    "permission_uid" => "7c52128a-4c92-4f26-896f-19328ffb0a7a",
                    "created_at" =>  Carbon::now(),
                  ],
                  [
                    "role_uid" => "933d8952-d4cd-4597-acb9-d8429b41f394",
                    "permission_uid" => "e9d06d83-73da-4005-822a-9d4cb30f1540",
                    "created_at" =>  Carbon::now(),
                  ],
                  [
                    "role_uid" => "933d8952-d4cd-4597-acb9-d8429b41f394",
                    "permission_uid" => "a26044ad-8e43-4eba-bff1-8c8c11358795",
                    "created_at" =>  Carbon::now(),
                  ],
                  [
                    "role_uid" => "933d8952-d4cd-4597-acb9-d8429b41f394",
                    "permission_uid" => "f1863945-9bd2-49a4-86ae-e24a52beb9fe",
                    "created_at" =>  Carbon::now(),
                  ]
        ]);

    }
}
