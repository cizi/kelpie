<?php

namespace App\FrontendModule\Presenters;

use App\Forms\UserChangePasswordForm;
use App\Model\UserRepository;
use Nette\Application\AbortException;
use Nette\Forms\Form;
use Nette\Security\Passwords;

class FeItem2velord10Presenter extends FrontendPresenter {

	/** @var UserChangePasswordForm */
	private $userChangePasswordForm;

	/** @var UserRepository */
	private $userRepository;

	public function __construct(UserChangePasswordForm $userChangePasswordForm, UserRepository $userRepository) {
		$this->userChangePasswordForm = $userChangePasswordForm;
		$this->userRepository = $userRepository;
	}

	public function startup() {
		parent::startup();

		if ($this->user->isLoggedIn() == false) {
			$this->redirect($this->getPresenterLink(1, 14) . ":default");
		}
	}

	public function createComponentChangePasswordForm() {
		$form = $this->userChangePasswordForm->create();
        $form->onSubmit[] = [$this, 'updateUserPassword'];

		return $form;
	}

	public function updateUserPassword(Form $form) {
        $values = $form->getHttpData();
        $userEntity = $this->userRepository->getUser($this->user->getId());
        $passwords = new Passwords();

		try {
			if (!$passwords->verify($values['passwordCurrent'], $userEntity->getPassword())) {
				$this->flashMessage(USER_EDIT_CURRENT_PASSWORD_FAILED, "alert-danger");
				$form->addError(USER_EDIT_CURRENT_PASSWORD_FAILED);
			} elseif ((trim($values['passwordConfirm']) == "") || (trim($values['password']) == "")) {
				$this->flashMessage(USER_EDIT_PASSWORDS_EMPTY, "alert-danger");
				$form->addError(USER_EDIT_PASSWORDS_EMPTY);
			} elseif (trim($values['passwordConfirm']) != trim($values['password'])) {
				$this->flashMessage(USER_EDIT_PASSWORDS_DOESNT_MATCH, "alert-danger");
				$form->addError(USER_EDIT_PASSWORDS_DOESNT_MATCH);
			} else {
				if ($this->userRepository->changePassword($this->user->getId(), $values['password'])) {
					$this->flashMessage(USER_EDIT_PASSWORD_CHANGED, "alert-success");
					$this->redirect("default");
				} else {
					$this->flashMessage(USER_EDIT_SAVE_FAILED, "alert-danger");
					$form->addError(USER_EDIT_SAVE_FAILED);
				}
			}
		} catch (\Exception $e) {
			if ($e instanceof AbortException) {
				throw $e;
			} else {
//                dump($e->getMessage(), $values); die;
				$this->flashMessage(USER_EDIT_SAVE_FAILED, "alert-danger");
				$form->addError(USER_EDIT_SAVE_FAILED);
			}
		}

        $this->redirect("Default");
	}
}
