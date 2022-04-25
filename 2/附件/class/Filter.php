<?php


class Filter extends Base
{

    public $lastModified;

    public $lastModifiedExpression;

    public $etagSeed;

    public $etagSeedExpression;
    
    public $cacheControl='max-age=3600, public';

    
    public function preFilter($filterChain)
    {

        $lastModified=$this->getLastModifiedValue();
        $etag=$this->getEtagValue();

        if($etag===false&&$lastModified===false)
            return true;

        if($etag)
            header('ETag: '.$etag);

        if(isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])&&isset($_SERVER['HTTP_IF_NONE_MATCH']))
        {
            if($this->checkLastModified($lastModified)&&$this->checkEtag($etag))
            {
                $this->send304Header();
                $this->sendCacheControlHeader();
                return false;
            }
        }
        elseif(isset($_SERVER['HTTP_IF_MODIFIED_SINCE']))
        {
            if($this->checkLastModified($lastModified))
            {
                $this->send304Header();
                $this->sendCacheControlHeader();
                return false;
            }
        }
        elseif(isset($_SERVER['HTTP_IF_NONE_MATCH']))
        {
            if($this->checkEtag($etag))
            {
                $this->send304Header();
                $this->sendCacheControlHeader();
                return false;
            }

        }

        if($lastModified)
            header('Last-Modified: '.gmdate('D, d M Y H:i:s', $lastModified).' GMT');

        $this->sendCacheControlHeader();
        return true;
    }

    
    protected function getLastModifiedValue()
    {
        if($this->lastModifiedExpression)
        {
            $value=$this->evaluateExpression($this->lastModifiedExpression);
            if(is_numeric($value)&&$value==(int)$value)
                return $value;
            elseif(($lastModified=strtotime($value))===false)
                throw new Exception("error");
            return $lastModified;
        }

        if($this->lastModified)
        {
            if(is_numeric($this->lastModified)&&$this->lastModified==(int)$this->lastModified)
                return $this->lastModified;
            elseif(($lastModified=strtotime($this->lastModified))===false)
                throw new Exception("error");
            return $lastModified;
        }
        return false;
    }

    
    protected function getEtagValue()
    {
        if($this->etagSeedExpression)
            return $this->generateEtag($this->evaluateExpression($this->etagSeedExpression));
        elseif($this->etagSeed)
            return $this->generateEtag($this->etagSeed);
        return false;
    }

    
    protected function checkEtag($etag)
    {
        return isset($_SERVER['HTTP_IF_NONE_MATCH'])&&$_SERVER['HTTP_IF_NONE_MATCH']==$etag;
    }

    
    protected function checkLastModified($lastModified)
    {
        return isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])&&@strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE'])>=$lastModified;
    }

    
    protected function send304Header()
    {
        header('HTTP/1.1 304 Not Modified');
    }
    
    protected function generateEtag($seed)
    {
        return '"'.base64_encode(sha1(serialize($seed),true)).'"';
    }
}
