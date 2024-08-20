<?php

namespace App\Forms;

use Nette;
use Nette\Application\UI\Form;

class EnumerationItemForm {

    use Nette\SmartObject;

    public const FORM_ID = "enumerationItemForm";

	/** @var FormFactory */
	private $factory;

	/**
	 * @param FormFactory $factory
	 */
	public function __construct(FormFactory $factory) {
		$this->factory = $factory;
	}

	/**
	 * @param array $languages
	 * @return Form
	 */
	public function create(array $languages, $linkBack) {
		$counter = 1;
		$form = $this->factory->create();

        $form->getElementPrototype()->addAttributes(["id" => self::FORM_ID]);
        $form->getElementPrototype()->addAttributes(["onsubmit" => "return requiredFields('" . self::FORM_ID . "');"]);

        foreach($languages as $lang) {
			$container = $form->addContainer($lang);

			$container->addText("lang")
				->setAttribute("class", "form-control menuItem")
				->setAttribute("tabindex", "-1")
				->setAttribute("readonly", "readonly")
				->setValue($lang);

			$container->addText("item", ENUM_EDIT_ITEM_NAME)
				->setAttribute("class", "form-control menuItem tinym_required_field")
				->setAttribute("validation", ENUM_EDIT_ITEM_NAME_REQ)
				->setAttribute("tabindex", $counter + 1);

			$container->addHidden("enum_header_id");
			$container->addHidden("order");
			$container->addHidden("id");
			$container->addHidden("is_ake");
			$container->addHidden("is_wcc");
			$container->addHidden("is_wcp");

			$counter += 1;
		}

        $form->addSelect("health_group", ENUM_EDIT_HEALT_TYPE, ["-" => "-", "AKE" => "AKE", "WCC" => "WCC", "WCP" => "WCP"])
            ->setAttribute("class", "form-control menuItem")
            ->setAttribute("style", "margin: 5px!important");

		$form->addSubmit("confirm", USER_EDIT_SAVE_BTN_LABEL)
			->setAttribute("class","btn btn-primary menuItem alignRight")
			->setAttribute("tabindex", $counter+1);

		$form->addButton("back", USER_EDIT_BACK_BTN_LABEL)
			->setAttribute("class", "btn btn-secondary menuItem alignRight")
			->setAttribute("onclick", "location.assign('". $linkBack ."')")
			->setAttribute("tabindex", $counter+1);

		return $form;
	}
}
