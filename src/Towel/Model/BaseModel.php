<?php

namespace Towel\Model;

use Doctrine\DBAL\Driver\PDOStatement;

class BaseModel extends \Towel\BaseApp {
  public $id_name = 'id'; //Field name of the Primary Key.
  public $fields; //Table Fields, is going to be discovered on object creation.
  public $fks; //Foreign Keys.
  public $table; //Table Name in the Database, must be definend in the child.
  public $record; //Keeps the Database Record, only fields defined in the table are allowed.
  public $fetchedRecord; //Keeps the last fetched records from a query, all fetched fields.
  public $isDirty = FALSE; //True if some field was modified with setField.
  public $isDeleted = FALSE; //True if the record have been deleted.

  public function __construct() {
    parent::__construct();
    $this->discoverFields();
  }

  /**
   * Gets info of the fields from the table.
   */
  public function discoverFields() {
    if (!empty($this->fields)) {
      return; // Already configured.
    }

    $sm = $this->db()->getSchemaManager();
    $columns = $sm->listTableColumns($this->table);
    foreach ($columns as $column) {
      $this->fields[$column->getName()] = array(
        'name' => $column->getName(),
        'type' => $column->getType(),
      );
    }

    try {
      $this->fks = $sm->listTableForeignKeys($this->table);
    } catch (\DBALException $e) {
      //Platform does not support foreign keys.
      $this->fks = array();
    }
  }

  /**
   * Gets the value of a field from the record.
   * Warning : If the object have a field with the same name as the table field
   *           the value of the object field will be returned, not the one on the table.
   *           use getField to have the right value.
   *
   * @param $name
   * @return mixed
   *
   * @throws \Exception
   */
  public function __get($name) {
    return $this->getField($name);
  }

  /**
   * Sets the value of a field in the record.
   *
   * Warning : If the object have a field with the same name as the table field
   *           the value of the object field will be set, not the one on the table.
   *           use setField to have the right value.
   *
   * @param $name
   * @param $value
   * @return mixed
   *
   * @throws \Exception
   */
  public function __set($name, $value) {
    return $this->setField($name, $value);
  }

  /**
   * Sets a field to the record.
   *
   * @param $name
   * @param $value
   * @return mixed
   *
   * @throws \Exception
   */
  public function setField($name, $value) {
    if (isset($this->fields[$name])) {
      $this->isDirty = TRUE;

      return $this->record[$name] = $value;
    }
    throw new \Exception('Not a valid Field for Set ' . $name);
  }

  /**
   * Gets a Field from Record.
   *
   * If the field is not in record will check in fetchedRecord, fetched record may have
   * some fields from the last query that you will need, like related records.
   *
   * @param $name
   * @return mixed
   * @throws \Exception
   */
  public function getField($name) {
    if (isset($this->fields[$name])) {
      return $this->record[$name];
    }

    if (isset($this->fetchedRecord[$name])) {
      return $this->fetchedRecord[$name];
    }

    throw new \Exception('Not a valid Field for Get ' . $name);
  }

  /**
   * Inserts the record.
   *
   * @return $this
   */
  public function insert() {
    $this->db()->insert($this->table, $this->record);
    $id = $this->db()->lastInsertId();
    $this->record[$this->id_name] = $id;
    $this->isDirty = FALSE;

    return $this;
  }

  /**
   * Updates the record.
   *
   * @return $this
   */
  public function update() {
    $this->db()->update($this->table, $this->record, $this->identifier());
    $this->isDirty = FALSE;

    return $this;
  }

  /**
   * Inserts or Update the record.
   *
   * @return $this
   */
  public function save() {
    if ($this->isNew()) {
      $this->insert();
    }
    else {
      $this->update();
    }

    return $this;
  }

  /**
   * Deletes the record.
   *
   * @return $this
   *
   * @throws \Exception
   */
  public function delete() {
    if ($this->isNew()) {
      throw new \Exception('ID is not setted for Delete');
    }

    $this->db()->delete($this->table, $this->identifier());
    $this->isDeleted = TRUE;

    return $this;
  }

  /**
   * Deletes all records in table.
   *
   * @return $this
   *
   * @throws \Exception
   */
  public function deleteAll() {
    $this->db()->executeQuery("DELETE FROM {$this->table}");

    return $this;
  }

  /**
   * Returns the Id or False if is not setted.
   *
   * @return Boolean True or False
   */
  public function isNew() {
    return !is_array($this->identifier());
  }

  /**
   * Provides the Id in array format column-value.
   * False if is a new object.
   *
   * @return array|bool
   */
  public function identifier() {
    if (empty($this->record[$this->id_name])) {
      return FALSE;
    }

    return array($this->id_name => $this->record[$this->id_name]);
  }

  /**
   * Gets the ID value or false.
   *
   * @return mixed ID Value or false.
   */
  public function getId() {
    if (empty($this->record[$this->id_name])) {
      return FALSE;
    }

    return $this->record[$this->id_name];
  }

  /**
   * Resets the values of the object.
   */
  public function resetObject() {
    $this->record = array();
    $this->isDirty = FALSE;
    $this->isDeleted = FALSE;
  }

  /**
   * Sets the internal record with a new record.
   *
   * @param $record
   *
   * @return \Towel\Model\BaseModel
   */
  public function setRecord($record) {
    $this->resetObject();
    $newRecord = array();
    foreach ($this->fields as $field_name => $field) {
      if (!empty($record[$field_name])) {
        $newRecord[$field_name] = $record[$field_name];
      }
      else {
        $newRecord[$field_name] = ''; //Adds the field but with empty string.
      }
    }
    $this->record = $newRecord;
    $this->fetchedRecord = $record;

    return $this;
  }

  /**
   * Update partial values of the record and keep the current
   * non modified values.
   *
   * @param $record
   * @return $this
   *
   * @throws \Exception
   */
  public function mergeRecord($record) {
    if ($this->isNew()) {
      throw new \Exception('Can not merge, is new');
    }
    foreach ($this->fields as $field_name => $field) {
      if (!empty($record[$field_name])) {
        $this->record[$field_name] = $record[$field_name];
      }
      $this->isDirty = TRUE;
    }

    return $this;
  }

  /**
   * Returns the internal Record.
   *
   * @return Array
   */
  public function getRecord() {
    return $this->record;
  }

  /**
   * Fetchs one record with the given query.
   *
   * If success sets the record in the current object and return $this.
   * If fails return false.
   *
   * @param $sql
   * @param $params
   *
   * @return The current instance with the record setted internally.
   */
  public function fetchOne($sql, $params = array()) {
    $result = $this->db()->fetchAssoc($sql, $params);

    if (!empty($result)) {
      $this->setRecord($result);

      return $this;
    }

    return FALSE;
  }

  /**
   * Finds a Record by Id. Returns the record array and sets the internal
   * record if you want to use the record object.
   *
   * @param String $id
   *
   * @return The current instance with the record setted internally or false is nothing have been found.
   */
  public function findById($id) {
    return $this->fetchOne(
      "SELECT * from {$this->table} WHERE {$this->id_name} = ?",
      array($id)
    );
  }

  /**
   * Finds all records of a table.
   *
   * @return mixed : PDOStatement with results.
   */
  public function findAllWithoutFetch() {
    $results = $this->db()->executeQuery("SELECT * FROM {$this->table}");

    return $results;
  }

  /**
   * Finds all records of a table.
   *
   * @return mixed : Array with results.
   */
  public function findAll() {
    $results = $this->findAllWithoutFetch();
    $return = $this->hydrate($results);

    return $return;
  }

  /**
   * Finds into table by any given field.
   *
   * @param $field_name
   * @param $value
   * @param $operator : A valid SQL operator for the comparison =, >, <, LIKE, IN, NOT IN. By default =
   *
   * @return PDOStatement : PDOStatement with results.
   */
  public function findByFieldWithoutFetch($field_name, $value, $operator = '=') {
    $query = $this->db()->createQueryBuilder();
    $query->select('t.*')
      ->from($this->table, 't')
      ->where("$field_name $operator ?")
      ->setParameter(0, $value);

    return $query->execute();
  }

  /**
   * Finds into table by any given field.
   *
   * @param $field_name
   * @param $value
   * @param $operator : A valid SQL operator for the comparison =, >, <, LIKE, IN, NOT IN. By default =
   *
   * @return Array of Model Objects
   * .
   */
  public function findByField($field_name, $value, $operator = '=') {
    $results = $this->findByFieldWithoutFetch($field_name, $value, $operator);
    $return = $this->hydrate($results);

    return $return;
  }

  /**
   * Default Hydrate.
   *
   * Use preHydrate and postHydrate methods to change the default behavior.
   * You can override this method too if you need.
   *
   * @param PDOStatement $results
   *
   * @return Array of Model Objects
   */
  public function hydrate(PDOStatement $results) {

    if (method_exists($this, 'preHydrate')) {
      $results = $this->preHydrate($results);
    }

    $return = array();

    if ($results) {
      $arrayResults = $results->fetchAll();
      foreach ($arrayResults as $arrayResult) {
        $className = get_class($this);
        $object = new $className();
        $return[$arrayResult[$this->id_name]] = $object->setRecord($arrayResult);
      }
    }

    if (method_exists($this, 'postHydrate')) {
      $return = $this->preHydrate($return);
    }

    return $return;
  }

  /**
   * Finds a related model instance by the given field name and the id value.
   *
   * Use it in a 1 to N relation, with an object instance of the N side to the get 1 related model.
   *
   * @param $modelName : The related model name.
   * @param $field : The field that must be used to relate.
   * @param $id : Optional, the ID the related model, if is not given the value of the field in the executor object will be used.
   *
   * @throws \Exception : if a invalid model is given.
   *
   * @return Related Instance.
   */
  public function findRelatedModel($modelName, $field, $id = NULL) {
    $relatedModel = $this->getInstance($modelName);

    if ($this->isNew() && $id === NULL) {
      throw new \Exception('No id for related');
    }

    if ($id === NULL) {
      $id = $this->getField($field);
    }

    $result = $relatedModel->findById($id);

    return $result;
  }

  /**
   * Finds related models instances using the field name and the id value.
   *
   * Use it in a 1 to N relation, with an object instance of the 1 side to the get N related models.
   *
   * @param $modelName : The related model name.
   * @param $field : The field that must be used to relate.
   * @param $id : Optional, the ID the related model, if is not given the value of the field in the executor object will be used.
   *
   * @throws \Exception : if a invalid model is given.
   *
   * @return Related Instance.
   */
  public function findRelatedModels($modelName, $field, $id = NULL) {
    $relatedModel = $this->getInstance($modelName);

    if ($this->isNew() && $id === NULL) {
      throw new \Exception('No id for related');
    }

    if ($id === NULL) {
      $id = $this->getId();
    }

    $result = $relatedModel->findByField($field, $id);

    return $result;
  }

  public function findRelatedModelsBridge($bridgeModel, $modelName, $id = NULL) {

    if ($this->isNew() && $id === NULL) {
      throw new \Exception('No id for related');
    }

    if ($id === NULL) {
      $id = $this->getId();
    }

    $bridge = $this->findRelatedModels($bridgeModel, $this->table . '_id', $id);

    $return = array();

    foreach ($bridge as $b) { //@Todo replace this for a query with IN (ids)
      $finalModel = $this->getInstance($modelName);
      $bridgeField = $finalModel->table . '_id';
      if (!isset($return[$b->$bridgeField])) {
        if ($finalModel->findById($b->$bridgeField)) {
          $return[$finalModel->getId()] = $finalModel;
        }
      }
    }

    return $return;
  }

  /**
   * Convert the array of objects into a plain array.
   *
   * @param $objects
   *
   * @return array
   */
  static public function plain($objects) {
    $data = array();
    foreach ($objects as $object) {
      $data[] = $object->getRecord();
    }

    return $data;
  }

  /**
   * Creates a QueryBuilder with the table selected.
   *
   * @return \Doctrine\DBAL\Query\QueryBuilder
   */
  public function createQuery() {
    $query = $this->db()->createQueryBuilder();
    $query->select('t.*')
      ->from($this->table, 't');

    return $query;
  }

  /**
   * Count how many records are in the Database.
   * @return Integer
   */
  public function count() {
    $count = $this->fetchOne("SELECT COUNT(*) AS total from {$this->table}");

    return $count->total;
  }

  /**
   * Finds all records of a table paged.
   *
   * @param int $page
   * @param int $limit
   *
   * @return mixed : Array with results.
   */
  public function findAllPaged($page = 0, $limit = 20) {
    $results = $this->findAllWithoutFetchPaged($page, $limit);
    $return = $this->hydrate($results);

    return $return;
  }

  /**
   * Finds all records of a table.
   *
   * @param int $page
   *
   * @param int $limit
   *
   * @return mixed : PDOStatement with results.
   */
  public function findAllWithoutFetchPaged($page = 0, $limit = 20) {
    $offset = $page * $limit;
    $results = $this->createQuery()
      ->setFirstResult($offset)
      ->setMaxResults($limit)
      ->execute();
    return $results;
  }
}

