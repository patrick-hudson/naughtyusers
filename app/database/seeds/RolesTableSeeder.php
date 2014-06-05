<?php

class RolesTableSeeder extends Seeder {

    public function run()
    {
        DB::table('roles')->delete();

        $adminRole = new Role;
        $adminRole->name = 'admin';
        $adminRole->save();

        $user = User::where('username','=','support')->first();
        $user->roles()->attach( $user->id );

    }

}
