<?php
$tabhandler['object']['password'] = 'showpassword'; // register a report rendering function
$tab['object']['password'] = 'Passwords'; // title of the report tab


/*
Create the following database

CREATE TABLE IF NOT EXISTS `tpa_passwords` (
`id` int(11) NOT NULL AUTO_INCREMENT,
  `protocol` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `entry_id` int(11) NOT NULL,
  `username` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `comment` text COLLATE utf8_unicode_ci NOT NULL,
  `added` datetime NOT NULL,
  `deleted` datetime NOT NULL,
  `user_name` text COLLATE utf8_unicode_ci NOT NULL,
  `hidden` int(1) DEFAULT '0',
  PRIMARY KEY (`id`)
)  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;

*/



  if (isset($_POST['updpass_x'])) {
  // redirect to original page once query is executed
  header( 'Location: '.$_SERVER['HTTP_REFERER'].'' ) ;
  return usePreparedExecuteBlade("
  UPDATE tpa_passwords
      SET 
        protocol = '".$_REQUEST['protocol']."',
        entry_id = '".$_REQUEST['object_id']."',
        username = '".$_REQUEST['username']."',
        password = '".$_REQUEST['password']."',
        comment  = '".$_REQUEST['comment']."'
      WHERE id = '".$_REQUEST['labelid']."'");

   //echo print_r($_POST);
  }


  if (isset($_POST['addpass_x'])) {
     header( 'Location: '.$_SERVER['HTTP_REFERER'].'' ) ;
     return usePreparedExecuteBlade("     
       INSERT INTO 
         tpa_passwords(
           protocol,
           username,
           password,
           entry_id,
           added,
           user_name,
           comment)
       VALUES(
         '".$_REQUEST['protocol']."',
         '".$_REQUEST['username']."',
         '".$_REQUEST['password']."',
         '".$_REQUEST['object_id']."',
         '".date("Y-m-d H:i:s")."',
         '".$remote_username."',
         '".$_REQUEST['comment']."')");
  }

  if (isset($_POST['delpass_x'])) {
     header( 'Location: '.$_SERVER['HTTP_REFERER'].'' ) ;
     return usePreparedExecuteBlade("     
     UPDATE tpa_passwords
       SET
       hidden = 1,
       deleted = '".date("Y-m-d H:i:s")."'
       WHERE id = '".$_REQUEST['labelid']."'");
   // echo "Deleted";
   // echo print_r($_POST);
   
  }
  function showpassword()  {
  $qparams = array ();
  $query = "
    SELECT 
      k.id as Pid,
      k.entry_id,
      k.username,
      k.password,
      k.comment,
      k.protocol,
      o.id
    FROM 
      tpa_passwords k
    LEFT JOIN
      Object o
    ON
      k.entry_id=o.id
    WHERE 
      o.id=".$_REQUEST['object_id']."
    AND k.hidden = 0;";

  	//$ret = array();
	$result = usePreparedSelectBlade ($query, $qparams);
  $array = $result->fetchAll (PDO::FETCH_ASSOC);

 echo "
   <br>
   <br>
   <table border=\"0\" cellpadding\"0\" cellspacing\"10\" align=\"center\">
     <tr>
       <td>&nbsp;</td>
       <td>Username</td>
       <td>Password</td>
       <td>Protocol</td>
       <td>Comment</td>
       <td>&nbsp;</td>
       ";
  echo "<form method=\"post\" name=\"pass-add\" autocomplete=off action=\"\" style=\"display: inline;\">";
  echo "  <tr>";
  echo "    <td><INPUT type=\"image\"      name=\"addpass\" value=\"\" src=\"pix/tango-list-add.png\"></td>";
  echo "    <td><input type=\"text\"       name=\"username\" value=\"".$array[$key]['username']."\"></td>";
  echo "    <td><input type=\"text\"       name=\"password\" value=\"".$array[$key]['password']."\"></td>";
  echo "    <td><input type=\"text\"       name=\"protocol\" value=\"".$array[$key]['protocol']."\"></td>";
  echo "    <td><input type=\"text\"       name=\"comment\"  value=\"".$array[$key]['comment']."\"></td>";
  echo "    <td><INPUT type=\"image\"      name=\"addpass\"  value=\"\" src=\"pix/tango-list-add.png\"></td>";
	echo "  </tr>";
	echo "<input type=\"hidden\" name=\"labelid\"    value='".$array[$key]['Pid']."'>";
  echo "</form>";
  echo "<tr><td height=\"20\"></td></tr>";       
       
 foreach ($array as $key => $item) {
  // echo $array[$key]['id'];
  echo "<form method=\"post\" name=\"pass-".$array[$key]['Pid']." \"autocomplete=off action=\"\">";
  // ok its a dirty work around, but at least it will prevent the passwords from deleting when hitting enter
  echo "  <INPUT type=\"image\" name=\"updpass\"  value=\"updpass\" style=\"position: absolute; left: -9999px; width: 1px; height: 1px;\"/>";
  // and here another dirty work around, but this time from Chrome.
  // it seems that chrome ignores autocomplete, yes...it ignores it, as workaround i have created an fake text & password field
  // don't we just love standards....oh wait...
  echo "   <input type=\"text\" name=\"prevent_autofill\" id=\"prevent_autofill\" value=\"\" style=\"display:none;\" />";
  echo "   <input type=\"password\" name=\"password_fake\" id=\"password_fake\" value=\"\" style=\"display:none;\" />";
  // end of dirty work around.
  echo "  <tr>";
  echo "    <td><INPUT type=\"image\"      name=\"delpass\"  value=\"\" src=\"pix/tango-list-remove.png\"></td>";
  echo "    <td><input type=\"text\"       name=\"username\" value=\"".$array[$key]['username']."\"></td>";
  echo "    <td><input type=\"password\"   name=\"password\" value=\"".$array[$key]['password']."\" onClick=\"setAttribute('type', 'text')\" onblur=\"setAttribute('type', 'password')\"></td>";
  echo "    <td><input type=\"text\"       name=\"protocol\" value=\"".$array[$key]['protocol']."\"></td>";
  echo "    <td><input type=\"text\"       name=\"comment\"  value=\"".$array[$key]['comment']."\"></td>";
  echo "    <td><INPUT type=\"image\"      name=\"updpass\"  value=\"\" src=\"pix/tango-document-save-16x16.png\"></td>";
  echo "  </tr>";
	echo "<input type=\"hidden\" name=\"labelid\"    value='".$array[$key]['Pid']."'>";
  echo "</form>";
  }  
  echo "</table>";
}
?>
