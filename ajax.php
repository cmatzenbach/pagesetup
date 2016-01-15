<?php 
//where did this ajax request come from?
$original = $_REQUEST['loc'];
//then let's go sit there instead
chdir("../../../" . $original);

include($dotdotslash . "EventVariables.php");
include ($dotdotslash . "../global/includes/GlobalVariables.php");//had to be called again to get badcharacters to work

//on with the fun
if ($multiplecontent) {
	$subsitefolders = array_keys($multiplenavtabslist); // find directory names to pull root files from
	$bb = 0;
	foreach ($subsitefolders as $subsitedir) {
		$rootfiles[$bb] = glob($subsitedir . "/*.php");
		$bb++;
	} 
}
else {
	$rootfiles = glob("*.php");
}
$howmanybad = 0;


// set up testnavtabs function - for non-multiple content sites
function testnavtabs() {
global $navtabs, $rootfiles, $howmanybad, $badcharacters; // initiated in EventVariables.php
foreach ($navtabs as $tab) {
	$tab = explode("+",$tab); //check if any tab has sub-tabs
	$regtab = substr($tab[0],0,3); //check if tab begins with reg
	if ($regtab == "Reg") {} //if reg tab but regpagephp is false, no need to check for it
		else { //if any tab other than reg page, or reg page php is true, check for the file
		$tabcheck = strtolower(str_replace($badcharacters, '', $tab[0]).".php"); //get file name
		if(in_array($tabcheck,$rootfiles)) { } //if it's there, do nothing
		else {echo $tabcheck . " was missing...<br>"; $howmanybad = $howmanybad + 1;} //if not there, tell user
	array_shift($tab); //check for sub-tabs
	if($tab) {
		foreach ($tab as $subtab) {   //same as above but for subtabs
		$subtabcheck = strtolower(str_replace($badcharacters, '', $subtab).".php");
		if(in_array($subtabcheck,$rootfiles)) { }
		else {echo $subtabcheck . " was missing...<br>"; $howmanybad = $howmanybad + 1;}
	}}
}}}

//create missing root files for non-multiplecontent sites
function addrootfiles() {
global $navtabs, $rootfiles, $howmanybad, $badcharacters, $regpagephp, $dotdotslash;
	foreach ($navtabs as $tab)
	{
		$tab = explode("+",$tab); //check if any tab has sub-tabs
		$regtab = substr($tab[0],0,3);  //check if tab begins with reg
			if ($regtab == "Reg" && $regpagephp == false) {} //if reg tab but regpagephp is false, no need to create index file for it
				else //if any tab other than reg page, or reg page php is true, create the root file for the page
				{
				$tabcheck = strtolower(str_replace($badcharacters, '', $tab[0]).".php"); //get rootfile name
					if(in_array($tabcheck,$rootfiles)) { } //if it exists, don't do anything
					else
					{
					$testtemplate = $dotdotslash . "../global/includes/test-template.php";
						if(copy($testtemplate, $tabcheck)) // if it doesn't exists, make it!
						echo "<br><b>Fixing... " . $tabcheck . " created</b><br>";
					}
				array_shift($tab); //see if there are any subtabs.  if so, make root files for them below
				if($tab)
				{
					foreach ($tab as $subtab)
					{ 
						$subtabcheck = strtolower(str_replace($badcharacters, '', $subtab).".php");
							if(in_array($subtabcheck,$rootfiles)) { }
							else
							{
							$testtemplate = $dotdotslash . "../global/includes/test-template.php";
								if(copy($testtemplate, $subtabcheck))
								{
									echo "<br><b>Fixing... " . $subtabcheck . " created</b><br>";
								}
							}
					}
				}
				}
	}
}



// set up testnavtabsmultiple function - for multiplecontent sites
function testnavtabsmultiple() {
global $multiplenavtabslist, $rootfiles, $howmanybad, $badcharacters, $subsitefolders; //initiated in EventVariables.php
$cc = 0;
foreach ($multiplenavtabslist as $navtabs) {
foreach ($navtabs as $tab) {
	$tab = explode("+",$tab); 
	$regtab = substr($tab[0],0,3);
	if ($regtab == "Reg") {}
		else {
		$tabcheck = strtolower(str_replace($badcharacters, '', $tab[0]).".php");
		if(in_array($subsitefolders[$cc] . "/" . $tabcheck,$rootfiles[$cc])) { } //see if file is in specific site's root file folder
		else {echo $subsitefolders[$cc] . "/" . $tabcheck . " was missing...<br>"; $howmanybad = $howmanybad + 1;} //which files in which subfolders are missing
	array_shift($tab);
	if($tab) { //if subtab
		foreach ($tab as $subtab) { 
		$subtabcheck = strtolower(str_replace($badcharacters, '', $subtab).".php");
		if(in_array($subsitefolders[$cc] . "/" . $subtabcheck,$rootfiles[$cc])) { }
		else {echo $subsitefolders[$cc] . "/" . $subtabcheck . " was missing...<br>"; $howmanybad = $howmanybad + 1;}
	}}
}}
$cc++;
}
}

// create missing root files for multiple content sites
function addrootfilesmultiple() {
global $multiplenavtabslist, $rootfiles, $howmanybad, $badcharacters, $regpagephp, $subsitefolders, $dotdotslash;
$aa = 0;
	foreach ($multiplenavtabslist as $navtabs) { // for each subsite in $multiplenavtabslist
		// check to see if the subsite folder for the root files exists
		if (!file_exists($subsitefolders[$aa])) {
			mkdir($subsitefolders[$aa]);  // if it doesn't, make it here
			if(!file_exists($subsitefolders[$aa] . "/index.php")) { //check to see if each root folder has index file
				copy("index.php",$subsitefolders[$aa] . "/index.php"); //if not, copy it over
				echo "<br><b>Fixing... " . $subsitefolders[$aa] . "/index.php created</b><br>";
			}
		}
		if (!file_exists("content/" . $subsitefolders[$aa])) {
			mkdir("content/" . $subsitefolders[$aa]);  // if it doesn't, make it here
		}
	foreach ($navtabs as $tab)  // for each page in the subsite
	{
		$tab = explode("+",$tab);
		$regtab = substr($tab[0],0,3);
			if ($regtab == "Reg" && $regpagephp == false) {}
				else
				{
				$tabcheck = strtolower(str_replace($badcharacters, '', $tab[0]).".php");
					if(in_array($subsitefolders[$aa] . "/" . $tabcheck,$rootfiles[$aa])) { }
					else
					{
					$testtemplate = $dotdotslash . "../global/includes/test-template.php";
						if(copy($testtemplate, $subsitefolders[$aa] . "/" . $tabcheck))
						echo "<br><b>Fixing... " . $subsitefolders[$aa] . "/" . $tabcheck . " created</b><br>";
					}
				array_shift($tab);
				if($tab)
				{
					foreach ($tab as $subtab)
					{ 
						$subtabcheck = strtolower(str_replace($badcharacters, '', $subtab).".php");
							if(in_array($subsitefolders[$aa] . "/" . $subtabcheck,$rootfiles[$aa])) { }
							else
							{
							$testtemplate = $dotdotslash . "../global/includes/test-template.php";
								if(copy($testtemplate, $subsitefolders[$aa] . "/" . $subtabcheck))
								{
									echo "<br><b>Fixing... " . $subsitefolders[$aa] . "/" . $subtabcheck . " created</b><br>";
								}
							}
					}
				}
				}
	}
	$aa++;
	}
}




if ($multiplecontent) {
	testnavtabsmultiple();
	addrootfilesmultiple();
}
else {
	testnavtabs();
	addrootfiles();
}

echo "<br /><span style=\"color:#07B307\">All good here, time to move on</span><br />";
echo "<style>#fixbutton {display:none;}";
?>
