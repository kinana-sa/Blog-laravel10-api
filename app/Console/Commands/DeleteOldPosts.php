<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Post;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class DeleteOldPosts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:delete-old-posts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete posts that are soft deleted 7 days ago and associated Media.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $posts = Post::onlyTrashed()->where('deleted_at','<',Carbon::now()->subDays(7))->get();

        foreach($posts as $post)
        {
            $images = $post->images;
            $videos = $post->videos;
    
            if ($images) {
                foreach($images as $image)
                {
                File::delete(public_path($image->url));
                $image->delete();
                }
            }
            if ($videos) {
                foreach($videos as $video)
                {
                File::delete(public_path($video->url));
                $video->delete();
                }
            }
            $post->forceDelete();
        }        
        $this->info('Old post have been deleted succussfully.');
    }
}
