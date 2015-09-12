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

	/**
	* Constructor
	*
	* @param \phpbb\user $user, \phpbb\template\template $template
	*/
	public function __construct(\phpbb\user $user, \phpbb\template\template $template, \phpbb\db\driver\driver_interface $db, $root_path)
	{
		$this->user = $user;
		$this->template = $template;
		$this->db = $db;
		$this->root_path = $root_path;

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

		$sql = 'SELECT COUNT(post_id) AS posts, poster_id
		FROM phpbb_posts
		WHERE topic_id = ' . (int) $topic_id . '
		GROUP BY poster_id
		ORDER BY posts DESC';

		$result = $this->db->sql_query_limit($sql, 5);

		while($row=$this->db->sql_fetchrow($result)) {
			var_dump($row);
			//$post_author_id = $row['poster_id'];
			$poster_id = $row['poster_id'];
			$post_count = $row['posts'];

			$display_username = get_username_string('full', $poster_id, $row['username'], $row['user_colour'], $row['post_username']);
			echo $display_username . 'with ' . $post_count . 'posts<br />';
		}

		$this->db->sql_freeresult($result);

		$topic_row['REPLIES'] =  '<a href="#">' . $topic_row['REPLIES'] . '</a>';

		$event['topic_row'] = $topic_row; 
	}
}
