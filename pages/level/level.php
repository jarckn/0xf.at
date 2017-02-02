<?php

/*
 * Levels site
 */

class Level extends Page
{
    function setMenu()
    {
        $this->menu_text = 'Level';
        $this->menu_image = 'fa fa-list';
        $this->menu_priority = 3;
    }
    
    function index()
    {
        $html = new HTML;
        $lv = new LevelView();

        $o = $lv->renderList(true);
        $o.= $lv->renderGraph();
        $o.= $lv->renderClockReport();


        $this->set('title','Level | 0xf.at von secion');
        $this->set('content',$o);
    }
    
    function maySeeThisPage()
    {
        return true;
    }
}