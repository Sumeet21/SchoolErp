<?php
/**
 * Display a selection (popup window) to pick a DN.
 *
 * @package phpLDAPadmin
 * @subpackage Page
 */

/**
 */

include './common.php';

$www['page'] = new page();

$request = array();
$request['container'] = get_request('container','GET');
$request['element'] = get_request('form_element','GET');
$request['rdn'] = get_request('rdn','GET');

echo '<div class="popup">';
printf('<h3 class="subtitle">%s</h3>',_('Entry Chooser'));

echo '<script type="text/javascript" language="javascript">';
echo '	function returnDN(dn) {';
printf('	opener.document.%s.value = dn;',$request['element']);
echo '	close();';
echo '	}';
echo '</script>';

echo '<table class="forminput" width=100% border=0>';
if ($request['container']) {
	printf('<tr><td class="heading" colspan=3>%s:</td><td>%s</td></tr>',_('Server'),$app['server']->getName());
	printf('<tr><td class="heading" colspan=3>%s:</td><td>%s</td></tr>',_('Looking in'),$request['container']);
	echo '<tr><td class="blank" colspan=4>&nbsp;</td></tr>';
}

# Has the user already begun to descend into a specific server tree?
if (isset($app['server']) && ! is_null($request['container'])) {

	$request['children'] = $app['server']->getContainerContents($request['container'],null,0,'(objectClass=*)',$_SESSION[APPCONFIG]->getValue('deref','tree'));
	sort($request['children']);

	foreach ($app['server']->getBaseDN() as $base) {
		if (DEBUG_ENABLED)
			debug_log('Comparing BaseDN [%s] with container [%s]',64,0,__FILE__,__LINE__,__METHOD__,$base,$request['container']);

		if (! pla_compare_dns($request['container'],$base)) {
			$parent_container = false;
			$href['up'] = sprintf('entry_chooser.php?form_element=%s&rdn=%s',$request['element'],rawurlencode($request['rdn']));
			break;

		} else {
			$parent_container = $app['server']->getContainer($request['container']);
			$href['up'] = sprintf('entry_chooser.php?form_element=%s&rdn=%s&server_id=%s&container=%s',
				$request['element'],$request['rdn'],$app['server']->getIndex(),rawurlencode($parent_container));
		}
	}

	echo '<tr>';
	echo '<td class="blank">&nbsp;</td>';
	printf('<td class="icon"><a href="%s"><img src="%s/up.png" alt="Up" /></a></td>',$href['up'],IMGDIR);
	printf('<td colspan=2><a href="%s">%s...</a></td>',$href['up'],_('Back Up'));
	echo '</tr>';

	if (! count($request['children']))
		printf('<td class="blank" colspan=2>&nbsp;</td><td colspan=2">(%s)</td>',_('no entries'));

	else
		foreach ($request['children'] as $dn) {
			$href['return'] = sprintf("javascript:returnDN('%s%s')",($request['rdn'] ? sprintf('%s,',$request['rdn']) : ''),rawurlencode($dn));
			$href['expand'] = sprintf('entry_chooser.php?server_id=%s&form_element=%s&rdn=%s&container=%s',
				$app['server']->getIndex(),$request['element'],$request['rdn'],rawurlencode($dn));

			echo '<tr>';
			echo '<td class="blank">&nbsp;</td>';
			printf('<td class="icon"><a href="%s"><img src="%s/plus.png" alt="Plus" /></a></td>',$href['expand'],IMGDIR);

			printf('<td colspan=2><a href="%s">%s</a></td>',$href['return'],$dn);
			echo '</tr>';
			echo "\n\n";
		}

# Draw the root of the selection tree (ie, list all the servers)
} else {
	foreach ($_SESSION[APPCONFIG]->getServerList() as $index => $server) {
		if ($server->isLoggedIn(null)) {
			printf('<tr><td class="heading" colspan=3>%s:</td><td class="heading">%s</td></tr>',_('Server'),$server->getName());
			foreach ($server->getBaseDN() as $dn) {
				if (! $dn) {
					printf('<tr><td class="blank">&nbsp;</td><td colspan=3>(%s)</td></tr>',_('Could not determine base DN'));

				} else {
					$href['return'] = sprintf("javascript:returnDN('%s%s')",($request['rdn'] ? sprintf('%s,',$request['rdn']) : ''),rawurlencode($dn));
					$href['expand'] = htmlspecialchars(sprintf('entry_chooser.php?server_id=%s&form_element=%s&rdn=%s&container=%s',
							$server->getIndex(),$request['element'],$request['rdn'],rawurlencode($dn)));

					echo '<tr>';
					echo '<td class="blank">&nbsp;</td>';
					printf('<td colspan=2 class="icon"><a href="%s"><img src="%s/plus.png" alt="Plus" /></a></td>',$href['expand'],IMGDIR);
					printf('<td colspan=2><a href="%s">%s</a></td>',$href['return'],$dn);
				}
			}

			echo '<tr><td class="blank" colspan=4>&nbsp;</td></tr>';
		}
	}
}

echo '</table>';
echo '</div>';

# Capture the output and put into the body of the page.
$www['body'] = new block();
$www['body']->SetBody(ob_get_contents());
$www['page']->block_add('body',$www['body']);
ob_end_clean();

# Render the popup.
$www['page']->display(array('CONTROL'=>false,'FOOT'=>false,'HEAD'=>false,'TREE'=>false));
?>
