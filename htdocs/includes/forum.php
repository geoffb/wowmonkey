<?php

define("ANON_USER", "Anonymous Coward", true);

function getQuickReplyHtml($thread_id)
{
	$skin = new skin("forum_quick_post.skn");
	$skin->token("SESSION_USER_ID", 0);
	$skin->token("SESSION_USER_NAME", ANON_USER . " (<a href=\"\">Login</a>)");
	$skin->token("THREAD_ID", $thread_id);
	return $skin->html;
}

function getThreadHtml($thread_id)
{
	$sql = sprintf("call sp_get_thread(%s)", $thread_id);
	$db = DB_GetConnection();
	if($db->multi_query($sql))
	{
		$thread = $db->store_result();
		if($db->next_result())
		{
			$posts = $db->store_result();
		}
	}
	$db->close();

	$t = $thread->fetch_assoc();

	$skin = new skin("thread.skn");
	$skin->token("THREAD_TITLE", $t["thread_title"]);

	//posts
	$num_rows = $posts->num_rows;
	for($r = 0; $r < $num_rows; $r++)
	{
		$p = $posts->fetch_assoc();
		$skin->addRow("posts", $p["post_user_id"], $p["post_message"]);
	}
	$skin->flushRows("posts");

	return $skin->html;
}

function saveThread($id, $title)
{
	$sql = sprintf("call sp_save_thread(%s, %s)", $id, prep_str($title));
	$db = DB_GetConnection();
	if($db->multi_query($sql))
	{
		$result = $db->store_result();
		$row = $result->fetch_assoc();
		return $row["thread_id"];
	}
	else { return 0; }
}

?>