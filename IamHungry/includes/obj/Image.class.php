<?php

/**
 * Class Image
 * @author Johan Massin
 * @copyright YouMeTrip, 2011
 */
class Image
{
    private $filename;
    private $fullPath;
    private $size;
    private $imageRes;
    public  $error = '';

    public function __construct($filename)
    {
        if(empty($filename) || ($filename == ''))
            throw new Exception('Unable to create Image Object : empty filename.');
        $size = @getimagesize($filename);
        if($size)
        {
            $this->filename = $filename;
            $this->fullPath = (is_file($filename)) ? realpath($filename) : $filename;
            $this->size = $size;
        }else
            $this->error = 'File not found';
    }

    private function destroyImage()
    {
        imagedestroy($this->imageRes);
    }

    public function display()
    {
        if($this->error != '')
            die($this->error);
        readfile($this->filename);
    }


    public function displayThumbnail($width, $height, $format = 'png')
    {
        if($this->error != '')
            die($this->error);
        $output = FOLDER_BASE_SITE.'/data/cache/thumbnails/'.md5($this->fullPath.$width.$height).'.'.$format;
        if(!file_exists($output))
            $this->generateThumbnail($width, $height, $format);
        header('Content-type: image/'.$format);
        readfile($output);
    }

    public function getThumbnail($width, $height, $format = 'png')
    {
        $path = '/data/pictures/thumbnails/'.md5($this->filename.$width.$height).'.'.$format;
        
        $file = FOLDER_BASE_SITE.$path;
        if(!file_exists($file)) {
            $this->testThumbnail($width, $height);
        }
        return URL_BASE_SITE.$path;
    }

    public function deleteThumbnail($width, $height, $format = 'png')
    {
        $file = FOLDER_BASE_SITE.'/data/cache/thumbnails/'.md5($this->fullPath.$width.$height).'.'.$format;
        if(file_exists($file))
            unlink($file);
    }
    
    
    /**
     * Function testThumbnail
     * @author zabuza@tayo.fr
     * @version jeremy, 06/2012
     * Redimensionner une image (jpg) automatiquement
     * @param <int> $width
     * @param <int> $height
     */
    public function testThumbnail($width = 0, $height = 0)
    {
    	$file	= $this->fullPath;
		$x		= $width;
		$y		= $height;
		$size	= getimagesize($file); 
		
		if($size) {
			if ($size['mime']=='image/jpeg' ) { 
				$img_big = imagecreatefromjpeg($file); // On ouvre l'image d'origine 
				$img_new = imagecreate($x, $y); 
				// création de la miniature 
				$img_mini = imagecreatetruecolor($x, $y) 
					or   $img_mini = imagecreate($x, $y); 
				
				// copie de l'image, avec le redimensionnement. 
				imagecopyresized($img_mini,$img_big,0,0,0,0,$x,$y,$size[0],$size[1]); 
				imagejpeg($img_mini, FOLDER_BASE_SITE.'/data/pictures/thumbnails/'.md5($this->filename.$width.$height).'.jpg');
			
			} 
			// TODO faire pour les gif et png
		}    	
    }

}

?>
