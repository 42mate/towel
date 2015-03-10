## Controllers

### hasErrorMessages

This method will help you to check if some error message was previously loaded by some validator o something else
in the process. If has error you can handle how to continue.

```php

$this->setMessage('error', 'Not a valid Name'); //An error is set.

//...later.

if ($this->hasErrorMesssages()) {
    $this->redirect('post/create/error');
}

``