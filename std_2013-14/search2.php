<html>
<head>
<title>Student Search</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link href="css/new.css" rel="stylesheet" type="text/css" />
</head>
<body>

<?php 
$id = $result['id']; 
$adm = $result['adm']; 
$name = $result['name'];
$class = $result['class'];
$idphoto = $result['idphoto'];

?>
<tr class=tr1>
<td class=td1 style="text-align:center;"><?php echo "<a href=\"upload.php?id=$id&find=$find&field=$field\"><img style='background-image:url(bg.jpg);' class='img1' src='upload/$class/$idphoto' title='upload profile photo'></a>";?></td>
<td class=td1 title="view profile"> &nbsp; <?php echo "<a href=\"update_show.php?id=$id\">$name</a>";?></td>
<td class=td1 style="text-align:center;"><?php echo $result['adm']; ?></td>
<td class=td1 style="text-align:center;"><?php echo $result['class']; ?></td>
<td class=td1 style="text-align:center;"><?php echo $result['house']; ?></td>
<?php
if ($_SESSION['user_name'] == 'admin' || $_SESSION['user_name'] == 'office' || $_SESSION['user_name'] == 'dilip')
{
?>
<td class=td1 style='text-align:center;'>
<?php
echo "<a href=\"id_card.php?id=$id\" target='_blank' title='generate ID card'>Icard</a>"; 

echo 
"&nbsp; &nbsp; <a href=\"update.php?id=$id\" title='edit profile'>Edit</a>";

echo 
"&nbsp; &nbsp; <a href=\"delete.php?id=$id\" title='delete profile'>Delete</a></td>";
}
?>
</tr>

</body>
</html>







