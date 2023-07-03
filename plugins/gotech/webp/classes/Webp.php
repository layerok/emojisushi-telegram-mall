<?php namespace GoTech\Webp\Classes;

use Exception;
use Illuminate\Http\UploadedFile;


use File as FileHelper;
use GoTech\Webp\Models\Settings as PluginConfig;

class Webp
{
    use \GoTech\Webp\Traits\BrowserCompatibility;

    public function file($file, $quality = null, $imgInfo = false, $compatibility = false)
    {
            // Extensiones permitidas para la generación de webp.
            $extensions = ['.jpg','.jpeg','.png','.bmp','.gif'];

            // Obtener el path y url del archivo original.
            $path       = $this->getPath($file);

            if (empty($path)) {
                return $file;
            }

            $path       = urldecode($path);
            $fileUrl    = url($path);

            // Validar que la extensión de la imágen soporte crear una WebP.
            if(!in_array('.'.FileHelper::extension($path), $extensions)) {
                return $file;
            }

            // Obtener URL absoluta.
            $filePath = $this->getLocalPath($path);

            // Crear nombramiento para imagen.webp, reemplaza la extensión del $filePath.
            $webpFilePath = str_replace($extensions, '.webp', $filePath);
            
            // Validar que la imágen original exista.
            if(!FileHelper::exists($filePath)) {
                
                if(FileHelper::exists($webpFilePath)) {
                    FileHelper::delete($webpFilePath);
                }

                return $file;
            }

            // Obtener información de la imagen original
            $fileInfo = $this->getFileInfo($filePath, $fileUrl);

            // CreateThumb Webp
            if(!FileHelper::exists($webpFilePath)) {
                Self::make(new UploadedFile($filePath, $fileInfo['name'], $fileInfo['mime_type'], $fileInfo['size'], true))->save($webpFilePath, $quality);
            }

            // image.webp url.
            $webpUrl = str_replace($extensions, '.webp', $fileUrl);

            // Obtener información de la imágen.webp
            $webpFileInfo = $this->getFileInfo($webpFilePath, $webpUrl);
            
            if($compatibility) {
                // Mostrar imágen original o webp si el browser es compatible.
                if($this->isCompatibleBrowser()) {
                    $fileInfo = $webpFileInfo;
                    $fileUrl  = $webpUrl;
                }
            } else {
                $fileInfo = $webpFileInfo;
                $fileUrl  = $webpUrl;
            }

            return ($imgInfo) ? $fileInfo : $fileUrl;
    }

    /**
     * getFileInfo
     * =================================================
     * Obtiene la información de la imágen.
     * @param string $path * Ruta absoluta del archivo
     * @param string $url
     * @return array $info
     */
    public function getFileInfo($path, $url)
    {
        return [
            'path'      => $url,
            
            'name'      => basename($path),
            'size'      => filesize($path),
            'mime_type' => mime_content_type($path),
            'extension' => pathinfo($path, PATHINFO_EXTENSION),
            'dimension' => getimagesize($path),
        ];
    }

    /**
     * Obtener la ruta de raiz: 
     * ================================
     * {{ 'assets/images/bg.jpg'|theme }}
     * ej: /themes/visitarsanluis/assets/images/bg.jpg
     * 
     * {{ 'backgrounds/bg1.jpg'|media }}
     * ej: /storage/app/media/backgrounds/bg1.jpg
     * 
     * {{ images.first.path }} (Image URL)
     * ej: /storage/app/uploads/public/5e7/12e/329/5e712e329b87b105488756.jpg
     * 
     * {{ images.first }} ( File object)
     * ej: /storage/app/uploads/public/5e7/12e/329/5e712e329b87b105488756.jpg
     * 
     * {{ images|first.thumb(800, 600, {'mode':'crop'}) }} ( Image resized )
     * ej: /storage/app/uploads/public/5e7/12e/329/5e712e329b87b105488756.jpg
     */
    public function getPath($file)
    {
        $baseUrl = url('/');
        
        if(is_string($file)) {
            $path = $file;
        }
        
        if(is_object($file)) {
            if(@$file->path) {
                $path = $file->path;
            } else {
                $path = (string) $file;
            }
        }

        if(!isset($path)) {
            return;
        }

        return str_replace($baseUrl, '', $path);
    }

    /**
     * getLocalPath
     * ========================================================================
     * Obtener la url absoluta del archivo, recibe como parámetro el path
     * responde ej: /srv/users/dev/apps/myapp/public/themes/mytheme/assets/images/bg.jpg
     * @param string $path
     * @return string
     */
    public function getLocalPath($path)
    {
        return public_path($path);
    }

    /**
     * @param UploadedFile $image
     * @return Cwebp|Traits\WebpTrait
     * @throws Exception
     */
    public static function make(UploadedFile $image)
    {
        $driver = "cwebp";

        if ($driver === 'php-gd') {
            //
        } elseif ($driver === 'cwebp') {
            return (new Cwebp())->make($image);
        }

        throw new Exception('Driver [' . $driver . '] is not supported.');
    }
}
