<?php
namespace Util;
class Image
{
    public function resizeImage($image, $destImage, $width='', $height='',$mode=0, $bgr=255, $bgg=255, $bgb=255)
    {
		

        $info = getimagesize($image);        
        
        if(!$info){
			return false;
		}
        
        $origWidth = $info[0];
        $origHeight = $info[1];
        
        if($width == '' || $width == 0){
			$width = $origWidth;
		}
		if($height == '' || $height == 0){
			$height = $origHeight;
		}
        
        $destWidth = $width;
        $destHeight = $height;     
        
        $mime = $info['mime'];
        
        switch ($mime) {
            case "image/jpg":
            case "image/jpeg":
                $ext = 'jpg';
                $tmpImage = imagecreatefromjpeg($image);
                break;
            case "image/gif":
                $ext = 'gif';
                $tmpImage = imagecreatefromgif($image);
                break;
            case "image/png":
                $ext = 'png';
                $tmpImage = imagecreatefrompng($image);
                break;
            default:
                return false;
                break;                
        }   
        
        if(!$tmpImage){
			return false;
		}

        if ($mode === 0) { //resize, to fit within bounding box

            //get image with the max width/height to fit inside bounding box
            if ($origWidth<$origHeight) {
                $ratio =  $origWidth/$origHeight;
                $destWidth = $ratio * $height;
            } else if ($origWidth>=$origHeight) {
                $ratio = $origHeight/$origWidth;
                $destHeight = $ratio * $width;
            }
            $newImage = imagecreatetruecolor($destWidth, $destHeight); 
            if($ext == 'png'){
				imagealphablending( $newImage, false );
				imagesavealpha( $newImage, true );
			}           
            imagecopyresampled($newImage, $tmpImage, 0, 0, 0, 0, $destWidth, $destHeight, $origWidth, $origHeight);

        } else if ($mode===1) { //crop to fill entire bounding box from center

            //swap greater than signs to get image with the max width/height to not fit inside bounding box            
            if ($origWidth>$origHeight) {
                $ratio =  $origWidth/$origHeight;
                $destWidth = $ratio * $height;
            } else if ($origWidth<=$origHeight) {
                $ratio = $origHeight/$origWidth;
                $destHeight = $ratio * $width;
            }

            $ratio = $origHeight/$origWidth;
            $destHeight = $ratio * $width;

            $cropImage = imagecreatetruecolor($destWidth, $destHeight);
            if($ext == 'png'){
				imagealphablending( $newImage, false );
				imagesavealpha( $newImage, true );
			}    
            imagecopyresampled($cropImage, $tmpImage, 0, 0, 0, 0, $destWidth, $destHeight, $origWidth, $origHeight);
                        
            //now strip off the top/bottom or left/right            
            if ($destWidth>$destHeight) {//landscape            
                $newImage = imagecreatetruecolor($width, $height);
                imagefilledrectangle($newImage,0,0,$width,$height,imagecolorallocate($newImage,$bgr,$bgg,$bgb));                 
                $x=($destWidth-$width)/2;
                imagecopyresampled($newImage, $cropImage, 0, 0, $x, 0, $width, $height, $width, $height);        
            } else {//portrait
                $newImage = imagecreatetruecolor($width, $height);
				 if($ext == 'png'){
					imagealphablending( $newImage, false );
					imagesavealpha( $newImage, true );
				}    
                imagefilledrectangle($newImage,0,0,$width,$height,imagecolorallocate($newImage,$bgr,$bgg,$bgb));                 
                $y=($destHeight-$height)/2;
                imagecopyresampled($newImage, $cropImage, 0, 0, 0, $y, $width, $height, $width, $height);                          
            }
        } else if ($mode === 2) { //resize to fit within bounding box, and fill extra pixels with background color
            
            //get image with the max width/height to fit inside bounding box
            if ($origWidth<$origHeight) {
                $ratio =  $origWidth/$origHeight;
                $destWidth = $ratio * $height;
            } else if ($origWidth>=$origHeight) {
                $ratio = $origHeight/$origWidth;
                $destHeight = $ratio * $width;
            }
            $newImage = imagecreatetruecolor($width, $height); //create image with bounding box dimensions and fill with color
            if($ext == 'png'){
				imagealphablending( $newImage, false );
				imagesavealpha( $newImage, true );
			}    
            imagefilledrectangle($newImage,0,0,$width,$height,imagecolorallocate($newImage,$bgr,$bgg,$bgb)); 
            if ($destWidth>$destHeight) {//landscape                   
                $y=($height-$destHeight)/2;
                imagecopyresampled($newImage, $tmpImage, 0, $y, 0, 0, $destWidth, $destHeight, $origWidth, $origHeight);
            } else {                
                $x=($width-$destWidth)/2;                
                imagecopyresampled($newImage, $tmpImage, $x, 0, 0, 0, $destWidth, $destHeight, $origWidth, $origHeight);                
            }

        }
                
        switch ($ext) {
            case "jpg":
            case "jpeg":
                $success = imagejpeg($newImage,$destImage,100);
                break;
            case "gif":
                $success = imagegif($newImage,$destImage);
                break;
            case "png":
                $success = imagepng($newImage,$destImage,9);
                break;
            default : 
                $success = false;            
                break;
        }
        //chmod($destImage,0666);
    
        return $success;
    }       
}

?> 
