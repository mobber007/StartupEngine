<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use GrahamCampbell\Markdown\Facades\Markdown;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Post extends Model implements AuditableContract
{
    use SoftDeletes;

    use Auditable;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at', 'published_at'];

    public function bodyHtml()
    {
        return Markdown::convertToHtml($this->content()->body->body);
    }

    public function category() {
        $category = \App\Category::where('id', '=', $this->category_id)->first();
        return $category;
    }

    public function json()
    {
        $json = $this->json;
        return json_decode($json);
    }

    public function content()
    {
        $json = $this->json;
        $array = json_decode($json, true)['versions'][1];
        return json_decode(json_encode($array));
    }

    public function image() {
        $image = $this->content()->body->image;
        if($image !== null ) {
            return $image;
        }
        else {
            return null;
        }
    }

    public function background() {
        $background = $this->content()->body->background;
        if($background !== null ) {
            return $background;
        }
        elseif($this->image() !== null) {
            $background = $this->image();
            return $background;
        }
        else {
            return null;
        }
    }

    public function excerpt() {
        $array = json_decode(json_encode($this->content()), true);
        if(isset($array['meta']['excerpt'])) {
            return $array['meta']['excerpt'];
        }
        else {
            return null;
        }
    }

    public function postType() {
        $postType = $this->post_type;
        $postType = PostType::where('slug', '=', $postType)->firstOrFail();
        return $postType;
    }

    public function schema() {
        return json_decode($this->postType()->json);
    }


    public function videoType($url) {
        if (strpos($url, 'youtube') > 0) {
            return 'youtube';
        } elseif (strpos($url, 'vimeo') > 0) {
            return 'vimeo';
        } else {
            return 'unknown';
        }
    }

}
