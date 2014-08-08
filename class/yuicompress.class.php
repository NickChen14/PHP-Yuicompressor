<?php

/*
 * @author Nick Chen
 * @version 1.0
 * @copyright Nick Chen
 */
class Yuicompressor {
    
    public $compressSubDirectroy;
    public $affixMin;
    
    private $yuicompressor;
    private $compressPath;
    private $excludeFilePath;
    private $compressFileExt;
    private $compressFileArray;
    private $os;
    
    function __construct()
    {
        $this->os = $this->os_info();
        $this->compressSubDirectroy = FALSE;
        $this->affixMin = '-min';
        $this->yuicompressor = dirname(dirname(__FILE__)).'/jar/yuicompressor-2.4.8.jar';
        $this->compressFileExt = array('js','css');
    }
    
    function __destruct()
    {
        
    }
    
    function os_info()
    {
        
        $oses   = array(
            'Windows' => 'Win16',
            'Windows' => '(Windows 95)|(Win95)|(Windows_95)',
            'Windows' => '(Windows 98)|(Win 9x 4.90)|(Windows ME)',
            'Windows' => '(Windows 98)|(Win98)',
            'Windows' => '(Windows NT 5.0)|(Windows 2000)',
            'Windows' => '(Windows NT 5.1)|(Windows XP)',
            'Windows' => '(Windows NT 5.2)',
            'Windows' => '(Windows NT 6.0)',
            'Windows' => '(Windows NT 6.1)',
            'Windows' => '(Windows NT 6.2)',
            'Windows' => '(Windows NT 4.0)|(WinNT4.0)|(WinNT)|(Windows NT)',
            'Mac' => '(Mac_PowerPC)|(Macintosh)'
        );
        $uagent = strtolower($_SERVER['HTTP_USER_AGENT']);
        foreach ($oses as $os => $pattern) {
            if (preg_match('/' . $pattern . '/i', $uagent)) {
                return $os;
            }
        }
        return 'Unknown';
    }
    
    function setCompressPath($path)
    {
        if(is_dir($path))
        {
            $this->compressPath = $path;
        }
        else
        {
            die('No such file or directory ...');
        }
    }
    
    function setExcludeFilePath($excludeFilePath = array())
    {
        if(is_array($excludeFilePath))
        {
            $this->excludeFilePath = $excludeFilePath;
        }
        else
        {
            die('excludeFilePath... must be an array');
        }
    }
    
    function arrangeCompressFiles($folder = '')
    {
        if($folder !== '')
        {
            $nowFolder = $folder;
        }
        else
        {
            $nowFolder = $this->compressPath;
        }
        
        foreach(scandir($nowFolder) as $file)
        {
            
            if($file !== '.' AND $file !== '..' AND !in_array($nowFolder . $file,$this->excludeFilePath))
            {
                if(is_file($nowFolder.$file))
                {
                    if(in_array(pathinfo($nowFolder . $file, PATHINFO_EXTENSION), $this->compressFileExt))
                    {
                         $this->compressFileArray[] = $nowFolder.$file;
                    }
                }
                else
                {
                    if($this->compressSubDirectroy)
                    {
                        $this->arrangeCompressFiles($nowFolder.$file.'/');
                    }
                }
            }
        }
    }
    
    function getCompressFiles()
    {
        return $this->compressFileArray;
    }
    
    function compressFiles()
    {
        if(is_array($this->compressFileArray) AND count($this->compressFileArray) > 0)
        {
            
            if($this->os == "Mac") {
                $this->yuicompressor = str_replace(' ','\ ',$this->yuicompressor);
            }
            
            foreach($this->compressFileArray as $key => $file)
            {
                $ext = '.'.pathinfo($file, PATHINFO_EXTENSION);
                $compressFileName = str_replace($ext,'',$file);
                
                $cmd = 'java -jar '.$this->yuicompressor.' "'.$file.'" -o "'.$compressFileName.$this->affixMin.$ext.'"';
                echo $cmd .'<br/>';
                exec($cmd,$output,$return);
                if($return === 1)
                {
                    echo $file . '  compress fail...<br/>';
                }
                else
                {
                    echo $file . '  compress success.<br/>';
                }
            }
        }
        else
        {
            die('no file compress...');
        }
    }
    
    
}
?>