<?php

namespace App\Filament\Pages;

use App\Filament\Resources\ContactResource;
use Filament\Resources\Pages\Page;
use App\Helpers\TranslationHelper;

class ContactPage extends Page
{
    // protected static ?string $title = 'Contact';
    protected static ?string $modelLabel = 'Contact';
    protected static string $resource = ContactResource::class;
    protected static string $view = 'filament.contact-resource.pages.contact-page';

    public function getHeading(): string
    {
        return TranslationHelper::translateIfNeeded('Contact');
        // return GoogleTranslate::trans('Contact', session('locale') ?? 'en');
    }
    public function getTitle(): string
    {
        return TranslationHelper::translateIfNeeded('Contact');
        // return GoogleTranslate::trans('Contact', session('locale') ?? 'en');
    }
}
