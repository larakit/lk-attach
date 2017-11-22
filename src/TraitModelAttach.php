<?php
namespace Larakit\Attach;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Larakit\NgAdminlte\LkNgAttach;

trait TraitModelAttach {
    
    /**
     * Конфигуратор блоков вложений
     *
     * @return mixed
     */
    static public function attachesConfig() {
        return [
            //            'menu'  => [
            //                'label' => 'Меню ресторана',
            //                'ext'   => ['pdf'],
            //                'max'   => 2,
            //            ],
            //            'docs'  => [
            //                'label' => 'Прочие документы (без ограничений)',
            //            ],
            //            'rules' => [
            //                'label' => 'Правила зоны отдыха',
            //                'ext'   => ['pdf', 'doc'],
            //            ],
        ];
    }
    
    public function getAttachesBlocksAttribute() {
        $ret = static::attachesConfig();
        foreach($ret as $name => $block) {
            $ret[$name]['items']      = [];
            $ext                      = Arr::get($block, 'ext');
            $ret[$name]['exts']       = count($ext) ? '|' . implode('|', $ext) . '|' : null;
            $ret[$name]['url_upload'] = route('attach-upload', [
                'model' => LkNgAttach::getKey(static::class),
                'id'    => $this->id,
                'type'  => $name,
            ]);
        }
        foreach($this->attaches as $attach) {
            $ret[$attach->block]['items'][] = $attach;
        }
        
        return $ret;
    }
    
    public function attaches() {
        return $this->morphMany(Attach::class, 'attachable')->orderBy('priority', 'desc');
    }
    
    static function getAttachKey() {
        $r = new \ReflectionClass(static::class);
        
        return Str::snake($r->getShortName(), '-');
    }
    
}