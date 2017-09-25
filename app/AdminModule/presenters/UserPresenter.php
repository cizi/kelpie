<?php

namespace App\AdminModule\Presenters;

use App\AdminModule\Model;
use App\Controller\EmailController;
use App\Enum\UserRoleEnum;
use App\Forms\UserFilterForm;
use App\Forms\UserForm;
use App\Model\Entity\UserEntity;
use App\Model\UserRepository;
use App\Model\WebconfigRepository;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Security\Passwords;
use Nette\Security\User;
use Nette\Utils\Paginator;

class UserPresenter extends SignPresenter {

	/** @var UserRepository */
	protected $userRepository;

	/** @var UserForm */
	private $userForm;

	/** @var UserFilterForm  */
	private $userFilterForm;

	/**
	 * @param UserRepository $userRepository
	 * @param UserForm $userForm
	 */
	public function __construct(UserRepository $userRepository, UserForm $userForm, UserFilterForm $userFilterForm) {
		$this->userRepository = $userRepository;
		$this->userForm = $userForm;
		$this->userFilterForm = $userFilterForm;
	}

	/**
	 * defaultní akce presenteru načte uživatele
	 */
	public function actionDefault($id, $filter) {
		$page = (empty($id) ? 1 : intval($id));
		$this['userFilterForm'][UserRepository::USER_CURRENT_PAGE]->setDefaultValue($page);
		$paginator = new Paginator();
		$paginator->setItemCount($this->userRepository->getUsersCount($filter)); // celkový počet položek
		$paginator->setItemsPerPage(50); // počet položek na stránce
		$paginator->setPage($page); // číslo aktuální stránky, číslováno od 1

		$userRoles = new UserRoleEnum();
		$this->template->paginator = $paginator;
		$this->template->users = $this->userRepository->findUsers($paginator, $filter);
		$this->template->roles = $userRoles->translatedForSelect();
	}

	/**
	 * @param int $id
	 */
	public function actionDeleteUser($id) {
		if ($this->userRepository->deleteUser($id)) {
			$this->flashMessage(USER_DELETED, "alert-success");
		} else {
			$this->flashMessage(USER_DELETED_FAILED, "alert-danger");
		}
		$this->redirect('default');
	}

	public function createComponentEditForm() {
		$form = $this->userForm->create($this->link("User:Default"), $this->langRepository->getCurrentLang($this->session));
		$form->onSuccess[] = $this->saveUser;

		return $form;
	}

	/**
	 * @param Form $form
	 * @param $values
	 */
	public function saveUser(Form $form, $values) {
		$userEntity = new UserEntity();
		$userEntity->hydrate((array)$values);
		$userEntity->setPassword(Passwords::hash($userEntity->getPassword()));
		$userEntity->setBreed((isset($values['breed']) && $values['breed'] != 0) ? $values['breed'] : NULL);
		$userEntity->setClub((isset($values['club']) && $values['club'] != 0) ? $values['club'] : NULL);
		$isEditation = (isset($values['id']) && $values['id'] != "");

		try {
			if ($isEditation) {	// pokud edituji tak prop�u jen heslo
				$userCurrent = $this->userRepository->getUser($values['id']);	// u�ivatel kter�ho m�n�m
				$userEntity->setPassword($userCurrent->getPassword());
				$this->userRepository->saveUser($userEntity);
				$this->flashMessage(USER_EDITED, "alert-success");
			} else {
				if ((trim($values['passwordConfirm']) == "") || (trim($values['password']) == "")) {
					$this->flashMessage(USER_EDIT_PASSWORDS_EMPTY, "alert-danger");
					$form->addError(USER_EDIT_PASSWORDS_EMPTY);
				} elseif (trim($values['passwordConfirm']) != trim($values['password'])) {
					$this->flashMessage(USER_EDIT_PASSWORDS_DOESNT_MATCH, "alert-danger");
					$form->addError(USER_EDIT_PASSWORDS_DOESNT_MATCH);
				} elseif ($this->userRepository->getUserByEmail($values['email']) == null) {
					$this->userRepository->saveUser($userEntity);

					$emailFrom = $this->webconfigRepository->getByKey(WebconfigRepository::KEY_CONTACT_FORM_RECIPIENT, WebconfigRepository::KEY_LANG_FOR_COMMON);
					$subject = USER_CREATED_MAIL_SUBJECT;
					$body = sprintf(USER_CREATED_MAIL_BODY, $this->getHttpRequest()->getUrl()->getBaseUrl(), $userEntity->getEmail(), $values['password']);
					EmailController::SendPlainEmail($emailFrom, $userEntity->getEmail(), $subject, $body);

					$this->flashMessage(USER_ADDED, "alert-success");
					$this->redirect("Default");
				} else {
					$this->flashMessage(USER_EMAIL_ALREADY_EXISTS, "alert-danger");
					$form->addError(USER_EMAIL_ALREADY_EXISTS);
				}
			}
		} catch (\Exception $e) {
			if ($e instanceof AbortException) {
				throw $e;
			} else {
				$this->flashMessage(USER_EDIT_SAVE_FAILED, "alert-danger");
				$form->addError(USER_EDIT_SAVE_FAILED);
			}
		}
	}

	/**
	 * @param int $id
	 */
	public function actionEdit($id) {
		$this->template->user = null;
		$userEntity = $this->userRepository->getUser($id);
		$this->template->user = $userEntity;

		if ($userEntity) {
			$this['editForm']->addHidden('id', $userEntity->getId());
			$this['editForm']['email']->setAttribute("readonly", "readonly");

			$this['editForm']['password']->setAttribute("readonly", "readonly");	// pokud edituji tak heslo nem�n�m
			$this['editForm']['passwordConfirm']->setAttribute("readonly", "readonly"); // pokud edituji tak heslo nem�n�m

			$this['editForm']->setDefaults($userEntity->extract());

			$this['editForm']['passwordConfirm']->setAttribute("class", "form-control");
			$this['editForm']['password']->setAttribute("class", "form-control");
		}
	}

	/**
	 *
	 */
	public function handleActiveSwitch() {
		$data = $this->request->getParameters();
		$userId = $data['idUser'];
		$switchTo = (!empty($data['to']) && $data['to'] == "false" ? false : true);

		if ($switchTo) {
			$this->userRepository->setUserActive($userId);
		} else {
			$this->userRepository->setUserInactive($userId);
		}

		$this->terminate();
	}

	public function createComponentUserFilterForm() {
		$form = $this->userFilterForm->create();
		$form->onSubmit[] = $this->submitUserFilterForm;

		$renderer = $form->getRenderer();
		$renderer->wrappers['controls']['container'] = NULL;
		$renderer->wrappers['pair']['container'] = 'div class=form-group';
		$renderer->wrappers['pair']['.error'] = 'has-error';
		$renderer->wrappers['control']['container'] = 'div class=col-md-4';
		$renderer->wrappers['label']['container'] = 'div class="col-md-4 control-label"';
		$renderer->wrappers['control']['description'] = 'span class=help-block';
		$renderer->wrappers['control']['errorcontainer'] = 'span class=help-block';

		return $form;
	}

	public function submitUserFilterForm(Form $form) {
		$array = $form->getHttpData();
		//$currentPage = (isset($array[UserRepository::USER_CURRENT_PAGE]) ? intval($array[UserRepository::USER_CURRENT_PAGE]) : 1);
		if (isset($array[UserRepository::USER_SEARCH_FIELD]) && (trim($array[UserRepository::USER_SEARCH_FIELD])) != "") {
			$this->redirect("default", 1, $array[UserRepository::USER_SEARCH_FIELD]);
		} else {
			$this->redirect("default");
		}
	}
}