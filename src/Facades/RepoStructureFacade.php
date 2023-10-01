<?php

namespace Kalpesh\RepoStructure\Facades;

use Illuminate\Support\Facades\Facade;

class RepoStructureFacade extends Facade{
  
  protected static function getFacadeAccessor(){
    return 'RepoStructure';
  }  
  
}