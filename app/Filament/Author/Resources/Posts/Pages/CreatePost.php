<?php

namespace App\Filament\Author\Resources\Posts\Pages;

use App\Filament\Author\Resources\Posts\PostResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePost extends CreateRecord
{
    protected static string $resource = PostResource::class;
}
