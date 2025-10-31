<?php
/**
 * ImpressCMS Conent Persistable Class
 *
 * @since 		ImpressCMS 1.2
 * @copyright 	The ImpressCMS Project <http://www.impresscms.org>
 * @license		GNU General Public License (GPL) <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @author		Rodrigo P Lima (aka TheRplima) <therplima@impresscms.org>
 * @version		$Id$
 */

defined("ICMS_ROOT_PATH") or die("ImpressCMS root path not defined");

/**
 * ImpressCMS Core Content Object Class
 *
 * @since ImpressCMS 1.2
 * @author Rodrigo P Lima (aka TheRplima) <therplima@impresscms.org>
 */
class mod_content_Content extends icms_ipf_seo_Object {
	private $_poster_info = false;
	public $updating_counter = false;
	public $tags = false;
	public $categories = false;

	public function __construct(&$handler) {
		global $contentConfig;

		icms_ipf_Object::__construct($handler);

		$this->quickInitVar('content_id', XOBJ_DTYPE_INT, true);
		$this->quickInitVar('content_pid', XOBJ_DTYPE_INT, false);
		$this->quickInitVar('content_uid', XOBJ_DTYPE_INT, true, false, false, 1);
		$this->quickInitVar('content_title', XOBJ_DTYPE_TXTBOX, true);
		$this->quickInitVar('content_body', XOBJ_DTYPE_TXTAREA);
		$this->quickInitVar('content_css', XOBJ_DTYPE_TXTAREA);
		$this->quickInitVar('content_tags', XOBJ_DTYPE_TXTAREA);
		$this->quickInitVar('content_visibility', XOBJ_DTYPE_INT, true, false, false, CONTENT_CONTENT_VISIBLE_MENUSUBS);
		$this->quickInitVar('content_published_date', XOBJ_DTYPE_LTIME, false);
		$this->quickInitVar('content_updated_date', XOBJ_DTYPE_LTIME);
		$this->quickInitVar('content_weight', XOBJ_DTYPE_INT, true, false, false, 0);
		$this->quickInitVar('content_status', XOBJ_DTYPE_INT, true, false, false, CONTENT_CONTENT_STATUS_PUBLISHED);
		$this->quickInitVar('content_makesymlink', XOBJ_DTYPE_INT, true, false, false, 0);
		$this->quickInitVar('content_showsubs', XOBJ_DTYPE_INT, false, false, false, $contentConfig['show_relateds']);
		$this->quickInitVar('content_cancomment', XOBJ_DTYPE_INT, false, false, false, true);
		$this->quickInitVar('content_comments', XOBJ_DTYPE_INT);
		$this->quickInitVar('content_notification_sent', XOBJ_DTYPE_INT);
		$this->quickInitVar('content_background_image', XOBJ_DTYPE_IMAGE);
		$this->quickInitVar('content_header_image', XOBJ_DTYPE_IMAGE);
		$this->quickInitVar('content_social_image', XOBJ_DTYPE_IMAGE);


		$this->hideFieldFromForm('content_comments');
		$this->hideFieldFromForm('content_notification_sent');
		$this->hideFieldFromSingleView('content_comments');
		$this->hideFieldFromSingleView('content_notification_sent');

		$this->initCommonVar('counter', false);
		$this->initCommonVar('dohtml', false, true);
		$this->initCommonVar('dobr', false, true);
		$this->initCommonVar('doimage', false, true);
		$this->initCommonVar('dosmiley', false, true);
		$this->initCommonVar('doxcode', false, true);

		$this->initNonPersistableVar('content_subs', XOBJ_DTYPE_INT);

		$this->setControl('content_body', 'dhtmltextarea');
		$this->setControl('content_uid', 'user');
		$this->setControl('content_status', array('itemHandler' => 'content', 'method' => 'getContent_statusArray', 'module' => 'content'));
		$this->setControl('content_visibility', array('itemHandler' => 'content', 'method' => 'getContent_visibleArray', 'module' => 'content'));
		$this->setControl('content_pid', array('itemHandler' => 'content', 'method' => 'getContentList', 'module' => 'content'));
		$this->setControl('categories', array('name' => 'categories', 'module' => 'imtagging'));
        $this->setControl('content_background_image', 'image');
        $this->setControl('content_header_image', 'image');
        $this->setControl('content_social_image', 'image');
		$this->setControl('content_makesymlink', 'yesno');
		$this->setControl('content_showsubs', 'yesno');
		$this->setControl('content_cancomment', 'yesno');

		parent::initiateSEO();
	}

	public function getVar($key, $format = 's') {
		if ($format == 's' && in_array($key, array('content_pid', 'content_uid', 'content_status', 'content_visibility', 'content_subs', 'content_tags'))) {
			return call_user_func(array($this, $key));
		}
		return parent::getVar($key, $format);
	}

	/**
	 * Retrieving the title of the parent page, linked to that
	 *
	 * @return string title of the parent content
	 */
	public function content_pid(): string
    {
		static $content_pidArray;
		if (!is_array($content_pidArray)) {
			$content_pidArray = $this->handler->getContentList();
		}
		$ret = $this->getVar('content_pid', 'e');
		if ($ret > 0) {
			$ret = '<a href="' . $this->handler->_moduleUrl . $this->handler->_itemname . '.php?content_id=' . $ret . '">' . str_replace('-', '', $content_pidArray[$ret]) . '</a>';
		} else {
			$ret = $content_pidArray[$ret];
		}
		return $ret;
	}

	/**
	 * Retrieving the name of the author of the content, linked to his profile
	 *
	 * @return string name of the author of the content
	 */
	public function content_uid(): string
    {
		return icms_member_user_Handler::getUserLink($this->getVar('content_uid', 'e'));
	}

	/**
	 * Retrieving the status of the content
	 *
	 * @param str status of the content
	 * @return mixed $content_statusArray[$ret] status of the content
	 */
	public function content_status() {
		$ret = $this->getVar('content_status', 'e');
		$content_statusArray = $this->handler->getContent_statusArray();
		return $content_statusArray[$ret];
	}

	/**
	 * Retrieving the visibility of the content
	 *
	 * @return mixed $content_visibleArray[$ret] visibility of the content
	 */
	public function content_visibility() {
		$ret = $this->getVar('content_visibility', 'e');
		$content_visibleArray = $this->handler->getContent_visibleArray();
		return $content_visibleArray[$ret];
	}

	/**
	 * Get formatted content tags with links
	 *
	 * @return string Comma-separated linked tags, or empty string if no tags
	 */
	public function content_tags(): string
	{
		$tagsStr = $this->getVar('content_tags', 'e');
		if ($tagsStr == '') {
			return '';
		}

		$tags = explode(',', $tagsStr);
		foreach ($tags as $k => $tag) {
			$tag = trim($tag);
			$tag = ' <a href="' . $this->handler->_moduleUrl . 'index.php?tag=' . $tag . '">' . $tag . '</a>';
			$tags[$k] = $tag;
		}
		return implode(',', $tags);
	}

	/**
	 * Retrieving the count of sub-pages of this page
	 *
	 * @return int number of sub-pages
	 */
	public function content_subs() {
		$ret = $this->handler->getContentsSubsCount($this->getVar('content_id', 'e'));

		if ($ret > 0) {
			$ret = '<a href="' . $this->handler->_moduleUrl . 'admin/' . $this->handler->_itemname . '.php?content_pid=' . $this->getVar('content_id', 'e') . '">' . $ret . ' <img src="' . $this->handler->_moduleUrl . 'images/viewsubs.gif" align="absmiddle" /></a>';
		}
		return $ret;
	}

	/**
	 * Get the number of reads/views for this content
	 *
	 * @return int Number of reads
	 */
	public function getReads(): int
	{
		return $this->getVar('counter');
	}

	/**
	 * Set/increment the number of reads for this content
	 *
	 * @param int|null $qtde Quantity to add (null to increment by 1)
	 * @return void
	 * @throws InvalidArgumentException if qtde is not a valid integer
	 */
	public function setReads($qtde = null): void
	{
		if ($qtde !== null && (!is_int($qtde) || $qtde < 0)) {
			throw new InvalidArgumentException('Quantity must be a non-negative integer or null');
		}

		$t = $this->getVar('counter');
		if (isset($qtde)) {
			$t += $qtde;
		} else {
			$t++;
		}
		$this->setVar('counter', $t);
	}

	/**
	 * Returns the need to br
	 *
	 * @return bool true | false
	 */
	public function need_do_br(): bool
    {
		global $icmsConfig;

		$content_module = icms_getModuleInfo('content');
		$groups = icms::$user->getGroups();

		if (file_exists(ICMS_EDITOR_PATH . "/" . $icmsConfig['editor_default'] . "/xoops_version.php") && icms::handler('icms_member_groupperm')->checkRight('use_wysiwygeditor', $content_module->getVar("mid"), $groups)) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * Check is user has access to view this content page
	 *
	 * User will be able to view the page if
	 *	- the status of the page is Published OR
	 *	- he is an admin OR
	 * 	  - he is the poster of this page
	 *
     * @param string $perm_name Permission name to check (optional, for future use)
     * @return bool true if user can view this page, false if not
	 */
	public function accessGranted($perm_name = ''): bool
    {
		$gperm_handler = icms::handler('icms_member_groupperm');
		$groups = is_object(icms::$user) ? icms::$user->getGroups() : array(ICMS_GROUP_ANONYMOUS);

		$module = icms::handler('icms_module')->getByDirname(basename(dirname(__FILE__, 2)));

		$agroups = $gperm_handler->getGroupIds('module_admin', $module->getVar("mid"));
		$allowed_groups = array_intersect($groups, $agroups);

		$viewperm = $gperm_handler->checkRight('content_read', $this->getVar('content_id', 'e'), $groups, $module->getVar("mid"));

		if (is_object(icms::$user) && icms::$user->getVar("uid") == $this->getVar('content_uid', 'e')) {
			return true;
		}

		if ($viewperm && $this->getVar('content_status', 'e') == CONTENT_CONTENT_STATUS_PUBLISHED) {
			return true;
		}

		if ($viewperm && count($allowed_groups) > 0) {
			return true;
		}
		return false;
	}

	/**
	 * Get the poster
	 *
	 * @param bool $link with link or not
	 * @return string poster name linked on his module poster page, or simply poster name
	 */
	public function getPoster(bool $link = false): string
    {
		if (!$this->_poster_info) {
			$poster_uid = $this->getVar('content_uid', 'e');
			$userObj = icms::handler('icms_member')->getuser($poster_uid);

			/**
			 * We need to make sure the poster is a valid user object. It is possible the user no longer
			 * exists if, for example, he was previously deleted. In that case, we will return Anonymous
			 */
			if (is_object($userObj)) {
				$this->_poster_info['uid'] = $poster_uid;
				$this->_poster_info['uname'] = $userObj->getVar('uname');
				$this->_poster_info['link'] = '<a href="' . $this->handler->_moduleUrl . 'index.php?uid=' . $this->_poster_info['uid'] . '">' . $this->_poster_info['uname'] . '</a>';
			} else {
				global $icmsConfig;
				$this->_poster_info['uid'] = 0;
				$this->_poster_info['uname'] = $icmsConfig['anonymous'];
			}
		}
		if ($link && $this->_poster_info['uid']) {
			return $this->_poster_info['link'];
		} else {
			return $this->_poster_info['uname'];
		}
	}

	/**
	 * Retrieve content info (author and date)
	 *
	 * @return str content info
	 */
	public function getContentInfo(): string
    {
		$ret = sprintf(_CO_CONTENT_CONTENT_INFO, $this->getPoster(true), $this->getVar('content_published_date'), $this->getVar('counter'));
		return $ret;
	}

	/**
	 * Check to see whether the current user can edit or delete this page
	 *
	 * @return bool true if he can, false if not
	 */
	public function userCanEditAndDelete(): bool
    {
		global $content_isAdmin;
		if (!is_object(icms::$user)) return false;
		if ($content_isAdmin) return true;
		return $this->getVar('content_uid', 'e') == icms::$user->getVar("uid");
	}

	/**
	 * Get preview item link
	 *
	 * @return string HTML link to preview the content
	 * @throws InvalidArgumentException if content object is invalid
	 */
	public function getPreviewItemLink(): string
    {
		try {
			$seo = $this->handler->makelink($this);
		} catch (InvalidArgumentException $e) {
			throw new InvalidArgumentException('Cannot create preview link: ' . $e->getMessage());
		}

		$ret = '<a href="' . $this->handler->_moduleUrl . $this->handler->_itemname . '.php?content_id=' . $this->getVar('content_id', 'e') . '&amp;page=' . $seo . '" title="' . _AM_CONTENT_PREVIEW . '" target="_blank">' . $this->getVar('content_title') . '</a>';

		return $ret;
	}

	public function getCloneItemLink(): string
    {
		$ret = '<a href="' . $this->handler->_moduleUrl . 'admin/' . $this->handler->_itemname . '.php?op=clone&amp;content_id=' . $this->getVar('content_id', 'e') . '" title="' . _AM_CONTENT_CONTENT_CLONE . '"><img src="' . ICMS_IMAGES_SET_URL . '/actions/editcopy.png" /></a>';

		return $ret;
	}

	public function getViewItemLink($onlyUrl = false, $withimage = true, $userSide = false): string
    {
		$ret = '<a href="' . $this->handler->_moduleUrl . 'admin/' . $this->handler->_itemname . '.php?op=view&amp;content_id=' . $this->getVar('content_id', 'e') . '" title="' . _AM_CONTENT_VIEW . '"><img src="' . ICMS_IMAGES_SET_URL . '/actions/viewmag.png" /></a>';

		return $ret;
	}

	public function getContentSubs($toarray) {
		return $this->handler->getContentSubs($this->getVar('content_id', 'e'), $toarray);
	}

	public function getContent_visibleControl(): string
    {
		$control = new icms_form_elements_Select('', 'content_visibility[]', $this->getVar('content_visibility', 'e'));
		$content_visibleArray = $this->handler->getContent_visibleArray();
		$control->addOptionArray($content_visibleArray);
		return $control->render();
	}

    public function getContent_statusControl(): string
    {
		$control = new icms_form_elements_Select('', 'content_status[]', $this->getVar('content_status', 'e'));
		$content_statusArray = $this->handler->getContent_statusArray();
		$control->addOptionArray($content_statusArray);
		return $control->render();
	}

	/**
	 * Retrieve content comment info (number of comments)
	 *
	 * @return string content comment info
	 */
	public function getCommentsInfo(): string
    {
		$content_comments = $this->getVar('content_comments');
		if ($content_comments) {
			return '<a href="' . $this->getItemLink(true) . '#comments_container">' . sprintf(_CO_CONTENT_CONTENT_COMMENTS_INFO, $content_comments) . '</a>';
		} else {
			return _CO_CONTENT_CONTENT_NO_COMMENT;
		}
	}

	/**
	 * Retrieve content lead, which is everything before the [more] tag
	 *
	 * @return string content lead
	 */
	public function getContentLead(): string
    {
		$ret = $this->getVar('content_body');
		$ret = icms_core_DataFilter::icms_substr(icms_cleanTags($ret, array()), 0, 300);
		return $ret;
	}

	/**
	 * Sending the notification related to a content being published
	 *
	 * @return VOID
	 */
	public function sendNotifContentPublished(): void
    {
		$module = icms::handler('icms_module')->getByDirname(basename(dirname(__FILE__, 2)));
		$tags ['CONTENT_TITLE'] = $this->getVar('content_title');
		$tags ['CONTENT_URL'] = $this->getItemLink(true);
		icms::handler('icms_data_notification')->triggerEvent('global', 0, 'content_published', $tags, array(), $module->getVar('mid'));
	}

	/**
	 * Get item link
	 *
	 * @param bool $onlyUrl Return only URL without HTML link tag
	 * @return string HTML link to the content or URL only
	 * @throws InvalidArgumentException if content object is invalid
	 */
	public function getItemLink($onlyUrl = false): string
    {
		try {
			$seo = $this->handler->makelink($this);
		} catch (InvalidArgumentException $e) {
			throw new InvalidArgumentException('Cannot create item link: ' . $e->getMessage());
		}

		$url = $this->handler->_moduleUrl . $this->handler->_itemname . '.php?content_id=' . $this->getVar('content_id') . '&amp;page=' . $seo;
		if ($onlyUrl) return $url;
		return '<a href="' . $url . '" title="">' . $this->getVar('content_title') . '</a>';
	}

	/**
	 * Overridding IcmsPersistable::toArray() method to add a few info
	 *
	 * @return array of article info
	 */
	public function toArray(): array
    {
        global $icmsConfig;
		$ret = parent::toArray();

		$ret['content_info'] = $this->getContentInfo();
		$ret['content_lead'] = $this->getContentLead();
		$ret['content_comment_info'] = $this->getCommentsInfo();
		$ret['content_css'] = $this->getVar('content_css', 'e');
		$ret['content_subs'] = $this->getContentSubs($this->getVar('content_id', 'e'), true);
		$ret['content_hassubs'] = (count($ret['content_subs']) > 0) ? true : false;
		$ret['editItemLink'] = $this->getEditItemLink($icmsConfig['show_content_edit_onlyurl'], $icmsConfig['show_content_edit_image'], $icmsConfig['show_content_edit_userside']);
		$ret['deleteItemLink'] = $this->getDeleteItemLink($icmsConfig['show_content_edit_onlyurl'], $icmsConfig['show_content_edit_image'], $icmsConfig['show_content_edit_userside']);
		$ret['userCanEditAndDelete'] = $this->userCanEditAndDelete();
		$ret['content_posterid'] = $this->getVar('content_uid', 'e');
		$ret['itemLink'] = $this->getItemLink();
		//$ret['accessgranted'] = $this->accessGranted();

		return $ret;
	}
}
