<?php

namespace App\FrontendModule\Presenters;

class FrontendPresenter extends BasePresenter {

	/** @var int menu level */
	private $level;

	/** @var int menu $order */
	private $order;

	public function startup(): void
    {
		parent::startup();
		$this->level = $this->getMenuLevelPresenter($this->presenter->getName());
		$this->order = $this->getMenuOrderPresenter($this->presenter->getName());

		$menuItem = $this->menuRepository->getMenuEntityByOrder($this->order, $this->langRepository->getCurrentLang($this->session));
		$userBlocks = $this->blockRepository->findAddedBlockFronted($menuItem->getLink(), $this->langRepository->getCurrentLang($this->session));

		$this->template->currentLink = $menuItem;
		$this->template->userBlocks = $userBlocks;
	}
}
