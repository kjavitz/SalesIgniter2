<?php
/*
$Id: navigation_history.php,v 1.6 2003/06/09 22:23:43 hpdl Exp $

osCommerce, Open Source E-Commerce Solutions
http://www.oscommerce.com

Copyright (c) 2003 osCommerce

Released under the GNU General Public License
*/

	class navigationHistory {
		public $path, $snapshot;
        public function __construct(){
            $this->reset();
        }

		public function reset() {
			$this->path = array();
			$this->snapshot = array();
		}

		public function add_current_page() {
			global $request_type, $cPath;

            $thisGetParamsString = implode('|',$_GET);
			$set = 'true';

			for ($i=0, $n=sizeof($this->path); $i<$n; $i++) {
                $getParamsString = implode('|',$this->path[$i]['get']);
				if ( isset($_GET['app'])&& isset($_GET['appPage'])&& $this->path[$i]['app'] == $_GET['app'] &&  $this->path[$i]['appPage'] == $_GET['appPage'] && $getParamsString == $thisGetParamsString) {
					if (isset($cPath)) {
						if (!isset($this->path[$i]['get']['cPath'])) {
							continue;
						} else {
							if ($this->path[$i]['get']['cPath'] == $cPath) {
								array_splice($this->path, ($i+1));
								$set = 'false';
								break;
							} else {
								$old_cPath = explode('_', $this->path[$i]['get']['cPath']);
								$new_cPath = explode('_', $cPath);

								for ($j=0, $n2=sizeof($old_cPath); $j<$n2; $j++) {
									if ($old_cPath[$j] != $new_cPath[$j]) {
										array_splice($this->path, ($i));
										$set = 'true';
										break 2;
									}
								}
							}
						}
					} else {
						array_splice($this->path, ($i));
						$set = 'true';
						break;
					}
				}
			}

			if ($set == 'true') {
				$this->path[] = array('app' => isset($_GET['app'])?$_GET['app']:'',
                		'appPage' =>isset($_GET['appPage'])?$_GET['appPage']:'',
				'mode' => $request_type,
				'get' => $_GET,
				'post' => $_POST);
			}
		}

		public function remove_current_page() {
			$last_entry_position = sizeof($this->path) - 1;
			if ($this->path[$last_entry_position]['app'] == $_GET['app'] && $this->path[$last_entry_position]['appPage'] == $_GET['appPage']) {
				unset($this->path[$last_entry_position]);
			}
		}

		public function set_snapshot($page = '') {
			global $request_type;

			if (is_array($page)) {
				$this->snapshot = array('app' => $page['app'],
				'appPage' => $page['appPage'],
				'mode' => $page['mode'],
				'get' => $page['get'],
				'post' => $page['post']);
			} else {
				$this->snapshot = array('app' => $_SERVER['app'],
				'appPage' => $_GET['appPage'],
				'mode' => $request_type,
				'get' => $_GET,
				'post' => $_POST);
			}
		}

        public function getSnapShot(){
            if (sizeof($this->snapshot) > 0) {
                return $this->snapshot;
            }
            return false;
        }

        public function getPath($history = 0){
            $pos = (sizeof($this->path)-1-$history);
            if (sizeof($this->path[$pos]) > 0) {
                return array('app' => $this->path[$pos]['app'],
                             'appPage' => $this->path[$pos]['appPage'],
                             'mode' => $this->path[$pos]['mode'],
                             'get' => $this->path[$pos]['get'],
                             'post' => $this->path[$pos]['post']);
            }
            return false;
        }

		public function clear_snapshot() {
			$this->snapshot = array();
		}

		public function set_path_as_snapshot($history = 0) {
			$pos = (sizeof($this->path)-1-$history);
			$this->snapshot = array('app' => $this->path[$pos]['app'],
			'appPage' => $this->path[$pos]['appPage'],
			'mode' => $this->path[$pos]['mode'],
			'get' => $this->path[$pos]['get'],
			'post' => $this->path[$pos]['post']);
		}

        public function debug() {
			for ($i=0, $n=sizeof($this->path); $i<$n; $i++) {
				echo $this->path[$i]['page'] . '?';
				while (list($key, $value) = each($this->path[$i]['get'])) {
					echo $key . '=' . $value . '&';
				}
				if (sizeof($this->path[$i]['post']) > 0) {
					echo '<br>';
					while (list($key, $value) = each($this->path[$i]['post'])) {
						echo '&nbsp;&nbsp;<b>' . $key . '=' . $value . '</b><br>';
					}
				}
				echo '<br>';
			}

			if (sizeof($this->snapshot) > 0) {
				echo '<br><br>';

				echo $this->snapshot['mode'] . ' ' . $this->snapshot['page'] . '?' . tep_array_to_string($this->snapshot['get'], array(tep_session_name())) . '<br>';
			}
		}

        public function unserialize($broken) {
			for(reset($broken);$kv=each($broken);) {
				$key=$kv['key'];
				if (gettype($this->$key)!="user function")
				$this->$key=$kv['value'];
			}
		}
        public function __destruct(){
        }
	}
?>
