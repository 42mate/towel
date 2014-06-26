<?php

namespace Towel\Model;

use Doctrine\DBAL\Driver\PDOStatement;

class BaseModel extends \Towel\BaseApp
{
    public $id_name = 'id';   //Field name of the Primary Key.
    public $fields;           //Table Fields, is going to be discovered on object creation.
    public $fks;              //Foreign Keys.
    public $table;            //Table Name in the Database, must be definend in the child.
    public $record;           //Keeps the Database Record, only fields defined in the table are allowed.
    public $fetchedRecord;    //Keeps the last fetched records from a query, all fetched fields.
    public $isDirty = false;  //True if some field was modified with setField.
    public $isDeleted = false;//True if the record have been deleted.

    public function __construct()
    {
        parent::__construct();
        $this->discoverFields();
    }

    /**
     * Gets info of the fields from the table.
     */
    public function discoverFields()
    {
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
    public function __get($name)
    {
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
    public function __set($name, $value)
    {
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
            $this->isDirty = true;
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
    public function insert()
    {
        $this->db()->insert($this->table, $this->record);
        $id = $this->db()->lastInsertId();
        $this->record[$this->id_name] = $id;
        $this->isDirty = false;
        return $this;
    }

    /**
     * Updates the record.
     *
     * @return $this
     */
    public function update()
    {
        $this->db()->update($this->table, $this->record, $this->identifier());
        $this->isDirty = false;
        return $this;
    }

    /**
     * Inserts or Update the record.
     *
     * @return $this
     */
    public function save()
    {
        if ($this->isNew()) {
            $this->insert();
        } else {
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
    public function delete()
    {
        if ($this->isNew()) {
            throw new \Exception('ID is not setted for Delete');
        }

        $this->db()->delete($this->table, $this->identifier());
        $this->isDeleted = true;
        return $this;
    }

    /**
     * Deletes all records in table.
     *
     * @return $this
     *
     * @throws \Exception
     */
    public function deleteAll()
    {
        $this->db()->executeQuery("DELETE FROM {$this->table}");
        return $this;
    }

    /**
     * Returns the Id or False if is not setted.
     *
     * @return Boolean True or False
     */
    public function isNew()
    {
        return !is_array($this->identifier());
    }

    /**
     * Provides the Id in array format column-value.
     * False if is a new object.
     *
     * @return array|bool
     */
    public function identifier()
    {
        if (empty($this->record[$this->id_name])) {
            return false;
        }
        return array($this->id_name => $this->record[$this->id_name]);
    }

    /**
     * Gets the ID value or false.
     *
     * @return mixed ID Value or false.
     */
    public function getId()
    {
        if (empty($this->record[$this->id_name])) {
            return false;
        }
        return $this->record[$this->id_name];
    }

    /**
     * Resets the values of the object.
     */
    public function resetObject()
    {
        $this->record = array();
        $this->isDirty = false;
        $this->isDeleted = false;
    }

    /**
     * Sets the internal record with a new record.
     *
     * @param $record
     *
     * @return \Towel\Model\BaseModel
     */
    public function setRecord($record)
    {
        $this->resetObject();
        $newRecord = array();
        foreach ($this->fields as $field_name => $field) {
            if (!empty($record[$field_name])) {
                $newRecord[$field_name] = $record[$field_name];
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
    public function mergeRecord($record)
    {
        if ($this->isNew()) {
            throw new \Exception('Can not merge, is new');
        }
        foreach ($this->fields as $field_name => $field) {
            if (!empty($record[$field_name])) {
                $this->record[$field_name] = $record[$field_name];
            }
            $this->isDirty = true;
        }
        return $this;
    }

    /**
     * Returns the internal Record.
     *
     * @return Array
     */
    public function getRecord()
    {
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
    public function fetchOne($sql, $params)
    {
        $result = $this->db()->fetchAssoc($sql, $params);

        if (!empty($result)) {
            $this->setRecord($result);
            return $this;
        }

        return false;
    }

    /**
     * Finds a Record by Id. Returns the record array and sets the internal
     * record if you want to use the record object.
     *
     * @param String $id
     *
     *  @return The current instance with the record setted internally.
     */
    public function findById($id)
    {
        $result = $this->fetchOne("SELECT * from {$this->table} WHERE {$this->id_name} = ?",
            array($id)
        );

        if ($result) {
            return $result;
        }

        return false;
    }

    /**
     * Finds all records of a table.
     *
     * @return mixed : PDOStatement with results.
     */
    public function findAllWithoutFetch()
    {
        $results = $this->db()->executeQuery("SELECT * FROM {$this->table}");
        return $results;
    }

    /**
     * Finds all records of a table.
     *
     * @return mixed : Array with results.
     */
    public function findAll()
    {
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
     * @return mixed : PDOStatement with results.
     */
    public function findByFieldWithoutFetch($field_name, $value, $operator = '=')
    {
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
    .
     */
    public function findByField($field_name, $value, $operator = '=')
    {
        $results = $this->findByFieldWithoutFetch($field_name, $value, $operator);
        $return = $this->hydrate($results);
        return $return;
    }

    /**
     * Finds related records by foreign key.
     *
     * It will use the internal id of the record if is set or
     * will use the given id to make the join.
     *
     * If the object is new you have to provide the id.
     *
     * @param $field_names : String of Array of fields name (same in table).
     * @param integer $id : The id for the join or null to use the internal id.
     *
     * @return PDOStatement.
     *
     * @throws \Exception : If no id is provided.
     */
    public function findRelatedWithoutFetch($field_names, $id = null)
    {
        if (is_string($field_names)) {
            $field_names = array($field_names);
        }

        foreach ($this->fks as $fk) {
            $localFieldsNames = $fk->getLocalColumns();
            if ($field_names == $localFieldsNames) {
                return $this->join(
                    $fk->getForeignTableName(),
                    $fk->getLocalColumns(),
                    $fk->getForeignColumns(),
                    $id
                );
            }
        }

        throw new \Exception('Not a valid field for join ' . $field_names);
    }

    /**
     * Finds related records by foreign key.
     *
     * It will use the internal id of the record if is set or
     * will use the given id to make the join.
     *
     * If the object is new you have to provide the id.
     *
     * @param $field_names : String of Array of fields name (same in table).
     * @param integer $id : The id for the join or null to use the internal id.
     *
     * @return Array of Model Objects
     *
     * @throws \Exception : If no id is provided.
     */
    public function findRelated($field_names, $id = null) {
        $results = $this->findRelatedWithoutFetch($field_names, $id);
        $return = $this->hydrate($results);
        return $return;
    }

    /**
     * Executes a inner Join with this table and the table related to the field.
     * if Id is provided is going to user that id if not the internal
     * record id is going to be used.
     *
     * @param $foreign_table : The foreign table.
     * @param $local_fields : The local fields, array with names.
     * @param $foreign_fields : The foreign fields, array with names.
     * @param integer $id : Optional.
     *
     * @see findRelated
     *
     * @return mixed : PDOStatement with results.
     */
    public function joinWithoutFetch($foreign_table, $local_fields, $foreign_fields, $id = null)
    {
        $condition = '';

        foreach ($local_fields as $key => $field) {
            $condition .= " t1.$field = t2.{$foreign_fields[$key]}";
        }

        if ($id === null) {
            $id = $this->getId();
        }

        $query = $this->db()->createQueryBuilder();
        $query->select('t1.*, t2.*')
            ->from($this->table, 't1')
            ->innerJoin('t1', $foreign_table, 't2', $condition)
            ->where("t1.{$this->id_name} = ?")
            ->setParameter(0, $id);

        return $query->execute();
    }

    /**
     * Executes a inner Join with this table and the table related to the field.
     * if Id is provided is going to user that id if not the internal
     * record id is going to be used.
     *
     * @param $foreign_table : The foreign table.
     * @param $local_fields : The local fields, array with names.
     * @param $foreign_fields : The foreign fields, array with names.
     * @param integer $id : Optional.
     *
     * @see findRelated
     *
     * @return Array of Model Objects
     */
    public function join($foreign_table, $local_fields, $foreign_fields, $id = null)
    {
        $results = $this->joinWithoutFetch($foreign_table, $local_fields, $foreign_fields, $id);
        return $this->hydrate($results);
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
}

