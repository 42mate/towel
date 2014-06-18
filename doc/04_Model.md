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

We will see later how to do more fancy stuff but for now will be enough.

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

Joins with other Tables
-----------------------


### 1 to Many Relations

### Many to Many Relations

### Implementing custom Queries

## The Hydrator

