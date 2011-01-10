<?php

/* Bigjoos, CoLdFuSiOn */

if ( ! defined( 'IN_TBDEV_ADMIN' ) )
{
	print "<h1>Incorrect access</h1>You cannot access this file directly.";
	exit();
}

require_once "include/user_functions.php";

    $lang = array_merge( $lang, load_language('ad_rules') );
    
    $params = array_merge( $_GET, $_POST );
    
    $params['mode'] = isset($params['mode']) ? $params['mode'] : '';
    
    switch($params['mode'])
    {
      
      case 'cat_new':
        New_Cat_Form();
        break;
      
      case 'cat_add':
        Do_Cat_Add();
        break;
        
      case 'cat_edit':
        Show_Cat_Edit_Form();
        break;
          
      case 'takeedit_cat':
        Do_Cat_Update();
        break;
        
      case 'cat_delete':
        Cat_Delete();
        break;
        
      case 'cat_delete_chk':
        Cat_Delete(true);
        break;
      
      case 'rules_new':
        New_Rules_Form();
        break;
        
      case 'rules_edit':
        Show_Rules_Edit();
        break;
      
      case 'takeedit_rules':
        Do_Rules_Update();
        break;
      
      case 'takeadd_rules':
        Do_Rules_Add();
        break;
         
      case 'rules_delete':
        Do_Rules_Delete();
        break;

      default:
        Do_show();
        break;
    }



function Do_show() {
  global $TBDEV, $lang;
  
  $sql = mysql_query("SELECT * FROM rules_categories") or die("Ooops");

  if ( !mysql_num_rows($sql) )
    stderr("ERROR", "There Are No Categories. <br /><br />
      <span class='btn'><a href='http://localhost/tb_new/admin.php?action=rules&amp;mode=cat_new'>Add Category</a></span>");

  $htmlout = '';
  
  $htmlout .= "<h2>{$lang['rules_cat_title']}</h2>
  <table style='width: 70%; cellpadding: 5px;'>";
  
  while ($arr = mysql_fetch_assoc($sql)) 
  {
    $htmlout .= "
      <tr>
        <td>{$arr['cid']}</td>
        <td><a href='{$TBDEV['baseurl']}/admin.php?action=rules&amp;mode=rules_edit&amp;catid={$arr['cid']}'>{$arr['rcat_name']}</a></td>
        <td>{$arr['min_class_read']}</td>
        <td><a href='{$TBDEV['baseurl']}/admin.php?action=rules&amp;mode=cat_edit&amp;catid={$arr['cid']}'>{$lang['rules_edit']}</a></td>
        <td><a href='{$TBDEV['baseurl']}/admin.php?action=rules&amp;mode=cat_delete&amp;catid={$arr['cid']}'>{$lang['rules_delete']}</a></td>
      </tr>";
  }
  

  $htmlout .= "</table><br /><br />
  <div>
  <span class='btn'><a href='{$TBDEV['baseurl']}/admin.php?action=rules&amp;mode=cat_new'>{$lang['rules_btn_newcat']}</a></span>&nbsp;
  <span class='btn'><a href='{$TBDEV['baseurl']}/admin.php?action=rules&amp;mode=rules_new'>{$lang['rules_btn_newrule']}</a></span>
  </div>";

  //$htmlout .= New_Cat_Form();

  //$htmlout .= New_rules_form();

  print stdhead("{$lang['rules_rules']}") . $htmlout . stdfoot();
  exit();
}

// ===added delete
function Do_Rules_Delete()
{
    if( !isset($_POST['fdata']) OR !is_array($_POST['fdata']) )
      stderr("Error", "Bad data!");
    
    $id = array();
    
    foreach( $_POST['fdata'] as $k => $v )
    {
      if( isset($v['rules_id']) AND !empty($v['rules_id']) )
      {
        $id[] = intval($v['rules_id']);
      }
    }
    
    if( !count($id) )
      stderr("Error", "No rules selected!");
    
    @mysql_query("DELETE FROM rules WHERE id IN( ".implode(',', $id)." )") or stderr("SQL Error", "OOps!");
    
    stderr("Info", "Rules successfully Deleted! <a href='admin.php?action=rules'>Go Back To Rules Admin?</a>");
}
// ====end


function Cat_Delete($chk=false)
{
    $id = isset($_GET['catid']) ? (int)$_GET['catid'] : 0;
    
    if (!is_valid_id($id))
        stderr("Error", "Bad ID!");
    
    if( !$chk )
    {
      stderr("Sanity Check!", "You're about to delete a rules category, this will delete ALL content within that category! <br />
      <a href='admin.php?action=rules&amp;catid={$id}&amp;mode=cat_delete_chk'><span style='font-weight: bold; color: green'>CONTINUE</span></a>
       or <a href='admin.php?action=rules'><span style='font-weight: bold; color: red'>CANCEL</span></a>");
    }
    
    @mysql_query("DELETE FROM rules WHERE id = $id") or stderr("SQL Error", "1OOps!");
    @mysql_query("DELETE FROM rules_categories WHERE cid = $id") or stderr("SQL Error", "2OOps!");
    stderr("Info", "Rules category deleted successfully! <a href='admin.php?action=rules'>Go Back To Rules Admin?</a>");
    
}

function Show_Cat_Edit_Form()
{
    
    global $lang, $CURUSER;
    
    $htmlout='';
    
    $maxclass = $CURUSER['class'];

    if (!isset($_GET['catid']) || empty($_GET['catid']) || !is_valid_id($_GET['catid']))
    $htmlout .= Do_Error("Error", "No Section selected");

    $cat_id = (int)$_GET['catid'];

    $sql = mysql_query("SELECT * FROM rules_categories WHERE cid = {$cat_id}") or stderr("SQL Error", "OOps!");

    if (!mysql_num_rows($sql))
        stderr("SQL Error", "Nothing doing here!");
        
    while ($row = mysql_fetch_assoc($sql)) 
    {
        
      $htmlout .= "<h2>heading No.{$row['cid']}</h2>

      <form name='inputform' method='post' action='admin.php?action=rules'>
      <input type='hidden' name='mode' value='takeedit_cat' />
      <input type='hidden' name='cat' value='{$row['cid']}' />
      <input type='text' value='" . htmlsafechars($row['rcat_name']) . "' name='rcat_name' style='width:380px;' />

      <select name='min_class_read'>";

      for ($i = 0; $i <= $maxclass; ++$i)
      {
      $htmlout .= '<option value="'.$i.'">'.get_user_class_name($i).'</option>';
      }

      $htmlout .= "</select>

      <input type='submit' name='submit' value='Edit' class='button' />
      </form>";

    }

    print stdhead("Edit options") . $htmlout . stdfoot();
    exit();
}


function Show_Rules_Edit()
{
    global $lang, $CURUSER;
    
    $htmlout='';
    
    $maxclass = $CURUSER['class'];

    if (!isset($_GET['catid']) || empty($_GET['catid']) || !is_valid_id($_GET['catid']))
      stderr("Error", "No Section selected");

    $cat_id = (int)$_GET['catid'];

    $sql = mysql_query("SELECT * FROM rules WHERE cid = {$cat_id}") or stderr("SQL Error", "OOps!");

    if (!mysql_num_rows($sql))
        stderr("SQL Error", "Nothing doing here!");
        
    $htmlout .= "<form name='compose' method='post' action='admin.php?action=rules'>
    <!--<input type='hidden' name='mode' value='rules_update' />-->";
      
    while ($row = mysql_fetch_assoc($sql)) 
    {
      $htmlout .= "<strong>Rules No.{$row['id']}</strong> - ".get_date($row['mtime'], 'DATE',0,1);
     
      $htmlout .= "<!--
      <input type='hidden' name='rules_id' value='{$row['id']}' />
      <input type='hidden' name='action' value='rules_delete' />
      <input type='hidden' name='id' value='{$row['id']}' />-->
      <br />
      <div style='text-align: left; width: 70%; border: 1px solid;'>
      <input type='text' value='".htmlsafechars($row['heading'])."' name='fdata[{$row['id']}][heading]' style='width:650px;' />
      <span style='float:right;'>
      <input type='checkbox' name='fdata[{$row['id']}][rules_id]' value='{$row['id']}' />
      </span>
      <br />
      <textarea name='fdata[{$row['id']}][body]' rows='10' cols='20' style='width:650px;'>".htmlsafechars($row['body'])."</textarea>
      </div>
      <br />";


    }
    
    $htmlout .= "<input type='submit' name='submit' value='With Selected' class='button' />&nbsp;
    <select name='mode'>
    <option value=''>--- Select One ---</option>
    <option value='takeedit_rules'>Update Rules</option>
    <option value='rules_delete'>Delete Rules</option>
    </select>
    </form>";
    
    print stdhead("Edit options") . $htmlout . stdfoot();
    exit();
}

function Do_Rules_Update()
{
    
    $time = TIME_NOW;
    $updateset = array();
    
    
    if (!isset($_POST['fdata']) || !is_array($_POST['fdata']) )
      stderr("Error", "Don't leave any fields blank");
    
    
    foreach( $_POST['fdata'] as $k => $v )
    {
      $holder ='';
      if( isset($v['rules_id']) AND !empty($v['rules_id']) )
      {
        foreach( array('heading', 'body') as $x )
        {
        isset($v[ $x ]) AND !empty($v[ $x ]) ? $holder .= "{$x} = ".sqlesc(strip_tags($v[ $x ])).", " : stderr('Error', "{$x}  is empty");
        }
        $updateset[] = "UPDATE rules SET {$holder} mtime = {$time} WHERE id = ".intval($v['rules_id']);
      }
    }
    /*
    echo '<pre>';
    print_r($updateset); 
    echo '</pre>';
    */
    foreach( $updateset as $x )
    {
      @mysql_query( $x ) or sqlerr();
    }
    
    if (mysql_affected_rows() == -1)
        stderr("SQL Error", "Update failed");

    stderr("Info", "Updated successfully <a href='admin.php?action=rules'>Go Back To Admin</a>");
}

function Do_Cat_Update()
{
    $cat_id = (int)$_POST['cat'];
    
    $min_class_read = sqlesc(intval($_POST['min_class_read']));

    if (!is_valid_id($cat_id))
        stderr("Error", "No values");

    if (empty($_POST['rcat_name']) || (strlen($_POST['rcat_name']) > 100))
        stderr("Error", "No value or value too big");

    $sql = "UPDATE rules_categories SET rcat_name = " . sqlesc(strip_tags($_POST['rcat_name'])) . ", min_class_read=$min_class_read WHERE cid=$cat_id";

    @mysql_query($sql);

    if (mysql_affected_rows() == -1)
        stderr("Warning", "Could not carry out that request");

    stderr("Info", "Updated successfully <a href='admin.php?action=rules'>Go Back To Admin</a>");
   
}

function Do_Cat_Add()
{
    global $TBDEV;
    
    $htmlout='';
    
    if (empty($_POST['rcat_name']) || strlen($_POST['rcat_name']) > 100)
        stderr("Error", "Field is blank or length too long!");

    $cat_name = sqlesc(strip_tags($_POST['rcat_name']));
    
    $min_class_read = sqlesc(strip_tags($_POST['min_class_read']));

    $sql = "INSERT INTO rules_categories (rcat_name,min_class_read) VALUES ($cat_name, $min_class_read)";

    @mysql_query($sql);

    if (mysql_affected_rows() == -1)
        stderr("Warning", "Couldn't forefill that request");


    $htmlout .= New_Cat_Form(1);
    //return $htmlout;

    print stdhead("Add New Heading") . $htmlout . stdfoot();

    exit();
}

function Do_Rules_Add()
{

    global $lang;
    
    $cat_id = (int)$_POST['cat'];

    if (!is_valid_id($cat_id))
      stderr("Error", "No heading");
    
    if (empty($_POST['heading']) || empty($_POST['body']) || strlen($_POST['heading']) > 100)
      stderr("Error", "Field is blank or length too long! <a href='admin.php?action=rules'>Go Back</a>");
    
      
    $heading = sqlesc(strip_tags($_POST['heading']));
    
    $body = sqlesc(strip_tags($_POST['body']));
    
    $sql = "INSERT INTO rules (cid, heading, body, ctime) VALUES ($cat_id, $heading, $body, ".TIME_NOW."+(3600*24*3))";

    @mysql_query($sql);

    if (mysql_affected_rows() == -1)
        stderr("Warning", "Couldn't forefill that request");

    New_rules_Form(1);

    exit();
}

function New_Cat_Form()
{
    global $CURUSER, $lang;
    
    $htmlout = '';
    
    $maxclass = $CURUSER['class'];

    $htmlout .= "<h2>Add A New heading</h2>

    <form name='inputform' method='post' action='admin.php?action=rules'>
    <input type='hidden' name='mode' value='cat_add' />
    
    <input type='text' value='' name='rcat_name' style='width:380px;' />
    
    <select name='min_class_read'>";
    
    for ($i = 0; $i <= $maxclass; ++$i)
    {
    $htmlout .= '<option value="'.$i.'">'.get_user_class_name($i).'</option>';
    }
    
    $htmlout .= "</select>

    <input type='submit' name='submit' value='Add' class='button' />

    </form>";

    print stdhead("Add New Category") . $htmlout . stdfoot();

    exit();
}

function New_rules_Form()
{
    global $CURUSER, $lang;
    
    $htmlout = '';
    
    $sql = mysql_query("SELECT * FROM rules_categories") or die("Ooops");

    if ( !mysql_num_rows($sql) )
      stderr("ERROR", "There Are No Categories. <br /><br />
        <span class='btn'><a href='http://localhost/tb_new/admin.php?action=rules&amp;mode=cat_add'>Add Category</a></span>");

    $htmlout .= "<h2>Add A New section</h2>
    <form name='inputform' method='post' action='admin.php?action=rules'>
    <input type='hidden' name='mode' value='takeadd_rules' />

    <input type='text' value='' name='heading' style='width:380px;' /><br /><br />

    <select name='cat'>
    <option value=''>--Select--</option>";

    while( $v = mysql_fetch_assoc($sql) ) 
    {
        $htmlout .= "<option value='{$v['cid']}'>{$v['rcat_name']}</option>";
    }

    $htmlout .= "</select><br /><br />
    <textarea name='body' rows='15' cols='20' class='textbox' style='width:650px;'>
    </textarea><br />

    <input type='submit' name='save_cat' value='Add' class='button' />

    </form>";

    print stdhead("Add New Rule") . $htmlout . stdfoot();

    exit();
}

function Do_Info($text)
{
    $info = "<div class='infohead'><img src='pic/warned0.gif' alt='Info' title='Info' /> Info</div><div class='infobody'>\n";
    $info .= $text;
    $info .= "</div>";
    $info .= "<a href='admin.php?action=rules'>Go Back To Admin</a> OR Add another?";
    return $info;
}

function Do_Error($heading, $text)
{
    $htmlout='';
    global $TBDEV;
    $htmlout .= "<div class='errorhead'><img src='pic/warned.gif' alt='Warned' /> $heading</div><div class='errorbody'>\n";
    $htmlout .=  "$text\n";
    $htmlout .= "</div>";
    return $htmlout;
    print stdhead("Error") . $HTMLOUT . stdfoot();
    exit;
}


?>