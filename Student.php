<?php

class Student extends ObjectModel
{
	/** @var int student ID */
	public $id_student;

	/** @var mixed string or array of Name */
	public $name;

	/** @var date student's Date */
	public $date;

	/** @var bool Status for display */
	public $status;

	/** @var float student's average ball */
	public $average_ball;

	/**
	 * @see ObjectModel::$definition
	 */
	public static $definition = [
		'table' => 'category',
		'primary' => 'id_category',
		'multilang' => true,
		'multilang_shop' => true,
		'fields' => [
			'name' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isStudentName', 'size' => 128],
			'date' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
			'active' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
			'$average_ball' => ['type' => self::TYPE_FLOAT, 'validate' => 'isBall']
		]
	];

	public function __construct($idStudent = null, $idLang = null, $idShop = null)
	{
		parent::__construct($idStudent, $idLang, $idShop);
	}

	/**
	 * @return array of Students
	 */
	public static function getAllStudents($id_lang)
	{
		$students = Db::getInstance()->executeS("SELECT s.*, sl.name 
													FROM `"._DB_PREFIX_."student` s 
													LEFT JOIN `"._DB_PREFIX_."student_lang` sl ON (s.`id_student` = sl.`id_student`)
													WHERE sl.`id_lang` = ".(int)$id_lang);
		return $students;
	}

	/**
	 * @return Student object
	 */
	public static function getBestStudent($id_lang)
	{
		$student = Db::getInstance()->execute("SELECT s.*, sl.name 
													FROM   `"._DB_PREFIX_."student` s
													LEFT JOIN `"._DB_PREFIX_."student_lang` sl ON (s.`id_student` = sl.`id_student`)
													WHERE sl.`id_lang` = ".(int)$id_lang."
													AND average_ball=(SELECT MAX(average_ball) FROM "._DB_PREFIX_."student)");
		return $student;
	}

	/**
	 * @return float maximum average ball
	 */
	public static function getMaxAverageBall()
	{
		$maxAverageBall = Db::getInstance()->getValue("SELECT MAX(average_ball) 
													FROM `"._DB_PREFIX_."student`");
		return $maxAverageBall;
	}
}