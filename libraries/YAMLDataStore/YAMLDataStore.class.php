<?php
/**
 * Cumula
 *
 * Cumula — framework for the cloud.
 *
 * @package    Cumula
 * @version    0.1.0
 * @author     Seabourne Consulting
 * @license    MIT License
 * @copyright  2011 Seabourne Consulting
 * @link       http://cumula.org
 */

require_once dirname(__FILE__) . '/includes/sfYamlDumper.php';
require_once dirname(__FILE__) . '/includes/sfYamlParser.php';


/**
 * YAMLDataStore Class
 *
 * An implementation of DataStore using YAML.  A source directory and filename are passed in the config values and is used to save the
 * information in YAML format.
 *
 * @package		Cumula
 * @subpackage	Core
 * @author     Seabourne Consulting
 */
class YAMLDataStore extends BaseDataStore {
	private $_storage;
	private $_sourceDirectory;
	private $_filename;
	
	/**
	 * Accepts an array of config values as name => value pairs.  Two possible config values are:
	 *   -source_directory: the absolute file path to save the config file to
	 *   -filename: the YAML filename to save the information as
	 * 
	 * @param $config_values
	 * @return unknown_type
	 */
	public function __construct(CumulaSchema $schema, $configValues) {
		parent::__construct($schema, $configValues);
		$this->_storage = array();
		$this->_sourceDirectory = $configValues['source_directory'];
		$this->_filename = $configValues['filename'];
	}
	
	/* (non-PHPdoc)
	 * @see core/interfaces/DataStore#connect()
	 */
	public function connect() {
		$this->_load();
	}
	
	/* (non-PHPdoc)
	 * @see core/interfaces/DataStore#disconnect()
	 */
	public function disconnect() {
		$this->_save();
	}
	
	/* (non-PHPdoc)
	 * @see core/interfaces/DataStore#create($obj)
	 */
	public function create($obj) {
		foreach($obj as $key => $value) {
			$this->_storage[$key] = $value;
		}
		$this->_save();
	}
	
	/* (non-PHPdoc)
	 * @see core/interfaces/DataStore#update($obj)
	 */
	public function update($obj) {
		foreach($obj as $key => $value) {
			$this->_storage[$key] = $value;
		}
		$this->_save();
	}
	
	/**
	 * Creates or Updates an object depending on whether it exists already.
	 * 
	 * @param $obj
	 * @return unknown_type
	 */
	public function createOrUpdate($obj) {
		foreach($obj as $key => $value) {
			if ($this->recordExists($key)) {
				$this->update($obj);
			} else {
				$this->create($obj);
			}
		}
	}
	
	/* (non-PHPdoc)
	 * @see core/interfaces/DataStore#delete($obj)
	 */
	public function destroy($obj) {
		if(is_string($obj)) {
			if ($this->recordExists($obj)) {
				unset($this->_storage[$obj]);
			}
		} else {
			foreach($obj as $key => $value) {
				unset($this->_storage[$key]);
			}
			$this->_save();
		}
	}
	
	/* (non-PHPdoc)
	 * @see core/interfaces/DataStore#query($args, $order, $sort)
	 */
	public function query($args, $order = null, $sort = null) {
		if ($this->recordExists($args)) {
			$obj = $this->_storage[$args];
		} else {
			$obj = null;
		}
		return $obj;
	}
	
	public function recordExists($id) {
		if(!isset($this->_storage))
			return false;
		return array_key_exists($id, $this->_storage);
	}
	
	/**
	 * Saves the data in the internal storage variable to the YAML file.
	 * @return unknown_type
	 */
	protected function _save() {
		if(!empty($this->_storage)) {
			$dumper = new sfYamlDumper();
			$yaml = $dumper->dump($this->_storage, 2);
			file_put_contents($this->_dataStoreFile(), $yaml);
		}
	}
	
	private function _dataStoreFile() {
		return $this->_sourceDirectory.'/'.$this->_filename;
	}
	
	public function translateFields($fields) {
		return $fields;
	}
	
	public function install() {
		return false;
	}
	
	public function uninstall() {
		return false;
	}
	
	public function lastRowId() {
		return count($this->_storage);
	}
	
	/**
	 * Loads the data in the external YAML file into the internal storage var.
	 * 
	 * @return boolean True if the information was loaded, false otherwise.
	 */
	protected function _load() {
		if (file_exists($this->_dataStoreFile())) {
			$yaml = new sfYamlParser();
			$this->_storage = $yaml->parse(file_get_contents($this->_dataStoreFile()));
			//$this->_storage = Spyc::YAMLLoadString(file_get_contents($this->_dataStoreFile()));
			return true;
		} else {
			return false;
		}
	}
}