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

	/**
	* Constructor
	*
	* @param \phpbb\user $user, \phpbb\template\template $template
	*/
	public function __construct(\phpbb\user $user, \phpbb\template\template $template, \phpbb\db\driver\driver_interface $db)
	{
		$this->user = $user;
		$this->template = $template;
		$this->db = $db;

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
		// 1. Themen ID bestimmen
		// 2. Anzahl der Antworten bestimmen
		// 3. Autoren des Themas finden
		// 4. Beiträge je Autor zählen
		// 5. je Zeile ein Autor + Beitragszahl 
		// 6. als overlay wie bei Mark Forum Read ausgeben o.ä.
		
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
		}

		$this->db->sql_freeresult($result);

		$topic_row['REPLIES'] =  '<a href="#">' . $topic_row['REPLIES'] . '</a>';

		$event['topic_row'] = $topic_row; 
	}
}
