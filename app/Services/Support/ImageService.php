<?php

namespace App\Services\Support;

use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Imagick\Driver;
use Intervention\Image\Exceptions\DecoderException;
use Intervention\Image\ImageManager;
use Throwable;

class ImageService
{
    protected $filePath;

    protected $sizes;

    protected $options;

    protected $webp;

    protected $watermark;

    protected $fullPath;

    public function __construct($filePath, $sizes = null, $options = null, $webp = false, $watermark = false)
    {
        $this->filePath = $filePath;
        $this->sizes = $sizes;
        $this->options = $options;
        $this->webp = $webp;
        $this->watermark = $watermark;
        $this->fullPath = $filePath ? Storage::path($filePath) : config('image_service.noThumb'); // Определяем полный путь файла при создании экземпляра класса
    }

    /**
     * @return \Illuminate\Config\Repository|\Illuminate\Foundation\Application|mixed|string|null
     */
    public function resize()
    {
        $imagePath = $this->getImagePath();

        if (! $imagePath) {
            return config('image_service.noThumb');
        }

        $mimeFile = $this->getMime();

        if ($mimeFile && in_array($mimeFile, config('image_service.noMime'))) {
            return $imagePath;
        }

        $originalSize = @getimagesize($this->fullPath);

        if ($originalSize === false) {
            Log::warning('ImageService: failed to read image size, using noThumb', [
                'filePath' => $this->filePath,
                'fullPath' => $this->fullPath,
            ]);

            return config('image_service.noThumb');
        }

        [$widthOriginal, $heightOriginal] = $originalSize;

        if ($widthOriginal <= 0 || $heightOriginal <= 0) {
            return config('image_service.noThumb');
        }

        [$widthResize, $heightResize] = $this->getResizeDimensions($widthOriginal, $heightOriginal);

        return $this->processImage($widthResize, $heightResize);
    }

    /**
     * @return string|null
     *                     Проверка наличия оригинального файла
     */
    private function getImagePath()
    {
        return $this->filePath && Storage::disk('public')->exists($this->filePath)
            ? 'storage/'.$this->filePath
            : null;
    }

    /**
     * @return string|null
     *                     Получение размеров для нарезки
     */
    private function getResizeDimensions($widthOriginal, $heightOriginal)
    {
        $width = $this->sizes[0] ?? $widthOriginal;
        $height = $this->sizes[1] ?? $heightOriginal;

        return [$width, $height];
    }

    /**
     * @return string
     *                Нарезка изображения
     */
    private function processImage($width, $height)
    {
        /** @var FilesystemAdapter $storage */
        $storage = Storage::disk('public');
        $thumbPath = $this->getThumbPath($width, $height);

        if ($storage->exists($thumbPath)) {
            return Storage::url($thumbPath);
        }

        $manager = new ImageManager(new Driver);
        try {
            $image = $manager->read($this->fullPath);
        } catch (DecoderException $e) {
            Log::warning('ImageService: failed to decode image, using noThumb', [
                'filePath' => $this->filePath,
                'fullPath' => $this->fullPath,
                'exception' => $e::class,
                'message' => $e->getMessage(),
            ]);

            return config('image_service.noThumb');
        } catch (Throwable $e) {
            Log::warning('ImageService: unexpected image processing error, using noThumb', [
                'filePath' => $this->filePath,
                'fullPath' => $this->fullPath,
                'exception' => $e::class,
                'message' => $e->getMessage(),
            ]);

            return config('image_service.noThumb');
        }

        $image = $this->setImageSizes($image, $width, $height);

        if ($this->watermark) {
            $this->setWatermark($image, $width, $height);
        }

        if ($this->webp) {
            $image->toWebp(75);
        }

        $image->save(Storage::path($thumbPath));

        return $storage->url($thumbPath);
    }

    /**
     * @return string
     *                Получение пути для нарезанного изображения
     */
    private function getThumbPath($width, $height)
    {
        $filePrefix = 'w'.$width.'_h'.$height.'_';
        $thumbFolder = config('image_service.thumbPath').dirname($this->filePath);

        if ($this->webp) {
            $filePathArray = explode('.', $this->filePath);
            $this->filePath = str_replace('.'.array_pop($filePathArray), '.webp', $this->filePath);
        }

        Storage::disk('public')->makeDirectory($thumbFolder);

        return $thumbFolder.'/'.$filePrefix.basename($this->filePath);
    }

    /**
     * @return mixed
     *               Нарезка разными способомами
     *               cover  - вписывает в заданный размер, вырезает часть изображения (left right center)
     *               contain - вписывается в заданный размер, пустые области становятся белыми
     */
    private function setImageSizes($img, $width, $height)
    {
        return match ($this->options[0]) {
            'cover' => $img->cover($width, $height, $this->options[1] ?? 'center'),
            default => $img->contain($width, $height),
        };
    }

    /**
     * @param  $filePath
     * @return mixed|string|null
     *                           Определение MIME файла
     */
    public function getMime()
    {
        if (file_exists($this->fullPath)) {
            return last(explode('/', mime_content_type($this->fullPath)));
        }

        return null;
    }

    /**
     * @return mixed
     *               Вставка Watermark
     */
    private function setWatermark($image, $width, $height)
    {
        $watermarkPath = Storage::path('watermark.png');
        $manager = new ImageManager(new Driver);
        $watermarkImage = $manager->read($watermarkPath);
        $watermarkImage->scale($width * 0.3, $height * 0.3);
        $image->place(
            $watermarkImage,
            'bottom-right',
            10,
            10,
            25
        );

        return $image;
    }
}
