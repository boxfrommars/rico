<?php

namespace App\Controllers\Admin;

class UploadController extends \Controller
{
    protected $_imageSettings = array(
        'default' => ['path' => '/assets/image/default/'],
    );

    protected $_fileSettings = array(
        'default' => ['path' => '/assets/file/default/'],
    );

    public function image()
    {
        return $this->_save('image');
    }

    public function file()
    {
        return $this->_save('file');
    }

    public function _save($resourceType)
    {
        $file = \Input::file("{$resourceType}-upload");
        $type = \Input::has('type') ? \Input::get('type') : 'default';

        $input = array(
            $resourceType => $file,
            'type' => $type,
        );

        switch ($resourceType) {
            case  'image':
                $settings = $this->_imageSettings;
                $rules = array('image' => 'required|image');
                break;
            default:
                $settings = $this->_fileSettings;
                $rules = array('file' => 'required');
                break;
        }

        $rules['type'] = 'required|in:' . implode(',', array_keys($settings));

        $validator = \Validator::make($input, $rules);

        if ($validator->fails()) {
            return \Response::json(['success' => false, 'errors' => $validator->errors()]);
        } else {
            $publicPath = $settings[$type]['path'];
            $destinationPath = public_path() . $publicPath;

            $filename = uniqid() . '_' . $file->getClientOriginalName();
            $file->move($destinationPath, $filename);

            return \Response::json(['success' => true, 'path' => asset($publicPath . $filename), 'filename' => $filename]);
        }
    }
}