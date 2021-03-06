<?php
/**
*
* @package phpBB Extension - Display Forum-Local Moderator
* @copyright (c) 2016 Max Weller (mweller@d120.de)
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/


namespace d120de\highlightlocalmod;

/**
* Extension class for custom enable/disable/purge actions
*/
class ext extends \phpbb\extension\base
{
	/**
	* Enable extension if phpBB version requirement is met
	*
	* @return bool
	* @access public
	*/
	public function is_enableable()
	{
		$config = $this->container->get('config');
		return version_compare($config['version'], '3.1.4-RC1', '>=');
	}
}
