<?php

namespace App\Forms;

use App\Model\EnumerationRepository;
use Nette;
use Nette\Application\UI\Form;

class MatingListDetailForm {

    use Nette\SmartObject;

    public const FORM_ID = "matingListDetailForm";

	/** @var FormFactory */
	private $factory;

	/** @var EnumerationRepository */
	private $enumerationRepository;

	/**
	 * @param FormFactory $factory
	 * @param EnumerationRepository $enumerationRepository
	 */
	public function __construct(FormFactory $factory, EnumerationRepository $enumerationRepository) {
		$this->factory = $factory;
		$this->enumerationRepository = $enumerationRepository;
	}

	/**
	 * @param array $languages
	 * @param int $level
	 * @return Form
	 */
	public function create($currentLang, $linkBack) {
		$counter = 1;
		$form = $this->factory->create();

        $form->getElementPrototype()->addAttributes(["id" => self::FORM_ID]);
        $form->getElementPrototype()->addAttributes(["onsubmit" => "return requiredFields('" . self::FORM_ID . "');"]);

        $plemeno = $this->enumerationRepository->findEnumItemsForSelect($currentLang, 7);
		$form->addHidden('cID');

		$form->addSelect("Plemeno", DOG_FORM_BREED, $plemeno)
			->setAttribute("tabindex", $counter);

		$form->addText("ChovStaniceVrh", LITTER_APPLICATION_BREEDING)
			->setAttribute("placeholder", LITTER_APPLICATION_BREEDING)
			->setAttribute("tabindex", $counter);

		$femaleContainer = $form->addContainer('fID');
		$femaleContainer->addText("Jmeno", DOG_FORM_NAME_FEMALE)
			->setAttribute("placeholder", DOG_FORM_NAME)
			->setAttribute("tabindex", $counter + 1);

		$femaleContainer->addText("CisloZapisu", LITTER_APPLICATION_RECORD_NUM_FORM)
			->setAttribute("placeholder", LITTER_APPLICATION_RECORD_NUM)
			->setAttribute("tabindex", $counter + 2);

		$femaleContainer->addText("DatNarozeni", MATING_LITTER_DOG_DATE)
			->setAttribute("placeholder", DOG_FORM_BIRT)
			->setAttribute("tabindex", $counter + 3);

		$femaleContainer->addText("Bonitace", MATING_FORM_BON_CODE)
			->setAttribute("placeholder", MATING_FORM_BON_CODE)
			->setAttribute("tabindex", $counter + 4);

		// --------------------------------------------------------

		$maleContainer = $form->addContainer('pID');
		$maleContainer->addText("Jmeno", LITTER_APPLICATION_MALE_NAME)
			->setAttribute("placeholder", LITTER_APPLICATION_MALE_NAME)
			->setAttribute("tabindex", $counter + 5);

		$maleContainer->addText("CisloZapisu", DOG_FORM_NO_OF_REC)
			->setAttribute("placeholder", DOG_FORM_NO_OF_REC)
			->setAttribute("tabindex", $counter + 6);

		$maleContainer->addText("DatNarozeni", MATING_LITTER_DOG_DATE)
			->setAttribute("placeholder", DOG_FORM_BIRT)
			->setAttribute("tabindex", $counter + 7);

		$maleContainer->addText("Bonitace", MATING_FORM_BON_CODE)
			->setAttribute("placeholder", MATING_FORM_BON_CODE)
			->setAttribute("tabindex", $counter + 8);

		// ------------------------------------------------------------

		$form->addText("MistoKryti", LITTER_APPLICATION_PLACE)
			->setAttribute("placeholder", LITTER_APPLICATION_PLACE)
			->setAttribute("tabindex", $counter + 9);

		$form->addText("DatumKryti", MATING_FORM_PLACE_DETAIL_DAY)
			->setAttribute("placeholder", MATING_FORM_PLACE_DETAIL_DAY)
			->setAttribute("tabindex", $counter + 10);

		$form->addText("DatumKrytiOpakovane", MATING_FORM_PLACE_DETAIL_DAY_REPEAT)
			->setAttribute("placeholder", MATING_FORM_PLACE_DETAIL_DAY_REPEAT)
			->setAttribute("tabindex", $counter + 11);

		$form->addText("PredpokladDatum", MATING_FORM_ESTIMATE_DATE)
			->setAttribute("placeholder", MATING_FORM_ESTIMATE_DATE)
			->setAttribute("tabindex", $counter + 12);

		$form->addText("DatumPorodu", LITTER_APPLICATION_BIRTH)
			->setAttribute("placeholder", LITTER_APPLICATION_BIRTH)
			->setAttribute("tabindex", $counter + 13);

		$form->addText("PocetStenat", LITTER_APPLICATION_DOG_LIVE)
			->setAttribute("placeholder", LITTER_APPLICATION_DOG_LIVE)
			->setAttribute("tabindex", $counter + 14);

		$form->addText("PocetStenatZTohoPsi", LITTER_APPLICATION_DOG_LIVE_MALE)
			->setAttribute("placeholder", LITTER_APPLICATION_DOG_LIVE_MALE)
			->setAttribute("tabindex", $counter + 15);

		$form->addText("PocetStenatZTohoFeny", LITTER_APPLICATION_DOG_LIVE_FEMALE)
			->setAttribute("placeholder", LITTER_APPLICATION_DOG_LIVE_FEMALE)
			->setAttribute("tabindex", $counter + 16);

		$form->addText("PocetStenatMrtvych", LITTER_APPLICATION_DOG_DEATH)
			->setAttribute("placeholder", LITTER_APPLICATION_DOG_DEATH)
			->setAttribute("tabindex", $counter + 17);

		$form->addTextArea("PocetStenatPozn", LITTER_APPLICATION_PUPPIES_DETAILS, 100, 10)
			->setAttribute("placeholder", LITTER_APPLICATION_PUPPIES_DETAILS)
			->setAttribute("tabindex", $counter + 18);

		$form->addCheckbox("Dohoda")
			->setAttribute("placeholder", MATING_FORM_RULES)
			->setAttribute("tabindex", $counter + 19);

		// ------------------------------------------------------------

		$form->addTextArea("MajitelFeny", MATING_FORM_FEMALE_OWNER, 70, 10)
			->setAttribute("placeholder", MATING_FORM_FEMALE_OWNER)
			->setAttribute("tabindex", $counter + 20);

		$form->addText("MajitelFenyTel", USER_EDIT_PHONE_LABEL)
			->setAttribute("placeholder", USER_EDIT_PHONE_LABEL)
			->setAttribute("tabindex", $counter + 21);

		$form->addTextArea("MajitelPsa", LITTER_APPLICATION_OWNWER_MALE, 70, 10)
			->setAttribute("placeholder", LITTER_APPLICATION_OWNWER_MALE)
			->setAttribute("tabindex", $counter + 22);

		$form->addText("Datum", MATING_FORM_DATE_SHORT)
			->setAttribute("placeholder", MATING_FORM_DATE_SHORT)
			->setAttribute("tabindex", $counter + 23);

		$form->addButton("back", MATING_FORM_OVERAGAIN)
			->setAttribute("class", "btn margin10")
			->setAttribute("onclick", "location.assign('" . $linkBack . "')")
			->setAttribute("tabindex", $counter + 25);

		$form->addSubmit("generate", MATING_FORM_GENERATE)
			->setAttribute("class", "btn btn-primary margin10")
			->setAttribute("tabindex", $counter + 26);

		return $form;
	}
}
