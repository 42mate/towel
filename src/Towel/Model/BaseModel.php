<?php

namespace Towel\Model;

class BaseModel extends \Towel\BaseApp
{
    public $id_name = 'id';
    public $record;
    public $fields;
    public $fks;
    public $table;
    public $isDirty = false;
    public $isDeleted = false;

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
        $sm = $this->db()->getSchemaManager();
        $columns = $sm->listTableColumns($this->table);
        foreach ($columns as $column) {
            $this->fields[$column->getName()] = array(
                'name' => $column->getName(),
                'type' => $column->getType(),
            );
        }
        $this->fks = $sm->listTableForeignKeys($this->table);
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
     * @param $name
     * @return mixed
     * @throws \Exception
     */
    public function getField($name) {
        if (isset($this->fields[$name])) {
            return $this->record[$name];
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
     * Fetchs one record with the given query.
     *
     * If success sets the record in the current object and return $this.
     * If fails return false.
     *
     * @param $sql
     * @param $params
     *
     *  @return mixed : PDOStatement with results of false.
     */
    public function fetchOne($sql, $params)
    {
        $result = $this->db()->fetchAssoc($sql, $params);

        if (!empty($result)) {
            $this->resetObject();
            $this->record = $result;
            return $this->record;
        }

        return false;
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
     * Finds a Record by Id.
     *
     * @param String $id
     *
     *  @return mixed : PDOStatement with results or False.
     */
    public function findById($id)
    {
        return $this->fetchOne("SELECT * from {$this->table} WHERE {$this->id_name} = ?",
            array($id)
        );
    }

    /**
     * Finds all records of a table.
     *
     * @return mixed : PDOStatement with results.
     */
    public function findAll()
    {
        $results = $this->db()->executeQuery("SELECT * FROM {$this->table}");
        return $results;
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
    public function findByField($field_name, $value, $operator = '=')
    {
        $query = $this->db()->createQueryBuilder();
        $query->select('t.*')
            ->from($this->table, 't')
            ->where("$field_name $operator ?")
            ->setParameter(0, $value);
        return $query->execute();
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
    public function findRelated($field_names, $id = null)
    {

        if ($this->isNew()) {
            throw new \Exception("There is not ID defined.");
        }

        if (is_string($field_names)) {
            $field_names = array($field_names);
        }

        foreach ($this->fks as $fk) {
            $localFieldsNames = $fk->getLocalColumns();
            if ($field_names == $localFieldsNames) {
                return $this->joinWithThis(
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
    public function joinWithThis($foreign_table, $local_fields, $foreign_fields, $id = null)
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
}

