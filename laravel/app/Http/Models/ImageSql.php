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
//        $json = '{ "possible_returning_customers" : [
//             {
//             		"persons_id" : 2,
//                "confident_rate" : 0.85,
//                "img_path" : "/images/guests/31233b4b1b51ac91e391e5afe130e2gd.jpg"
//             },
//             {
//             		"persons_id" : 3,
//                "confident_rate" : 0.75,
//                "img_path" : "/images/guests/41233b4b1b51ac91e391e5afe130e2gd.jpg"
//             },
//             {
//             		"persons_id" : 4,
//                "confident_rate" : 0.55,
//                "img_path" : "/images/guests/51233b4b1b51ac91e391e5afe130e2gd.jpg"
//             }
//          ]}';
//
//        $this->getConn()->table('faces')
//            ->where('idFaces', '=', 4)
//            ->update(array('possible_returning_customers' => $json));

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


    public function createImageFromInputArray(array $input) {
        $insertArray = array(
            'feature' => json_encode($input['feature']),
            'img_path' => $input['img_path'],
            'faces_id' => $input['faces_id']
        );

        $id = $this->getConn()->table('images')
            ->insertGetId($insertArray);

        return $this->getImage($id);
    }
    
    public function deleteImagesByIds($imagesId) {

        $imagesIdArray = explode(',', $imagesId);
        $this->getConn()->table('images')
            ->whereIn('images_id', $imagesIdArray)
            ->delete();
    }
}