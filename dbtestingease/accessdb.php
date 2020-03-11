<?php

 $folderpath=$_GET['fname'];
 $panelORsite=$_GET['pors'];
 $dbshoworopen=$_GET['db'];
 $xpanel=$_GET['do'];
 $username=get_current_user();
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
           
                 $url='http://'.$server.'/db/adminer.php?username='.$username.'&db='.$config['SQL_DATABASE'].'&pass='.$config['SQL_PASSWORD'] ;
                 header("Location: ".$url);
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
          $takeDB=tokenParser($file,'site');
          $DB_NAME=$takeDB['DB_NAME'];
          $DB_USERNAME=$takeDB['DB_USER'];
          $DB_PASS=$takeDB['DB_PASSWORD'];
          $DB_PREFIX=$takeDB['$table_prefix'];
      if( $dbshoworopen =='show')
          {
        
         echo '<br> dbname ='.$DB_NAME.'<br> username ='.$DB_USERNAME.'<br> pass ='.$DB_PASS.'<br> prefix ='.$DB_PREFIX;
            }
         
          else if($dbshoworopen=='open')
         
          {
       
              $url='http://'.$server.'/db/adminer.php?username='.$username.'&db='.$DB_NAME.'&pass='.$DB_PASS ;

               header("Location: ".$url);
           
          }       
           
      }
      else
      {
        echo '<br> Config File not found';
      }

    }



function tokenParser($wpconfig_path,$val) {

    $defines = array();
    $wpconfig_file = @file_get_contents($wpconfig_path);

    if (!function_exists('token_get_all')) {
      return $defines;
    }

    if ($wpconfig_file === false) {
      return $defines;
    }

    $defines = array();
    $tokens  = token_get_all($wpconfig_file);
  
    $token   = reset($tokens);   //reset points the value to first value of the array
    

    if($val=='site'){
      $tokenvalue='define';
      $token1='$table_prefix';
      $sy1='(';
      $sy2=',';
      $sy3=')';
   $sy4=';';
        }else if($val=='panel'){
          $tokenvalue='$config';
          $sy1='[';
          $sy2=']';
          $sy3=';';
          }
    

    while ($token) {
    

      if (is_array($token)) {

    

        if ($token[0] == T_WHITESPACE || $token[0] == T_COMMENT || $token[0] == T_DOC_COMMENT) {
          // do nothing
        } else if ($token[0] == T_VARIABLE && strtolower($token[1]) == $tokenvalue) {  //for taking panel config need to define as T_VARIABLE  because we are getting values inside the $config
          $state = 1;
        } else if ($token[0] == T_VARIABLE && strtolower($token[1]) == $token1) {  //for taking panel config need to define as T_VARIABLE  because we are getting values inside the $table_prefix
         $key   = $token[1];
          $state   = 3;
         
        }else if ($token[0] == T_STRING && strtolower($token[1]) == $tokenvalue) {             //for taking site config need to define as T_String  because we are getting values inside define
          $state = 1;
        } else if ($state == 2 && isConstant($token[0])) {
          $key   = $token[1];
          $state   = 3;
      
        } else if ($state == 4 && isConstant($token[0])) {
          $value   = $token[1];
         
          $state   = 5;
        }
      } else {
      
        $symbol = trim($token);
        if ($symbol == $sy1 && $state == 1) {
          $state = 2;
        }
       else if ($symbol == $sy2 && $state == 3) {
          $state = 4;
        } else if ($state == 3) {
          
          $state = 4;
        }else if ($symbol == $sy3 || $symbol == $sy4 && $state == 5) {
          $defines[tokenStrip($key)] = tokenStrip($value);
          $state = 0;
        }
      }
      $token = next($tokens);
      
    }

    return $defines;

  }

  function tokenStrip($value)
  {
    return preg_replace('!^([\'"])(.*)\1$!', '$2', $value);
  }

   function isConstant($token)
  {
    return $token == T_CONSTANT_ENCAPSED_STRING || $token == T_STRING || $token == T_LNUMBER || $token == T_DNUMBER;
  }



