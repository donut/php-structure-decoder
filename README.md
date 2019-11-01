# StructureDecoder
A compositional way to validate and decode objects and associative arrays into structured types (classes, etc). Modeled after [glennsl/bs-json](https://github.com/glennsl/bs-json).

## Example usage

Example from [jw-platform-php](https://github.com/RightThisMinute/jw-platform-php) that decodes the following JSON after it's run through PHP's `json_decode()`.

```json
{
"mediaid": "abc123def456",
"description": "The talents of three of the world’s greatest storytellers—Roald Dahl, Walt Disney and Steven Spielberg—finally unite to bring Dahl’s beloved classic “The BFG” to life. Directed by Spielberg, Disney’s “The BFG” tells the imaginative story of a young girl and the Giant who introduces her to the wonders and perils of Giant Country.",
"pubdate": 1449848296,
"tags": "fantasy,action and adventure and fun,trailer",
"image": "http://cdn.jwplayer.com/thumbs/abc123def456-720.jpg",
"title": "The BFG",
"sources": [
  {
    "width": 1280,
    "type": "application/vnd.apple.mpegurl",
    "file": "http://cdn.jwplayer.com/manifests/abc123def456.m3u8",
    "height": 720
  },
  {
    "width": 320,
    "height": 180,
    "type": "video/mp4",
    "file": "http://cdn.jwplayer.com/videos/abc123def456-xzy8910.mp4",
    "label": "H.264 320px"
  },
  {
    "width": 480,
    "height": 270,
    "type": "video/mp4",
    "file": "http://cdn.jwplayer.com/videos/abc123def456-xzy8911.mp4",
    "label": "H.264 480px"
  },
  {
    "width": 720,
    "height": 406,
    "type": "video/mp4",
    "file": "http://cdn.jwplayer.com/videos/abc123def456-xzy8912.mp4",
    "label": "H.264 720px"
  },
  {
    "width": 1280,
    "height": 720,
    "type": "video/mp4",
    "file": "http://cdn.jwplayer.com/videos/abc123def456-xzy8913.mp4",
    "label": "H.264 1280px"
  },
  {
    "type": "audio/mp4",
    "file": "http://cdn.jwplayer.com/videos/abc123def456-xzy8914.m4a",
    "label": "AAC Audio"
  }
],
"tracks": [
  {
    "kind": "thumbnails",
    "file": "http://cdn.jwplayer.com/strips/abc123def456-120.vtt"
  }
],
"link": "http://cdn.jwplayer.com/previews/abc123def456",
"recommendations": "//cdn.jwplayer.com/feed.json?feed_id=wZuMVmMk&related_media_id=abc123def456",
"duration": 110
}
```


```php 
<?php
declare(strict_types=1);


namespace RightThisMinute\JWPlatform\Delivery\response;


use function RightThisMinute\StructureDecoder\field;
use RightThisMinute\StructureDecoder\types as T;
use function RightThisMinute\StructureDecoder\optional_field;

class Video
{
  public static function fromJSON ($json) : self
  {
    $image = function($v) { return Image::fromJSON($v); };

    $tags = function($v) {
      $tags = T\string()($v);
      return preg_split
        ('/(\s*,\s*)+/', $tags, PREG_SPLIT_NO_EMPTY);
    };

    $source = function($v) { return Source::fromJSON($v); };

    return new self
      ( field($json, 'mediaid', T\string())
      , field($json, 'title', T\string())
      , field($json, 'description', T\string())
      , field($json, 'duration', T\int())
      , field($json, 'pubdate', T\int())
      , field($json, 'link', T\string())
      , field($json, 'image', T\string())
      , field($json, 'images', T\array_of($image))
      , field($json, 'tags', $tags)
      , field($json, 'variations', T\object())
      , field($json, 'sources', T\array_of($source))
      , optional_field($json, 'feedid', T\string()) );
  }

  /** @var string */
  public $mediaid;

  /** @var string */
  public $description;

  /** @var int */
  public $pubdate;

  /** @var string */
  public $title;

  /** @var string */
  public $image;

  /** @var string[] */
  public $tags;

  /** @var object */
  public $variations;

  /** @var Image[] */
  public $images;

  /** @var string */
  public $link;

  /** @var int */
  public $duration;

  /** @var Source[] */
  public $sources;

  /** @var string|null */
  public $feedid;

  /**
   * Video constructor.
   *
   * @param string $mediaid
   * @param string $title
   * @param string $description
   * @param int $duration
   * @param int $pubdate
   * @param string $link
   * @param string $image
   * @param Image[] $images
   * @param string[] $tags
   * @param object $variations
   * @param Source[] $sources
   * @param string|null $feedid
   */
  public function __construct
    ( string $mediaid
    , string $title
    , string $description
    , int $duration
    , int $pubdate
    , string $link
    , string $image
    , array $images
    , array $tags
    , object $variations
    , array $sources
    , ?string $feedid=null )
  {
    $this->mediaid = $mediaid;
    $this->description = $description;
    $this->pubdate = $pubdate;
    $this->title = $title;
    $this->image = $image;
    $this->tags = $tags;
    $this->variations = $variations;
    $this->images = $images;
    $this->link = $link;
    $this->duration = $duration;
    $this->sources = $sources;
    $this->feedid = $feedid;
  }

}


class Image
{
  public static function fromJSON ($json) : self
  {
    return new self
      ( field($json, 'src', T\string())
      , field($json, 'type', T\string())
      , field($json, 'width', T\int()) );
  }

  /** @var string */
  public $src;

  /** @var string */
  public $type;

  /** @var int */
  public $width;

  public function __construct (string $src, string $type, int $width)
  {
    $this->src = $src;
    $this->type = $type;
    $this->width = $width;
  }
}


class Track
{
  /** @var string */
  public $kind;

  /** @var string */
  public $file;

  public function __construct (string $kind, string $file)
  {
    $this->kind = $kind;
    $this->file = $file;
  }
}

class Source
{
  public static function fromJSON ($json) : self
  {
    return new self
      ( field($json, 'type', T\string())
      , field($json, 'file', T\string())
      , optional_field($json, 'label', T\string())
      , optional_field($json, 'width', T\int())
      , optional_field($json, 'height', T\int()) );
  }


  /** @var string */
  public $type;

  /** @var string */
  public $file;

  /** @var string|null */
  public $label;

  /** @var int|null */
  public $width;

  /** @var int|null */
  public $height;

  public function __construct
    ( string $type
    , string $file
    , ?string $label
    , ?int $width
    , ?int $height )
  {
    $this->type = $type;
    $this->file = $file;
    $this->label = $label;
    $this->width = $width;
    $this->height = $height;
  }
}
```
