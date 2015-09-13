<?php
/**
 *
 * @package phpBB.de Who Posted In This Topic
 * @copyright (c) 2015 phpBB.de
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace crizzo\whoposted\event;

/**
* @ignore
*/
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
* Event listener
*/
class listener implements EventSubscriberInterface
{
	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\user */
	protected $user;
	
	/** @var \phpbb\db\driver\driver_interface */
	protected $db;
	/**  */
	protected $root_path;
	/** @var string phpEx */
	protected $php_ext;
	/**
	* Constructor
	*
	* @param \phpbb\user $user, \phpbb\template\template $template
	*/
	public function __construct(\phpbb\user $user, \phpbb\template\template $template, \phpbb\db\driver\driver_interface $db, $root_path, $php_ext)
	{
		$this->user = $user;
		$this->template = $template;
		$this->db = $db;
		$this->root_path = $root_path;
		$this->php_ext = $php_ext;

		$this->user->add_lang_ext('crizzo/whoposted', 'whoposted');
	}

	/**
	* Assign functions defined in this class to event listeners in the core
	*
	* @return array
	* @static
	* @access public
	*/
	static public function getSubscribedEvents()
	{
		return array(
			'core.viewforum_modify_topicrow'	=> 'modify_replies',
		);
	}
	/**
	 * Changes the regex replacement for second pass
	 *
	 * @param object $event
	 * @return null
	 * @access public
	 */
	public function modify_replies($event)
	{
		if (!function_exists('get_username_string'))
		{
			include($this->root_path . 'includes/functions_content.' . $this->php_ext);
		}
		// 1. output each line with user + post-count
		// 2. output in "inline-popup" like in "mark posts read"

		$topic_row = $event['topic_row'];

		$topic_id = $topic_row['TOPIC_ID'];

		$sql = 'SELECT COUNT(p.post_id) AS posts, p.poster_id, u.username, u.user_colour
		FROM phpbb_posts p, phpbb_users u
		WHERE p.topic_id = ' . (int) $topic_id . '
		AND p.poster_id = u.user_id
		GROUP BY p.poster_id
		ORDER BY posts DESC';

		$result = $this->db->sql_query_limit($sql, 5);

		while($row=$this->db->sql_fetchrow($result)) {
			//var_dump($row);
			$post_count = $row['posts'];

			$display_username = get_username_string('full', $row['poster_id'], $row['username'], $row['user_colour']);
			echo $display_username . ' with ' . $post_count . ' posts<br />';
		}

		$this->db->sql_freeresult($result);

		$sql_forum_id = 'SELECT forum_id
		FROM phpbb_posts
		WHERE topic_id = ' . (int) $topic_id;

		$result_forum_id = $this->db->sql_query_limit($sql_forum_id, 5);

		while($row2=$this->db->sql_fetchrow($result_forum_id)) {
			var_dump($row2);
			$forum_id = $row2['forum_id'];
		}

		$whoposted_url = ($this->user->data['is_registered']) ? append_sid($this->root_path . "viewforum.$this->php_ext", "f=$forum_id&amp;whoposted=" . $topic_id) : '';

		$topic_row['REPLIES'] =  '<a href='. $whoposted_url . '>' . $topic_row['REPLIES'] . '</a>';

		$event['topic_row'] = $topic_row;
	}
}
