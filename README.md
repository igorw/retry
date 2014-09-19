# retry

A tiny library for retrying failing operations.

Since the network is reliable, things should always work. Am I right? For those cases when they don't, there is *retry*.

```php
<?
use function igorw\retry;

// retry an operation up to 5 times
$user = retry(5, function () use ($id) {
    return User::find($id);
});

// here is why you want to start using HHVM
$user = retry(5, () ==> User::find($id));

// this is probably a bad idea
$user = retry(INF, () ==> {
    throw new RuntimeException('never gonna give you up');
});
?>
```

I know. You're welcome.
