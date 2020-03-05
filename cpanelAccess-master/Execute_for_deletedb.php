
<?php

set_time_limit(300);
require_once('/home/midhubala1/public_html/Cpanel_API_deleteDB/cpaneluapi.class.php');
$cPanel = new cpanelAPI('midhubala1', 'mid@123', 'midhubala1.rxforge.in');
//DB should not get delete we should add inside  below array
$dbLiveArray=array('midhubal_midDb','midhubal_clone_878300','midhubal_clone_b87f40','midhubal_clone_1eba50','midhubal_clone_f0a040','midhubal_iwpdb29','midhubal_iwpdb19','midhubal_clone_c78ef0','midhubal_clone_5e3f10','midhubal_clone_f576d0','midhubal_clone_78c3e0','midhubal_clone_dcc840','midhubal_clone_111e10','midhubal_clone_121790','midhubal_clone_1f7c60','midhubal_clone_e0ef20','midhubal_clone_e0ef20','midhubal_clone_e0ef20','midhubal_clone_06dc10','midhubal_clone_8b9560','midhubal_clone_c8a5d0','midhubal_clone_052b70','midhubal_secfix1','midhubal_wpfresh','midhubal_clone_e5b280','midhubal_clone_e5b280','midhubal_clone_c8f1b0','midhubal_clone_1a5870','midhubal_clone_1a5870','midhubal_clone_2391e0');


//'midhubal_midDb','midhubal_clone_878300','midhubal_clone_b87f40','midhubal_clone_1eba50','midhubal_clone_f0a040','midhubal_iwpdb29','midhubal_iwpdb19','midhubal_clone_c78ef0','midhubal_clone_5e3f10','midhubal_clone_f576d0','midhubal_clone_78c3e0','midhubal_clone_dcc840','midhubal_clone_111e10','midhubal_clone_121790','midhubal_clone_1f7c60','midhubal_clone_e0ef20','midhubal_clone_e0ef20','midhubal_clone_e0ef20','midhubal_clone_06dc10','midhubal_clone_8b9560','midhubal_clone_c8a5d0','midhubal_clone_052b70','midhubal_secfix1','midhubal_wpfresh','midhubal_clone_e5b280','midhubal_clone_e5b280','midhubal_clone_c8f1b0','midhubal_clone_1a5870','midhubal_clone_1a5870'


$list_database = $cPanel->api2->MysqlFE->listdbs();
//print_r($list_database);
$json=json_encode($list_database );
$arr=json_decode($json,true);
$arr_length =sizeof($arr);    
//echo $arr_length;
//$value=$arr['cpanelresult']['data'][5]['db'];

$second_array=$arr['cpanelresult']['data'];
 $arr_length2 =sizeof($second_array); 
 //echo $arr_length2;
 $arr_db=array();
  for ($i=0;$i<$arr_length2;$i++)
  {
    $value=$arr['cpanelresult']['data'][$i]['db'];
      if(in_array($value,$dbLiveArray))
  {
     // echo "\n";
    //  echo $value;
   }
  else
  {
    $delete_array['db']=$value;    //push values in array insertion of db in array with key
    
     echo "inserted Db value ".$value."<br>";
/* $output_jason rerutn values come as stdclass object for convert that need to do encode and decode jason to get as array below code for decode that use it if necessary .
   $json_deleteres=json_encode($output_jason );  
   $decodedjson_val=json_decode($json_deleteres,true);
   
   */
  }
 }
  $output_jason=$cPanel->api2->MysqlFE->deletedb($delete_array);
     sleep(5);
        $json_deleteres=json_encode($output_jason );  
   $decodedjson_val=json_decode($json_deleteres,true);
   print_r($decodedjson_val);
 
 echo "Cpanel Deleting Operation Completed <br>";
/* 
 //check after delete available db count
$list_database1 = $cPanel->api2->MysqlFE->listdbs();

$json1=json_encode($list_database1 );
//print_r($json);
$arr1=json_decode($json1,true);
$third_array=$arr1['cpanelresult']['data'];
$arr_length3 =sizeof($third_array); 
echo $arr_length3;
*/


?>






