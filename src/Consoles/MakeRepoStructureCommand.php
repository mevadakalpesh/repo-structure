<?php

namespace Kalpesh\RepoStructure\Consoles;

use Illuminate\Console\Command;
use Kalpesh\RepoStructure\Facades\RepoStructureFacade;

class MakeRepoStructureCommand extends Command
{
  /**
  * The name and signature of the console command.
  *
  * @var string
  */
  protected $signature = 'make:repo {RepositoryName} {--interface} {--class}';

  /**
  * The console command description.
  *
  * @var string
  */
  protected $description = 'Create Repository Structure';

  /**
  * Execute the console command.
  */
  public function handle() {

    $option = [
      'interface' => !blank($this->option('interface')) ? $this->option('interface') : false,
      'class' => !blank($this->option('class')) ? $this->option('class') : false
    ];
    $repositoryName = $this->argument('RepositoryName');

    $resultCheck = RepoStructureFacade::name($repositoryName)
    ->option($option)
    ->checkFileExist();

    if (!blank($resultCheck->error)) {
      $files = $resultCheck->error;
      $choiceOption = ['0' => 'No Replace','1' => 'Yes Both Files','2' => 'Just Interface','3' => 'Just Class'];
      $choice = $this->choice("These files already exist. Do you want to replace:\n$files?", $choiceOption);
      $resultCheck->setReplaceChoice($choice);
    }

    $message = $resultCheck->execute();
    $this->info($message);

  }
}