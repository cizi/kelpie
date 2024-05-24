<?php

namespace App\FrontendModule\Presenters;

use Nette;
use Tracy\ILogger;
use Nette\Application\Response;
use Nette\Application\Request;
use Nette\Application\BadRequestException;
use Nette\Application\Responses\CallbackResponse;
use Nette\Application\Responses\ForwardResponse;

class ErrorPresenter implements Nette\Application\IPresenter {

    use Nette\SmartObject;

	/** @var ILogger */
	private $logger;

	public function __construct(ILogger $logger)
	{
		$this->logger = $logger;
	}

    /**
     * @param Request $request
     * @return Response
     */
	public function run(Request $request): Response
    {
		$e = $request->getParameter('exception');

		if ($e instanceof BadRequestException) {
			// $this->logger->log("HTTP code {$e->getCode()}: {$e->getMessage()} in {$e->getFile()}:{$e->getLine()}", 'access');
			return new ForwardResponse($request->setPresenterName('Error:Error4xx'));
		}

		$this->logger->log($e, ILogger::EXCEPTION);
		return new CallbackResponse(function () {
			$pathToErrorPresenter =  __DIR__ . "../../templates/Error/500.phtml";
			require $pathToErrorPresenter;
		});
	}

}
