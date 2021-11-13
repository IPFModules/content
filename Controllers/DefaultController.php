<?php

namespace ImpressCMS\Modules\IPFModules\Content\Controllers;

use icms;
use ImpressCMS\Core\Metagen;
use ImpressCMS\Core\Models\Module;
use ImpressCMS\Core\Models\ModuleHandler;
use ImpressCMS\Core\Response\ViewResponse;
use mod_content_Content;
use mod_content_ContentHandler;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Sunrise\Http\Router\Annotation\Route;
use Sunrise\Http\Router\Exception\PageNotFoundException;
use function icms_getModuleConfig;
use function icms_getModuleHandler;
use function icms_getModuleInfo;
use function icms_loadLanguageFile;

/**
 * Default controller (replaces content.php from core)
 *
 * @package ImpressCMS\Modules\IPFModules\Content\Controllers
 */
class DefaultController
{

	/**
	 * Use alternative page data to get link
	 *
	 * @Route(
	 *     name="content_page",
	 *     path="/page/{page}",
	 *     methods={"GET", "POST"}
	 * )
	 * @Route(
	 *     name="main_page",
	 *     path="/{page}",
	 *     methods={"GET", "POST"},
	 *     priority=1000
	 * )
	 *
	 * @param ServerRequestInterface $request Request
	 *
	 * @return ResponseInterface
	 */
	public function getAlternativePage(ServerRequestInterface $request): ResponseInterface
	{
		$params = $request->getQueryParams();
		$params['page'] = $request->getAttribute('page', 0);

		return $this->getLegacyPage(
			(clone $request)->withQueryParams($params)
		);
	}

	/**
	 * Gets index page
	 *
	 * @Route(
	 *     name="legacy_content",
	 *     path="/content.php",
	 *     methods={"GET", "POST"}
	 * )
	 *
	 * @param ServerRequestInterface $request Request
	 *
	 * @return ResponseInterface
	 */
	public function getLegacyPage(ServerRequestInterface $request): ResponseInterface {
		$this->defineConstants($request);

		include_once __DIR__ . '/../include/common.php';
		$icmsModule = &icms::$module;
		icms_loadLanguageFile('content', 'common');
		icms_loadLanguageFile('content', 'main');
		/** @noinspection PhpUndefinedVariableInspection */
		$icmsModuleConfig = $contentConfig;

		/** @noinspection AdditionOperationOnArraysInspection */
		$params = $request->getQueryParams() + $request->getParsedBody();

		/**
		 * @var mod_content_ContentHandler $contentHandler
		 */
		$contentHandler = icms_getModuleHandler('content', 'content');

		/**
		 * @var mod_content_Content|null $content
		 */
		$content = $contentHandler->getByPage($params['page'] ?? 0);
		if (!$content) {
			throw new PageNotFoundException();
		}

		if (!$content->accessGranted()) {
			/**
			 * @var ResponseFactoryInterface $responseFactory
			 */
			$responseFactory = icms::getInstance()->get('response_factory');
			return $responseFactory->createResponse(403);
		}

		$response = new ViewResponse(
			[
				'template_main' => 'content_content.html',
			]
		);

		$contentHandler->updateCounter($content->content_id);
		$response->assign('content_content', $content->toArray());
		$response->assign('showInfo', $contentConfig['show_contentinfo']);
		$response->assign('showSubs', $contentConfig['show_relateds'] && $content->content_showsubs);
		$response->assign(
			'content_category_path',
			$contentConfig['show_breadcrumb'] ?
					$contentHandler->getBreadcrumbForPid($content->getVar('content_id', 'e'), 1) :
					false
		);

		if ($contentConfig['com_rule'] && $content->content_cancomment) {
			$response->assign('content_content_comment', true);
			include_once ICMS_INCLUDE_PATH . '/comment_view.php';
		}

		$metaGenerator = new Metagen(
			$content->content_title,
			$content->getVar('meta_keywords', 'n'),
			$content->getVar('meta_description', 'n')
		);
		$metaGenerator->createMetaTags();

		$response->addStylesheet(CONTENT_URL . '/include/content.css');
		$response->assign(
			'content_module_home',
			$this->getModuleName(true, true)
		);

		return $response;
	}

	/**
	 * Define all system constants
	 *
	 * @param ServerRequestInterface $request
	 */
	private function defineConstants(ServerRequestInterface $request): void
	{
		define(
			"CONTENT_DIRNAME",
			$request->getAttribute('module', 'content')
		);
		define("CONTENT_URL", ICMS_MODULES_URL . '/' . CONTENT_DIRNAME . '/');
		define("CONTENT_ROOT_PATH", ICMS_MODULES_PATH . '/' . CONTENT_DIRNAME . '/');
		define("CONTENT_IMAGES_URL", CONTENT_URL . 'images/');
		define("CONTENT_ADMIN_URL", CONTENT_URL . 'admin/');
	}

	/**
	 * Get module name
	 *
	 * @param bool $withLink
	 * @param bool $forBreadCrumb
	 * @param bool $moduleName
	 *
	 * @return string
	 */
	protected function getModuleName($withLink = true, $forBreadCrumb = false, $moduleName = false) {
		if (!$moduleName) {
			$moduleName = icms::$module->dirname;
		}

		if ($moduleName !== icms::$module->dirname) {
			$moduleInfo = icms_getModuleInfo($moduleName);

			if (!isset($moduleInfo)) {
				return '';
			}
		}

		if (!$withLink) {
			return $moduleName;
		}

		return sprintf("<a href=\"$1%s/$2%s/\">$2%s</a>", ICMS_MODULES_URL, $moduleName);
	}

}