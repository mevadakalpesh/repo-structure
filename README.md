# Repo Structure

Repo Structure is a Laravel package that allows you to create repository structure with single command including class,interface,Provider as well as binding code and Service with Common functions.

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
  
  /**
  * Service Folder Name
  */
  "service_folder" => app_path('Service'),
  
  /**
  * Prefix of Service File Example PostService
  */
  "service_file_prefix" => "Service",

  /**
  * Prefix of Service File Example PostService
  */
  "Interface_repo_bind_name" => "Repository",
  
  /**
  * Your Modal Path
  */
  "model_path" => app_path('Models'),
  
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
    -- Service
       -- PostService.php
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

Your Service `PostService.php` something looks like.

```php
<?php

namespace App\Service;

use App\Repository\Interfaces\PostRepositoryInterface;

/**
 * Service class for handling Post related operations.
 */
class PostService
{
    /**
     * Constructor method.
     *
     * @param PostRepositoryInterface $postRepository The repository instance.
     */
    public function __construct(
        public PostRepositoryInterface $postRepository
    ) {
    }

    /**
     * Retrieve the first Post record matching the criteria.
     *
     * @param array $where Conditions to match.
     * @param array $with  Relationships to load.
     * @return mixed The first matching record.
     */
    public function firstPost(array $where = [], array $with = [])
    {
        return $this->postRepository->first(
            where: $where,
            with: $with
        );
    }

    /**
     * Retrieve all Post records matching the criteria.
     *
     * @param array $where Conditions to match.
     * @param array $with  Relationships to load.
     * @return mixed The collection of matching records.
     */
    public function getPost(array $where = [], array $with = [])
    {
        return $this->postRepository->get(
            where: $where,
            with: $with
        );
    }

    /**
     * Create a new Post record.
     *
     * @param array $data Data for the new record.
     * @return mixed The created record.
     */
    public function createPost(array $data = [])
    {
        return $this->postRepository->create(
            data: $data
        );
    }

    /**
     * Update existing Post records matching the criteria.
     *
     * @param array $where Conditions to match.
     * @param array $data  Data to update.
     * @return mixed The result of the update operation.
     */
    public function updatePost(array $where = [], array $data = [])
    {
        return $this->postRepository->update(
            where: $where,
            data: $data
        );
    }

    /**
     * Delete Post records matching the criteria.
     *
     * @param array $where Conditions to match.
     * @return mixed The result of the delete operation.
     */
    public function deletePost(array $where = [])
    {
        return $this->postRepository->delete(
            where: $where
        );
    }
}

```

## Customizing Stub Files

The following stub files are available for customization:

- **`repo-interface.php.stub`**
- **`repo-service.php.stub`**
- **`repo-class.php.stub`**

To modify the default code in these files, you need to publish the stub files to your project. After publishing, you can modify the contents of these files as per your needs.

### Publishing Stub Files

Run the following Artisan command to publish the stub files to your project:

```bash
php artisan vendor:publish --provider="Vendor\Package\PackageServiceProvider" --tag="stubs"
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
