<?php

/*
 * Home site
 */

class Play extends Page
{
    function setMenu()
    {
        $this->menu_text = 'Start';
        $this->menu_image = 'fa fa-play-circle';
        $this->menu_priority = 2;
    }
    
    function index()
    {
        $html = new HTML;

        $o = '<h1 class="text-center">'.$html->buttonGoto('Level 1 starten','/play/1','','btn-lg').'</h1>';

        $solved = $_SESSION['solved'];

        if($solved)
        {
            ksort($solved);
            foreach($solved as $lid=>$level)
            {
                if($lid<=1) continue;
                $o.= '<h1 class="text-center">'.$html->buttonGoto('Level '.($lid).' starten','/play/'.$lid,'','btn-lg').'</h1>';
            }
        }

        //$o.=$html->well('<h2>Session:</h2>'.nl2br(print_r($_SESSION,true)));
        //$um = new UsersModel;
        //$o.=$html->well('<h2>File:</h2>'.nl2br(print_r($um->loadUser($_SESSION['user'],$_SESSION['sid'],false),true)));
        //var_dump($um->loadUser($_SESSION['user'],$_SESSION['uid'],false));
        

        $this->set('title','Start | 0xf.at von secion');
        //$this->set('header','Header');
        $this->set('content',$o);
        //$this->set('playing',true);
    }

    

    function levelSolved($level)
    {
        $html = new HTML;
        $a = new Algorithms;
        $cm = new CommentModel();
        $hm = new HackitsModel;
        
        $next = $hm->getNextLevel($level);

        $diff = (time()-$_SESSION['starttime'][$level]);
        if(!$_SESSION['solvedtime'][$level])
            $_SESSION['solvedtime'][$level]=$diff;
        else
            $diff = $_SESSION['solvedtime'][$level];

        $nb = '<a href="/replay/'.$level.'">Level wiederholen</a>';
        $o = $html->success('<h2>Level '.($level).' erfolgreich!</h2><h4>Gebrauchte Zeit: '.$a->time_duration($diff).'</h4>').$nb.'<hr>';
        


        
        if($next)
            $o.= $html->buttonGoto('N&auml;chstes Level','/play/'.$next,'','btn-lg');

        $o.= $cm->getCommentForm($level);
        $o.= $cm->getCommentsFromLevel($level);


        if($hm->isUserCounted($level,$_SERVER['REMOTE_ADDR'],session_id()))
        {

            $fh = fopen(ROOT.DS.'stats'.DS.'level'.$level.'.csv','a');
            fwrite($fh, implode(';', array($_SERVER['REMOTE_ADDR'],session_id(),time(),$diff,$_SESSION['user']))."\n");
            fclose($fh);

            $fh = fopen(ROOT.DS.'stats'.DS.'all.csv','a');
            fwrite($fh, implode(';', array($level,$_SERVER['REMOTE_ADDR'],session_id(),time(),$diff,$_SESSION['user']))."\n");
            fclose($fh);
        }

        $_SESSION['solved'][$level] = true;
        $_SESSION['starttime'][$level] = time();

        if($_SESSION['user'])
        {
            $um = new UsersModel;
            $um->updateUser();
        }

        return $o;
    }

    function solutionforlevel7()
    {
        $html = new HTML;

        if(!$_SESSION['levels']['hackit7'])
            $o = '<h3>Zu fr&uuml;! Kommen Sie wieder, wenn Sie Level 7 bearbeiten!</h3>';
        else
        {
            $o = '<h3>Wir wissen nicht wie Sie dies gefunden haben, aber Sie haben \'s! Das Passwort f&uuml;r Level 7 lautet: </h3><h4>'.$_SESSION['levels']['hackit7'].'</h4>';
            $o.=$html->buttonGoto('Zur&uuml;ck zu Level 7','/play/7');
        }
        
        $this->set('title','Gerrits geheime Seite | 0xf.at von secion');
		$this->set('author','Christian Haschek');
        $this->set('content',$o);
        $this->set('playing',true);
    }


    function catchall($params)
    {
        $html = new HTML;
        $level = $params[0];
        if(!$level){$this->index();return;}

        if(file_exists(ROOT.DS.'data'.DS.'levels'.DS.'hackit'.$level.'.php'))
        {
            $lid = $level;
            $f = "Hackit$level";
            $hackit = new $f;

            if(!$_SESSION['starttime'][$level])
                $_SESSION['starttime'][$level] = time();

            $hackit->prepare();

            $o = '<h1>Level '.$level.'</h1>'."\n";
            if($hackit->author)
                $o.= 'erstellt von '.$hackit->author.'<br/><br/>';
            $val = $hackit->isSolved();
            switch(true)
            {
                case $val === NULL:
                    $o.= $hackit->render();
                break;

                case ($val === true || $_SESSION['solved'][$level]):
                    $o = $this->levelSolved($level);
                break;

                case $val === false:
                    if(($_REQUEST['pw']))
                        $o.= $html->error('Falsches Passwort');
                    $o.= $hackit->render();
                break;
            }

            
        }
        else
        {
            $o = $html->error("No such level found");
        }
        

        $this->set('title','0xf | Level '.($lid).', von secion');
        $this->set('author','Christian Haschek');
        $this->set('content',$o);
        $this->set('playing',true);
    }
    
    function maySeeThisPage()
    {
            return true;
    }
}