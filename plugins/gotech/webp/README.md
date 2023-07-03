## Before Installation
You will need to install **WebP** before installing this plugin, and take into account the following basic requirements.

#### Requerements
* Ubuntu 18.04.3 (LTS) x64 or higher
* PHP 7.2 or higher
* Install WebP for Ubuntu
* [Pikanji.Agent](https://octobercms.com/plugin/pikanji-agent)

> We haven't tried it in other versions, feel free to give it a try and tell us about your experience.

## How to install WebP on ubuntu
Login to your server as root user and run on your terminal

    $ sudo apt install webp

For more information you can visit this [page](https://developers.google.com/speed/webp)

## Install Plugin
Run the following command in your terminal or install from the marketplace 

    $ php artisan plugin:install GoTech.Webp

## Usage
Use the filter `|webp` to create and get the link of the **image.webp** 

#### Get image.webp from twig filter |theme

    {{ 'assets/images/image1.jpg'|theme|webp }}

#### Get image.webp from twig filter |media

    {{ 'backgrounds/bg1.jpg'|media|webp }}

#### Get image.webp from System\Models\File instance
    
    {{ post.featured_images.first|webp }}

#### Get image.webp from thumb

    {{ post.featured_images.first.thumb(800, 600, {'mode':'crop'})|webp }}

#### filter `|webp` parameters

    {{ image.path|webp(<quality :optional>, <getinfo :optional>) }}

Where `<quality>` is 0 - 100 integer. 0 - lowest quality, 100 - highest quality. Default quality is 70 defined by backend settings.

Also you can get image info by `<getinfo>` indicate with boolean to get all the information of the image. Default is false, and return the image link.

## Implementation
Due to the [incompatibility of some browsers](https://caniuse.com/webp) with **webp** images, you must indicate both image formats in your code, so that the user can view our images correctly, if the browser is not compatible.

#### 1. Frontend controlled compatibility
The browser takes care of managing the compatibility, it is the safest way. Although it does not allow great flexibility if you need to add webp images to an element by style attribute or others.

```
<picture>
    {# Preparing the image I'm going to use ;) #}
    {% set picture = post.featured_images.first.thumb(800, 600, {'mode':'crop'}) %}

    <!-- image.jpg to image.webp -->
    <source srcset="{{ picture|webp }}" type="image/webp">

    <!-- Original image to old browsers -->
    <source srcset="{{ picture }}" type="image/jpg">
    <img src="{{ picture }}">
</picture>
```

#### 2. Backend controlled compatibility
If backend compatibility is enabled, the link of the images will be dynamic and the original image or the webp image will be displayed, depending on the browser.

This allows for more flexibility and less coding by omitting the use of `<picture> </picture>`. You can do this, and the backend takes care of loading the images supported by your browser.

```
<!-- Usage as usual -->
<img src="{{ image|webp }}" alt="">

<!-- Or anywhere -->
<div style="background-image: url({{ image|webp }});></div>
```

To enable backend compatibility , you need to go to:

    Backend > Settings > WebP


## Testing
You can do a quick test if everything has gone well, after installation, by going to: https://yourdomain.tld/caniuse/webp

## A little challenge
Finally, I invite you to take a [Google PageSpeed Insights](https://developers.google.com/speed/pagespeed/insights/) test before and after the implementation, and share the results with your review.

Thank you!