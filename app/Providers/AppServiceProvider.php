<?php

namespace App\Providers;

use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use Filament\Pages\Page;
use Filament\Resources\Resource;
use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        FilamentShield::buildPermissionKeyUsing(
            function (string $entity, string $affix, string $subject, string $case, string $separator) {
                return match (true) {
                    # if `configurePermissionIdentifierUsing()` was used previously, then this needs to be adjusted accordingly
                    is_subclass_of($entity, Resource::class) => Str::of($affix)
                        ->snake()
                        ->append('_')
                        ->append(
                            Str::of($entity)
                                ->afterLast('\\')
                                ->beforeLast('Resource')
                                ->replace('\\', '')
                                ->snake()
                                ->replace('_', '::')
                        )
                        ->toString(),
                    is_subclass_of($entity, Page::class) => Str::of('page_')
                        ->append(class_basename($entity))
                        ->toString(),
                    is_subclass_of($entity, Widget::class) => Str::of('widget_')
                        ->append(class_basename($entity))
                        ->toString()
                };
            }
        );

        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        Model::unguard();
    }
}
