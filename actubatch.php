<?php
error_reporting(E_ALL);
set_time_limit(160);
require __DIR__ . '/vendor/autoload.php';
use Automattic\WooCommerce\Client;
use Automattic\WooCommerce\HttpClient\HttpClientException;
$woocommerce = new Client(
    'https://www.arignon.com.ar',
    'ck_5e9e49b954cfc51d346cc854362b37d00c2f3dfe',
    'cs_eb89e854d410556a8eafb0c67074b05e2e63d055',
    [        'wp_api' => true,        'version' => 'wc/v2'   ]
);
/*** * Busco todos los productos de Woo, los busco x sku y los actualizo */
if ( isset($_GET['CIP'])) $params['search'] = $_GET['CIP'];
$params['per_page'] = 100;
$TODOS = $woocommerce->get('products',$params);
$ACTUALIZAR=[];
foreach ( $TODOS as $prod=>$data ) 
        {   
            $params['sku']= $data->sku;
            $params['id'] = $data->id;     
            $ID_PRODUCTO= $data->id;
            $listado= Art($params['sku']); //Find the product in real store database, not WP. 
            if ( ( !$listado ) ||  $listado->result <> "OK") 
                continue;
            foreach ( $listado->records as $item )
                {
                $actualizar[$data->id]['id']=  $data->id ;
                $actualizar[$data->id]['regular_price']= $item->Precio_final - 1000;     
                $actualizar[$data->id]['stock_quantity'] = $item->Stock_actual;
                if ( $item->Precio_final < 100 || $item->Stock_actual == '0' || $item->Stock_minimo == '0' )
                {   $actualizar[$data->id]['status'] = 'draft';
                    $actualizar[$data->id]['in_stock'] = false;                   
                }                
                }           
        }
foreach ($actualizar as   $ID=>$data)
    $ACTUALIZAR['update'][]=$data;
print_r($woocommerce->post('products/batch', $ACTUALIZAR)); //BATCH update is faster


 


 

 
//print_r());


// example
//$gift = getProductBySku('uranium-968') ?? createProduct('uranium-968') ?? false;
$PRODUCTO =  $woocommerce->get("products/$ID_PRODUCTO");


//print_r($PRODUCTO );




function Art($p1=null,$p2=null,$p3=null,$p4=null)
{	
    $params=array();	
    $SERVER='www.arignon.com.ar'	;
    $basico = Basicos("$p1 $p2 $p3 $p4");
    if ($basico) $p1=$basico;   

    if ($p1 <> null && $p1 <> 'A' && $p1 <> 'C' ) $p1= "/$p1";
    if ($p2 <> null ) $p2= "/$p2";
    if ($p3 <> null ) $p3= "/$p3";
    if ($p4 <> null ) $p4= "/$p4";     
    $URL_BASE = 'https://'.$SERVER.'/back/api' ;
    //$URL_BASE = 'https://www.arignon.com.ar/back/api/buscart' ;
    $URL_BASE.='/buscart';
    $json = SendGet($URL_BASE.$p1.$p2.$p3.$p4);	
     //debug_a_admins('Art fn',$URL_BASE.$p1.$p2);//

     //debug_a_admins('js',$json);//
    return json_decode($json);
}
function Basicos($text='')
	{
		$par =explode(" ", $text );    
		$par = array_filter($par, fn($value) => !is_null($value) && $value !== '' && $value !== ' ');

		//array_map(fn($val) => trim($val), $par);				  		
        sort($par);        
		$basicos=[];        
		$basicos[] = array(121,'aglo blanco');
		$basicos[] = array(123,'mdf blanco');
		$basicos[] = array(2984,'canto blanco 22');		
		$basicos[] = array(23806,'fondo 3');
		$basicos[] = array(0,'canto color mela');
		$basicos[] = array(0,'canto color pvc');
		
		$basicos[] = array(1818,'mdf crudo');

		$basicos[] = array(23868,'mdf color');
		$basicos[] = array(23869,'aglo color');
		$basicos[] = array(1808,'fondo color');		

		$basicos[] = array(5109,'corre 350 l');		
		$basicos[] = array(5111,'corre 400 l');	
		$basicos[] = array(5113,'corre 450 l');	
		$basicos[] = array(5115,'corre 500 l');	




		foreach ( $basicos as $b )
			{
                $data = $b[1];
			    $claves = explode(" ",$data);
                sort($claves);				
				if ($claves===$par) return $b[0];
			}
		return false;		         
		
	}
function Grupo($p1=null,$p2=null)
{	
    $params=array();
    if ($p1 <> null ) $p1= "/$p1";
    if ($p2 <> null ) $p2= "/$p2";
    $URL_BASE ='https://perezcompany.com.ar/back/api/buscaporgrupo';
    $json = Funciones::SendGet($URL_BASE.$p1.$p2);	
    //
    return json_decode($json);
}
function NombreGrupo($p1='zz')
{	
    $params=array();
    if ($p1 <> null ) $p1= "/$p1";
    $URL_BASE = 'https://'.$SERVER.'/back/api' ;		
    $URL_BASE.='/nombregrupo';
    $json = Funciones::SendGet($URL_BASE.$p1);	
    $g = json_decode($json);
    $g = $g->records;
    return $g[0]->Detalle;
}

function debug_a_admins(   $quien, $msg )
{
    $bot_api_key  = "676438755:AAG3QBJ5owYiwMjV2wiluXIJB5DGxFyjKbY";
    $bot_username = '@Buchonbot';
    $chatIds = array("662767623"); // Los destinatarios 

    foreach ($chatIds as $chatId) {
    $data = array(   'chat_id' => $chatId,
    'text' => 'Debug '.$quien. '  '.var_export($msg,true) ,
    'parse_mode' => 'HTML' );
     $response = file_get_contents("https://api.telegram.org/bot$bot_api_key/sendMessage?" . http_build_query($data) );
    }
    return ; 
}

function SendPostFile($target_url,$params)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $target_url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible;)");   
    curl_setopt($ch, CURLOPT_HTTPHEADER,array('Content-Type: multipart/form-data'));
    curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);   
    curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);  
    curl_setopt($ch, CURLOPT_TIMEOUT, 1000);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION ,true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $params);

    $result = curl_exec ($ch);

    if ($result === FALSE) 
        echo "Error sending   " . curl_error($ch);

    curl_close ($ch);
    return $result;
    return json_decode($result);
}

function SendPost($url,$params )
{
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
curl_setopt($ch, CURLOPT_POSTFIELDS, ($params));
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$result = curl_exec($ch);
curl_close($ch);
return $result;
}
function SendGet($url )
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);        
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}




function getProductBySku($sku)
{
	global $woocommerce;

	$params = [
		'sku' => $sku
	];

	$ret = $woocommerce->get('products', $params);

	if (is_array($ret) && (count($ret) == 1) && ($ret[0]->sku ?? '') == $sku) {
		return $ret[0];
	} else {
		return null;
	}
}
