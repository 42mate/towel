Model
=====

Models in Towel are very very simple, we are not doing anything fancy like Doctrine ORM,
we have chosen a very simple approach to interact with the Database.

Somethings to have in mind before start
---------------------------------------

First of all, we are not pretending to do a new ORM, we just want to have a simple database
layer to interact directly with the database tables and implement new query methods quickly using SQL
and not some custom and new language that does exactly the same.

We use Dbal to interact with the database, so you have to read something about it for a better use.

We have Objects that are directly mapped with tables in the database.

This objects gets the fields definitions from the database table, you don't have to
define a schema in your code, Towel will look into the database table and get the meta information
from there.

Each table needs one, and just one, primary key. We don't support multiple keys. You have to define
the primary key field name in the object or use our default name for that, id is the default name.

All the model objects must extends from Towel\BaseModel.

Sample Database
---------------

Lets assume for our sample that we have this database.

User
Post
Tags
PostTags

Create a Model Object
---------------------

To create a Model object you have to define a class that extends from Towel\BaseModel and put it into
your app model folder.

```php
namespace Application\YourAppName\Model;

use \Towel\Model\BaseModel;

class Post extends BaseModel
{
    public $table = 'Post';
}
```

The only requirement for this class is to have the table name defined. For this sample the table
name is Post.

That is all that you have to do to create a Model Object. As it is the model will allow you
create, update, delete and do some basic queries by default.

We will see later how to do more fancy stuffs.

Create a new Record
-------------------

```php
$post = new Post();
$post->title = "some title";
$post->created = time();
$post->body = "the post content";
$post->uid = $user->id
$post->save();
```

Update the Record
-----------------


```php
//$post is an instance of a previously created object or retrieved from the database.
$post->title = "change the title";
$post->save();
```

Delete the Record
-----------------

```php
//$post is an instance of a previously created object or retrieved from the database.
$post->delete();
```

```php
$post = new Post() //Or with an instance of a previously created object or retrieved from the database.
$post->deleteAll(); //Deletes all content in the table !!!
```

Find a Record by ID
-------------------

```php
$post = new Post(); //Or with an instance of a previously created object or retrieved from the database.

if ($post->findById($id)) {
    echo $post->title; //Will print the title.
} else {
    echo "Post does not exist";
}
```

Find Records
------------

```php
$post = new Post(); //Or with an instance of a previously created object or retrieved from the database.

$posts = $post->findAll();
foreach ($posts as $post) {
    //Do Something.
    echo $post->title;
}
```

Find Records By Field
---------------------
Use findByField method, this receives the field name, same as in database, and the value.
An optional third parameter can be given to set the operator for the comparsion, any SQL comparsion operator
can be used like =, >, <, <=, >=, like, ilike, set this parameter as string.

```php
$post = new Post();

$results = $post->findByField('uid', $userId);

foreach ($results as $result) {
   echo $result->title;
}

```

1 to Many Relations
-------------------

You have two side to tacle this problem, from the 1 side, or from the N side.

a) Getting the 1 side from some instance from the N side.

You have to use findRelatedModel, the first parameter is the model class name, with namespace, and the second is the field name in the current
object that is going to be used to read the value and match in the ID of the wanted model.

```php
$post->findById($postId);
$category = $post->findRelatedModel('Application\Name\Model\Category', 'category_id');
```

$category will be an instance of Model Category with the related value.

b) Getting the N values from an instance of the 1 side.

You have to use findRelatedModels (note the s), the first parameter is the model class name, with namespace, and the second is the field name in the wanted
model that is going to be used to read the value and match with the current object id.

```php
$category->findById($categoryId);
$posts = $category->findRelatedModels('Application\Name\Model\Post', 'category_id');
```

$posts will be an array with instances of Model Posts.

Many to Many Relations
----------------------

To get many to many relation you'll need to use findRelatedModelsBridge method.

```php
        $post->findById($postId);
        $tags = $post->findRelatedModelsBridge('Application\Name\Model\PostTag', 'Application\Name\Model\Tag');
```

$tags is going to be an array with instances of Tag model class.

Implementing custom Queries
---------------------------

The Hydrator
------------

