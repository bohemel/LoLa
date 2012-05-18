<?php
/**
 * @file
 * Definition of the Crud class.
 */

/**
 * The Crud class handles basic store and retrieve operations for objects.
 * 
 * Acts as a base class for entities in the system and provides a CRUD 
 * interface.
 */
class Crud {

  protected static $data_dir = 'data';

  protected static $entity = 'none';
  protected static $class_name = 'Crud';

  protected $primary_key = 'id';
  public $id = NULL;
  public $data = array();

  /**
   * Constructor
   *
   * @param mixed $data
   *   If $data is an array, one of two things can happend.
   *    1. If the primary key index is set in the array the objects is read and
   *       merged with the data provided in the argument.
   *    2. If the primary key field is NOT set in the given array a new record
   *       is created and saved to disk.
   *   If $data is numeric. The record with that id will be loaded from disk.
   */  
  function __construct($data) {
    
    if (is_array($data) && !empty($data[$this->primary_key])) {
      $this->id = $data[$this->primary_key];
      $this->read();
      $this->data += $data;
      $this->save();
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

  /**
   * Returns TRUE if record is successfully loaded
   */
  function isLoaded() {
    return !empty($this->id) && !empty($this->data);
  }

  /**
   * Loades record with current id to data array.
   */
  function read() {
    if (file_exists($this->dataFile())) {
      $this->data = $this->readDataFile();
    }
    else
     $this->data = array();
  }

  /**
   * Returns the directory where records for this entity is stored.
   */
  private function entityDir() {
    return static::$data_dir . '/' . static::$entity;
  }

  /**
   * Returns name of data file for current record.
   */
  private function dataFile() {
    return $this->entityDir() . '/' . $this->id . '.dat';
  }

  /**
   * Reads and parses the data file for the current record.
   */
  private function readDataFile() {
    return unserialize(file_get_contents($this->dataFile()));
  }

  /**
   * Writes current record to disk.
   */
  private function writeDataFile($data) {
    if (!is_dir($this->entityDir()))
      mkdir($this->entityDir(), 0770, TRUE);
    return file_put_contents($this->dataFile(), serialize($data));
  }

  /**
   * Returns filename of entity meta file.
   */
  private static function metaFile() {
    return static::$data_dir . '/' . static::$entity . '.meta.dat';
  }

  /**
   * Saves given meta data in meta data file.
   */
  private static function saveMetaData($data) {
    return file_put_contents(static::metaFile(), serialize($data));
  }

  /**
   * Returns and if needed initializes meta data for entity.
   */
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

