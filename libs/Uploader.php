<?php
namespace libs;

// 这个项目无论使用多少次其实只需要一个对象   如果多了就浪费
// 单利模式 三私一公  
class Uploader{
    // 不允许new来生成对象
      private function __construct(){ }
      // 不允许克隆
      private function __clone(){}
      // 保存唯一的对象（只有静态的属性属于这个类是唯一的）
      private static $_obj = null;

      public static function make(){
          if(self::$_obj===null){
             //生成一个对象
              self::$_obj = new self;
          }
          return self::$_obj;     
      }
      //   一级目录
      private $_root = ROOT.'public/uploads/';
      private $_ext = ['image/jpeg','image/ejpeg','image/png','image/gif','image/bmp'];
      private $_maxSize = 1024*1024*1.5;
      private $_file;  // 保存用户上传的图片信息
      private $_subDir; //二级目录
      //   上传图片
      // 参数1 表单名
      // 参数2 二级目录
      public function upload($name,$subdir){
          $this->_file = $_FILES[$name];
          $this->_subDir = $subdir;
          if(!$this->_checkType())
        {
            die('图片类型不正确！');
        }

        if(!$this->_checkSize())
        {
            die('图片尺寸不正确！');
        }
          //   创建目录
          $dir=$this->_makeDir();
        //   var_dump($dir);
          //生成文件名
          $name=$this->_makeName();
           //移动图片
          move_uploaded_file($this->_file['tmp_name'],$this->_root.$dir.$name);
         
        //   返回上传图片的路径
          return $dir.$name; 
      }
        // 创建目录
      private function _makeDir(){
         //今天的日期目录
         $dir = $this->_subDir.'/'.date('Ymd');
        //  var_dump($dir);
         if(!is_dir($this->_root.$dir)){
             mkdir($this->_root.$dir,0777,TRUE);
             
         }
         return $dir.'/';
      }
      private function _makeName(){
           // 生成唯一文件名
           $name = md5(time().rand(1,9999));//32位字符串
                
           // 补上文件后缀
           // 先取出原来后缀名字
           // $ext = strrchr( $_FILES['image']['name'] , '.'); 
           $str=strrchr($this->_file['name'],'.');
           // 全名
           //    $name = ;
           // var_dump($name);
           return $name.$str;
           var_dump($name.$str);
      }
      private function _checkType()
      {
          return in_array($this->_file['type'], $this->_ext);
      }
  
      private function _checkSize()
      {
          return $this->_file['size'] < $this->_maxSize;
      }
}

// 生成对象\  上传图片
// $uploader = Uploader::make();