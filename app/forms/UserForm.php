<?php

namespace App\Forms;

use App\Enum\StateEnum;
use App\Enum\UserRoleEnum;
use App\Model\EnumerationRepository;
use Nette;
use Nette\Application\UI\Form;

class UserForm {

    use Nette\SmartObject;

    public const FORM_ID = "registrationForm";

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
	 * @return Form
	 */
	public function create($linkBack, $langCurrent) {
		$form = $this->factory->create();
		$form->getElementPrototype()->addAttributes(["id" => self::FORM_ID]);
		$form->getElementPrototype()->addAttributes(["onsubmit" => "return requiredFields('" . self::FORM_ID . "');"]);

		$form->addText("email", USER_EDIT_EMAIL_LABEL)
			->setHtmlAttribute("type","email")
			->setHtmlAttribute("class", "tinym_required_field form-control")
			->setHtmlAttribute("placeholder", USER_EDIT_EMAIL_LABEL)
			->setHtmlAttribute("validation", USER_EDIT_EMAIL_VALIDATION)
			->setHtmlAttribute("tabindex", "1");

		$form->addPassword("password", USER_EDIT_PASS_LABEL)
			->setHtmlAttribute("type","password")
			->setHtmlAttribute("class", "tinym_required_field form-control")
			->setHtmlAttribute("placeholder", USER_EDIT_PASS_LABEL)
			->setHtmlAttribute("validation", USER_EDIT_PASS_REQ)
			->setHtmlAttribute("tabindex", "2");

		$form->addPassword("passwordConfirm", USER_EDIT_PASS_AGAIN_LABEL)
			->setHtmlAttribute("type","password")
			->setHtmlAttribute("class", "tinym_required_field form-control")
			->setHtmlAttribute("placeholder", USER_EDIT_PASS_AGAIN_LABEL)
			->setHtmlAttribute("validation", USER_EDIT_PASS_AGAIN_REQ)
			->setHtmlAttribute("tabindex", "3");

		$userRole = new UserRoleEnum();
		$form->addSelect("role", USER_EDIT_ROLE_LABEL, $userRole->translatedForSelect())
			->setHtmlAttribute("class", "form-control")
			->setHtmlAttribute("readonly", "readonly")
			->setHtmlAttribute("tabindex", "4");;

		$form->addCheckbox('active')
			->setHtmlAttribute("data-toggle", "toggle")
			->setHtmlAttribute("data-height", "25")
			->setHtmlAttribute("data-width", "50")
			->setDefaultValue("checked")
			->setHtmlAttribute("tabindex", "5");

		$form->addText("title_before", USER_EDIT_TITLE_BEFORE_LABEL)
			->setHtmlAttribute("class", "form-control")
			->setHtmlAttribute("placeholder", USER_EDIT_TITLE_BEFORE_LABEL)
			->setHtmlAttribute("tabindex", "6");

		$form->addText("name", USER_EDIT_NAME_LABEL)
			->setHtmlAttribute("class", "tinym_required_field form-control")
			->setHtmlAttribute("placeholder", USER_EDIT_NAME_LABEL)
			->setHtmlAttribute("validation", USER_EDIT_NAME_LABEL_VALIDATION)
			->setHtmlAttribute("tabindex", "7");

		$form->addText("surname", USER_EDIT_SURNAME_LABEL)
			->setHtmlAttribute("class", "tinym_required_field form-control")
			->setHtmlAttribute("placeholder", USER_EDIT_SURNAME_LABEL)
			->setHtmlAttribute("validation", USER_EDIT_SURNAME_LABEL_VALIDATION)
			->setHtmlAttribute("tabindex", "8");

		$form->addText("title_after", USER_EDIT_TITLE_AFTER_LABEL)
			->setHtmlAttribute("class", "form-control")
			->setHtmlAttribute("placeholder", USER_EDIT_TITLE_AFTER_LABEL)
			->setHtmlAttribute("tabindex", "9");

		$form->addText("street", USER_EDIT_STREET_LABEL)
			->setHtmlAttribute("class", "form-control")
			->setHtmlAttribute("placeholder", USER_EDIT_STREET_LABEL)
			->setHtmlAttribute("tabindex", "10");

		$form->addText("city", USER_EDIT_CITY_LABEL)
			->setHtmlAttribute("class", "form-control")
			->setHtmlAttribute("placeholder", USER_EDIT_CITY_LABEL)
			->setHtmlAttribute("tabindex", "11");

		$form->addText("zip", USER_EDIT_ZIP_LABEL)
			->setHtmlAttribute("class", "form-control")
			->setHtmlAttribute("placeholder", USER_EDIT_ZIP_LABEL)
			->setHtmlAttribute("tabindex", "12");

		$states = new StateEnum();
		$form->addSelect("state", USER_EDIT_STATE_LABEL, $states->arrayKeyValue())
			->setHtmlAttribute("class", "form-control")
			->setHtmlAttribute("placeholder", USER_EDIT_STATE_LABEL)
			->setDefaultValue("CZECH_REPUBLIC")
			->setHtmlAttribute("tabindex", "13");

		$form->addText("web", USER_EDIT_WEB_LABEL)
			->setHtmlAttribute("class", "form-control")
			->setHtmlAttribute("placeholder", USER_EDIT_WEB_LABEL)
			->setHtmlAttribute("tabindex", "14");

		$form->addText("phone", USER_EDIT_PHONE_LABEL)
			->setHtmlAttribute("class", "form-control")
			->setHtmlAttribute("placeholder", USER_EDIT_PHONE_LABEL)
			->setHtmlAttribute("tabindex", "15");

		$form->addText("fax", USER_EDIT_FAX_LABEL)
			->setHtmlAttribute("class", "form-control")
			->setHtmlAttribute("placeholder", USER_EDIT_FAX_LABEL)
			->setHtmlAttribute("tabindex", "16");

		$form->addText("station", USER_EDIT_STATION_LABEL)
			->setHtmlAttribute("class", "form-control")
			->setHtmlAttribute("placeholder", USER_EDIT_STATION_LABEL)
			->setHtmlAttribute("tabindex", "17");

		$form->addMultiSelect("breed", USER_EDIT_BREED_LABEL, $this->enumerationRepository->findEnumItemsForSelectIgnoreEmpty($langCurrent, 7))
			->setHtmlAttribute("class", "form-control")
			->setHtmlAttribute("placeholder", USER_EDIT_STATE_LABEL)
			->setHtmlAttribute("tabindex", "18");

		$sharing = $this->enumerationRepository->findEnumItemsForSelect($langCurrent, 9);
		reset($sharing);
		$first_key = key($sharing);
		$form->addRadioList("sharing", USER_EDIT_SHARING_LABEL, $sharing)
			->setHtmlAttribute("class", "form-check-input margin10")
			->setDefaultValue($first_key)
			->setHtmlAttribute("placeholder", USER_EDIT_SHARING_LABEL)
			->setHtmlAttribute("tabindex", "19");

		$clubs = $this->enumerationRepository->findEnumItemsForSelect($langCurrent, 17);
		$form->addSelect("club", USER_EDIT_CLUB, $clubs)
			->setHtmlAttribute("class", "form-control")
			->setHtmlAttribute("placeholder", USER_EDIT_CLUB)
			->setHtmlAttribute("tabindex", "20");

		$form->addText("clubNo", USER_EDIT_CLUB_NO)
			->setHtmlAttribute("class", "form-control")
			->setHtmlAttribute("placeholder", USER_EDIT_CLUB_NO)
			->setHtmlAttribute("tabindex", "21");

		$form->addCheckbox("news", "")
			->setHtmlAttribute("tabindex", "22");

		$form->addCheckbox("privacy", "")
			->setHtmlAttribute("tabindex", "23");

		$form->addHidden("privacy_tries_count", 0);

		$form->addSubmit("confirm", USER_EDIT_SAVE_BTN_LABEL)
			->setHtmlAttribute("class","btn btn-primary")
			->setHtmlAttribute("tabindex", "24");

		$form->addButton("back", USER_EDIT_BACK_BTN_LABEL)
			->setHtmlAttribute("class", "btn btn-secondary")
			->setHtmlAttribute("onclick", "location.assign('". $linkBack ."')")
			->setHtmlAttribute("tabindex", "25");

		return $form;
	}
}
