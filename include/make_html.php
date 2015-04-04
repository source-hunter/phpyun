<?php
/*
* $Author ：PHPYUN开发团队
*
* 官网: http://www.phpyun.com
*
* 版权所有 2009-2014 宿迁鑫潮信息技术有限公司，并保留所有权利。
*
* 软件声明：未经授权前提下，不得用于商业运营、二次开发以及任何形式的再次发布。
 */
class html
{
    var $dir;
    var $rootdir; 
    var $name; 
    var $dirname;
    var $url; 
    var $time;
    var $dirtype;
    var $nametype;


    function html($nametype = 'name', $dirtype = 'year', $rootdir = 'html')
    {
        $this -> setvar($nametype, $dirtype, $rootdir);
        }

    function setvar($nametype = 'name', $dirtype = 'year', $rootdir = 'html')
    {
        $this -> rootdir = $rootdir;
        $this -> dirtype = $dirtype;
        $this -> nametype = $nametype;
        }

    function createdir($dir = '')
    {
        $this -> dir = $dir?$dir:$this -> dir;

        if (!is_dir($this -> dir))
            {
            $temp = @explode('/', $this -> dir);
            $cur_dir = '';
            for($i = 0;$i < count($temp);$i++)
            {
                $cur_dir .= $temp[$i] . '/';
                if (!is_dir($cur_dir))
                    {
                    @mkdir($cur_dir, 0777);
                    }
                }
            }
        }

    function getdir($dirname = '', $time = 0)
    {
        $this -> time = $time?$time:$this -> time;
        $this -> dirname = $dirname?$dirname:$this -> dirname;

        switch($this -> dirtype)
        {
        case 'name':
            if(empty($this -> dirname))
                $this -> dir = $this -> rootdir;
            else
                $this -> dir = $this -> rootdir . '/' . $this -> dirname;
            break;
        case 'year':
            $this -> dir = $this -> rootdir . '/' . date("Y", $this -> time);
            break;

        case 'month':
            $this -> dir = $this -> rootdir . '/' . date("Y-m", $this -> time);
            break;

        case 'day':
            $this -> dir = $this -> rootdir . '/' . date("Y-m-d", $this -> time);
            break;
            }

        $this -> createdir();

        return $this -> dir;
        }

    function geturlname($url = '')
    {
        $this -> url = $url?$url:$this -> url;

        $filename = basename($this -> url);
        $filename = @explode(".", $filename);
        return $filename[0];
        }

    function geturlquery($url = '')
    {
        $this -> url = $url?$url:$this -> url;

        $durl = parse_url($this -> url);
        $durl = @explode("&", $durl[query]);
        foreach($durl as $surl)
        {
            $gurl = explode("=", $surl);
            $eurl[] = $gurl[1];
            }
        return join("_", $eurl);
        }

    function getname($url = '', $time = 0, $dirname = '')
    {
        $this -> url = $url?$url:$this -> url;
        $this -> dirname = $dirname?$dirname:$this -> dirname;
        $this -> time = $time?$time:$this -> time;

        $this -> getdir();

        switch($this -> nametype)
        {
        case 'name':
            $filename = $this -> geturlname() . '.htm';
            $this -> name = $this -> dir . '/' . $filename;
            break;

        case 'time':
            $this -> name = $this -> dir . '/' . $this -> time . '.htm';
            break;

        case 'query':
            $this -> name = $this -> dir . '/' . $this -> geturlquery() . '.htm';
            break;

        case 'namequery':
            $this -> name = $this -> dir . '/' . $this -> geturlname() . '-' . $this -> geturlquery() . '.htm';
            break;

        case 'nametime':
            $this -> name = $this -> dir . '/' . $this -> geturlname() . '-' . $this -> time . '.htm';
            break;

            }
        return $this -> name;
        }

    function createhtml($url = '', $time = 0, $dirname = '', $htmlname = '')
    {
        $this -> url = $url?$url:$this -> url;
        $this -> dirname = $dirname?$dirname:$this -> dirname;
        $this -> time = $time?$time:$this -> time;
      
        if(empty($htmlname))
            $this -> getname();
        else
            $this -> name = $dirname . '/' . $htmlname; 


        $content = file($this -> url) or die("Failed to open the url " . $this -> url . " !");;

      

        $content = join("", $content);
        $fp = @fopen($this -> name, "w") or die("Failed to open the file " . $this -> name . " !");
        if(@fwrite($fp, $content))
            return true;
        else
            return false;
        fclose($fp);
        }
   
    function deletehtml($url = '', $time = 0, $dirname = '')
    {
        $this -> url = $url?$url:$this -> url;
        $this -> time = $time?$time:$this -> time;

        $this -> getname();

        if(@unlink($this -> name))
            return true;
        else
            return false;
        }

  
    function deletedir($file)
    {
        if(file_exists($file))
            {
            if(is_dir($file))
                {
                $handle = opendir($file);
                while(false !== ($filename = readdir($handle)))
                {
                    if($filename != "." && $filename != "..")
                        $this -> deletedir($file . "/" . $filename);
                    }
                closedir($handle);
                rmdir($file);
                return true;
                }else{
                unlink($file);
                }
            }
        }

    }
?>
