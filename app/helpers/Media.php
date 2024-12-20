<?php
class Media{



    public static function base64($file){
        return base64_encode(file_get_contents($file));
    }

    public static function audio($audio){
 
        $audio_path = APPMEDIA.'audios/';
        $audioData = file_exists($audio_path.$audio) ? self::base64($audio_path.$audio) : self::base64($audio_path.'default.mp3');

        return 'data:audio/mpeg;base64, '.$audioData; 
    }

    public static function avatar($avatar){

        $avatar_path = APPMEDIA.'avatars/';
        $imageData = file_exists($avatar_path.$avatar) ? self::base64($avatar_path.$avatar) :  self::base64($avatar_path.'default.jpg');

        return 'data:image/jpeg;base64,'.$imageData;
    }

    public static function cover($cover){

        $cover_path = APPMEDIA.'covers/';
        $imageData = file_exists($cover_path.$cover) ? self::base64($cover_path.$cover) : self::base64($cover_path.'default.jpg');
       
        return 'data:image/jpeg;base64,'.$imageData;
    }

    public static function video($video){
        
        $video_path = APPMEDIA.'videos/';
        $videoData = file_exists($video_path.$video) ? self::base64($video_path.$video) : self::base64($video_path.'default.mp4');

        return 'data:video/mp4;base64,'.$videoData;   
    }


}
?>