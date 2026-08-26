<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(['email' => 'admin@akkagyz.kz'], [
            'name' => 'Админ Данияр',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        $sellers = [
            [
                'email' => 'papercostore@akkagyz.kz',
                'name' => 'PaperCo Store',
                'store_name' => 'PaperCo Store',
                'store_status' => 'approved',
                'store_description' => 'Канцтовары премиум-класса для офиса и учёбы.',
            ],
            [
                'email' => 'ecocraft@akkagyz.kz',
                'name' => 'EcoCraft Мастерская',
                'store_name' => 'EcoCraft Мастерская',
                'store_status' => 'approved',
                'store_description' => 'Эко-упаковка и крафтовые изделия ручной работы.',
            ],
            [
                'email' => 'artline@akkagyz.kz',
                'name' => 'ArtLine Store',
                'store_name' => 'ArtLine Store',
                'store_status' => 'approved',
                'store_description' => 'Товары для творчества, скетчинга и рисования.',
            ],
            [
                'email' => 'notecraft@akkagyz.kz',
                'name' => 'NoteCraft KZ',
                'store_name' => 'NoteCraft KZ',
                'store_status' => 'pending',
                'store_description' => 'Новый продавец: тетради и блокноты ручной сшивки.',
            ],
        ];

        foreach ($sellers as $seller) {
            User::updateOrCreate(['email' => $seller['email']], [
                'name' => $seller['name'],
                'password' => Hash::make('password'),
                'role' => 'seller',
                'store_name' => $seller['store_name'],
                'store_status' => $seller['store_status'],
                'store_description' => $seller['store_description'],
                'store_approved_at' => $seller['store_status'] === 'approved' ? now() : null,
                'email_verified_at' => now(),
            ]);
        }

        $customers = [
            ['name' => 'Айгерим К.', 'email' => 'aigerim@akkagyz.kz'],
            ['name' => 'Нурлан Б.', 'email' => 'nurlan@akkagyz.kz'],
            ['name' => 'Алия С.', 'email' => 'aliya@akkagyz.kz'],
        ];

        foreach ($customers as $customer) {
            User::updateOrCreate(['email' => $customer['email']], [
                'name' => $customer['name'],
                'password' => Hash::make('password'),
                'role' => 'customer',
                'email_verified_at' => now(),
            ]);
        }
    }
}
