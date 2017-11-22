<?php
namespace Larakit\Attach;

use Illuminate\Database\Eloquent\Model;
use Larakit\Helpers\HelperText;

class Attach extends Model {
    
    protected $table      = 'attaches';
    protected $connection = 'mysql';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $appends  = [
        'url',
        'size_text',
        'hash',
    ];
    protected $fillable = [
        'name',
        'ext',
        'priority',
        'size',
        'block',
        'attachable_id',
        'attachable_type',
    ];
    
    function getSizeTextAttribute() {
        return HelperText::fileSize($this->size);
    }
    
    function getHashAttribute() {
        return hashids_encode($this->id);
    }
    
    public function attachable() {
        return $this->morphTo();
    }
    
    static function getAttachKey() {
        $r = new \ReflectionClass(static::class);
        
        return Str::snake($r->getShortName(), '-');
    }
    
    static function uploadToModel($model, $block) {
        return self::upload(get_class($model), $model->id, $block);
    }
    
    static function upload($type, $id, $block) {
        $file = \Request::file('file');
        if($file) {
            $model       = Attach::create(
                [
                    'attachable_id'   => $id,
                    'attachable_type' => $type,
                    'block'           => $block,
                ]
            );
            $ext         = $file->getClientOriginalExtension();
            $model->name = str_replace('.' . $ext, '', $file->getClientOriginalName());
            $model->ext  = $ext;
            $model->size = $file->getClientSize();
            $model->save();
            $path = $model->makePath();
            $dir  = dirname($path);
            if(!file_exists($dir)) {
                mkdir($dir, 0777, true);
            }
            if($file->move(dirname($path), str_replace($dir . '/', '', $path))) {
                return $model;
            }
        }
        
        return null;
        
    }
    
    function makeUrl() {
        $prefix   = [];
        $prefix[] = '!';
        $prefix[] = 'attaches';
        $prefix[] = mb_substr($this->id, -1);
        $prefix[] = mb_substr($this->id, -2, 1);
        $prefix[] = $this->id;
        $link     = '/' . implode('/', $prefix) . '/';
        $link .= hashids_encode($this->id);
        $link .= '.' . $this->ext;
        $prefix = (string) env('LARAKIT_ATTACH_PREFIX');
        
        return $prefix . $link;
    }
    
    function makePath() {
        return public_path($this->makeUrl());
    }
    
    /**
     * Формирование ссылки на загруженное изображение с проверкой наличия на диске
     *
     * @param null $size
     *
     * @return mixed|null|string
     */
    function getUrlAttribute() {
        $url = $this->makeUrl();
        //на случай получения картинок с другого сервера
        $prefix = (string) env('LARAKIT_ATTACH_PREFIX');
        if(!$prefix) {
            //если со своего - проверяем их наличие
            $file = public_path($url);
            if(file_exists($file)) {
                return $url;
            }
        }
        else {
            return $url;
        }
        
        return null;
    }
    
}

Attach::deleting(
    function ($model) {
        $path = $model->makePath();
        if(file_exists($path)) {
            unlink($path);
        }
    }
);

Attach::saving(
    function ($model) {
        $model->name     = (string) $model->name;
        $model->ext      = (string) $model->ext;
        $model->size     = (int) $model->size;
        $model->priority = (int) $model->priority;
    }
);