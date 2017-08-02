<?php
	/**
	 * Created by PhpStorm.
	 * User: vladimir
	 * Date: 02.08.17
	 * Time: 15:15
	 */

	namespace KosmosKosmos\Loggable\Traits;


	use Backend\Facades\BackendAuth;
	use Illuminate\Support\Facades\Log;
	use RainLab\User\Facades\Auth;

	trait Loggable {
		protected function getLogUser() {
			$user = '';
			if (BackendAuth::check()) {
				$user .= 'Backend User '.BackendAuth::getUser()->login;
			}
			if (Auth::check()){
				if (strlen($user)) {
					$user .= ' or ';
				}
				$user .= 'Frontend User '.Auth::getUser()->name;
			}

			return $user;
		}
		public function beforeSave () {
			foreach ($this->getAttributes() as $key => $value) {
				if ($this->$key != $this->getOriginal($key)) {
					Log::info(get_class($this).'# '.
							$this->id.': '.
							$key.' changed from '.
							$this->getOriginal($key).' to '.
							(is_array($this->$key) ? json_encode($this->$key) : $this->$key).' by '.
							$this->getLogUser());
				}
			}
		}

		public function beforeDelete () {
			Log::info(get_class($this).'# '.
					$this->id.': '.
					'is being deleted by '.
					$this->getLogUser());
		}

		public function afterCreate () {
			Log::info(get_class($this).'# '.
					$this->id.': '.
					'was created by '.
					$this->getLogUser());
		}
	}