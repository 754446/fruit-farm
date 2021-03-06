<?PHP
if (!defined('PSWeb') || PSWeb !== true) { Header('Location: /404'); return; }
$_OPTIMIZATION["title"] = "Аккаунт - Торговая лавка";
?>
<div class="s-bk-lf">
    <div class="acc-title">Торговая лавка</div>
</div>
<div class="silver-bk">Торговая лавка позволит вам продать все ваши фрукты за серебро, которое можно обменять на реальные 
деньги. Вырученное с продажи серебро распределяется между двумя счетами (счет для покупок и счет 
для вывода) в пропорциях: <?=100-$db_config["percent_sell"]; ?>% на счет для покупок и <?=$db_config["percent_sell"]; ?>% на вывод.<br /><br />
Курс продажи любых плодов: <font color="#f59f97"><?=$db_config["items_per_coin"]; ?> фруктов = 1 серебро.</font>
<div class="clr"></div><BR />
<?PHP
# Продажа
if(isset($_POST['sell'])){
    $all_items = 0;
    $string_null = '';
    $items_string = '';
    $items_values = '';
    foreach($items as $item => $description){
        $all_items += $user_data[$description['char'].'_b'];
        $$item = $user_data[$item];
        $string_null.= '`'.$description['char'].'_b` = \'0\',';
        $items_string.= '`'.$description['char'].'_s`,';
        $items_values.= '\''.$user_data[$description['char'].'_b'].'\',';
    }
    if($all_items > 0){
        $money_add = $func->SellItems($all_items, $db_config['items_per_coin']);
        $money_b = ( (100 - $db_config['percent_sell']) / 100) * $money_add;
        $money_p = ( ($db_config['percent_sell']) / 100) * $money_add;
        # Обновляем юзверя
        $db->Query("UPDATE `db_users_b` SET ".$string_null." `money_b` = `money_b` + '$money_b',`money_p` = `money_p` + '$money_p', `last_sbor` = '".time()."' WHERE `id` = '$user_id'");
        $da = time();
        $dd = $da + 60*60*24*15;
        # Вставляем запись в статистику
        $db->Query("INSERT INTO `db_sell_items` (`user`, `user_id`, ".$items_string." `amount`, `all_sell`, `date_add`, `date_del`) VALUES 
        ('$user_name','$user_id',".$items_values."'$money_add','$all_items','$da','$dd')");
        echo '<center><font color = "green"><b>Вы продали '.$all_items.' плодов, на сумму '.$money_add.' серебра</b></font></center><BR />';
        $db->Query("SELECT * FROM `db_users_b` WHERE id = '$user_id' LIMIT 1");
        $user_data = $db->FetchArray();
    }else{
        echo '<center><font color = "red"><b>Вам нечего продавать :(</b></font></center><BR />';
    }
}
?>	       
<form action="" method="post">
    <table width="480" border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
            <td height="30" align="center" valign="middle">&nbsp;</td>
            <td height="30" align="center" valign="middle"><strong>У вас в наличии</strong></td>
            <td height="30" align="center" valign="middle"><strong>На сумму (серебра)</strong></td>
        </tr>
        <?PHP
        foreach($items as $item => $description){
            echo '<tr>';
            echo '<td width="30" height="30" align="center" valign="middle"><div class="sm-line-nt"><img src="'.$description['img_small'].'" /></div></td>';
            echo '<td align="center" valign="middle">'.$user_data[$description['char'].'_b'].' плодов</td>';
            echo '<td align="center" valign="middle">'.$func->SellItems($user_data[$description['char'].'_b'], $db_config['items_per_coin']).'</td>';
            echo '</tr>';
        }
        ?>
        <tr>
            <td align="center" valign="middle" colspan="3">
            <BR />
            <input type="submit" name="sell" value="Продать все" class="button_0" style="height: 30px;"></td>
        </tr>
    </table>
</form>
</div>							
<div class="clr"></div>	
