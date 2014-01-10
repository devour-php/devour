<?php

namespace Devour\Table;

interface TableFactoryInterface {

  public function setTableClass($class);

  public function create();

}
