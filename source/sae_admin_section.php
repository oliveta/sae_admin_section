<?php

$plugin['version'] = '0.1';
$plugin['author'] = 'oliveta';
$plugin['author_uri'] = '';
$plugin['description'] = 'Menu items organization ';
$plugin['type'] = 1;
if (!defined('PLUGIN_LIFECYCLE_NOTIFY')) define('PLUGIN_LIFECYCLE_NOTIFY', 0x0002);
$plugin['flags'] = PLUGIN_LIFECYCLE_NOTIFY;



if (0) {
<<<EOF
# --- BEGIN PLUGIN HELP ---

h1. Menu items organization in textpattern

This plugin provides the possibility to easily create and reorder sections (menu items) inside an administrative interface. In this way the owner of the website can easily add/delete/reorder sections (menu items) of his website, the funcionality which was left until now to advanced textpattern users.

h3. Installation

To use this plugin enable it under Plugin section of admin area.

h3. Use - admin area

New tab Menu will appear after the installation. You can create/selete  menu items by standart procedure. The page template <b>general</b> will beassigned to the newly created sections.  For sections reordering drag and drop the selected section to the place you want it to appear.

h3. Use - public area

Use 
<code>
<txp:sae_section_menu order="asc|desc" class="" offset="" length="" />
</code>
 to produce a list of menu items inside your template. Items will have the <b>class</b> applied. Default class is <b>menu_item</b>.
# --- END PLUGIN HELP ---
EOF;
}

# --- BEGIN PLUGIN CODE ---	

function sae_section_menu($atts) {
                extract(lAtts(array(
                        'order'  => 'asc',
						'offset'=>0,
						'length'=>0,
						'class'=>'menu_item'
                ),$atts));
$out="";
                $menu=safe_rows("name,title","txp_section","page='general' order by sectionorder ".$order." limit ".$offset.",".($length==0?'1844674407370955161':$length));
				if ($menu)
				{
				$out.="<ul class='menu'>";
				foreach ($menu as $menu_item)
				{
				extract($menu_item);
				$out.="<li class='".$class."'><a href='".hu.$name."'>".$title."</a></li>";
				
				
				}
				$out.='</ul>';
				}
				return $out;
        }




if (@txpinterface == 'admin') {
	if (sae_admin_section_checkDB())
	{
		ob_start('sae_admin_tab_add');
		}
		$myevent =array('section'=>'Menu edit');

		foreach ($myevent as $key=>$value)
		{
		add_privs("sae_admin_".$key,'1,2,3,4,5,6,');
		
		register_callback("sae_admin_".$key, "sae_admin_".$key);
		
		}
		register_callback('sae_admin_section_revertDB',
   'plugin_lifecycle.sae_admin_section', 'deleted');
register_callback('sae_admin_section_enebled',
   'plugin_lifecycle.sae_admin_section', 'enabled');;
register_callback('sae_admin_section_revertDB', 
   'plugin_lifecycle.sae_admin_section', 'disabled');


		
		
		
	}
	
//-------------------------------------------------------------	

function sae_admin_tab_add($buffer){

	$find1 = gTxt('Content').'</a></td>';
	$class = ( in_array($GLOBALS['event'], array('event_section')) ) ? 'tabup' : 'tabdown';
	$replace = '<td class="'.$class.'"><a href="?event=sae_admin_section" class="plain">Menu</a></td>';
	$out=((strpos($find1, $buffer)===0)?$buffer:str_replace($find1, $find1.$replace, $buffer));
	return $out;
}

//-------------------------------------------------------------	

	
function sae_admin_section($event, $step) {

		if (!defined('txpinterface')) die('txpinterface is undefined.');
		if ($event == 'sae_admin_section' && sae_admin_section_checkDB()) {
		
		require_privs('category');
		if(!$step or !in_array($step, array(
		'sae_admin_section_list','sae_admin_section_create',
		'sae_admin_section_multiedit','sae_admin_section_save',
		'sae_admin_section_edit'
		))){
		
		sae_admin_section_list('sae_admin_section');
	} 
	else {$step($event);}
	
	
	
}else {
	pagetop("","");
	}

	}


//-------------------------------------------------------------
function sae_admin_section_enebled() {
			if (sae_admin_section_checkDB() != true) {
				return sae_admin_section_setupDB();
			} else {
				return '';
			}
		
}
//-------------------------------------------------------------

function sae_admin_section_checkDB() {
	$columns = getRows('SHOW COLUMNS FROM '.safe_pfx('txp_section'));
	foreach($columns as $column => $columnData) {
		$columns[$columnData['Field']] = '';
		unset($columns[$column]);
	}
	
	return isset($columns['sectionorder']) ? true : false;
}
//-------------------------------------------------------------
function sae_admin_section_setupDB() {
	if (safe_alter('txp_section', 'ADD `sectionorder` INT(11) NOT NULL') == true
) {

$q=safe_count("txp_page","name='general'");
if ($q==0  && safe_insert("txp_page",'name="general", user_html="<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"en\" lang=\"en\">\n<head>\n\t<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />\n\n\t<title><txp:page_title /></title>\n\n\t<link rel=\"home\" href=\"<txp:site_url />\" />\n\n\t<txp:feed_link flavor=\"atom\" format=\"link\" label=\"Atom\" />\n\t<txp:feed_link flavor=\"rss\" format=\"link\" label=\"RSS\" />\n\n\t<txp:css format=\"link\" />\n\n\t<txp:rsd />\n</head>\n<body id=\"<txp:section />\">\n\n<!-- accessibility -->\n<div id=\"accessibility\">\n\t<ul>\n\t\t<li><a href=\"#content\"><txp:text item=\"go_content\" /></a></li>\n\t\t<li><a href=\"#sidebar-1\"><txp:text item=\"go_nav\" /></a></li>\n\t\t<li><a href=\"#sidebar-2\"><txp:text item=\"go_search\" /></a></li>\n\t</ul>\n</div>\n\n<div id=\"container\">\n\n<!-- head -->\n\t<div id=\"head\">\n\t\t<p id=\"site-name\"><txp:link_to_home><txp:site_name /></txp:link_to_home></p>\n\t\t<p id=\"site-slogan\"><txp:site_slogan /></p>\n\t</div>\n\n<!-- left -->\n\t<div id=\"sidebar-1\">\n\t\t<txp:section_list default_title=\'<txp:text item=\"home\" />\' include_default=\"1\" wraptag=\"ul\" break=\"li\">\n\t\t\t<txp:if_section name=\'<txp:section />\'>&raquo;</txp:if_section>\n\t\t\t<txp:section link=\"1\" title=\"1\" />\n\t\t\t<txp:if_section name=\'<txp:section />\'>\n\t\t\t\t<txp:article_custom  section=\'<txp:section />\' wraptag=\"ul\" break=\"li\">\n\t\t\t\t\t<txp:if_article_id>&rsaquo;</txp:if_article_id>\n\t\t\t\t\t<txp:permlink><txp:title /></txp:permlink>\n\t\t\t\t</txp:article_custom>\n\t\t\t</txp:if_section>\n\t\t</txp:section_list>\n\n\t\t<txp:search_input wraptag=\"p\" />\n\n\t\t<p><txp:feed_link label=\"RSS\" /> / <txp:feed_link flavor=\"atom\" label=\"Atom\" /></p>\n\t</div>\n\n<!-- right -->\n\t<div id=\"sidebar-2\">\n\t\t<txp:linklist wraptag=\"p\" />\n\n\t\t<p><a href=\"http://textpattern.com/\"><txp:image id=\"2\" /></a></p>\n\t</div>\n\n<!-- center -->\n\t<div id=\"content\">\n\t\t<txp:if_article_list><h1><txp:section title=\"1\" /></h1></txp:if_article_list>\n\n\t\t<div class=\"hfeed\">\n\t\t<txp:article listform=\"article_listing\" limit=\"5\" />\n\t\t</div>\n\t\n<txp:if_individual_article>\n\t\t<div class=\"divider\"><txp:image id=\"1\" /></div>\n\n\t\t<p><txp:link_to_prev>&#171; <txp:prev_title /></txp:link_to_prev> \n\t\t\t<txp:link_to_next><txp:next_title /> &#187;</txp:link_to_next></p>\n<txp:else />\n\t\t<p><txp:older>&#171; <txp:text item=\"older\" /></txp:older> \n\t\t\t<txp:newer><txp:text item=\"newer\" /> &#187;</txp:newer></p>\n</txp:if_individual_article>\n\t</div>\n\n<!-- footer -->\n\t<div id=\"foot\">&nbsp;</div>\n\n</div>\n\n</body>\n</html>"')==true)
{
		return ' <strong>sae_admin_section</strong> and DB setup OK.';
		}
		else {
		
		return ' <strong>sae_admin_section</strong> setup OK. Page template "general" either exists or cannot be created.';
		
		}
	} else {
		return array(
			' <strong>sae_admin_section</strong>. DB setup failed.',
			E_ERROR
		);
	}
}
//-------------------------------------------------------------

function sae_admin_section_revertDB() {
if (sae_admin_section_checkDB()==true)
{
	if (safe_alter('txp_section', 'DROP `sectionorder`') == true) {
		if (safe_update('txp_plugin', 'status = 0', 'name = \'sae_admin_section\'') == true) {
			return 'DB reverted and <strong>sae_admin_section</strong> disabled OK.';
		} else {
			return array(
				'DB reverted OK. Failed to disabled <strong>sae_admin_section</strong>.',
				E_ERROR
			);
		}
	} else {
		return array(
			'DB revert failed. No status change.',
			E_ERROR
		);
	}
	}
}

//-------------------------------------------------------------

function sae_admin_section_list($event,$message="")
/*
listing of sections
*/
	{
	
	$out='<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/mootools/1.2.3/mootools-yui-compressed.js"></script>';
	$out.='<script type="text/javascript" src="'.hu.'textpattern/js/mootools-more.js"></script>';
	$out.='<script>var dir="'.hu.'";var tab="'.safe_pfx('txp_section').'";</script>';
	$out.='<script type="text/javascript" src="'.hu.'textpattern/js/reorder.js"></script>';
	$out.= n.n.hed('Menu Items', 3).
	form(
				fInput('text', 'title', '', 'edit', '', '', 20).
				fInput('submit', '', gTxt('Create'), 'smallerbox').
				eInput('sae_admin_section').
				sInput('sae_admin_section_create')
			);
      
		$rs =safe_rows('title,name','txp_section','(page="general") order by `sectionorder`');
		if ($rs)
		{
			$items = array();
			foreach ($rs as $a)
			{
				extract($a);
				$pocet=safe_count("textpattern","section='$name'");
	   
				// article count
				$url = 'index.php?event=list'.a.'search_method=section'.a.'crit='.$name;
				$count = $pocet!=0 ? '('.href($pocet, $url).')' : '(0)';
				$edit_link = eLink('sae_admin_section', 'sae_admin_section_edit', 'name', $name, $title);
				

				$items[] = 
					'<li id="item_'.$name.'" class="sort"><div >'.checkbox('selected[]', $name, 0).sp.str_repeat(sp.sp, 1 * 2).$edit_link.sp.small($count).'</div></li>';
			}

			if ($items)
			{
				$out .= sae_admin_section_multiedit_form($event, $items);
			}
			
		}

		else
		{
			$out .= graf("No menu items exist.");
		}
		pagetop(gTxt('Menu'),$message);
		$out = array('<table cellspacing="20" align="center">',
		'<tr>',
			tdtl($out,' class="categories"'),
		'</tr>',
		endTable());
		echo join(n,$out);
		

		
	}

//-------------------------------------------------------------

function sae_admin_section_create($event)
/*
creating a new section with template general
*/
	{
	
		global $txpcfg;

		$title = ps('title');

		$name = strtolower(sanitizeForUrl($title));

		if (!$name)
		{
			$message = gTxt($event.'_section_invalid', array('{name}' => $name));

			 sae_admin_section_list('sae_admin_section',$message);
		}

		$exists = safe_field('name', 'txp_section', "name = '".doSlash($name)."'");

		if ($exists)
		{
			$message = gTxt($event.'_section_already_exists', array('{name}' => $title));

			 sae_admin_section_list('sae_admin_section',$message);
		}
		if (safe_update('txp_section','sectionorder=sectionorder+1','page="general"')==true && safe_insert('txp_section', "page='general', css='default', on_frontpage=0, name = '".doSlash($name)."', title = '".doSlash($title)."'")==true)
		{
			
			rebuild_tree_full($event);

			$message = gTxt($event.'_section_created', array('{name}' => $name));

			
		
		}
		else {
		$message="Failed to create a menu item";
		}
		sae_admin_section_list('sae_admin_section',$message);
	}

//-------------------------------------------------------------
	function sae_admin_section_edit($event, $message='', $name='')
	{
		pagetop(gTxt('Menu'), $message);

		$name     = ($name=='')?gps('name'):$name;
        $row = safe_row("*", "txp_section", "name='$name'");
		if($row){
			extract($row);
			$out = stackRows(
				fLabelCell("Name") . fInputCell('name', $title, 1, 30),
				"",
				hInput('id',$name),
				tdcs(fInput('submit', '', gTxt('save_button'),'smallerbox'), 2)
			);
		}
		$out.=  eInput( 'sae_admin_section' ) . sInput( $event.'_save' ) . hInput( 'old_name',$name );
		echo script_js('document.write(\'<link rel="stylesheet" type="text/css" href="'.hu.'textpattern/js/event.css" />\');');
		echo '<div style="text-align:center">'.n.n.hed('', 3).sp.small(href('Menu items','index.php?event=sae_admin_section')).form( startTable( 'edit' ) . $out . endTable() ).'</div>';
	}

//-------------------------------------------------------------

	function sae_admin_section_save($event)
	{
		global $txpcfg;

		extract(doSlash(psa(array('name', 'old_name'))));
		
		
$title=$name;

		$name = strtolower(sanitizeForUrl($name));

		
		if (!$name)
		{
			$message = "Name of the section is not valid";

			 sae_admin_section_list('sae_admin_section',$message);
		}

		
		$existing_id = safe_field('name', 'txp_section', "name = '$name'");

		if ($existing_id and $existing_id != $name)
		{
			$message = "Name of the section already exists.";

			 sae_admin_section_list('sae_admin_section',$message);
		}

		if (safe_update('txp_section', "name='$name', title = '$title' ", "name = '$old_name'")==true)
		{
			safe_update('textpattern', "section = replace(section,'$old_name','$name')", "section  rLIKE '((^.*,?".$old_name.",.*$)|(^.*,".$old_name.",?.*$)|(^".$old_name."$))'");
		}

		rebuild_tree_full($event);

		

		$message = "Section name has been updated to ".doStrip($title);

		sae_admin_section_list('sae_admin_section',$message);
	}


// -------------------------------------------------------------

function sae_admin_section_multiedit_form($area, $array)
	{
		$methods = array('delete'=>gTxt('delete'));
		if ($array) {
		return
		form(
			'<ul id="sortable">'.join('',$array).'</ul>'.
			eInput('sae_admin_section').sInput('sae_admin_section_multiedit').hInput('type',$area).
			small(gTxt('with_selected')).sp.selectInput('edit_method',$methods,'',1).sp.
			fInput('submit','',gTxt('go'),'smallerbox')
			,'margin-top:1em',"verify('".gTxt('are_you_sure')."')"
		);
		} return;
	}

// -------------------------------------------------------------
	function sae_admin_section_multiedit()
	{
		$type = ps('type');
		$method = ps('edit_method');
		$things = ps('selected');
$message="";

		if ($method == 'delete' and is_array($things) and $things and in_array($type, array('sae_admin_section')))
		{
			
			 $ids="'".join("','",safe_column("section","textpattern","1=1"))."'";
			 
	
	
			if (safe_delete('txp_section','name IN ('."'".join("','",$things)."'".') '.($ids==''?'':'AND name NOT IN ('.$ids.')')))
				{
					rebuild_tree_full($type);

					$message = gTxt($type.'_categories_deleted', array('{list}' => join(', ',$things)));

					
				}
				
			}
		

		 sae_admin_section_list('sae_admin_section',$message);
	}
	




# --- END PLUGIN CODE ---

?>
