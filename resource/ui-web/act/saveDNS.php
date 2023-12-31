<?php require_once('../auth.php'); ?>
<?php if (isset($auth) && $auth) {?>
<?php
$conf = json_decode(file_get_contents('/opt/de_GWD/0conf'), true);
$DoH1 = $_GET['DoH1'];
$DoH2 = $_GET['DoH2'];

$dnsChina = $_GET['dnsChina'];
$dnsChina = str_replace(PHP_EOL, ' ', $dnsChina);

$hostsCustomize = $_GET['hostsCustomize'];
$hostsCustomize = str_replace("\t", ' ', $hostsCustomize);
$hostsCustomize = preg_replace("/\s(?=\s)/", "\\1", $hostsCustomize);
$hostsCustomize = str_replace(" ", ",", $hostsCustomize);
$arr = explode("\n",$hostsCustomize);
$arr = array_filter($arr);
foreach($arr as $k=>$v){
        $arr = explode(',',$v);
        $hosts[$arr[1]] = $arr[0];
}

$conf['dns']['doh1'] = $DoH1;
$conf['dns']['doh2'] = $DoH2;
$conf['dns']['china'] = $dnsChina;
$conf['dns']['hosts'] = array();
$conf['dns']['hosts'] = $hosts;
$newJsonString = json_encode($conf, JSON_PRETTY_PRINT);
file_put_contents('/opt/de_GWD/0conf', $newJsonString);

shell_exec('sudo /opt/de_GWD/ui-saveDNSChina');
shell_exec('sudo systemctl restart smartdns');

if ( $conf['dns']['DNSsplit'] === "gfw" ){
	shell_exec('sudo /opt/de_GWD/ui-dnsGFW');
} else {
	shell_exec('sudo /opt/de_GWD/ui-dnsCHNW');
}

shell_exec('sudo /opt/de_GWD/ui-saveListBW');
shell_exec('sudo systemctl restart v2dns');
shell_exec('sudo systemctl restart iptables-proxy');
shell_exec('sudo systemctl restart AdGuardHome');
?>
<?php }?>
