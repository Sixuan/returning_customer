<?php
/**
 * Created by PhpStorm.
 * User: sixuanliu
 * Date: 3/12/16
 * Time: 6:29 PM
 */

namespace App\Http\Models;


class ImageSql extends BaseModelSql
{
    /**
     * @var ImageSql
     */
    private static $imageSqlSingleton;

    /**
     * @return ImageSql
     */
    public static function getInstance() {
        if(self::$imageSqlSingleton == null) {
            self::$imageSqlSingleton = new ImageSql();
        }
        return self::$imageSqlSingleton;
    }

    public function getImage($imageId) {
        $image = $this->getConn()->table('images as i')
            ->leftJoin('faces as f', 'i.faces_id', '=', 'f.faces_id')
            ->where('i.images_id', '=', $imageId)
            ->first([
                'i.faces_id',
                'i.img_path',
                'f.age',
                'f.gender',
                'f.cameras_id',
                'f.persons_id',
                'f.confident_rate'
            ]);

        return (array)$image;
    }

    public function getImagesByPrimaryKeyId($idImages) {
        $image = $this->getConn()->table('images as i')
            ->leftJoin('faces as f', 'i.faces_id', '=', 'f.faces_id')
            ->where('i.idImages', '=', $idImages)
            ->first([
                'i.faces_id',
                'i.img_path',
                'f.age',
                'f.gender',
                'f.cameras_id',
                'f.persons_id',
                'f.confident_rate'
            ]);

        return (array)$image;
    }

    public function createImageFromInputArray(array $input) {
        $insertArray = array(
            'feature' => json_encode($input['feature']),
            'img_path' => $input['img_path'],
            'faces_id' => $input['faces_id'],
            'images_id' => $input['images_id']
        );

        $this->getConn()->table('images')
            ->insert($insertArray);

        return $this->getImage($input['images_id']);
    }

}