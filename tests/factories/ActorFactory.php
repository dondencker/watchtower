<?php
    /** @var Closure $factory */

    $factory('spec\stubs\Actor', []);

    $factory('Dencker\Watchtower\Models\Role','role', [
        'name'=>$faker->word,
        'code'=>$faker->word,
        'is_super_user'=>false,
    ]);

    $factory('Dencker\Watchtower\Models\Role','superuser_role', [
        'name'=>$faker->word,
        'code'=>$faker->word,
        'is_super_user'=>true
    ]);

    $factory('Dencker\Watchtower\Models\Permission','permission', [
        'name'=>$faker->word,
        'code'=>$faker->word,
    ]);