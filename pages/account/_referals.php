<?PHP
$_OPTIMIZATION["title"] = "������� - ����������� ���������";
$user_id = $_SESSION["user_id"];
$db->Query("SELECT COUNT(*) FROM db_users_a WHERE referer_id = '$user_id'");
$refs = $db->FetchRow();
?> 
<div class="s-bk-lf">
	<div class="acc-title">����������� ���������</div>
</div>
<div class="silver-bk">
����������� � ���� ����� ������ � ��������, �� ������ �������� 10% �� ������� ���������� �������  
������������ ���� ���������. ����� �� ��� �� ���������. ���� ��������� ������������ ����� 
�������� ��� ����� 100 000 �������. 
���� ������������ ������ ��� ����������� � ���������� ������������ ���� �����.<br /><br />
<img src="/img/piar-link.png" style="vertical-align:-2px; margin-right:5px;" /><font color="#000;">http://<?=$_SERVER['HTTP_HOST']; ?>/?i=<?=$_SESSION["user_id"]; ?></font>
<p><center>���������� ����� ���������: <font color="#000;"><?=$refs; ?> ���.</font></center></p>
<table cellpadding='3' cellspacing='0' border='0' bordercolor='#336633' align='center' width='98%'>
<tr height='25' valign=top align=center>
	<td class="m-tb"> �����</td>
	<td class="m-tb"> ���� �����������</td>
	<td class="m-tb"> ����� �� ��������</td>
</tr>
<?PHP
  $all_money = 0;
  $db->Query("SELECT db_users_a.user, db_users_a.date_reg, db_users_b.to_referer FROM db_users_a, db_users_b 
  WHERE db_users_a.id = db_users_b.id AND db_users_a.referer_id = '$user_id' ORDER BY to_referer DESC");
	if($db->NumRows() > 0){
  		while($ref = $db->FetchArray()){
		?>
		<tr height="25" class="htt" valign="top" align="center">
			<td align="center"> <?=$ref["user"]; ?>�</td>
			<td align="center"> <?=date("d.m.Y � H:i:s",$ref["date_reg"]); ?>�</td>
			<td align="center"> <?=sprintf("%.2f",$ref["to_referer"]); ?>�</td>
		</tr>
		<?PHP
		$all_money += $ref["to_referer"];
		}
	}else echo '<tr><td align="center" colspan="3">� ��� ��� ���������</td></tr>'
  ?>
</table>
<div class="clr"></div>	
</div>