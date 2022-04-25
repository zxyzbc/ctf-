<?php

class TestView extends ListView
{
    const FILTER_POS_HEADER='header';
    const FILTER_POS_BODY='body';

    public $columns=array();
    
    public $rowCssClass=array('odd','even');
    
    public $rowCssClassExpression;
    
    public $rowHtmlOptionsExpression;
    
    public $selectableRows=1;

    public $data=array();
    public $filterSelector='{filter}';
    
    public $filterCssClass='filters';
    
    public $filterPosition='body';
    
    public $filter;
    
    public $hideHeader=false;
    


    
    public function renderTableHeader()
    {
        if(!$this->hideHeader)
        {
            echo "<thead>\n";

            if($this->filterPosition===self::FILTER_POS_HEADER)
                $this->renderFilter();


            if($this->filterPosition===self::FILTER_POS_BODY)
                $this->renderFilter();

            echo "</thead>\n";
        }
        elseif($this->filter!==null && ($this->filterPosition===self::FILTER_POS_HEADER || $this->filterPosition===self::FILTER_POS_BODY))
        {
            echo "<thead>\n";
            $this->renderFilter();
            echo "</thead>\n";
        }
    }
    
    public function renderFilter()
    {
        if($this->filter!==null)
        {
            echo "<tr class=\"{$this->filterCssClass}\">\n";

            echo "</tr>\n";
        }
    }
    
    public function renderTableRow($row)
    {
        $htmlOptions=array();
        if($this->rowHtmlOptionsExpression!==null)
        {
            $data=$this->data[$row];
            $options=$this->evaluateExpression($this->rowHtmlOptionsExpression,array('row'=>$row,'data'=>$data));
            if(is_array($options))
                $htmlOptions = $options;
        }

        if($this->rowCssClassExpression!==null)
        {
            $data=$this->dataProvider->data[$row];
            $class=$this->evaluateExpression($this->rowCssClassExpression,array('row'=>$row,'data'=>$data));
        }
        elseif(is_array($this->rowCssClass) && ($n=count($this->rowCssClass))>0)
            $class=$this->rowCssClass[$row%$n];

        if(!empty($class))
        {
            if(isset($htmlOptions['class']))
                $htmlOptions['class'].=' '.$class;
            else
                $htmlOptions['class']=$class;
        }
    }
    public function renderTableBody()
    {
        $data=$this->data;
        $n=count($data);
        echo "<tbody>\n";

        if($n>0)
        {
            for($row=0;$row<$n;++$row)
                $this->renderTableRow($row);
        }
        else
        {
            echo '<tr><td colspan="'.count($this->columns).'" class="empty">';

            echo "</td></tr>\n";
        }
        echo "</tbody>\n";
    }

}
