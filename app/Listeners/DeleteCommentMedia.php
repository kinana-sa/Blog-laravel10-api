<?php

namespace App\Listeners;

use App\Events\CommentDeleting;
use App\Http\Controllers\Api\Traits\FileUploadTrait;

class DeleteCommentMedia
{
    use FileUploadTrait;
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(CommentDeleting $event): void
    {
        $comment = $event->comment;
        $image = $comment->image;
        $video = $comment->video;

        if ($image) {
            $this->deleteFile($image->url);
            $image->delete();
        }
        if ($video) {
            $this->deleteFile($video->url);
            $video->delete();
        }
    }
}
