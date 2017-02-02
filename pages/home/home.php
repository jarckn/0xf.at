<?php

/*
 * Home site
 */

class Home extends Page
{
    function setMenu()
    {
        $this->menu_text = '<img height="60px;" src="https://www.secion.de/files/secion/gfx/secion-logo-white.png"/>';
        //$this->menu_image = 'fa fa-terminal';
        $this->menu_image = false;
        $this->menu_priority = 1;
    }
    
    function index()
    {
        $html = new HTML;
        $hv = new HomeView();

        $lv = new LevelView();

        $levellist = $lv->renderList();

        $o = '<pre><code class="language-css">p { color: red }</code></pre>';
        $greeting = $html->well('<h2>What\'s 0xf.at?</h2>'.
            $html->p('0xf.at (or oxfat it you prefer) is a website without logins or ads where you can solve password-riddles (so called hackits).').
            $html->p('This is a tribute site to the old <a href="http://isatcis.com">Starfleet Academy Hackits</a> site which has been offline for many years now.').
			$html->p('It has been developed by <a href="https://github.com/HaschekSolutions/0xf.at">HaschekSolutions</a>.')
            );

        $o = $greeting.$levellist;


        $this->set('title','Willkommen bei 0xf.at, pr&aumlsentiert von secion');
        $this->set('content',$o);
    }
    
    function maySeeThisPage()
    {
            return true;
    }
}