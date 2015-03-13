<?php

	include('simple_html_dom.php');
	include('helpers.php');
	$root_url ="http://www.kba.co.ke/bankcodes/";

	
	$xml = new SimpleXMLElement('<banks_data/>'); // new xml document
	$html = file_get_html($root_url.'index.html');


	$i = 0;
	$ilen = count( $html->find('a') ) -1;	//counts all the links ... ignores last link (not a bank)
	foreach($html->find('a') as $element){ //fetches alll links on the page
       if( ++$i == $ilen ) break; // skips last url
       $link = $element->href; // gets the link
       $branches= file_get_html($root_url.$link); // fetches  page content

       // gets bank name and clearing center from title
       $unprocessed_name =$branches->find('/html/body/table/tbody/tr[2]/td/table[2]/tbody/tr[1]/td[2]/strong')[0];


       	//gets clearing centre alone
    	$clearing_center =get_inbetween($unprocessed_name);

    	//gets bank name  alone
    	$full_name = clean_text($unprocessed_name);

    	//XML stuff ... looks self explanatory
    	$bank = $xml->addChild('bank');
    	$bank->addChild("bank_name",$full_name);
    	$bank->addChild("clearing_centre",$clearing_center);
    	$branchesxml = $bank->addChild('branches');
    	
    	//xpath of  branch code
    	$items_number =$branches->find('/html/body/table/tbody/tr[2]/td/table[2]/tbody/tr/td[1]');
    	//xpath of  branch name
      	$items_name  =$branches->find('/html/body/table/tbody/tr[2]/td/table[2]/tbody/tr/td[2]');
      	

      	//combine arrays
      	$items = array_combine($items_number, $items_name);
      	$counter=0;
      	foreach ($items as $key => $value) {

       		if($counter++ < 2) continue ; // skips first 2 <td/> tags , they dont contain branch information
       		
       		//creates xml child
       		$branch = $branchesxml->addChild('branch');

       		//removes html tags and utf characters (not sure what thaat even means)
       		$key = clean_text($key);

       		//branch code is $clearing centre combined with branch id
       		$branch_code =  $clearing_center.$key;

       		//echo $branch_code;
       		$branch_name =clean_text($value);

       		//more xml stuff
       		$branch->addAttribute("branch_name",$branch_name);
       		$branch->addAttribute("branch_code",$branch_code);

       	}
       

	}	

if(isset($_GET['format'])){
	$format=$_GET['format'];
	if ($format == 'json') {
		//header('Content-Type: application/json');
		echo json_encode($xml);
	}elseif($format == 'xml'){

		Header('Content-type: text/xml');
		print($xml->asXML());
	}else{
		echo("Hio format siijui");
	}
}else{
	//defaults to xml
	Header('Content-type: text/xml');
		print($xml->asXML());
}


?>