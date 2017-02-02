<?php

/*
 * Home site
 */

class Contact extends Page
{
    function setMenu()
    {
        $this->menu_text = 'Kontakt';
        $this->menu_image = 'fa fa-envelope';
		$this->menu_priority = 4;
    }
    
    function index()
    {
        $html = new HTML;
        $cv = new ContactView();

        $o = '<pre><code class="language-css">p { color: red }</code></pre>';
        $greeting = $html->well('<h2>Kontakt</h2>'.
            $html->p('<b>Adresse</b></br>secion GmbH</br>Paul-Dessau-Stra&szlig;e 8</br>22761 Hamburg').
            $html->p('<b>Telefon</b></br>+49-40-389071-0').
			$html->p('<b>E-Mail</b></br>info@secion.de')
            );

        $o = $greeting;

        $this->set('title','Kontakt | 0xf.at von secion');
        $this->set('content',$o);
    }
    
    function maySeeThisPage()
    {
            return true;
    }
}