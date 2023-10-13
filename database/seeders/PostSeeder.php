<?php

namespace Database\Seeders;

use App\Models\Post;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Post::create([
            'title' => 'First Post',
            'body' => 'The Content of First Post.',
            'user_id' => '1'
        ]);
        Post::create([
            'title' => 'Second Post',
            'body' => 'The Content of Second Post.',
            'user_id' => '2'
        ]);
        Post::create([
            'title' => 'Third Post',
            'body' => 'The Content of Third Post.',
            'user_id' => '1'
        ]);
    }
}
