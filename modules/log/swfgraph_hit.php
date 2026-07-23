<?php
//Make sure there are no spaces before "<?php"

include_once('../../config.inc.php');
include_once('../../include/lanai/php_compat.php');
include_once('../../include/adodb/adodb.inc.php');
$ADODB_CACHE_DIR=$cfg_dir."/cache/";
$db=ADONewConnection(lanai_normalize_dbtype($dbtype));
$db->NConnect($dbhost, $dbuser, $dbpw, $dbname);
$cfg['tablepre']=$tablepre;

// load log class
include_once("module.php");
// load chart function
include "../../include/swfchart/charts.php";

// Some data
$ydata1 = array();
$ydata2 = array();
$datax = array();

$oblog=new SysLog();
$rs=$oblog->loadLastStat(7);

//data
$chart [ 'chart_data' ][ 0 ][ 0 ] = "";
$chart [ 'chart_data' ][ 1 ][ 0 ] = "Hits";

$i=1;
while (!$rs->EOF) {
	$chart [ 'chart_data' ][ 0 ][ $i] = adodb_date2("d M y",$rs->fields['statDate']);
	$chart [ 'chart_data' ][ 1 ][ $i ] = $rs->fields['statHit'];
	$i++;
	$rs->movenext();
}


//schedule for the next update
$chart[ 'live_update' ] = array ( 'url'=>"modules/log/swfgraph_hit.php?time=".time(), 'delay'=>10 );

$chart[ 'chart_type' ] = "column";

$chart[ 'chart_pref' ] = array ( 'line_thickness'=>2, 'point_shape'=>"none", 'fill_shape'=>false );

$chart [ 'axis_category' ] = array (   'skip'         =>  0,
                                       'font'         =>  "Arial", 
                                       'bold'         =>  true, 
                                       'size'         =>  10, 
                                        'alpha'        =>  75,
                                       'orientation'  =>  "diagonal_up, "
                                   ); 
                                   
$chart [ 'axis_ticks' ] = array (   'value_ticks'      =>  true, 
                                    'category_ticks'   =>  true, 
                                    'position'         =>  "centered", 
                                    'major_thickness'  =>  2, 
                                    'major_color'      =>  "000000", 
                                    'minor_thickness'  =>  1, 
                                    'minor_color'      =>  "000000",
                                    'minor_count'      =>  3
                                );                       
                                
$chart [ 'axis_value' ] = array (  
                                    'prefix'        =>  "", 
                                    'suffix'        =>  "", 
                                    'decimals'      =>  0,
                                    'decimal_char'  =>  ".",
                                    'separator'     =>  "", 
                                    'show_min'      =>  true, 
                                    'font'          =>  "Arial", 
                                    'bold'          =>  true, 
                                    'size'          =>  12, 
                                    'color'         =>  "000000", 
                                    'alpha'         =>  75,
                                    'orientation'   =>  "diagonal_up"
                                   );                                       
                                      
$chart [ 'chart_value' ] = array (  'prefix'         =>  "", 
                                    'suffix'         =>  "", 
                                    'decimals'       =>  0,
                                    'decimal_char'   =>  ".",  
                                    'separator'      =>  "",
                                    'position'       =>  "cursor",
                                    'hide_zero'      =>  true, 
                                    'as_percentage'  =>  false, 
                                    'font'           =>  "Arial", 
                                    'bold'           =>  true, 
                                    'size'           =>  8, 
                                    'color'          =>  "000000", 
                                    'alpha'          =>  100
                                  ); 

$chart [ 'legend_label' ] = array (   'layout'  =>  "horizontal",
                                      'bullet'  =>  "circle",
                                      'font'    =>  "Arial", 
                                      'bold'    =>  true, 
                                      'size'    =>  12, 
                                      'color'   =>  "000000", 
                                      'alpha'   =>  90
                                  );                             
$chart [ 'chart_transition' ] = array ( 'type'      =>  "scale",
                                        'delay'     =>  1, 
                                        'duration'  => 1, 
                                        'order'     =>  "series"                                 
                                      ); 
$chart[ 'chart_rect' ] = array ( 'x'=>80, 'y'=>50, 'width'=>550, 'height'=> 220, 'positive_color'=>"000066", 'negative_color'=>"000000", 'positive_alpha'=>10, 'negative_alpha'=>30 );
$chart[ 'draw' ] = array ( array ( 'type'=>"text", 'color'=>"000000", 'alpha'=>10, 'font'=>"arial", 'rotation'=>-90, 'bold'=>true, 'size'=>75, 'x'=>-10, 'y'=>290, 'width'=>300, 'height'=>200, 'text'=>"Hits", 'h_align'=>"left", 'v_align'=>"top" ));
$chart[ 'series_gap' ] = array ( 'set_gap'=>40, 'bar_gap'=>-25 );                        
$chart [ 'series_color' ] = array ("FF6600" );
SendChartData ( $chart );

?>
