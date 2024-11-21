<?php

namespace Kalpesh\RepoStructure;

use Illuminate\Support\ServiceProvider;
use Kalpesh\RepoStructure\Services\RepositoryStructure;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;

class RepoStructureProvider extends ServiceProvider
{
  /**
  * Register services.
  */
  public function register(): void
  {
    $this->commands([
      \Kalpesh\RepoStructure\Consoles\MakeRepoStructureCommand::class,
    ]);

    $this->app->bind('RepoStructure', function() {
      $setting = $this->app['config']->get('repo-sturcture');
      return new RepositoryStructure($setting);
    });
  }

  /**
  * Bootstrap services.
  */
  public function boot(): void
  {
    $this->publishes([
      __DIR__.'/Config/repo-sturcture.php' => config_path("repo-sturcture.php")
    ], "config");
    
    $this->publishes([
        __DIR__.'../resources/stubs' => base_path('stubs'),
    ], 'stubs');

    $this->createRepositoryProvider();
    
  }

  protected function createRepositoryProvider() {
    $providerPath = app_path('Providers').'/RepositoryProvider.php';
    if (!File::exists($providerPath)) {
      $result = Artisan::call('make:provider RepositoryProvider');
    }
  }
}
