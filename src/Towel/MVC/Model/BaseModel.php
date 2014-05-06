<?php

namespace Towel\MVC\Model;

class BaseModel extends \Towel\BaseApp
{
    public $id = 'id';
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
     *
     * @param $name
     * @return mixed
     *
     * @todo put a prefix like get_field_name to avoid name colissions
     *
     * @throws \Exception
     */
    public function __get($name)
    {
        if (isset($this->fields[$name])) {
            return $this->record[$name];
        }
        throw new \Exception('Not a valid Field for Get ' . $name);
    }

    /**
     * Sets the value of a field in the record.
     *
     * @param $name
     * @param $value
     * @return mixed
     *
     * @todo put a prefix like set_field_name to avoid name colissions
     *
     * @throws \Exception
     */
    public function __set($name, $value)
    {
        if (isset($this->fields[$name])) {
            $this->isDirty = true;
            return $this->record[$name] = $value;
        }
        throw new \Exception('Not a valid Field for Set ' . $name);
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
        $this->record[$this->id] = $id;
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
        if (empty($this->record[$this->id])) {
            return false;
        }
        return array($this->id => $this->record[$this->id]);
    }

    /**
     * Gets the ID value or false.
     *
     * @return mixed ID Value or false.
     */
    public function getId()
    {
        if (empty($this->record[$this->id])) {
            return false;
        }
        return $this->record[$this->id];
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
     * @return $this or false
     */
    public function fetchOne($sql, $params)
    {
        $result = $this->db()->fetchAssoc($sql, $params);

        if (!empty($result)) {
            $this->record = $result;
            return $this;
        }

        return $this;
    }

    /**
     * Sets the internal record with a new record.
     *
     * @param $record
     *
     * @return $this
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
     * @return \App\Model\BaseModel
     */
    public function findById($id)
    {
        return $this->fetchOne("SELECT * from {$this->table} WHERE {$this->id} = ?",
            array($id)
        );
    }

    /**
     * Finds all records of a table.
     *
     * @return mixed
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
     *
     * @return mixed
     */
    public function findByField($field_name, $value)
    {
        $query = $this->db()->createQueryBuilder();
        $query->select('t.*')
            ->from($this->table, 't')
            ->where("$field_name = ?")
            ->setParameter(0, $value);
        return $query->execute();
    }

    /**
     * Finds related records by foreing key.
     *
     * It will use the internal id of the record if is setted or
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
            ->where("t1.{$this->id} = ?")
            ->setParameter(0, $id);

        return $query->execute();
    }
}

