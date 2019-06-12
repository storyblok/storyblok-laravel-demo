In this short article, we will show you how you can use the headless CMS Storyblok in combination with the PHP Framework for Web Artisans "[Laravel](https://laravel.com/)". At the end of this article, you will have a Laravel Application which renders [components](https://www.storyblok.com/docs/terminology/component)  according to the data of the API of Storyblok. During this article we will use the JSON you will receive after creating a new space.

## Starting with Storyblok

Before we can create a new laravel project we will start with creating a new Storyblok space:

**1:** Create a new Account if you don't have one yet: [https://app.storyblok.com/#!/signup](https://app.storyblok.com/#!/signup

![Sign up a new account](//a.storyblok.com/f/51376/2400x1270/1d8e165f4f/register.jpg)

**2:** Choose **"I'm a dev"** and than ***"Create a new Space"** (right option)

![Create a new space](//a.storyblok.com/f/51376/3250x1752/e6ed906ef4/new-space.jpg)

**3:** Click on the **"Home"** entry and continue with the next steps of the tutorial below.

![Click on Home](//a.storyblok.com/f/51376/3360x1344/996384a5af/home-entry.jpg)

## Laravel

I'm sure that most of you are already familiar with Laravel and it's basics - if not I would suggest you start with the [Installation of Laravel](https://laravel.com/docs/5.4). The [Laravel framework](https://laravel.com/docs/5.4) has a few system requirements:

- [PHP >= 5.6.4](http://php.net/)
- [OpenSSL PHP Extension](http://php.net/manual/book.openssl.php)
- [PDO PHP Extension](http://php.net/manual/pdo.installation.php)
- [Mbstring PHP Extension](http://php.net/manual/mbstring.installation.php)
- [Tokenizer PHP Extension](http://php.net/manual/book.tokenizer.php)
- [XML PHP Extension](http://php.net/manual/xml.installation.php)

## Start a new Laravel project

You can add Storyblok to existing projects as well - for simplicity we will show how to add Storyblok to a completely fresh project - so a beginner to the world of [Laravel](https://laravel.com/) can use Storyblok as their CMS as well because it's API-based and only returns data for your application. Execute the following command so you've got a freshly created project ready to start with:

~~~bash
laravel new storyblok-laravel
~~~

You can simply run your fresh application after executing:

~~~bash
composer install && php artisan serve
~~~

You can read more about the laravel setup in their documentation mentioned above.


## Install the Storyblok PHP Client

Storyblok already provides a [PHP client](https://github.com/storyblok/php-client) - so we won't have to think about how we're doing the API requests and receive data from the content delivery API – all we have to do is:

~~~bash
composer require storyblok/php-client
~~~

This will add the `\Storyblok\Client` to your `composer.json`.


## Let's load our Story

In the `routes/web.php,` we will initialize the Storyblok Client and directly load the [Story](https://www.storyblok.com/docs/terminology/story) with the `slug` `"home"` as default– and a [route parameter](https://laravel.com/docs/5.4/routing#parameters-optional-parameters) to load a story according to the slug which was received as an optional parameter. 

**1.** Make sure to replace `PREVIEW_TOKEN` in the code below with the token that you can see in the app.storyblok.com home entry.

![Preview token](//a.storyblok.com/f/51376/3360x1888/bf92b45f22/preview-token.jpg)

**2.** The code below will try to render the `index.blade.php`, since this is missing you will receive an error for now as we have to create it first. 

~~~php
Route::get('/{slug?}', function ($slug = 'home') {
  $storyblok = new \Storyblok\Client('PREVIEW_TOKEN');
  $data = $storyblok->getStoryBySlug($slug)->getStoryContent();
  return view('index', ['story' => (object)$data['story']]);
});
~~~

### Create index.blade.php

In your `resources/views/index.blade.php` you can copy the code below, which will define the basic sceleton (html, head, body, scripts) but does not yet define the layout as this is defined by content types which will be included dynamically.

~~~php
<!doctype html>
<html lang="{{ config('app.locale') }}">
  <head>
    <title>{{ $story->name }}</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, maximum-scale=1.0, minimum-scale=1.0, user-scalable=0" />
    <meta http-equiv="X-UA-Compatible" content="IE=11"/>
    <meta name="generator" content="storyblok">
    <meta name="cms" content="https://www.storyblok.com">
    <link rel="icon" type="image/png" href="//app.storyblok.com/images/favicon-32x32.png" sizes="32x32">
    <link rel="stylesheet" href="{{ url('css/app.css') }}" media="all" />
  </head>
  <body>

  @include('components/' . $story->content['component'], ['blok' => $story->content])
  
  <script type="text/javascript" src="{{ url('js/app.js') }}"></script>

  <script type="text/javascript" src="//app.storyblok.com/storyblok-latest.js"></script>
  <script type="text/javascript">
    storyblok.init();
    storyblok.on('change', function() {
      window.location.reload(true);
    });
  </script>
  </body>
</html>
~~~

#### Dynamically including the content type

In the code above you can see the line:

~~~php
@include('components/' . $story->content['component'], ['blok' => $story->content])
~~~

Which allows you to render a `partial` through the blade templating engine. In our case the `content.component` property in the `story` object will always contain the name of the content type you've defined in Storyblok (page, post, event, ...). In our set-up for this tutorial we created a content type called `page`.  

## Create our first component in views/components

To still have some kind of overview in our `*.blade.php` files we will create a `views/components` folder so we know which components are used for those includes.

The page content type only has one property of the type `blocks` called **body**, which allows you to nest other components in it to build out a dynamic page consisting of multiple different components that you can define.

### Create the page.blade.php

Add the following resource: `resources/views/components/page.blade.php`.

~~~php
@foreach ($blok['body'] as $blok)
  @include('components/' . $blok['component'], ['blok' => $blok])
@endforeach
~~~

The next component in our demo content is the `teaser` component, which is nested in that page content type.

### Let's create the teaser.blade.php

Add the next component `resources/views/components/teaser.blade.php`.

~~~php
{!! isset($blok['_editable']) ? $blok['_editable'] : '' !!}
<div class="teaser">
    <h1>
      <!--
      You can access every attribute you
      define in the schema in the blok variable
      -->
      {{$blok['headline']}}
    </h1>
    <h2>
        You can create new components like this - to create your own set of components.
    </h2>
</div>
~~~

### Let's create the grid.blade.php component

The demo content already provides the content for that component, similar to the teaser component we will have to create a file for this as well.

~~~php
{!! isset($blok['_editable']) ? $blok['_editable'] : '' !!}
<div class="grid">
  @foreach(array_get($blok, 'columns', []) as $blok)
      @include('components/' . $blok['component'], ['blok' => $blok])
  @endforeach
</div>
~~~

### Last component: feature.blade.php

You can see above that the grid component also only contains one array property which includes another component, called "feature".

~~~php
{!! isset($blok['_editable']) ? $blok['_editable'] : '' !!}
<div class="feature">
  {{ $blok['name'] }}
</div>
~~~

## Configuring the preview:

We will have to also tell Storyblok where to find our dev environment (the website to be embedded on the left). You can either add it at the **bottom of the on-boarding** in your `home` content entry or you can change it at any time in the Space Settings.

![Storyblok on boarding](//a.storyblok.com/f/39898/2248x1266/c92a21bbb7/storyblok_server.jpg)

## Well done!

Try out to insert a text or add new components, after one click on "Save" your component should be updated with the changed content. You can add some styling to the HTML and receive something like we did below.

You can now create as many components, and content types as you want, build new layouts with nested components - or go flat with content types like `post`, `project`, and `product`.

![Endresult](//a.storyblok.com/f/39898/2908x1652/96bdc74d47/jekyll-running.png)

## Conclusion

You can create as many components and with as many fields as you want. You can even nest them as deep as you want them to. I would suggest you to read the [Component Terminology](https://www.storyblok.com/docs/terminology/component) before you start creating your own components.tutorial.md


## Bonus: The Storyblok JavaScript Bridge

~~~php
{!! isset($blok['_editable']) ? $blok['_editable'] : '' !!}
~~~

This line of code will output the text included in the `_editable` property of an Storyblok component. If your application will be opened in the preview mode or Storyblok Editor, we need some kind of match to your website so we can identify a component. 

The content of the `_editable` property is actually nothing more than a simple HTML comment - with the Storyblok script we included in the `index.blade.php` we can enable frontend editing without touching your actual HTML. Have a look at the [JavaScript Bridge](https://www.storyblok.com/docs/Guides/storyblok-latest-js) documentation for more information and even events.

If you're running on an Nginx with server side includes on, you can use this [Github Gist](https://gist.github.com/DominikAngerer/ca61d41bae3afcc646cfee286579ad36) to manually parse the HTML comments and apply the attributes accordingly.