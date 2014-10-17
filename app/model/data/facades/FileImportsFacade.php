<?php
namespace App\Model\Data\Facades;
use App\Model\Data\Entities\DbConnection;
use App\Model\Data\Files\CsvImport;

/**
 * Class FileImportsFacade - model pro práci s importy souborů
 * @package App\Model\Data\Facades
 */
class FileImportsFacade {
  /** @var  string $dataDirectory */
  private $dataDirectory;
  /** @var DatabasesFacade $databasesFacade */
  private $databasesFacade;

  public function __construct($dataDirectory,DatabasesFacade $databasesFacade){
    $this->dataDirectory=$dataDirectory;
    $this->databasesFacade=$databasesFacade;
  }

  /**
   * Funkce pro změnu kódování datového souboru
   * @param string $filename
   * @param string $originalEncoding
   */
  public function changeFileEncoding($filename,$originalEncoding){
    CsvImport::iconvFile($this->getFilePath($filename),$originalEncoding);
  }

  /**
   * Funkce pro smazání pracovních souborů sloužících k importům dat
   * @param string $filename
   */
  public function deleteFile($filename){
    @unlink($this->getFilePath($filename));
    @unlink($this->getFilePath($filename.'.utf8'));
    @unlink($this->getFilePath($filename.'.cp1250'));
    @unlink($this->getFilePath($filename.'.iso-8859-1'));
  }

  /**
   * Funkce vracející data z CSV souboru
   * @param string $filename
   * @param int $count
   * @param string $delimitier
   * @param string $enclosure
   * @param string $escapeCharacter
   * @param int $offset = 0 - počet řádek, které se mají na začátku souboru přeskočit...
   * @return array
   */
  public function getRowsFromCSV($filename,$count=10000,$delimitier=',',$enclosure='"',$escapeCharacter='\\',$offset=0){
    return CsvImport::getRowsFromCSV($this->getFilePath($filename),$count,$delimitier,$enclosure,$escapeCharacter,$offset);
  }

  /**
   * Funkce vracející počet řádků
   * @param string $filename
   * @return int
   */
  public function getRowsCountInCSV($filename){
    return CsvImport::getRowsCountInCsv($this->getFilePath($filename));
  }

  /**
   * Funkce vracející počet sloupců v CSV souboru
   * @param string $filename
   * @param string $delimitier
   * @param string $enclosure
   * @param string $escapeCharacter
   * @return int
   */
  public function getColsCountInCSV($filename,$delimitier=',',$enclosure='"',$escapeCharacter='\\'){
    return CsvImport::getColsCountInCsv($this->getFilePath($filename),$delimitier,$enclosure,$escapeCharacter);
  }

  /**
   * Funkce vracející přehled sloupců v CSV souboru
   * @param string $filename
   * @param string $delimitier
   * @param string $enclosure
   * @param string $escapeCharacter
   * @return \App\Model\Data\Entities\DbColumn[]
   */
  public function getColsInCSV($filename,$delimitier=',',$enclosure='"',$escapeCharacter='\\'){
    return CsvImport::analyzeCSVColumns($filename,$delimitier,$enclosure,$escapeCharacter);
  }

  /**
   * Funkce vracející výchozí oddělovač...
   * @param string $filename
   * @return string
   */
  public function getCSVDelimitier($filename){
    return CsvImport::getCSVDelimitier($this->getFilePath($filename));
  }

  /**
   * Funkce vracející nové pracovní jméno souboru
   * @return string
   */
  public function getTempFilename(){
    $filename=time();
    while(file_exists($this->getFilePath($filename))){
      $filename.='x';
    }
    return $filename;
  }

  public function getFilePath($filename){
    return $this->dataDirectory.'/'.$filename;
  }

  public function importCsvFile($filename,DbConnection $dbConnection,&$table,$encoding='utf-8',$delimitier=',',$enclosure='"',$escapeCharacter='\\'){
    //připravení parametrů pro DB tabulku
    $this->changeFileEncoding($filename,$encoding);
    $csvColumns=$this->getColsInCSV($filename,$delimitier,$enclosure,$escapeCharacter);

    //otevření databáze a vytvoření DB tabulky
    $this->databasesFacade->openDatabase($dbConnection);
    $this->databasesFacade->createTable($table,$csvColumns);

    //projití CSV souboru a import dat do DB
    $colsNames=array();
    foreach ($csvColumns as $column){
      $colsNames[]=$column->name;
    }
    $colsCount=count($colsNames);

    $csvFile=CsvImport::openCsv($this->getFilePath($filename));
    while($row=CsvImport::getRowsFromOpenedCSVfile($csvFile,1,$delimitier,$enclosure,$escapeCharacter)){
      $insertArr=array();
      for ($i=0;$i<$colsCount;$i++){
        $insertArr[$colsNames[$i]]=$row[$i];
      }
      $this->databasesFacade->insertRow($table,$insertArr);
    }
    CsvImport::closeCsv($csvFile);
  }

} 