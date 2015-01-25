<?php

namespace Towel\Tests;

class myTable extends \Towel\Model\BaseModel {
    public $table = 'myTable';
}

class Post extends \Towel\Model\BaseModel {
    public $table = 'post';
}

class Category extends \Towel\Model\BaseModel {
    public $table = 'category';
}

class Tag extends \Towel\Model\BaseModel {
    public $table = 'tag';
}

class PostTag extends \Towel\Model\BaseModel {
    public $table = 'post_tag';
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
        $this->assertEquals($modelRecord->id, $model->id);

        //Deletes the inserted record.
        $this->assertTrue(!$model->isDeleted);
        $model->delete();
        $this->assertTrue($model->isDeleted);

        //Verifies that is deleted.
        $modelRecord = $model->findById($model->id);
        $this->assertTrue(!$modelRecord);
    }

    public function testFind() {
        $this->model->deleteAll();
        $test = array('one', 'two', 'three');

        foreach($test as $name) {
            $model = new myTable();
            $model->name = $name;
            $model->save();
        }

        $records = $model->findAll();

        $this->assertEquals(count($records), 3);

        $first = array_shift($records);
        $this->assertEquals($test[0], $first->name);
        $first = array_shift($records);
        $this->assertEquals($test[1], $first->name);
        $first = array_shift($records);
        $this->assertEquals($test[2], $first->name);
        $this->assertEquals(count($records), 0);
        $this->assertNotEquals(count($records), 1);
    }

    public function testFindByField() {
        $this->model->deleteAll();

        $model = new myTable();
        $test = array('one', 'two', 'three');

        foreach($test as $name) {
            $model = new myTable();
            $model->name = $name;
            $model->save();
        }

        $results = $model->findByField('name', 'one');
        $this->assertEquals(1, count($results));
        $result = array_shift($results);
        $this->assertEquals('one', $result->name);

        try {
            $results = $model->findByField('wrongField', 'one');
        } catch (\Exception $e) {
            $this->assertEquals("An exception occurred while executing 'SELECT t.* FROM myTable t WHERE wrongField = ?' with params [\"one\"]:\n\nSQLSTATE[HY000]: General error: 1 no such column: wrongField", $e->getMessage());
        }

    }

    public function testRelated() {

        (new Category())->deleteAll();
        (new Post())->deleteAll();

        foreach (array(1,2,3,4) as $c) {
            $cat = new Category();
            $cat->name = 'cat' . $c;
            $cat->save();
        }

        $post = new Post();
        $post->title = 'foo';
        $post->body = 'bar';
        $post->category_id = 1;
        $post->save();

        $post = new Post();
        $post->title = 'zaraza';
        $post->body = 'bar';
        $post->category_id = 1;
        $post->save();

        $post = new Post();
        $post->title = '3333';
        $post->body = 'bar';
        $post->category_id = 1;
        $post->save();

        $post2 = new Post();
        $post2->title = 'foo 1';
        $post2->body = 'bar 1';
        $post2->category_id = 2;
        $post2->save();

        $category = $post2->findRelatedModel('Towel\Tests\Category', 'category_id');
        $this->assertEquals($category->name, 'cat2');

        $category = $post->findRelatedModel('Towel\Tests\Category', 'category_id');
        $this->assertEquals($category->name, 'cat1');

        $posts = $category->findRelatedModels('Towel\Tests\Post', 'category_id');
        $this->assertEquals(count($posts), 3);

        foreach($posts as $post) {
            $this->assertEquals($post->category_id, 1);
        }

        $posts = $category->findRelatedModels('Towel\Tests\Post', 'category_id', 2);
        $this->assertEquals(count($posts), 1);

        foreach($posts as $post) {
            $this->assertEquals($post->category_id, 2);
        }

    }

    public function testManyToMany() {
        (new Category())->deleteAll();
        (new Post())->deleteAll();
        (new Tag())->deleteAll();
        (new PostTag())->deleteAll();

        foreach (array(1,2,3,4) as $t) {
            $tag = new Tag();
            $tag->name = 'tag' . $t;
            $tag->save();
        }

        $post = new Post();
        $post->title = 'foo';
        $post->body = 'bar';
        $post->category_id = 1;
        $post->save();

        $post = new Post();
        $post->title = 'zaraza';
        $post->body = 'bar';
        $post->category_id = 1;
        $post->save();

        $pt = new PostTag();
        $pt->post_id = 2;
        $pt->tag_id = 1;
        $pt->save();

        $pt = new PostTag();
        $pt->post_id = 2;
        $pt->tag_id = 2;
        $pt->save();

        $pt = new PostTag();
        $pt->post_id = 2;
        $pt->tag_id = 3;
        $pt->save();

        $tags = $post->findRelatedModelsBridge('Towel\Tests\PostTag', 'Towel\Tests\Tag');

        $this->assertEquals(3, count($tags)); //@todo write more tests !

    }
}