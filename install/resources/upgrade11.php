<?php
/**
 * MyBB 1.2
 * Copyright © 2007 MyBB Group, All Rights Reserved
 *
 * Website: http://www.mybboard.com
 * License: http://www.mybboard.com/eula.html
 *
 * $Id$
 */

/**
 * Upgrade Script: 1.2.10 or 1.2.11
 */


$upgrade_detail = array(
	"revert_all_templates" => 0,
	"revert_all_themes" => 0,
	"revert_all_settings" => 0
);

@set_time_limit(0);

function upgrade11_dbchanges()
{
	global $db, $output, $mybb;

	$output->print_header("Performing Queries");

	echo "<p>Performing necessary upgrade queries..</p>";

	$query = $db->simple_select("templates", "*", "title IN ('showthread_inlinemoderation','showthread_ratethread','editpost','newreply','usercp_drafts','newthread','usercp_options','forumdisplay_inlinemoderation','report','private_empty','usercp_profile','usercp_attachments','usercp_usergroups_joingroup','usercp_avatar','usercp_avatar_gallery','usercp_usergroups_memberof','managegroup','managegroup_adduser','managegroup_joinrequests','private_send','polls_editpoll','private_archive','calendar_addevent','moderation_inline_deleteposts','private_tracking','moderation_threadnotes','showthread_quickreply','member_emailuser','moderation_reports','member_login','index_loginform','moderation_deletethread','moderation_mergeposts','polls_newpoll','member_register_agreement','usercp_password','usercp_email','reputation_add','moderation_deletepoll','usercp_changeavatar','usercp_notepad','member_resetpassword','member_lostpw','usercp_changename','moderation_deleteposts','moderation_split','sendthread','usercp_editsig','private_read','error_nopermission','private_folders','moderation_move','moderation_merge','member_activate','usercp_editlists','calendar_editevent','member_resendactivation','moderation_inline_deletethreads','moderation_inline_movethreads','moderation_inline_mergeposts','moderation_inline_splitposts','member_register')");
	while($template = $db->fetch_array($query))
	{
		if($template['title'] == "private_read")
		{
			$template['template'] = str_replace("private.php?action=delete&amp;pmid={\$pm['pmid']}", "private.php?action=delete&amp;pmid={\$pm['pmid']}&amp;my_post_key={\$mybb->post_code}", $template['template']);
			
			continue;
		}
		
		// Remove any duplicates
		$template['template'] = str_replace("<input type=\"hidden\" name=\"my_post_key\" value=\"{\$mybb->post_code}\" />", "", $template['template']);
				
		$template['template'] = preg_replace("#<form(.*?)method\=\\\"post\\\"(.*?)>#i", "<form$1method=\"post\"$2>\n<input type=\"hidden\" name=\"my_post_key\" value=\"{\$mybb->post_code}\" />", $template['template']);
		
		$db->update_query(TABLE_PREFIX."templates", array('template' => $db->escape_string($template['template'])), "tid='{$template['tid']}'", 1);
	}

	$contents .= "Click next to continue with the upgrade process.</p>";
	$output->print_contents($contents);
	$output->print_footer("rebuildsettings");
}

?>