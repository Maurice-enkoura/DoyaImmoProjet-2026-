<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageOptimizerService
{
    /**
     * Redimensionne et compresse une image téléchargée avant de la stocker sur le disque public.
     *
     * @param UploadedFile $file Le fichier image téléchargé
     * @param string $folder Le dossier cible (ex: 'biens/images' ou 'propositions/1/images')
     * @param int $maxWidth Largeur maximale (défaut: 1200px)
     * @param int $maxHeight Hauteur maximale (défaut: 1200px)
     * @param int $quality Qualité JPEG/WebP (défaut: 80%)
     * @return string Chemin relatif du fichier stocké
     */
    public static function storeAndOptimize(
        UploadedFile $file,
        string $folder = 'biens/images',
        int $maxWidth = 1200,
        int $maxHeight = 1200,
        int $quality = 80
    ): string {
        $extension = strtolower($file->getClientOriginalExtension());
        $realPath = $file->getRealPath();

        // Si l'extension n'est pas une image supportée par GD ou si la fonction GD n'existe pas, faire un store standard
        if (!extension_loaded('gd') || !in_array($extension, ['jpg', 'jpeg', 'png', 'webp'])) {
            return $file->store($folder, 'public');
        }

        // Créer l'image GD source
        $srcImage = null;
        switch ($extension) {
            case 'jpg':
            case 'jpeg':
                if (function_exists('imagecreatefromjpeg')) {
                    $srcImage = @imagecreatefromjpeg($realPath);
                }
                break;
            case 'png':
                if (function_exists('imagecreatefrompng')) {
                    $srcImage = @imagecreatefrompng($realPath);
                }
                break;
            case 'webp':
                if (function_exists('imagecreatefromwebp')) {
                    $srcImage = @imagecreatefromwebp($realPath);
                }
                break;
        }

        if (!$srcImage) {
            return $file->store($folder, 'public');
        }

        // Correction automatique de l'orientation EXIF si JPEG
        if (function_exists('exif_read_data') && in_array($extension, ['jpg', 'jpeg'])) {
            $exif = @exif_read_data($realPath);
            if (!empty($exif['Orientation'])) {
                switch ($exif['Orientation']) {
                    case 3:
                        $srcImage = imagerotate($srcImage, 180, 0);
                        break;
                    case 6:
                        $srcImage = imagerotate($srcImage, -90, 0);
                        break;
                    case 8:
                        $srcImage = imagerotate($srcImage, 90, 0);
                        break;
                }
            }
        }

        $origWidth = imagesx($srcImage);
        $origHeight = imagesy($srcImage);

        // Calculer les nouvelles dimensions en gardant le ratio
        $ratio = min($maxWidth / $origWidth, $maxHeight / $origHeight);
        if ($ratio < 1) {
            $newWidth = (int) round($origWidth * $ratio);
            $newHeight = (int) round($origHeight * $ratio);
        } else {
            $newWidth = $origWidth;
            $newHeight = $origHeight;
        }

        // Créer la nouvelle image redimensionnée
        $dstImage = imagecreatetruecolor($newWidth, $newHeight);

        // Gérer la transparence pour PNG/WebP
        if (in_array($extension, ['png', 'webp'])) {
            imagealphablending($dstImage, false);
            imagesavealpha($dstImage, true);
            $transparent = imagecolorallocatealpha($dstImage, 255, 255, 255, 127);
            imagefilledrectangle($dstImage, 0, 0, $newWidth, $newHeight, $transparent);
        }

        imagecopyresampled(
            $dstImage,
            $srcImage,
            0, 0, 0, 0,
            $newWidth,
            $newHeight,
            $origWidth,
            $origHeight
        );

        // Capturer le flux binaire de l'image optimisée
        ob_start();
        if ($extension === 'png') {
            imagepng($dstImage, null, 8);
        } elseif ($extension === 'webp') {
            imagewebp($dstImage, null, $quality);
        } else {
            imagejpeg($dstImage, null, $quality);
        }
        $imageData = ob_get_clean();

        // Nettoyer la mémoire GD
        imagedestroy($srcImage);
        imagedestroy($dstImage);

        // Nom de fichier unique
        $filename = Str::random(40) . '.' . $extension;
        $path = trim($folder, '/') . '/' . $filename;

        // Sauvegarder sur le disque public Laravel
        Storage::disk('public')->put($path, $imageData);

        return $path;
    }
}
