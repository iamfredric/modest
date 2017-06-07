# Modest
This is a data binding library for wordpress development. 

### 1.0 Installation
...

### 2.0 Usage

#### 2.1 ...
```php
// Create your class and let it exted Modest
class Post extends Modest
{}

// From here on we can call following methods to create the instance

// Sets up an instance from the current queried post object
Post::current();

// Sets up an instance from given WP_Post obejct
Post::make(get_post(...));

// Finds the database object with given id and sets up a new instace
Post::find(1);

// Create a new database instance
Post::create([...])

// Todo...
Post::where...()
Post::all()...
```
All of the above methods will return a new intance. 

#### 2.2 Custom params
When creating a class, you can configure it... 
```php
class Post extends Modest
{
    // The post type is determined by the class name, 
    // If you want to override this, just define the typ param.
    protected $type = 'page';

    // Here you can define params that should be casted to Carbon instances,
    // By default date and modified are defined
    protected $dates = [
        'date', 'modified'
    ];

    // Here you can define variables that should be hidden 
    // By default password is hidden
    protected $hidden = [
        'password'
    ];
    
    // The excerpt length
    protected $excerptLength = 120;
    
    // If you need to modify any value you can use getNameAttribute method
    // For example if you need to prefix the title with a dash use the following method
    public function getTitleAttribute($title)
    {
        return "- {$title}";        
    }
}
```

#### 2.2 The object
When you have created the instance
```php
$post = Post::current();

// Get values in different ways
$post->id;
$post->get('id');
$post['id'];

// Cast all values to an array
$post->toArray();

// If you need the worpress equivalent array
$post->toWordpressArray();

// Cast all values to json
$post->toJson();

// If you need to get to the hidden attributes you can access the attributes object directly
$post->attributes->get('password');
$post->attributes->password;
$post->attributes['password'];
$post->attributes->toArray()
$post->attributes->toJson();

// If you want to update current post in database
$post->title = 'Updated title';
$post = $post->save();

$post = $post->update(['title' => 'Updated title']);
```

### 3.0 Get a collection of posts
```php
$posts = Post::all();

$posts = Post::whereTitle('Value')->get();
$posts->where('title', '!=', 'Value')->get();
```
