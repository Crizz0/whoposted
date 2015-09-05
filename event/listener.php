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

	/**
	* Constructor
	*
	* @param \phpbb\user $user, \phpbb\template\template $template
	*/
	public function __construct(\phpbb\user $user, \phpbb\template\template $template)
	{
		$this->user = $user;
		$this->template = $template;

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

		$topic_row['REPLIES'] =  '<a href="#">' . $topic_row['REPLIES'] . '</a>';

		$event['topic_row'] = $topic_row; 
	}
}
