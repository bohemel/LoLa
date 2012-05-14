<?php

class Crud {

  protected static $data_dir = 'data';

  protected static $entity = 'none';
  protected static $class_name = 'Crud';

  protected $primary_key = 'id';
  public $id = NULL;
  public $data = array();

  function __construct($data) {
    if (is_array($data) && !empty($data[$this->primary_key])) {
      $this->id = $data[$this->primary_key];
      $this->read();
    }
    elseif (is_array($data) && empty($data[$this->primary_key])) {
      $this->create($data);
    }
    elseif (is_numeric($data)) {
      $this->id = intval($data);
      $this->read();
    }
    return $this;
  }  

  function isLoaded() {
    return !empty($this->id) && !empty($this->data);
  }

  function read() {
    if (file_exists($this->dataFile())) {
      $this->data = $this->readDataFile();
    }
    else
     $this->data = array();
  }

  private function entityDir() {
    return static::$data_dir . '/' . static::$entity;
  }

  private function dataFile() {
    return $this->entityDir() . '/' . $this->id . '.dat';
  }

  private function readDataFile() {
    return unserialize(file_get_contents($this->dataFile()));
  }

  private function writeDataFile($data) {
    if (!is_dir($this->entityDir()))
      mkdir($this->entityDir(), 0770, TRUE);
    return file_put_contents($this->dataFile(), serialize($data));
  }

  private static function metaFile() {
    return static::$data_dir . '/' . static::$entity . '.meta.dat';
  }

  private static function saveMetaData($data) {
    return file_put_contents(static::metaFile(), serialize($data));
  }

  private static function getMetaData() {
    if(file_exists(static::metaFile()))
      $meta_data = unserialize(file_get_contents(static::metaFile()));
    else
      $meta_data = array(
        'auto_increment' => 0,
      );
    return $meta_data;
  }

  private function getNextFreeId() {
    $meta = static::getMetaData();
    $next_id = ++$meta['auto_increment'];
    static::saveMetaData($meta);
    return $next_id;
  }

  function create($data) {
    $this->id = $this->getNextFreeId();
    $this->data = array_merge($this->data, $data, array($this->primary_key => $this->id));
    $this->data['modified'] = time();
    $this->data['created'] = time();
    $this->writeDataFile($this->data);
    return $this;
  }

  function update($data = array()) {
    $this->data = array_merge($this->data, $data);
    $this->data['modified'] = time();
    $this->writeDataFile($this->data);
    return $this;
  }

  static function getPage($entity_class, $page = 0, $size = 20) {
    $meta = $entity_class::getMetaData();
    $count = $size;
    $max_id = $meta['auto_increment'];
    $result = array();
    while ($count >= 0 && $max_id >= 0) {
      $entity = new $entity_class($max_id);
      if ($entity->isLoaded()) {
        $result[] = $entity;
        $count--;
      }
      $max_id--;
    }
    return $result;
  }

}

