<?PHP
if (!defined('PSWeb') || PSWeb !== true) { Header('Location: /404'); return; }
$_OPTIMIZATION["title"] = "Аккаунт - Обменник";
?>  
<div class="s-bk-lf">
	<div class="acc-title">Обменник</div>
</div>
<div class="silver-bk">
	В обменном пункте Вы можете обменять серебро для вывода на серебро для покупок. 
	При обмене серебра Вы получаете +<?=$db_config["percent_swap"]; ?>% на счет для покупок.<br /><br />
	<center><font color="red">Обмен возможен только в одну сторону</font></p></center>
	<?PHP
	if(isset($_POST["sum"])){
		$sum = intval($_POST["sum"]);
		if($sum >= 1000){
			if($user_data["money_p"] >= $sum){
				$add_sum = ($db_config["percent_swap"] > 0) ? ( ($db_config["percent_swap"] / 100) * $sum) + $sum : $sum;
				$ta = time();
				$td = $ta + 60*60*24*15;
				$db->Query("UPDATE db_users_b SET money_b = money_b + $add_sum, money_p = money_p - $sum WHERE id = '$user_id'");
				$db->Query("INSERT INTO db_swap_ser (user_id, user, amount_b, amount_p, date_add, date_del) VALUES ('$user_id','$user_name','$add_sum','$sum','$ta','$td')");
				echo "<center><font color = 'green'><b>Обмен произведен</b></font></center><BR />";
			}else echo "<center><font color = 'red'><b>Недостаточно серебра для обмена</b></font></center><BR />";
		}else echo "<center><font color = 'red'><b>Минимальная сумма для обмена 1000 серебра</b></font></center><BR />";
	}
	?>
	<form action="" method="post">
		<table width="400" border="0" align="center">
		  <tr>
			<td><font color="#000;">Отдаете серебро для вывода</font> [мин. 1000]: </td>
			<td align="center"><input type="text" class="lg" name="sum" id="sum" value="1000" onkeyup="GetSumPer();" style="margin:0px; width:60px;"/></td>
		  </tr>
		  <tr>
			<td><font color="#000;">Получаете серебро для покупок</font> [+<?=$db_config["percent_swap"]; ?>%]: </td>
			<td align="center"><span id="res_sum" name="res">0.00</span>
				<input type="hidden" name="per" id="percent" value="<?=$db_config["percent_swap"]; ?>" disabled="disabled"/></td>
		  </tr>
		  <tr>
			<td colspan="2" align="center"><BR /><input type="submit" name="swap" value="Обменять" class="button_0" style="height: 30px; margin-top:10px;" /></td>
		  </tr>
		</table>
		<BR />
	</form>
	<script language="javascript">GetSumPer();</script>
	<div class="clr"></div>	
</div>