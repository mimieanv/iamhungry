<?php

/**
 * class Picture
 * Used to handle pictures
 */
class Picture extends SqlRow {

    public $image = null;
	public $file_name;
	public $full_path;

    /**
     * @param $id, id of the picture in DB
     */
    public function __construct($id) {
        if(!is_numeric($id))
            $id = sql_result("SELECT id FROM picture WHERE filename = '{$id}'; ");
        if($id == -1)
            throw new Exception('Incorrect picture name');
        parent::__construct('picture', $id);
        $this->image = new Image(FOLDER_BASE_SITE.'/data/pictures/'.$this->filename);
        $this->full_path = FOLDER_BASE_SITE.'/data/pictures/'.$this->filename;
    }
    
    /**
     * Function getUrl
     * Return URI for the current picture (raw - not resized)
     */
    public function getUrl(){
        return URL_BASE_SITE."/data/pictures/".$this->filename;
    }

    public function getThumbnail($width = 0, $height = 0, $format = 'png')
    {
        return $this->image->getThumbnail($width, $height, $format);
    }

    /**
     * Function getProducts
     * Returns the products where the picture is used
     * @return Array of products objects
     */
    public function getProducts()
    {
        $productsList = Array();
        $qProducts = DB::getInstance()->query("SELECT id_product FROM product_picture WHERE id_picture = '{$this->id}' ;");
        while($product = $qProducts->fetch_object())
            $productsList[] = new Product($product->id);

        return $productsList;
    }
}
