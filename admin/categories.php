<?php
/*
+------------------------------------------------
|   TBDev.net BitTorrent Tracker PHP
|   =============================================
|   by CoLdFuSiOn
|   (c) 2003 - 2011 TBDev.Net
|   http://www.tbdev.net
|   =============================================
|   svn: http://sourceforge.net/projects/tbdevnet/
|   Licence Info: GPL
+------------------------------------------------
|   $Date$
|   $Revision: 208 $
|   $Author$
|   $URL$
+------------------------------------------------
*/

if ( ! defined( 'IN_TBDEV_ADMIN' ) )
{
	print "<h1>Incorrect access</h1>You cannot access this file directly.";
	exit();
}

require_once "include/user_functions.php";

    $params = array_merge( $_GET, $_POST );

    $params['mode'] = isset($params['mode']) ? $params['mode'] : '';
    
    switch($params['mode'])
    {
      case 'takemove_cat':
        move_cat();
        break;
        
      case 'move_cat':
        move_cat_form();
        break;
        
      case 'takeadd_cat':
        add_cat();
        break;
        
      case 'takedel_cat':
        delete_cat();
        break;
        
      case 'del_cat':
        delete_cat_form();
        break;
        
      case 'takeedit_cat':
        edit_cat();
        break;
        
      case 'edit_cat':
        edit_cat_form();
        break;
        
      case 'cat_form':
        show_cat_form();
        break;

      default:
        show_categories();
        break;
    }


function move_cat() {
    
    global $TBDEV, $params;
    
    if( ( !isset($params['id']) OR !is_valid_id($params['id']) ) OR ( !isset($params['new_cat_id']) OR !is_valid_id($params['new_cat_id']) ) )
    {
      stderr( 'MOD ERROR', 'No category ID selected' );
    }
    
    if( !is_valid_id($params['new_cat_id']) OR ($params['id'] == $params['new_cat_id']) )
    {
      stderr( 'MOD ERROR', 'You can not move torrents into the same category' );
    }
    
    $old_cat_id = intval($params['id']);
    $new_cat_id = intval($params['new_cat_id']);
    
    // make sure both categories exist
    $q = @mysql_query( "SELECT id FROM categories WHERE id IN($old_cat_id, $new_cat_id)" );
    
    if( 2 != mysql_num_rows($q) )
    {
      stderr( 'MOD ERROR', 'That category does not exist or has been deleted' );
    }
    
    //all go
    @mysql_query( "UPDATE torrents SET category = $new_cat_id WHERE category = $old_cat_id" );
    
    if( -1 != mysql_affected_rows() )
    {
      header( "Location: {$TBDEV['baseurl']}/admin.php?action=categories" );
    }
    else
    {
      stderr( 'MOD ERROR', 'There was an error deleting the category' );
    }
}



function move_cat_form() {

    global $params;
    
    if( !isset($params['id']) OR !is_valid_id($params['id']) )
    {
      stderr( 'MOD ERROR', 'No category ID selected' );
    }
    
    $q = @mysql_query( "SELECT * FROM categories WHERE id = ".intval($params['id']) );
    
    if( false == mysql_num_rows($q) )
    {
      stderr( 'MOD ERROR', 'That category does not exist or has been deleted' );
    }
    
    $r = mysql_fetch_assoc($q);
    
    
    $check = '';
    
    $select = "<select name='new_cat_id'>\n<option value='0'>Select Category</option>\n";

    $cats = genrelist();
  
    foreach ($cats as $c)
    {
      $select .= ($c['id'] != $r['id']) ? "<option value='{$c["id"]}'>" . htmlsafechars($c['name']) . "</option>\n" : "";
    }
    
    $select .= "</select>\n";
    
    $check .= "<tr>
      <td style='text-align:right; width:50%;'><span style='color:limegreen;font-weight:bold;'>Select a new category:</span></td>
      <td>$select</td>
    </tr>";
    
    
    $htmlout = '';
    
    $htmlout .= "
                     <div class='cblock'>
                         <div class='cblock-header'>Move category</div>
                         <div class='cblock-content'>";

    $htmlout .= "            <form action='admin.php?action=categories' method='post'>
                                  <input type='hidden' name='mode' value='takemove_cat' />
                                  <input type='hidden' name='id' value='{$r['id']}' />

                                  <table style='text-align:center; width:80%;' cellspacing='2' cellpadding='4px'>
                                        <tr>
                                           <td colspan='2' class='colhead'>You are about to move category: ".htmlsafechars($r['name'])."</td>
                                        </tr>
                                        <tr>
                                           <td colspan='2'>Note: This tool will move ALL torrents FROM one category to ANOTHER category only! It will NOT delete any categories or torrents.</td>
                                        </tr>
                                        <tr>
                                           <td style='width:50%; text-align:right;'><span style='color:red;font-weight:bold;'>Old Category Name:</span></td>
                                           <td>".htmlsafechars($r['name'])."</td>
                                        </tr>
                                        {$check}
                                        <tr>
                                           <td colspan='2' style='text-align:center;'>
                                              <input type='submit' class='btn' value='Move' />
                                              <input type='button' class='btn' value='Cancel' onclick=\"history.go(-1)\" />
                                           </td>
                                        </tr>
                                  </table>
                             </form>";
    $htmlout .= "        </div>
                     </div>";
      
      print stdhead("Move category {$r['name']}") . $htmlout . stdfoot();
}


function add_cat() {

    global $TBDEV, $params;
    
    foreach( array( 'new_cat_name', 'new_cat_desc', 'new_cat_image') as $x )
    {
      if( !isset($params[ $x ]) OR empty($params[ $x ]) )
        stderr( 'MOD ERROR', 'Some fields were left blank' );
    }
    
    if ( !preg_match( "/^cat_[A-Za-z0-9_]+\.(?:gif|jpg|jpeg|png)$/i", $params['new_cat_image'] ) )
    {
					stderr( 'MOD ERROR', 'File name is not allowed' );
    }
    
    $cat_name = sqlesc($params['new_cat_name']);
    $cat_desc = sqlesc($params['new_cat_desc']);
    $cat_image = sqlesc($params['new_cat_image']);
    
    @mysql_query( "INSERT INTO categories (name, cat_desc, image)
                  VALUES($cat_name, $cat_desc, $cat_image)" );
      
    if( -1 == mysql_affected_rows() )
    {
      stderr( 'MOD ERROR', 'That category does not exist or has been deleted' );
    }
    else
    {
      header( "Location: {$TBDEV['baseurl']}/admin.php?action=categories" );
    }
}

function delete_cat() {

    global $TBDEV, $params;
    
    if( !isset($params['id']) OR !is_valid_id($params['id']) )
    {
      stderr( 'MOD ERROR', 'No category ID selected' );
    }
    
    $q = @mysql_query( "SELECT * FROM categories WHERE id = ".intval($params['id']) );
    
    if( false == mysql_num_rows($q) )
    {
      stderr( 'MOD ERROR', 'That category does not exist or has been deleted' );
    }
    
    $r = mysql_fetch_assoc($q);
    
    $old_cat_id = intval($r['id']);
    
    if( isset($params['new_cat_id']) )
    {
      if( !is_valid_id($params['new_cat_id']) OR ($r['id'] == $params['new_cat_id']) )
      {
        stderr( 'MOD ERROR', 'That category does not exist or has been deleted' );
      }
      
      $new_cat_id = intval($params['new_cat_id']);
      
      //make sure category isn't out of range before moving torrents! else orphans!
      $q = @mysql_query( "SELECT COUNT(*) FROM categories WHERE id = $new_cat_id" );
      
      $count = mysql_fetch_array($q, MYSQL_NUM);
      
      if( !$count[0] )
      {
        stderr( 'MOD ERROR', 'That category does not exist or has been deleted' );
      }
      
      //all go
      @mysql_query( "UPDATE torrents SET category = $new_cat_id WHERE category = $old_cat_id" );
    }
    
    @mysql_query( "DELETE FROM categories WHERE id = $old_cat_id" );
    
    if( mysql_affected_rows() )
    {
      header( "Location: {$TBDEV['baseurl']}/admin.php?action=categories" );
    }
    else
    {
      stderr( 'MOD ERROR', 'There was an error deleting the category' );
    }
}



function delete_cat_form() {

    global $params;
    
    if( !isset($params['id']) OR !is_valid_id($params['id']) )
    {
      stderr( 'MOD ERROR', 'No category ID selected' );
    }
    
    $q = @mysql_query( "SELECT * FROM categories WHERE id = ".intval($params['id']) );
    
    if( false == mysql_num_rows($q) )
    {
      stderr( 'MOD ERROR', 'That category does not exist or has been deleted' );
    }
    
    $r = mysql_fetch_assoc($q);
    
    $q = @mysql_query( "SELECT COUNT(*) FROM torrents WHERE category = ".intval($r['id']) );
    
    $count = mysql_fetch_array($q, MYSQL_NUM);
    
    $check = '';
    
    if($count[0])
    {
      $select = "<select name='new_cat_id'>\n<option value='0'>Select Category</option>\n";

      $cats = genrelist();
    
      foreach ($cats as $c)
      {
        $select .= ($c['id'] != $r['id']) ? "<option value='{$c["id"]}'>" . htmlsafechars($c['name']) . "</option>\n" : "";
      }
      
      $select .= "</select>\n";
      
      $check .= "<tr>
        <td style='text-align:right;width:50%;'>Select a new category:<br /><span style='color:red;font-weight:bold;'>Warning: There are torrents in this category, so you need to select a category to move them to.</span></td>
        <td>$select</td>
      </tr>";
    }
    
    $htmlout = '';
    
    $htmlout .= "
                     <div class='cblock'>
                         <div class='cblock-header'>Delete category</div>
                         <div class='cblock-content'>";

    $htmlout .= "            <form action='admin.php?action=categories' method='post'>
                                  <input type='hidden' name='mode' value='takedel_cat' />
                                  <input type='hidden' name='id' value='{$r['id']}' />
                                  <table style='text-align:center; width:80%;'cellspacing='2' cellpadding='2'>
                                        <tr>
                                           <td colspan='2' class='colhead'>You are about to delete category: ".htmlsafechars($r['name'])."</td>
                                        </tr>
                                        <tr>
                                           <td style='text-align:right; width:50%;'>Cat Name:</td>
                                           <td>".htmlsafechars($r['name'])."</td>
                                        </tr>
                                        <tr>
                                           <td style='text-align:right;'>Description:</td>
                                           <td>".htmlsafechars($r['cat_desc'])."</td>
                                        </tr>
                                        <tr>
                                           <td style='text-align:right;'>Image:</td>
                                           <td>".htmlsafechars($r['image'])."</td>
                                        </tr>
                                        {$check}
                                        <tr>
                                           <td colspan='2' style='text-align:center;'>
                                              <input type='submit' class='btn' value='Delete' />
                                              <input type='button' class='btn' value='Cancel' onclick=\"history.go(-1)\" />
                                           </td>
                                        </tr>
                                  </table>
                             </form>
                         </div>
                     </div>";
      
      print stdhead("Deleting category {$r['name']}") . $htmlout . stdfoot();
}


function edit_cat() {

    global $TBDEV, $params;
    
    if( !isset($params['id']) OR !is_valid_id($params['id']) )
    {
      stderr( 'MOD ERROR', 'No category ID selected' );
    }
    
    foreach( array( 'cat_name', 'cat_desc', 'cat_image') as $x )
    {
      if( !isset($params[ $x ]) OR empty($params[ $x ]) )
        stderr( 'MOD ERROR', 'Some fields were left blank' );
    }
    
    if ( !preg_match( "/^cat_[A-Za-z0-9_]+\.(?:gif|jpg|jpeg|png)$/i", $params['cat_image'] ) )
    {
					stderr( 'MOD ERROR', 'File name is not allowed' );
    }
    
    $cat_name = sqlesc($params['cat_name']);
    $cat_desc = sqlesc($params['cat_desc']);
    $cat_image = sqlesc($params['cat_image']);
    $cat_id = intval($params['id']);
    
    @mysql_query( "UPDATE categories SET name = $cat_name, cat_desc = $cat_desc, image = $cat_image WHERE id = $cat_id" );
      
    if( -1 == mysql_affected_rows() )
    {
      stderr( 'MOD ERROR', 'That category does not exist or has been deleted' );
    }
    else
    {
      header( "Location: {$TBDEV['baseurl']}/admin.php?action=categories" );
    }
}



function edit_cat_form() {

    global $TBDEV, $params;
    
    if( !isset($params['id']) OR !is_valid_id($params['id']) )
    {
      stderr( 'MOD ERROR', 'No category ID selected' );
    }
    
    $htmlout = '';
    
    $q = @mysql_query( "SELECT * FROM categories WHERE id = ".intval($params['id']) );
    
    if( false == mysql_num_rows($q) )
    {
      stderr( 'MOD ERROR', 'That category does not exist or has been deleted' );
    }
    
    $r = mysql_fetch_assoc($q);
    
    $dh = opendir( $TBDEV['pic_base_url'].'caticons' );
		
		$files = array();
		
 		while ( FALSE !== ( $file = readdir( $dh ) ) )
 		{
 			if ( ($file != ".") && ($file != "..") )
 			{
				if ( preg_match( "/^cat_[A-Za-z0-9_]+\.(?:gif|jpg|jpeg|png)$/i", $file ) )
				{
					$files[] = $file;
				}
 			}
 		}
 		
 		closedir( $dh );
 		
 		if( is_array($files) AND count($files) )
 		{
      $select = "<select name='cat_image'>\n<option value='0'>Select Image</option>\n";

      foreach ($files as $f)
      {
        $selected = ($f == $r['image']) ? " selected='selected'" : "";
        $select .= "<option value='" . htmlsafechars($f) . "'$selected>" . htmlsafechars($f) . "</option>\n";

      }
      
      $select .= "</select>\n";
      
      $check = "<tr>
        <td style='text-align:right;width:50%;'>Select a new image:<br /><span style='color:limegreen;font-weight:bold;'>Info: If you want a new image, you have to upload it to the /caticon/ directory first.</span></td>
        <td>$select</td>
      </tr>";
 		}
 		else
 		{
      $check = "<tr>
        <td style='text-align:right;width:50%;'>Select a new image:</td>
        <td><span style='color:red;font-weight:bold;'>Warning: There are no images in the directory, please upload one.</span></td>
      </tr>";
 		}
 		

    $htmlout .= "
                     <div class='cblock'>
                         <div class='cblock-header'>Edit Categories</div>
                         <div class='cblock-content'>";

    $htmlout .= "            <form action='admin.php?action=categories' method='post'>
                                  <input type='hidden' name='mode' value='takeedit_cat' />
                                  <input type='hidden' name='id' value='{$r['id']}' />
                                  <table style='text-align:center; width:80%;' cellspacing='2' cellpadding='2'>
                                        <tr>
                                           <td style='text-align:right;'>New Cat Name:</td>
                                           <td><input type='text' name='cat_name' class='option' size='50' value='".htmlsafechars($r['name'])."' /></td>
                                        </tr>
                                        <tr>
                                           <td style='text-align:right;'>Description:</td>
                                           <td><textarea cols='50' rows='5' name='cat_desc'>".htmlsafechars($r['cat_desc'])."</textarea></td>
                                        </tr>
                                        {$check}
                                        <tr>
                                           <td colspan='2' style='text-align:center;'>
                                              <input type='submit' class='btn' value='Edit' /><input type='button' class='btn' value='Cancel' onclick=\"history.go(-1)\" />
                                           </td>
                                        </tr>
                                  </table>
                             </form>
                         </div>
                     </div>";

      print stdhead( "Editing category: {$r['name']}") . $htmlout . stdfoot();
}


function show_categories() {
    
    global $TBDEV;
    
    $htmlout = '';

    $dh = opendir( $TBDEV['pic_base_url'].'caticons' );

		$files = array();
		
 		while ( FALSE !== ( $file = readdir( $dh ) ) )
 		{
 			if ( ($file != ".") && ($file != "..") )
 			{
				if ( preg_match( "/^cat_[A-Za-z0-9_]+\.(?:gif|jpg|jpeg|png)$/i", $file ) )
				{
					$files[] = $file;
				}
 			}
 		}
 		
 		closedir( $dh );
 		
 		if( is_array($files) AND count($files) )
 		{
      $select = "<select name='new_cat_image'>\n<option value='0'>Select Image</option>\n";

      foreach ($files as $f)
      {
        $i = 0;
        $select .= "<option value='" . htmlsafechars($f) . "'>" . htmlsafechars($f) . "</option>\n";
        $i++;
      }
      
      $select .= "</select>\n";
      
      $check = "<tr>
        <td style='text-align:right; width:50%;'>Select a new image:<br /><span style='color:limegreen;font-weight:bold;'>Warning: If you want a new image, you have to upload it to the /caticon/ directory first.</span></td>
        <td>$select</td>
      </tr>";
 		}
 		else
 		{
      $check = "<tr>
        <td style='text-align:right; width:50%;'>Select a new image:</td>
        <td><span style='color:red;font-weight:bold;'>Warning: There are no images in the directory, please upload one.</span></td>
      </tr>";
 		}

    $htmlout .= "
                     <div class='cblock'>
                         <div class='cblock-header'>Admin Categories</div>
                         <div class='cblock-content'>";


    $htmlout .= "            <form action='admin.php?action=categories' method='post'>
                                  <input type='hidden' name='mode' value='takeadd_cat' />
                                  <table style='width:80%; border:1px solid #000;' cellspacing='2' cellpadding='2'>
                                        <tr>
                                           <td class='colhead' colspan='2' style='text-align:center;'>
                                              <b>Make a new category:</b>
                                           </td>
                                        </tr>
                                        <tr>
                                           <td style='text-align:right;'>New Cat Name:</td>
                                           <td style='text-align:left;'><input type='text' name='new_cat_name' size='50' maxlength='50' /></td>
                                        </tr>
                                        <tr>
                                           <td style='text-align:right;'>New Cat Description:</td>
                                           <td style='text-align:left;'><textarea cols='50' rows='5' name='new_cat_desc'></textarea></td>
                                        </tr>
                                        <!--<tr>
                                           <td style='text-align:right;'>New Filename (Eg: films.gif or films.png):</td>
                                           <td style='text-align:left;'><input type='text' name='new_cat_image' class='option' size='50' /></td>
                                        </tr>-->
                                        {$check}
                                        <tr>
                                           <td colspan='2' style='text-align:center;'>
                                              <input type='submit' value='Add New' class='btn' />
                                              <input type='reset' value='Reset' class='btn' />
                                           </td>
                                        </tr>
                                  </table>
                             </form>


                             <div class='inner_header' style='width:80%; margin:20px auto 20px auto;'>Current Categories:</div>
                                <table style='width:80%; border:1px solid #000;' cellpadding='5px'>
                                      <tr>
                                         <td class='colhead' style='width:60px;'>Cat ID</td>
                                         <td class='colhead' style='width:120px;'>Cat Name</td>
                                         <td class='colhead' style='width:200px;'>Cat Description</td>
                                         <td class='colhead' style='width:45px;'>Image</td>
                                         <td class='colhead' style='width:40px;'>Edit</td>
                                         <td class='colhead' style='width:40px;'>Delete</td>
                                         <td class='colhead' style='width:40px;'>Move</td>
                                      </tr>";


    $query = @mysql_query( "SELECT * FROM categories" );

    if( false == mysql_num_rows($query) )
    {
      $htmlout .= "                   <tr>
                                         <td colspan='7'><h1>No categories defined.</h1></td>
                                      </tr>";
    }
    else
    {
      while($row = mysql_fetch_assoc($query))
      {
        $cat_image = file_exists($TBDEV['pic_base_url'].'caticons/'.$row['image']) ? "<img src='{$TBDEV['pic_base_url']}caticons/{$row['image']}' alt='{$row['id']}' />" : "No Image";

        $htmlout .= "                 <tr>
                                         <td style='height:48px; width:60px;'><b>ID({$row['id']})</b></td>
                                         <td style='width:120px;'>{$row['name']}</td>
                                         <td style='width:250px;'>{$row['cat_desc']}</td>
                                         <td style='text-align:center; width:45px;'>$cat_image</td>
                                         <td style='text-align:center; width:18px;'>
                                            <a href='admin.php?action=categories&amp;mode=edit_cat&amp;id={$row['id']}'>
                                              <img src='{$TBDEV['pic_base_url']}aff_tick.gif' alt='Edit Category' title='Edit' width='12' height='12' />
                                            </a>
                                         </td>
                                         <td style='text-align:center; width:18px;'>
                                            <a href='admin.php?action=categories&amp;mode=del_cat&amp;id={$row['id']}'>
                                              <img src='{$TBDEV['pic_base_url']}aff_cross.gif' alt='Delete Category' title='Delete' width='12' height='12' />
                                            </a>
                                         </td>
                                         <td style='text-align:center; width:18px;'>
                                            <a href='admin.php?action=categories&amp;mode=move_cat&amp;id={$row['id']}'>
                                              <img src='{$TBDEV['pic_base_url']}plus.gif' alt='Move Category' title='Move' width='12' height='12' />
                                            </a>
                                         </td>
                                      </tr>";
      }


    } //endif

    $htmlout .= '</table>';

    $htmlout .= "
                         </div>
                     </div>";

    print stdhead('Admin Categories') . $htmlout . stdfoot();
}

?>