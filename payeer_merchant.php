<?PHP
##################################################
# �������������� ���������� ����� Payeer		 #
# ��� �������� ��������� ����� � �����������     #
# �����: ������������� PSWeb.ru                  #
# ����: psweb.ru                                 #
# email: i@psweb.ru                              #
##################################################

//�������� IP ������� ���������� Payeer
if (!in_array($_SERVER['REMOTE_ADDR'], array('185.71.65.92', '185.71.65.189', '149.202.17.210'))) die('ERROR IP');
if (isset($_POST['m_operation_id']) && isset($_POST['m_sign']))
{
# ������������� �������
	function __autoload($name){ include('classes/_class.'.$name.'.php');}
# ����� ������� 
	$config = new config;
	$m_key = $config->secretW;
# ��������� ������ ��� ��������� �������
	$arHash = array(
	$_POST['m_operation_id'],
	$_POST['m_operation_ps'],
	$_POST['m_operation_date'],
	$_POST['m_operation_pay_date'],
	$_POST['m_shop'],
	$_POST['m_orderid'],
	$_POST['m_amount'],
	$_POST['m_curr'],
	$_POST['m_desc'],
	$_POST['m_status']
	);
# ���� ���� �������� �������������� ���������, �� ��������� �� �������
	if (isset($_POST['m_params']))
	{
	$arHash[] = $_POST['m_params'];
	}
# ��������� � ������ ��������� ����
	$arHash[] = $m_key;
# ��������� �������
	$sign_hash = strtoupper(hash('sha256', implode(':', $arHash)));
# ���� ������� ��������� � ������ ������� ���������
	if ($_POST['m_sign'] == $sign_hash && $_POST['m_status'] == 'success')
	{
	# ���� ������
		$db = new db($config->HostDB, $config->UserDB, $config->PassDB, $config->BaseDB);
	# �������
		$func = new func;
	# ���������� � ������� �� ����
		$db->Query("SELECT * FROM db_payeer_insert WHERE id = ".$_POST['m_orderid']) or die($_POST['m_orderid'].'|error');
	# ���� � ���� ��� ������ �������, ������ "������"
		if($db->NumRows() == 0){ exit($_POST['m_orderid'].'|error');}
	# ������ ���������� � �������	
		$payeer_row = $db->FetchArray();
	# ���� ������ ������� 1 ('���������'), ���������� '����������'
		if($payeer_row['status'] == 1){ exit($_POST['m_orderid'].'|success');}
	# ���� ����� ������� � ���������� �� ����� ����� � ����
		if($payeer_row['sum'] != $_POST['m_amount']){ exit($_POST['m_orderid'].'|error');}
	# ����� �������		
		$amount = $payeer_row['sum'];
	# ID ������������
		$user_id = $payeer_row['user_id']; 
	# ��������� �� ����
		$db->Query("SELECT * FROM db_config WHERE id = '1' LIMIT 1");
		$config_site = $db->FetchArray();
	# ���������� � ������������ � ��������
		$db->Query("SELECT user, referer_id FROM db_users_a WHERE id = '".$user_id."' LIMIT 1");
		$user_data = $db->FetchArray();
		$user_name = $user_data['user'];
		$refid = $user_data['referer_id'];
	# ��������� ������
		$serebro = sprintf("%.4f", floatval($config_site['ser_per_wmr'] * $amount) );
		$db->Query("SELECT insert_sum FROM db_users_b WHERE id = '".$user_id."' LIMIT 1");
		$insert_sum = $db->FetchRow();
	# ����� ��� ������ ����������
		$serebro = intval($insert_sum == 0) ? ($serebro + ($serebro * 0.55) ) : $serebro;
	# ������/����� ��� ���������� �� ������������ �����
		$add_tree = ( $amount >= 500) ? 2 : 0;
	# ���������� ��������
		$to_referer = ($serebro * 0.10);
	# ��������� ������������
		$db->Query("UPDATE db_users_b SET money_b = money_b + '$serebro', e_t = e_t + '$add_tree', to_referer = to_referer + '$to_referer', last_sbor = '".time()."', insert_sum = insert_sum + '$amount' WHERE id = '$user_id'") or die ($_POST['m_orderid'].'|error');
		$db->Query("UPDATE db_payeer_insert SET status = '1' WHERE id = '".$_POST['m_orderid']."'") or die(mysql_error());
	# ��������� �������� �������� � ������
		$add_tree_referer = ($insert_sum <= 0.01) ? ", a_t = a_t + 1" : "";
		$db->Query("UPDATE db_users_b SET money_b = money_b + $to_referer, from_referals = from_referals + '$to_referer' $add_tree_referer WHERE id = '$refid'") or die(mysql_error());
	# ���������� ����������
		$da = time();
		$dd = $da + 60*60*24*15;
		$db->Query("INSERT INTO db_insert_money (user, user_id, money, serebro, date_add, date_del) VALUES ('$user_name','$user_id','$amount','$serebro','$da','$dd')") or die(mysql_error());
	# ���������� ���������� �����
		$db->Query("UPDATE db_stats SET all_insert = all_insert + '$amount' WHERE id = '1'") or die(mysql_error());
		exit($_POST['m_orderid'].'|success');
	}
}
exit($_POST['m_orderid'].'|error');
?>