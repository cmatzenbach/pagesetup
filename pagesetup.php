<script type="text/javascript">    
 function loadPhp() {
  // set current location (event working on)
  var full = window.location.pathname;
  var loc = full.substring(5, full.lastIndexOf('/'));
  if (window.XMLHttpRequest) {
    // code for IE7+, Firefox, Chrome, Opera, Safari
    xmlhttp=new XMLHttpRequest();
  } 
  xmlhttp.onreadystatechange=function() {
    if (xmlhttp.readyState==4 && xmlhttp.status==200) {
      document.getElementById("results1").innerHTML=xmlhttp.responseText;
    }
  }
  xmlhttp.open("GET","<?php echo $dotdotslash?>../global/modules/option/ajax.php?loc=" + loc + "&t=" + Math.random(),true);
  xmlhttp.send();
}
</script>
<div id="step1">
<p><strong>Welcome to the web/ page setup</strong></p><br />
<h3>1. Have you setup $navtabs in your EventVariables file?</h3>
<p>Preview:</p>
<div id="navtabspreview">
<ul>
<?php 
	unset($regtab);
	foreach ($navtabs as $tab) {
	$tab = explode("+",$tab);
	echo "<li>" . $tab[0];
	if($tab[1] && $submenublank == true) {echo " (not a content page)";}
	if(!$regpagephp){$regtab = substr($tab[0],0,3);}
	if ($regtab == "Reg") {echo " (not a content page)";}
	echo "</li>";
	$parent = $tab[0];
	// sub-menu items
	array_shift($tab);
	if($tab) {
		echo "<ul>";
	foreach ($tab as $subtab) {
		if ($subtab == "|") {$subtab = "<em>divider</em>";}
		if (substr($subtab,0,1) == "|"){
		$subtab = "<b>" . str_replace("|","",$subtab) . "</b>";}
		echo "<li>". $subtab;
		if($submenuanchor == true && $subtab != "<em>divider</em>") {echo " (part of " . $parent . " content page)";}
		echo "</li>";}
		echo "</ul>";
	}
	echo "</li>\r";
}
?>
</ul>
</div>
<p> - If not, go do them now</p>
<p> - If so, click below to fix your root files</p>
</div>
<input type="button" onclick="loadPhp();" value="Fix" id="fixbutton" />
<div id="results1"></div>
<br />
<div id="step2">
<h3>2. Preview content templates</h3><a name="drop" class="anchor"></a>
<p> - Click through the menu below to preview the different content templates using your site's own css. When you like a template, click "Apply Now" to add the file to your content folder.</p>
<style>
#dropdowns {display:table; width:100%;border-bottom:3px solid gray;}
#dropdowns div {display:table-cell; width:20%;}
a.anchor {
	display: block;
	position: relative;
	top: -118px;
	visibility: hidden;
	}
</style>
<div id="dropdowns">
	<div id="templates">
		<form action="<?php echo $whereiam?>#drop" method="get" name="listing" id="listing">
			<input type="hidden" name="option" value="pagesetup">
			<select name="templateselect" id="templateselect" onChange="this.form.submit()">
				<option value="">-- Preview Templates --</option>
				<option value="loading">list loading...</option>
			</select>
    </div>
    <div id="addto">
    <p>to apply this template to...</p>
    </div>
    <div id="navdropdown">
			<select name="navselect" id="navselect" onChange="this.form.submit()">
				<option value="">-- Select $navtab --</option>
				<option value="loading">list loading...</option>
			</select>
		</form>
    </div>
    <div id="clickhere">
    	<form method="post" action=""><input type="Submit" name="submit" value="Apply Now" /></form>
    </div>

</div>
<?php
$selectedtemplate = $_GET['templateselect'];
$selectednavtab = $_GET['navselect'];
// Create array of template folder names
$mytemplatefolders = glob($dotdotslash . '../global/templates/*', GLOB_ONLYDIR);
$folderlinks = $mytemplatefolders;
// Alphabetize
natcasesort($mytemplatefolders);
$alltemplatetypes = array();
$alltemplatefiles = array();
$mytemplatefolders = str_replace($dotdotslash . '../global/templates/','',$mytemplatefolders);
$mytemplatefolders = str_replace('_',' ',$mytemplatefolders);
foreach ($mytemplatefolders as $templatefolder){
	$templatefolder = ucwords($templatefolder);
	$alltemplatetypes[] = $templatefolder;
	if(!$selectedtemplate){}
	else {
	if($selectedtemplate == $templatefolder)
	{
	echo $templatefolder;
	}
	}
}

$foldercounter = count($folderlinks);
$y = 0;

foreach ($folderlinks as $folderpath){
	$folderpath = $folderpath . "/*.php";
	$mytemplatefiles[$y] = glob($folderpath);
	$y++;
}
$fileholder = array();
$filenameholder = array();
$filecounterarray = array();
for ($z = 0; $z < $foldercounter; $z++) {
	foreach ($mytemplatefiles[$z] as $lefile){
		$fileholder[$z][] = $lefile;
		$filenameholder[$z][] = $lefile;
	}

}
for ($a = 0; $a < $foldercounter; $a++) {
	$filecounter = count($filenameholder[$a]);
	$filecounterarray[$a] = $filecounter;
	natcasesort($filenameholder[$a]);
	$filenameholder[$a] = str_replace($folderlinks[$a] . '/','',$filenameholder[$a]);
}
?>

<script>
var filterdropdown = document.listing.templateselect;
<?php
$alltemplatetypes = array_unique($alltemplatetypes);
sort($alltemplatetypes);
$x = 2;
$c = 0;
foreach($alltemplatetypes as $templatetype)
{
	echo "filterdropdown.options[filterdropdown.options.length] = new Option(\"" . $templatetype . "\",\"" . $templatetype . "\");";
	echo "filterdropdown.options[" . $x . "].disabled=true;";
	//create options for files within folders
	for ($b = 0; $b < $filecounterarray[$c]; $b++) {
		echo "filterdropdown.options[filterdropdown.options.length] = new Option(\"" . $filenameholder[$c][$b] . "\",\"" . $filenameholder[$c][$b] . "\");";
	}
	$x = $x + ($filecounterarray[$c]+1);
	$c++;
}
?>
//remove the loading...
filterdropdown.remove(1);

//function to allow selecting current filter in dropdown
function selectItemByValue(elmnt, value){

    for(var i=0; i < elmnt.options.length; i++)
    {
      if(elmnt.options[i].value == value)
        elmnt.selectedIndex = i;
    }
  }
<?php
if($selectedtemplate) {
echo "selectItemByValue(filterdropdown, \"" . $selectedtemplate . "\");";
}
?>

</script>

<script>
var dropdownnavs = document.listing.navselect;
<?php
if ($splashpage) { $navtabs[] = "Splash Page"; }
foreach($navtabs as $tab)
{	
	$tab = explode("+",$tab);
	if ($tab[0] == "Splash Page") { 
		$splashpagelink = str_replace(["content/",".php"], '', $splashpagelink);
		$href = $splashpagelink;
	 }
	else {
	$href = strtolower(str_replace($badcharacters, '', $tab[0])); }
	if ($tab[1] && $submenublank == true) {}
	else {
		if(!$regpagephp){$regtab = substr($tab[0],0,3);}
			if($regtab == "Reg") {}
			else {echo "dropdownnavs.options[dropdownnavs.options.length] = new Option(\"" . $tab[0] . "\",\"" . $href . "\");";}
	}
	array_shift($tab);
	if($tab && !$submenuanchor == true) {
		foreach ($tab as $subtab) {
		$subhref = strtolower(str_replace($badcharacters, '', $subtab));
		if ($subtab == "|" || substr($subtab,0,1) == "|")	{}
		else {
		echo "dropdownnavs.options[dropdownnavs.options.length] = new Option(\"" . $subtab . "\",\"" . $subhref . "\");";
		}
	}
	}
}
?>
//remove the loading...
dropdownnavs.remove(1);

//function to allow selecting current filter in dropdown
function selectItemByValue(elmnt, value){

    for(var i=0; i < elmnt.options.length; i++)
    {
      if(elmnt.options[i].value == value)
        elmnt.selectedIndex = i;
    }
  }
<?php
if($selectednavtab)
echo "selectItemByValue(dropdownnavs, \"" . $selectednavtab . "\");";
?>
</script>

<?php 
// Recursive array search (finds folder for user-selected file)
function recursive_array_search($needle,$haystack) {
    foreach($haystack as $key=>$value) {
        $current_key=$key;
        if($needle===$value OR (is_array($value) && recursive_array_search($needle,$value) !== false)) {
            return $current_key;
        }
    }
    return false;
}
$folderlocation = recursive_array_search($selectedtemplate, $filenameholder);
// Take folder path and combine with file name
if($selectedtemplate) {
	include $folderlinks[$folderlocation] . '/' . $selectedtemplate;
}



?>
<?php if ($_POST['submit'])
{
if(copy($folderlinks[$folderlocation] . '/' . $selectedtemplate, 'content/' . $selectednavtab . ".php")) {
	echo "<br><b>Copied " . $selectedtemplate . " to " . $selectednavtab . " successfully! Remember to GET the files from Remote Server in Dreamweaver to be able to edit.</b><br>";
};
}?>
<div id="results2"></div></div>