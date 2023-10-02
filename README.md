# Repo Structure

Repo Structure is a Laravel package that allows you to create repository structure with single command including class,interface,Provider as well as binding code.

---

## Installation

You can install the package via composer if this command give error add
`:dev-main` at last as a version:

```bash
composer require mevada-kalpesh/repo-structure
```
Add This Provider in `Providers` in `config/app.php` after installing the
package
```bash
App\Providers\RepositoryProvider::class
```

You can publish the config file with:
```bash
php artisan vendor:publish --provider="Kalpesh\RepoStructure\RepoStructureProvider"
```

This is the contents of the published config file:

```php
// config/repo-sturcture.php

<?php
return [

  /**
  * Prefix of Class File Example PostRepositoryClass
  */
  "class_file_prefix" => 'RepositoryClass',

  /**
  * Prefix of Interface File Example PostRepositoryInterface
  */
  "interface_file_prefix" => 'RepositoryInterface',

  /**
  * File Directory for create files
  * app_path is require
  */
  "file_dir" => app_path('Repository'),

  /**
  * Interface Folder Name
  */
  "interface_folder" => "Interfaces",

  /**
  * Class Folder Name
  */
  "class_folder" => "Classes",

];
```

## Usage

You just do run below command to build a full repository structure including Interface , class , Provider as well as bind code in provider . you just start to use repo in your controller

```php
php artisan make:repo Post
```

after run this commad it will create like this file structure


```php
-- app
    -- Repository
       -- Classes
          -- PostRepositoryClass.php
       -- Interfces
           -- PostRepositoryInterface.php
```

 Your Repository Class `PostRepositoryClass.php` something looks like 
 

```php
<?php

namespace App\Repository\Classes;

use App\Repository\Interfaces\PostRepositoryInterface;

class PostRepositoryClass implements PostRepositoryInterface
{
    //here is your method
}
```

Your Repository Interface `PostRepositoryInterface.php` something looks like.

```php
<?php

namespace App\Repository\Interfaces;

interface PostRepositoryInterface 
{
   // here is your method
}
```

Your Repository Provider `RepositoryProvider.php` something looks like.


```php
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repository\Classes\PostRepositoryClass; 
use App\Repository\Interfaces\PostRepositoryInterface; 

class RepositoryProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
       $this->app->bind(PostRepositoryInterface::class, function () {
         return new PostRepositoryClass();
       });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}

```

Make sure you add the namespace correctly as shown above.

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
