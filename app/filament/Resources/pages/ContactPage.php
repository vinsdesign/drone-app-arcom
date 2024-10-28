<?php

namespace App\Filament\Pages;

use App\Filament\Resources\ContactResource;
use Filament\Resources\Pages\Page;

class ContactPage extends Page
{
    // protected static ?string $title = 'Contact';
    protected static ?string $modelLabel = 'Contact';
    protected static string $resource = ContactResource::class;
    protected static string $view = 'filament.contact-resource.pages.contact-page';
}
