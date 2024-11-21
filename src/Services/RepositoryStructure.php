<?php

namespace Kalpesh\RepoStructure\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class RepositoryStructure {

  protected array $setting;
  protected array $options;
  protected string $repositoryName;
  protected string $classFolder;
  protected string $interfaceFolder;
  protected string $interfacePrefix;
  protected string $classPrefix;
  public $error = null;
  protected $choice = null;
  protected $message = null;

  public function __construct(array $setting) {
    $this->setting = $setting;
    $this->classFolder = ucfirst(Str::camel($this->setting['class_folder']));
    $this->interfaceFolder = ucfirst(Str::camel($this->setting['interface_folder']));
    $this->interfacePrefix = ucfirst(Str::camel($this->setting['interface_file_prefix']));
    $this->classPrefix = ucfirst(Str::camel($this->setting['class_file_prefix']));
    $this->serviceFolder = ucfirst(Str::camel($this->setting['service_folder']));
    $this->servicePrefix = ucfirst(Str::camel($this->setting['service_file_prefix']));
    $this->InterfaceBindName = ucfirst(Str::camel($this->setting['Interface_repo_bind_name']));
  }

  public function execute() {
    try {

      if ($this->choice == "No Replace") {
        $this->message = "No replacement";
        return $this->message;
      }

      $this->makeFile();

      return $this->message;
    } catch(\Exception $e) {
      throw $e;
    }

  }

  public function name(string $repositoryName) {
    $this->repositoryName = ucfirst(Str::camel($repositoryName));
    return $this;
  }

  public function option(array $options) {
    $this->options = $options;
    return $this;
  }

  protected function makeDirectory() {

    $directoryPath = $this->setting['file_dir'];
    $isAppDir = $this->checkDirBelongsFromAppPath();
    if (!$isAppDir) {
      throw \Exception("The Directory Path Must belongs with App Path");
    }

    if (!File::exists($directoryPath)) {
      $result = File::makeDirectory($directoryPath, 0755, true);
      if (!$result) {
        throw \Exception("Directory Path Not Found");
      }
    }
    $this->createSubFolders();
  }

  protected function createSubFolders() {
    $classFolder = $this->classFolder;
    $InterfaceFolder = $this->interfaceFolder;
    $serviceFolder = $this->serviceFolder;

    if (!File::exists($this->setting['file_dir'].'/'.$classFolder)) {
      File::makeDirectory($this->setting['file_dir'].'/'.$classFolder, 0755, true);
    }

    if (!File::exists($this->setting['file_dir'].'/'.$InterfaceFolder)) {
      File::makeDirectory($this->setting['file_dir'].'/'.$InterfaceFolder, 0755, true);
    }
    
    if (!File::exists($this->setting['service_folder'])) {
      File::makeDirectory($this->setting['service_folder'], 0755, true);
    }
    
  }

  protected function makeFile() {

    if ($this->choice == "Just Interface") {
      $this->createInterface();
      $this->message = "Just Interface Replaced Successfully..!";
    }

    if ($this->choice == "Just Class") {
      $this->createClass();
      $this->message = "Just Class Replaced Successfully..!";
    }

    if ($this->choice == "Yes Both Files" || $this->choice == null) {
      $this->message = "Both Files Replaced Successfully..!";
      $this->createInterface();
      $this->createClass();
      $this->createService();
    }

    if ($this->choice == null) {
      $this->modifyProvider();
      $this->message = "Repository Create Successfully.!";
    }

  }

  protected function createClass() {
    $className = $this->getClassFileName();
    $classStub = $this->geClassStub();
    if (!blank($className)) {
      File::put($className, $classStub);
    }
  }

  protected function createInterface() {
    $interfaceName = $this->getInterfaceFileName();
    $interfaceStub = $this->geInterfaceStub();
    if (!blank($interfaceName)) {
      File::put($interfaceName, $interfaceStub);
    }
  }


  protected function createService() {
    $serviceName = $this->getServiceFileName();
    $serviceStub = $this->geServiceStub();
    if (!blank($serviceName)) {
      File::put($serviceName, $serviceStub);
    }
  }

  public function checkFileExist() {

    $isDirCreated = $this->makeDirectory();

    $interfaceName = $this->getInterfaceFileName();
    $className = $this->getClassFileName();

    $fileExistNames = [];

    // check for interface
    if (File::exists($interfaceName)) {
      $fileExistNames[] = 'app/'. (string) Str::of(trim($interfaceName, '/'))->replace(trim(app_path(), '/'), '')->trim('/');
    }

    // check for class
    if (File::exists($className)) {
      $fileExistNames[] = 'app/'. (string) Str::of(trim($className, '/'))->replace(trim(app_path(), '/'), '')->trim('/');
    }

    if (!empty($fileExistNames)) {
      $this->error = implode("\n", $fileExistNames);
    }
    return $this;
  }

  protected function getInterfaceFileName() : string
  {
    $interfacePrefix = $this->interfacePrefix;
    $interfaceFolder = $this->interfaceFolder;
    $interfaceFileName = trim($this->repositoryName.$interfacePrefix);
    $directoryPath = rtrim($this->setting['file_dir'], '/');
    $interfaceFileNameWithPath = "$directoryPath/$interfaceFolder/$interfaceFileName.php";
    return $interfaceFileNameWithPath;
  }

  protected function getServiceFileName() : string
  {
    $serviceFileName = trim($this->repositoryName.$this->servicePrefix);
    $serviceFolder = rtrim($this->setting['service_folder'], '/');
    $serviceFileNameWithPath = "$serviceFolder/$serviceFileName.php";
    return $serviceFileNameWithPath;
  }

  protected function getClassFileName() : string
  {
    $classPrefix = $this->classPrefix;
    $classFileName = trim($this->repositoryName.$classPrefix);
    $classFolder = $this->classFolder;
    $directoryPath = rtrim($this->setting['file_dir'], '/');
    $classFileNameWithPath = "$directoryPath/$classFolder/$classFileName.php";
    return $classFileNameWithPath;
  }

  protected function geInterfaceStub() {
    $stubFile = __DIR__.'/../../resources/stubs/repo-interface.php.stub';
    $InterfaceName = $this->repositoryName.$this->interfacePrefix;

    return strtr(File::get($stubFile), [
      '{{InterfaceName}}' => $InterfaceName,
      '{{namespace}}' => $this->getNamespace()['interface_namespace']
    ]);
  }

  protected function geServiceStub() {

    $stubFile = __DIR__.'/../../resources/stubs/repo-service.php.stub';
    $serviceName = $this->repositoryName.$this->servicePrefix;
    $namespaceArray = $this->getNamespace();
    
    return strtr(File::get($stubFile), [
      '{{serviceName}}' => $serviceName,
      '{{namespace}}' => $namespaceArray['service_namespace'],
      '{{interfaceNamespace}}' => $this->getInterfaceNamespace(),
      '{{repositoryName}}' => $this->repositoryName.$this->interfacePrefix,
      '{{repositoryNameVariable}}' => lcfirst(Str::studly($this->getRepositoryBindName())),
      '{{coreRepoName}}' => ucwords($this->repositoryName)
    ]);

  }

  protected function getRepositoryBindName() {
    return $this->repositoryName.$this->InterfaceBindName;
  }

  protected function getInterfaceNamespace() {
    $namespaceArray = $this->getNamespace();
    return $namespaceArray['interface_namespace'].'\\'.$this->repositoryName.$this->interfacePrefix;
  }

  protected function getClassNamespace() {
    $namespaceArray = $this->getNamespace();
    return $namespaceArray['class_namespace'].'\\'.$this->repositoryName.$this->classPrefix;
  }


  protected function geClassStub() {
    $stubFile = __DIR__.'/../../resources/stubs/repo-class.php.stub';
    $ClassName = $this->repositoryName.$this->classPrefix;
    $InterfaceName = $this->repositoryName.$this->interfacePrefix;
    $InterfaceNamespace = $this->getNamespace()['interface_namespace'].'\\'.$InterfaceName;
    $ModelNamespace = "App\\".(string)Str::of(trim($this->setting['model_path'], '/'))->replace(trim(app_path(),'/'), '')->trim('/');
    $Model = ucfirst($this->repositoryName);
    
    return strtr(File::get($stubFile), [
      '{{ClassName}}'     => $ClassName,
      '{{namespace}}'     => $this->getNamespace()['class_namespace'],
      '{{InterfaceName}}' => $InterfaceName,
      '{{InterfaceNamespace}}' => $InterfaceNamespace,
      '{{ModelNamespace}}'  => $ModelNamespace,
      '{{Model}}' => $Model
    ]);
  }
  
  protected function checkDirBelongsFromAppPath() : bool
  {
    return Str::contains(
      trim($this->setting['file_dir'], '/'),
      trim(app_path(), '/')
    );
  }

  protected function getNamespace() {
    $rootNamespace = "App\\".(string) Str::of(trim($this->setting['file_dir'], '/'))->replace(trim(app_path(), '/'), '')->trim('/');
    $serviceNamespace = "App\\".(string) Str::of(trim($this->setting['service_folder'], '/'))->replace(trim(app_path(), '/'), '')->trim('/');
    $classNamespace = $rootNamespace.'\\'.$this->classFolder;
    $interfaceNamespace = $rootNamespace.'\\'.$this->interfaceFolder;
    
    return [
      'class_namespace' => $classNamespace,
      'interface_namespace' => $interfaceNamespace,
      'service_namespace' => $serviceNamespace,
    ];
  }

  protected function modifyProvider() {
    $providerPath = app_path('Providers/RepositoryProvider.php');
    $providerContent = File::get($providerPath);
    $this->addNamespaceToFile($providerPath, $providerContent);
    $this->addBideCodeToFile($providerPath);
  }

  protected function modifyFile($filePath, $fileContent, $putCode, $position) {
    if ($position !== false) {
      $newContents = substr_replace($fileContent, $putCode, $position, 0);
      File::put($filePath, $newContents);
    }
  }

  protected function addNamespaceToFile(string $providerPath, string $providerContent) {
    $namespaceArray = $this->getNamespace();
    $classNamespace = $this->getInterfaceNamespace();
    $interfaceNamespace = $this->getClassNamespace();
    $namespace = "\nuse $classNamespace; \nuse $interfaceNamespace; \n";
    $position = strpos($providerContent, 'class RepositoryProvider extends ServiceProvider');
    $this->modifyFile($providerPath, $providerContent, $namespace, $position);
  }

  protected function addBideCodeToFile($providerPath) {
    $namespaceArray = $this->getNamespace();
    $ClassName = $this->repositoryName.$this->classPrefix;
    $InterfaceName = $this->repositoryName.$this->interfacePrefix;
    $providerContent = File::get($providerPath);
    $codeToAdd = "\n       \$this->app->bind($InterfaceName::class,$ClassName::class);\n";
    $position = strpos($providerContent, 'public function register(): void');
    $position = strpos($providerContent, '{', $position);
    $this->modifyFile($providerPath, $providerContent, $codeToAdd, $position+1);
  }

  public function setReplaceChoice(string $choice) {
    $this->choice = $choice;
  }

}
