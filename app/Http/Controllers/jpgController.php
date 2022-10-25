<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class jpgController extends Controller
{
    /** @var array */

    public function conver(Request $request)
    {
        $from = $request->file('formFile');
        $type = $request->type;

        if ($request->type != 'gif' && $request->type != 'jpeg' && $request->type != 'jpg' && $request->type != 'png' && $request->type != 'webp') {
            return view('test');
        }
        $type_save = $from->extension();
        if ($type_save != 'gif' && $type_save != 'jpeg' && $type_save != 'jpg' && $type_save != 'png' && $type_save != 'webp') {
            return view('test');
        }

        $to = $_SERVER['DOCUMENT_ROOT'] . '/file/my-image.' . $type;
        if ($request->type == 'png') {
            $quality = 9;
        } else {
            $quality = 100;
        }
        $converter = new ImageConverter();
        return $converter->convert($from, $to, $quality);
    }

    public function test()
    {
        return view('test');
    }
}

class ImageConverter
{
    /** @var array */
    private $imageFormat = [
        'gif',
        'jpeg',
        'jpg',
        'png',
        'webp',
    ];

    /** @var array */
    private $constImageFormat = [
        IMAGETYPE_GIF => 'gif',
        IMAGETYPE_JPEG => 'jpeg',
        IMAGETYPE_PNG => 'png',
        IMAGETYPE_WEBP => 'webp',
    ];

    /**
     * Do image conversion work
     *
     * @param string $from
     * @param string $to
     *
     * @return resource
     * @throws \InvalidArgumentException
     */
    public function convert($from, $to, $quality = null)
    {
        $image = $this->loadImage($from);
        if (!$image) {
            throw new \InvalidArgumentException(sprintf('Cannot load image from %s', $from));
        }

        $this->saveImage($to, $image, $quality);

        // $result = "<img src=" . $to . "> ";
        $this->file_force_download($to);
    }

    private function file_force_download($file)
    {
        if (file_exists($file)) {
            // сбрасываем буфер вывода PHP, чтобы избежать переполнения памяти выделенной под скрипт
            // если этого не сделать файл будет читаться в память полностью!
            if (ob_get_level()) {
                ob_end_clean();
            }
            // заставляем браузер показать окно сохранения файла
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . basename($file));
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            // читаем файл и отправляем его пользователю
            readfile($file);
            unlink($file);
            // exit;
        }
    }

    private function loadImage($from)
    {
        $extension = $this->getRealExtension($from);

        if (!array_key_exists($extension, $this->constImageFormat)) {
            throw new \InvalidArgumentException(sprintf('The %s extension is unsupported', $extension));
        }

        $format = $this->constImageFormat[$extension];

        switch ($format) {
            case 'gif':
                $image = imagecreatefromgif($from);
                break;
            case 'jpg':
            case 'jpeg':
                $image = imagecreatefromjpeg($from);
                break;
            case 'png':
                $image = imagecreatefrompng($from);
                break;
            case 'webp':
                $image = imagecreatefromwebp($from);
                break;
            default:
                $image = null;
        }
        return $image;
    }

    private function saveImage($to, $image, $quality)
    {
        $extension = $this->getExtension($to);

        if ($extension === 'jpg') {
            $extension = 'jpeg';
        }

        if (!in_array($extension, $this->imageFormat)) {
            throw new \InvalidArgumentException(sprintf('The %s extension is unsupported', $extension));
        }
        if (!file_exists(dirname($to))) {
            $this->makeDirectory($to);
        }

        if (isset($quality) && !is_int($quality)) {
            throw new \InvalidArgumentException(sprintf('The %s quality has to be an integer', $quality));
        }

        switch ($extension) {
            case 'gif':
                $image = imagegif($image, $to);
                break;
            case 'jpg':
            case 'jpeg':
                if ($quality < -1 && $quality > 100) {
                    throw new \InvalidArgumentException(sprintf('The %s quality is out of range', $quality));
                }
                $image = imagejpeg($image, $to, $quality);
                break;
            case 'png':
                if ($quality < -1 && $quality > 9) {
                    throw new \InvalidArgumentException(sprintf('The %s quality is out of range', $quality));
                }
                $image = imagepng($image, $to, $quality);
                break;
            case 'webp':
                if ($quality < 0 || $quality > 100) {
                    throw new \InvalidArgumentException(sprintf('The %s quality is out of range', $quality));
                }
                $image = imagewebp($image, $to, $quality);
                break;
            default:
                $image = null;
        }

        return $image;
    }

    /**
     * Определение расширения
     */
    private function getRealExtension($path)
    {
        $extension = exif_imagetype($path);

        if (!array_key_exists($extension, $this->constImageFormat)) {
            throw new \InvalidArgumentException(sprintf('Cannot detect %s extension', $path));
        }

        return $extension;
    }

    /**
     * Получение расширения
     *
     * @param string $path
     *
     * @return string
     */
    private function getExtension($path)
    {
        $pathInfo = pathinfo($path);

        if (!array_key_exists('extension', $pathInfo)) {
            throw new \InvalidArgumentException(sprintf('Cannot find extension from %s', $path));
        }

        return $pathInfo['extension'];
    }

    /**
     * Создания директории
     *
     * @return bool
     * @throws \InvalidArgumentException
     */
    private function makeDirectory($to)
    {
        $result = @mkdir(dirname($to), 0755);

        if (!$result) {
            throw new \InvalidArgumentException(\sprintf('Cannot create %s directory', $to));
        }

        return $result;
    }
}
