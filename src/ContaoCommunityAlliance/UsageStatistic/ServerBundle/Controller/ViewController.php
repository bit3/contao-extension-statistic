<?php

namespace ContaoCommunityAlliance\UsageStatistic\ServerBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route(service="usage_statistic_server.controller.view_controller")
 */
class ViewController
{

	/**
	 * @var TranslatorInterface
	 */
	protected $translator;

	/**
	 * @return TranslatorInterface
	 */
	public function getTranslator()
	{
		return $this->translator;
	}

	/**
	 * @param TranslatorInterface $translator
	 *
	 * @return ViewController
	 */
	public function setTranslator(TranslatorInterface $translator)
	{
		$this->translator = $translator;
		return $this;
	}

	/**
	 * @Route("/")
	 *
	 * @return RedirectResponse
	 */
	public function indexAction()
	{
		$content = $this->translator->trans('welcome', [], 'messages') .
			PHP_EOL . PHP_EOL .
			$this->translator->trans('help', [], 'messages');

		$response = new Response();
		$response->setStatusCode(404);
		$response->setCharset('UTF-8');
		$response->setContent($content);
		$response->headers->set('Content-Type', 'text/plain; charset=UTF-8');

		return $response;
	}

	/**
	 * @Route("/{path}", requirements={"path"=".*"})
	 *
	 * @return Response
	 */
	public function notFoundAction(Request $request, $path)
	{
		$url     = preg_replace('~\.\w+$~', '', $request->getUri()) . '.json';
		$content = $this->translator->trans('not-found', ['%url%' => $url], 'messages') .
			PHP_EOL . PHP_EOL .
			$this->translator->trans('help', [], 'messages');

		$response = new Response();
		$response->setStatusCode(404);
		$response->setCharset('UTF-8');
		$response->setContent($content);
		$response->headers->set('Content-Type', 'text/plain; charset=UTF-8');

		return $response;
	}
}
