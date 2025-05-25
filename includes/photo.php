<?php
declare(strict_types=1);



class Photo
{
    private static $absUserDatabase = __DIR__ . "/../images/users/";
    private static $relUserDatabase = "/../images/users/";
    private static $absServiceDatabase = __DIR__ . "/../images/services/";
    private static $relServiceDatabase = "/../images/services/";

    private static $userDefaultPhoto = "/../images/users/default.jpg";

    private static $serviceDefaultPhoto = "/../images/services/default.jpg";

    public static function getUserProfilePic(int $id): string
    {
        $path = self::$absUserDatabase . "" . $id . ".";
        if (file_exists($path . "png")) {
            return self::$relUserDatabase . "" . $id . ".png";
        } elseif (file_exists($path . "jpg")) {
            return self::$relUserDatabase . "" . $id . ".jpg";
        } else {
            return self::$userDefaultPhoto;
        }
    }

    public static function getServicePhotos(int $id): array|string
    {
        $photos = [];
        $dir = self::$absServiceDatabase . "" . $id . "/";
        if (is_dir($dir)) {
            $files = scandir($dir);
            foreach ($files as $file) {
                if ($file !== '.' && $file !== '..') {
                    $photos[] = self::$relServiceDatabase . $id . '/' . $file;
                }
            }
        }
        return $photos;
    }

    public static function getServiceMainPhoto(int $id): string
    {
        $dir = self::$absServiceDatabase . '' . $id . '/';
        if (is_dir($dir)) {
            $files = scandir($dir);
            foreach ($files as $file) {
                if ($file !== '.' && $file !== '..') {
                    return self::$relServiceDatabase . $id . "/" . $file;
                }
            }
        }
        return self::$serviceDefaultPhoto;
    }


    public static function setUserProfilePic(array $files, int $id): void
    {
        if (isset($files['photo']) && is_uploaded_file($files['photo']['tmp_name'])) {
            $fileType = mime_content_type($files['photo']['tmp_name']);
            if (!in_array($fileType, ['image/jpeg', 'image/png']))
                return;

            $ext = $fileType === 'image/png' ? 'png' : 'jpg';
            $uploadDir = self::$absUserDatabase;
            $newFilename = $id . '.' . $ext;

            foreach (glob($uploadDir . $id . '.*') as $existingFile) {
                unlink($existingFile);
            }

            move_uploaded_file($files['photo']['tmp_name'], $uploadDir . $newFilename);
        }
    }



    public static function setServicePhotos(array $files, int $newServiceId): void
    {
        $uploadDir = self::$absServiceDatabase . '' . $newServiceId . '/';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        if (isset($files['photos']) && is_array($files['photos']['tmp_name'])) {
            foreach ($files['photos']['tmp_name'] as $index => $tmpName) {
                if (is_uploaded_file($tmpName)) {
                    $fileType = mime_content_type($tmpName);

                    if (!in_array($fileType, ['image/jpeg', 'image/png']))
                        continue;
                    $ext = $fileType === 'image/png' ? 'png' : 'jpg';
                    $newFilename = $index . '.' . $ext;

                    foreach (glob($uploadDir . $index . '.*') as $existingFile) {
                        unlink($existingFile);
                    }

                    move_uploaded_file($tmpName, $uploadDir . $newFilename);
                }
            }
        }
    }

}