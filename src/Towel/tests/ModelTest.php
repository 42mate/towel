<?php

namespace Towel\Tests;

class myTable extends \Towel\Model\BaseModel {
    public $table = 'myTable';
}

class ModelTest extends \PHPUnit_Framework_TestCase {

    public function setUp() {
        $this->model = new myTable();
        $this->model->deleteAll();
    }

    public function testCreateInstanceAndFields() {
        $model = new myTable();
        $this->assertNull($model->id);
        $this->assertNull($model->name);
        try {
            $this->assertNull($model->fooBar);
        } catch(\Exception $e) {
            $this->assertEquals('Not a valid Field for Get fooBar', $e->getMessage());
        }
    }

    public function testInsertUpdate() {
        $model = new myTable();
        $this->assertTrue($model->isNew());
        $model->name = 'This is a Test';
        $model->save();
        $this->assertFalse($model->isNew());
        $this->assertTrue(is_numeric($model->id));
        $newId = $model->id;
        $this->assertEquals($model->name, 'This is a Test');
        $model->name = 'This is an updated test';
        $this->assertTrue($model->isDirty);
        $model->save();
        $this->assertTrue(!$model->isDirty);
        $this->assertEquals($model->name, 'This is an updated test');
        $this->assertEquals($model->id, $newId);
    }

    public function testDelete() {
        $this->model->deleteAll();

        //Creates a new Entry.
        $model = new myTable();
        $model->name = 'This is a Test';
        $this->assertTrue($model->isDirty);
        $model->save();
        $this->assertTrue(!$model->isDirty);
        $this->assertTrue(is_numeric($model->id));

        //Gets the inserted record.
        $modelId = $model->id;
        $modelRecord = $model->findById($model->id);
        $this->assertEquals($modelRecord['id'], $model->id);

        //Deletes the inserted record.
        $this->assertTrue(!$model->isDeleted);
        $model->delete();
        $this->assertTrue($model->isDeleted);

        //Verifies that is deleted.
        $modelRecord = $model->findById($model->id);
        $this->assertTrue(!$modelRecord);
    }

}