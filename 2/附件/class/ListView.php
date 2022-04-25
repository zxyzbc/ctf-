<?php

abstract class ListView extends Base
{

    public $tagName='div';
    public $template;

    public function run()
    {
        echo "<".$this->tagName.">\n";
        $this->renderContent();
        echo "<".$this->tagName.">\n";
    }


    public function renderContent()
    {
        ob_start();
        echo preg_replace_callback("/{(\w+)}/",array($this,'renderSection'),$this->template);
        ob_end_flush();
    }


    protected function renderSection($matches)
    {
        $method='render'.$matches[1];
        if(method_exists($this,$method))
        {
            $this->$method();
            $html=ob_get_contents();
            ob_clean();
            return $html;
        }
        else
            return $matches[0];
    }
}
