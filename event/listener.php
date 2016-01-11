<?php
/**
*
* @package phpBB Extension - Display Forum-Local Moderator
* @copyright (c) 2016 Max Weller (mweller@d120.de)
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace d120de\highlightlocalmod\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
* Event listener
*/
class listener implements EventSubscriberInterface
{
	/** @var \phpbb\request\request */
	protected $request;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\user */
	protected $user;

	/** @var \phpbb\auth\auth */
	protected $auth;

	/** @var \phpbb\db\driver\driver */
	protected $db;

	/** @var \phpbb\config\config */
	protected $config;

	/**
	* Constructor
	*
	* @param \phpbb\request\request				$request
	* @param \phpbb\template\template			$template
	* @param \phpbb\user						$user
	* @param \phpbb\auth\auth					$auth
	* @param \phpbb\db\driver\driver			$db
	* @param \phpbb\config\config				$config
	*/
	public function __construct(\phpbb\request\request $request, \phpbb\template\template $template, \phpbb\user $user, \phpbb\auth\auth $auth, \phpbb\db\driver\driver_interface $db, \phpbb\config\config $config)
	{
		$this->request = $request;
		$this->template = $template;
		$this->user = $user;
		$this->auth = $auth;
		$this->db = $db;
		$this->config = $config;
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
      'core.viewtopic_get_post_data' => 'store_forum_id',
      'core.modify_user_rank' => 'modify_user_rank',
      'core.modify_username_string' => 'modify_username_string'
		);
  }

  #user_data, user_posts

  public function modify_user_rank($event) {
    $data = $event['user_data'];
    if (array_search($event["user_data"]["user_id"], $this->localMods) !== false) {
      $data['user_rank'] = '2';
    }
    $event['user_data'] = $data;
  }

  #_profile_cache, custom_profile_url, guest_username, mode, user_id, username, username_colour, username_string  
  public function modify_username_string($event) {
    if (!$this->localMods) return;
    if ($event["mode"] == "full") {
      if (array_search($event["user_id"], $this->localMods) !== false) {
        $event["username_string"] = preg_replace('/class="username"/', 'class="username-colored" style="color:#309030;font-style:italic"', $event["username_string"]);
      }
    }
  }


  public $theCurrentForumId;
  public $localMods;
  public function store_forum_id($event) {
    #echo "<h2>Forum ID: $event[forum_id]</h2>";
    $this->theCurrentForumId = $event['forum_id'];
    $result = $this->auth->acl_get_list(false, 'm_', $this->theCurrentForumId);
    $this->localMods = $result[$this->theCurrentForumId]['m_'];
    #var_dump($this->localMods);
  }

/*
  public function test_event($event) {
    #var_dump($event);
    $users = $event['user_cache'];
    foreach($users as $k=>$user) {
      var_dump($user);
      $user["author_colour"] = "#FF0000";
      $user["author_full"] = "$user[author_full] (MOD)";
      var_dump($user);
      $users[$k] = $user;
    }
    var_dump($users);
    $event['user_cache'] = $users;
    var_dump($event['user_cache']);
  }
 */

}

