<?php

namespace App\Forms;

use App\Model\DogRepository;
use App\Model\EnumerationRepository;
use App\Model\LangRepository;
use Nette;
use Nette\Application\UI\Form;

class MatingListForm extends Nette\Object {

	/** @var FormFactory */
	private $factory;

	/** @var DogRepository */
	private $dogRepository;

	/** @var EnumerationRepository */
	private $enumerationRepository;

	/**
	 * @param FormFactory $factory
	 * @param DogRepository $dogRepository
	 * @param EnumerationRepository $enumerationRepository
	 */
	public function __construct(FormFactory $factory, DogRepository $dogRepository, EnumerationRepository $enumerationRepository) {
		$this->factory = $factory;
		$this->dogRepository = $dogRepository;
		$this->enumerationRepository = $enumerationRepository;
	}

	/**
	 * @param array $languages
	 * @param int $level
	 * @return Form
	 */
	public function create($currentLang) {
		$counter = 1;
		$form = $this->factory->create();
		$form->getElementPrototype()->addAttributes(["onsubmit" => "return requiredFields();"]);

		$clubs = $this->enumerationRepository->findEnumItemsForSelect($currentLang, 18);
		$form->addSelect("cID", MATING_FORM_CLUB, $clubs)
			->setAttribute("class", "form-control")
			->setAttribute("tabindex", $counter + 1);

		$males = $this->dogRepository->findMaleDogsForSelect(false);
		$form->addSelect("pID", MATING_FORM_FID, $males)
			->setAttribute("class", "form-control")
			->setAttribute("tabindex", $counter + 2);

		$females = $this->dogRepository->findFemaleDogsForSelect(false);
		$form->addSelect("fID", MATING_FORM_MID, $females)
			->setAttribute("class", "form-control")
			->setAttribute("tabindex", $counter + 3);

		$form->addSubmit("save", MATING_FORM_SAVE)
			->setAttribute("class", "btn btn-primary margin10")
			->setAttribute("tabindex", $counter + 4);

		return $form;
	}
}