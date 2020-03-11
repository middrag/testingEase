<?php

$cpanel_host='mba.rxforge.in';
$cpanel_user='mdrxforge';
$cpanel_pass='X4&5Q6,)p2i&';

$mi=$_SERVER['DOCUMENT_ROOT'];
define('__ROOT__', dirname(dirname(__FILE__)));
define('__BEFORE__',dirname(dirname(dirname(__FILE__)))); //for before /public_html/

$mid1=array();
 foreach (glob($mi.'/*',GLOB_ONLYDIR) as $value) {
  $mid=basename($value);
array_push($mid1,$mid);
}

//for taking folders before public Html if config file placed before /public_html directory
foreach (glob(__BEFORE__.'/*',GLOB_ONLYDIR) as $value) {

  $mid=basename($value);
array_push($mid1,$mid);
}

$dbname=array();
$dbusername=array();

foreach ($mid1 as $foldername) 
{
  
$iwpconfig=__ROOT__.'/'.$foldername.'/config.php';
$wpconfig=__ROOT__.'/'.$foldername.'/wp-config.php';
$wpconfig1=__BEFORE__.'/'.$foldername.'/wp-config.php';

if(file_exists($iwpconfig) == true && file_exists($wpconfig) == false && file_exists($wpconfig1) == false )
{ 


 $configContent = tokenParser($iwpconfig,'panel');

    if(!empty($configContent))
          {
           array_push($dbname, $configContent['SQL_DATABASE']);
           array_push($dbusername, $configContent['SQL_USERNAME']);

          }
}
 else if(file_exists($iwpconfig) == false && file_exists($wpconfig) == true && file_exists($wpconfig1) == false)
  {
          $configContent = tokenParser($wpconfig,'site');

          if(!empty($configContent))
           {
               array_push($dbname, $configContent['DB_NAME']);
               array_push($dbusername,$configContent['DB_USER']);
            }
    }
      else if(file_exists($iwpconfig) == false && file_exists($wpconfig) == false && file_exists($wpconfig1) == true )
      { 


        $configContent = tokenParser($wpconfig1,'site');
        if(!empty($configContent))
          {
          array_push($dbname, $configContent['DB_NAME']);
           array_push($dbusername,$configContent['DB_USER']);

          }
  
  
      }
        else if(file_exists($iwpconfig) == false && file_exists($wpconfig) == false && file_exists($wpconfig1) == false  )
         {
             //echo "Config File not available.";
         }

  }



 set_time_limit(300);
 define('__ROOT1__', dirname(__FILE__));//current Folder name
 
require_once(__ROOT1__.'/cpaneluapi.class.php');
$cPanel = new cpanelAPI($cpanel_user, $cpanel_pass, $cpanel_host);
//DB should not get delete we should add inside  below array
$dbLiveArray=$dbname;
$list_database = $cPanel->api2->MysqlFE->listdbs();
$json=json_encode($list_database );
$arr=json_decode($json,true);
$arr_length =sizeof($arr);    
$second_array=$arr['cpanelresult']['data'];
 $arr_length2 =sizeof($second_array); 
 $arr_db=array();
  for ($i=0;$i<$arr_length2;$i++)
  {
    $value=$arr['cpanelresult']['data'][$i]['db'];
   
      if(in_array($value,$dbLiveArray))
  {
   //   echo "\n";
      //echo $value;

   }
  else
  {
    $delete_array['db']=$value;    //push values in array insertion of db in array with key
    $output_jason=$cPanel->api2->MysqlFE->deletedb($delete_array);
     sleep(5);
 }
 }

 $dbuserLiveArray1=$dbusername;

$list_database = $cPanel->api2->MysqlFE->listusers();
$json=json_encode($list_database );
$arr=json_decode($json,true);
$arr_length =sizeof($arr);    
$second_array=$arr['cpanelresult']['data'];
 $arr_length2 =sizeof($second_array); 
 $arr_db=array();
  for ($i=0;$i<$arr_length2;$i++)
  {
    $value=$arr['cpanelresult']['data'][$i]['user'];
   
      if(in_array($value,$dbuserLiveArray1))
  {
//echo for db details not deleted.
   }  
  else
  {
    $delete_array1['dbuser']=$value;    //push values in array insertion of db in array with key
    $output_jason=$cPanel->api2->MysqlFE->deletedbuser($delete_array1);
     sleep(5);
 }
 }

echo "DB and DB user delete process completed.";


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
      $sy1='(';
      $sy2=',';
      $sy3=')';
  
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
        } else if ($symbol == $sy2 && $state == 3) {
          $state = 4;
        } else if ($symbol == $sy3 && $state == 5) {
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







