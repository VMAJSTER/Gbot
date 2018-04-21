<?php


	class adminList
	{

		function start($db,$ts,$config,$ft,$cache,$function)
		{
			$edit = $config['functions']['adminList']['channel_description'];
			foreach($config['functions']['adminList']['admin_groups'] as $admins)
			{
				foreach($cache['serverGroupList'] as $serverGroupList)
				{
					if($serverGroupList['sgid'] == $admins)
					{
						$serverGroupList = $serverGroupList;
						break;
					}
				}
				if(isset($serverGroupList))
				{
					$edit .= $serverGroupList['name'].'\n [list]';
					foreach($ts->getElement('data',$ts->serverGroupClientList($serverGroupList['sgid'],'-names')) as $groups)
					{
						if(!isset($groups[""]))
						{
							$status = NULL;
							foreach($cache['clientList'] as $clientList)
							{
								if($clientList['client_database_id'] == $groups['cldbid'])
								{
									$status = $clientList;
									break;
								}
							}

							if($status != NULL)
							{
								$clientInfo = $ts->getElement('data',$ts->clientInfo($status['clid']));
								if($config['functions'][$function]['client_afk'] < $clientInfo['client_idle_time']/1000)
								{
									$edit .= '[*][url=client://0/'.$status['client_unique_identifier'].']'.$status['client_nickname'].'[/url] jest [COLOR=#bc7d00]AFK[/COLOR] na kanale [url=channelID://'.$status['cid'].']'.$ts->getElement('data',$ts->channelInfo($status['cid']))['channel_name'].'[/url] od '.$ft->secToHR($clientInfo['client_idle_time']/1000).' \n';
								}
								else
								{
									$edit .= '[*][url=client://0/'.$status['client_unique_identifier'].']'.$status['client_nickname'].'[/url] jest [COLOR=#009700]Online[/COLOR] na kanale [url=channelID://'.$status['cid'].']'.$ts->getElement('data',$ts->channelInfo($status['cid']))['channel_name'].'[/url] od '.$ft->secToHR($clientInfo['connection_connected_time']/1000).' \n';
								}
							}
							else
							{
								$clientInfo = $status = $ts->getElement('data',$ts->clientDbInfo($groups['cldbid']));
								$edit .= '[*][url=client://0/'.$groups['client_unique_identifier'].']'.$groups['client_nickname'].'[/url] jest [COLOR=#ff0000]Offline[/COLOR] od '.$ft->secToHR(time()-$clientInfo['client_lastconnected']).' \n';
							}

						}
						else
						{
							$edit .= '[*][B]Brak osób w grupie![/B]';
						}
					}
					$edit .= '[/list]';
				}
				else
				{
					echo PREFIX.'ERROR: '.$function.' Nie znaleziono grupy: '.$admins; 
					$ft->add_log($config['connection']['bot_name'],'ERROR: '.$function,'Nie znaleziono grupy: '.$admins);
				}
			}
			$edit .= $cache['footer'];
			$ts->channelEdit($config['functions']['adminList']['channel_id'],array('channel_description' => $edit));
		}

		private function replace()
		{

		}

	}