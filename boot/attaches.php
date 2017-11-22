<?php
/**
 * Created by Larakit.
 * Link: http://github.com/larakit
 * User: Alexey Berdnikov
 * Date: 19.06.17
 * Time: 11:24
 */

//################################################################################
//      загрузка вложения
//################################################################################
Route::post('!/attach/{model}/{id}/{type}/upload', function () {
    $model = \Larakit\NgAdminlte\LkNgAttach::model();
    if($model) {
        $type   = Request::route('type');
        $attach = \Larakit\Attach\Attach::uploadToModel($model, $type);
        if($attach) {
            return [
                'result'  => 'success',
                'message' => 'Вложение успешно загружено',
                'type'    => $type,
                'model'   => \Larakit\NgAdminlte\LkNgAttach::model()->toArray(),
            ];
        } else {
            return [
                'result'  => 'error',
                'message' => 'Вложение не загружено',
                'type'    => $type,
                'model'   => $model->toArray(),
            ];
        }
        
    }
})->name('attach-upload');

//################################################################################
//      удаление файла
//################################################################################
Route::any('!/attach/{hash}/delete', function () {
    $id = (int) \Illuminate\Support\Arr::get(hashids_decode(\Request::route('hash')), 0);
    $attach = \Larakit\Attach\Attach::find($id);
    if($attach) {
        if($attach->delete()) {
            return [
                'result'  => 'success',
                'message' => 'Вложение удалено',
                'model'   => $attach->attachable->toArray(),
            ];
        }
    }
    
    return [
        'result'  => 'error',
        'message' => 'Вложение не найдено',
    ];
})->name('attach-delete');

//################################################################################
//      редактирование файла
//################################################################################
Route::any('!/attach/{hash}/save', function () {
    $id     = (int) \Illuminate\Support\Arr::get(hashids_decode(\Request::route('hash')), 0);
    $data   = \Request::only([
        'name',
        'priority',
    ]);
    $errors = \Larakit\Attach\AttachValidator::instance()
        ->validate($data);
    
    if($errors) {
        return [
            'result'  => 'error',
            'message' => implode('<br>', $errors),
            'errors'  => $errors,
        ];
    }
    $model = \Larakit\Attach\Attach::findOrFail($id);
    $model->fill($data);
    $model->save();
    $message = 'Данные вложения успешно обновлены!';
    
    return [
        'result'  => 'success',
        'model'   => $model->attachable->toArray(),
        'message' => $message,
    ];
})->name('attach-save');


