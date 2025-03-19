<?php
/** PAI_coblist class file 
 * package    PAI_COBList 20250318
 * @license   Copyright © 2018-2025 Pathfinder Associates, Inc.
 * Public Methods: 
 *		CheckFile-checks uploaded CSV for format and size
 *		ProcessFile-main process to create working arrays/tables and create XLSX
 *		RunDelta-main process to compare two runs and create delta array
 *		GetRuns-returns runs from RunLog table where type = 1 or 5 (archived) 20201210
 *		CompressRunBlob - deletes all logid from RunBlob and set RunLog.type = 4 20201101
 *		CheckUserID-checks userid and access in last RunBlob for admin rights
 *		Updated BuildSlip to also show number for slips size - 1 is any, 2 is A-D, 3 is A, 4 is P
 *		Added Voter error for missing and multiple
 *		Added Owner to slip waitlist and error for vacant rack
 *		Added SELECT DISTINCT to build dbRenter since it was duplicating all units
 *		Don't add to DBRenter if a Partner
 *		Added phone and email to wait list
 *		Changed Lift ot Limbo in slips to track 180 days period to terminate
 *		Changed Kayak header to Trained
 *		Added Tenure to slip/rack field
 *		Added Access & Lease End to Listing sheet
 *		Added LeaseEnd to Units and blank row above field header
 *		Added logic in GetFullUnit for missing space after #
 */
class COBList
{
    /**
     * main class
     */   
	// Private Variables //
		const iVersion = "4.3.0";
		private $dbUser = array();
		private $hdrUser = array();
		private $dbRes = array();
		private $hdrRes = array();
		private $dbSold = array();
		private $hdrSold = array();
		private $dbGone = array();
		private $hdrGone = array();
		private $dbErr = array();
		private $hdrErr = array();
		private $dbSlip = array();
		private $hdrSlip = array();
		private $hdrSlip2 = array();
		private $hdrWait = array();
		private $hdrWait2 = array();
		private $dbKayak = array();
		private $hdrKayak = array();
		private $dbRenter = array();
		private $hdrRenter = array();
		private $dbStaff = array();
		private $hdrStaff = array();
		private $dbGridT1 = array();
		private $hdrGridT1 = array();
		private $dbGridT2 = array();
		private $hdrGridT2 = array();
		private $dbVoterT1 = array();
		private $hdrVoterT1 = array();
		private $dbVoterT2 = array();
		private $hdrVoterT2 = array();
		private $dbPets = array();
		private $hdrPets = array();
		private $dbVoter = array();
		private $hdrVoter = array();
		private $dbUnit = array();
		private $hdrUnit = array();
		private $pdo;
		private $paicrypt;

	//Constants for fields in User tab
		const iCreated = 0;
		const iUsername = 1;
		const iEnabled = 2;
		const iFirstName = 3;
		const iEmail = 4;
		const iAccess = 5;
		const iUser1LastName = 6;
		const iUnit = 7;
		const iHomePhone = 8;
		const iUser1WorkPhone = 9;
		const iUser1CellPhone = 10;
		const iUser1Occupation = 11;
		const iUser1Employer = 12;
		const iUser1Hobbies = 13;
		const iUser2FirstName = 14;
		const iUser2LastName = 15;
		const iUser2Email = 16;
		const iUser2WorkPhone = 17;
		const iUser2CellPhone = 18;
		const iUser2Occupation = 19;
		const iUser2Employer = 20;
		const iUser2Hobbies = 21;
		const iChild1Name = 22;
		const iChild2Name = 23;
		const iChild3Name = 24;
		const iChild4Name = 25;
		const iChild1Birthdate = 26;
		const iChild2Birthdate = 27;
		const iChild3Birthdate = 28;
		const iChild4Birthdate = 29;
		const iOwner = 30;
		const iMailings = 31;
		const i2ndAddress = 32;
		const iEmergencyContact = 33;
		const iUnitWatcher = 34;
		const iStack = 35;
		const iSlip = 36;
		const iPets = 37;
		const iOfficialVoter = 38;
		const iShowProfile = 39;
		const iShowEmail = 40;
		const iShowPhone = 41;
		const iShowChildren = 42;
		const iAdminNotes = 43;
		const iLastLogin = 44;
		const iUserID = 45;
		const iVoter = 46;
		const iAddress = 47;
		const iCityStateZip = 48;
		const iFloor = 49;
	
	// indicate whether to show all phone/email or use Profile for external use
	public $showInfo = true;
	// indicate whether full export so trigger all error messages
	public $fullRun = true;
	// set fileTime of import file
	public $fileTime;
	// set runTime of import file
	public $runTime;
	// set Delta report type
	public $typeDelta;
	// Set was / is ID for DELTA
	public $wasDelta;
	public $isDelta;
	
	// indicate whether to log timings
	public $logging = false;
	public $sTime = 0;


	public function __construct ()
	// opens DB, includes enxryption
	{
		ini_set('max_execution_time', 300); //300 seconds = 5 minutes
		date_default_timezone_set('America/New_York');
		
		//open db
		//open database so the pdo object is available to all functions
		if (! $this->opendb()) {
		return false;
		}
		
		//set up encryption
		include ("PAI_crypt.class.php");
		//get the secret key and nonce not stored in www folders
		if (file_exists("COBfolder.php")) {include ("COBfolder.php");}
		if (!isset($pfolder)) {$pfolder="";}
		require_once ($pfolder . 'COBkey.php');
		$this->paicrypt = new PAI_crypt($COBkey,$COBnonce);
	}
	
   public function __destruct()
    {
        // close db & encryption
		//now delete working db tables and close db
//		$this->DeleteRecords();
		unset($this->pdo);
		unset ($this->paicrypt);
    }

	public function Checkfile(&$checkmsg)
    {
	// called by COBListmenu to upload the file and check for format/size & load to dbUser
		$checkmsg="";
	// fix cases where import is null
		if(!isset($_FILES["import"])) {return false;}
		
		if($_FILES["import"]["error"] > 0){
			$checkmsg =  "Error: " . $_FILES["import"]["error"] . "<br>";
			$checkmsg .=  "Error on upload. Please click the left menu item to re-run";
			return false;
		} 
		
		// get file info
		$filename = $_FILES["import"]["name"];
		$filetype = $_FILES["import"]["type"];
		$filesize = $_FILES["import"]["size"];
		$tempfile = $_FILES["import"]["tmp_name"];
	
		// Verify file extension
		$ext = pathinfo($filename, PATHINFO_EXTENSION);
		if(!($ext == "csv")) {
			$checkmsg =  $filename . " Error: Please select a CSV file format.";
			return false;
		} 
		// Verify file size - 1MB maximum
		$maxsize = 1 * 1024 * 1024;
		if($filesize > $maxsize) {
			$checkmsg =  "Error: Please select a smaller CSV file.";
			return false;
		} 
		$temp = file($tempfile);
		
		// convert to 2 dimensional array
		foreach ($temp as $line) {
			$this->dbUser[] = str_getcsv($line);
		}		

		// check if exported from website with correct format
		if (!(trim($this->dbUser[0][0])=="Created")) {
			$checkmsg =  "Error: '" . strlen($this->dbUser[0][0]) . "' Not valid exported CSV file.";
			return false;
		}
		if (!(count($this->dbUser[0])==45)) {
			$checkmsg =  "Error: " . count($this->dbUser[0]) . " Not valid exported CSV file.";
			return false;
		}

		return true;
	// end of Checkfile
	}
	
	public function ProcessFile(&$checkmsg)
	{
		// Called by COBListmenu to process dbUser and build all worksheets
		// BuildMainArrays
		//		GetVoter
		//		GetAddress
		//		GetFloor
		//		GetEmail
		//		GetLeaseDates
		//		GetBestPhone
		// BuildVoterHdr
		// CheckData
		//		CheckPhoneFormat
		//		CheckUnitMaster
		// BuildUnit
		//		GetFullUnit
		//		GetBestPhone
		//		GetEmail
		// BuildListingHdr
		// BuildStaffHdr
		// BuildErrHdr
		// BuildGrids
		// BuildSlip
		//		IsAllRented
		//			GetFullUnit
		//		GetBestPhone
		//		GetEmail
		// CreateXLFile
		// WriteSKResponse

		//setup start time logging
		$this->sTime = microtime(true);

		if ($this->logging){$this->addError("T00", "ProcessFile", $this->timeRun($this->sTime),"","");}
		if ($this->fullRun) {
			// delete all records from UserUnit, Slips & WaitList table
			$this->DeleteRecords();
		}
		if ($this->logging){$this->addError("T10", "DB Clean", $this->timeRun($this->sTime),"","");}

		// remove header 
		$this->hdrUser = array_shift($this->dbUser);

		// extend header for userid, voter, addr, city, floor
		$this->hdrUser[] = "UserID";
		$this->hdrUser[] = "Voter";
		$this->hdrUser[] = "Address";
		$this->hdrUser[] = "CityStateZip";
		$this->hdrUser[] = "Floor";

		//header flipped set to strings
		$keys = array_keys(array_flip($this->hdrUser));
		$this->hdrUser = array_fill_keys($keys, "string");

		// now build proper address fields, floor, etc. & other arrays like Renter, Pets, etc
		$this->BuildMainArrays();
		$this->BuildVoterHdr();

		// now checkdata for errors
		$this->CheckData();
		if ($this->logging){$this->addError("T20", "Start BuildUnit", $this->timeRun($this->sTime),"","");}
		$this->BuildUnit();

		if ($this->fullRun) {
			// now build other arrays
			$this->BuildListingHdr();
			if ($this->logging){$this->addError("T40", "Start Staff", $this->timeRun($this->sTime),"","");}
			$this->BuildStaffHdr();
			$this->BuildErrHdr();
			if ($this->logging){$this->addError("T50", "Start Grids", $this->timeRun($this->sTime),"","");}
			$this->BuildGrids();
			if ($this->logging){$this->addError("T55", "Start Slip", $this->timeRun($this->sTime),"","");}
			$this->BuildSlip();
			// now create the Slips & Kayak file for future static viewing
			if ($this->logging){$this->addError("T56", "Start SKResponse", $this->timeRun($this->sTime),"","");}
			$this->WriteSKResponse($checkmsg);
		}
		

		//now log this run
		// normal run
		$type = 1;
		// external run
		if (!$this->showInfo) {$type = 2;}
		// partial run
		if (!$this->fullRun) {$type = 3;}
		
		if ($this->logging){$this->addError("T80", "Start Log", $this->timeRun($this->sTime),"","");}
		$logid = $this->LogRun($checkmsg, $type);
		
		// now create Excel file
		if ($this->logging){$this->addError("T90", "Start CreateFile", $this->timeRun($this->sTime),"","");}
		$this->CreateXLFile();
		
		// comment line below to test db's
//		$this->DeleteRecords();
		
		return true;
	// end of ProcessFile
	}

	public function RunDelta(&$checkmsg)
	{
		// run delta to create JSON or XLSX of adds, changes, deletes
		//build response
		$result = $this->BuildDeltaResponse();
		
		// now close up
		$checkmsg = "Report complete";

		return $result;
	// end of RunDelta
	}

	public function CompressRunBlob($logid)
	{
		// given a logid change RunLog to type 4 and delete RunBlog records
		// first delete RunBlob records
		$stmt = $this->pdo->query('DELETE FROM RunBlob WHERE logid = ' . $logid);

		// then update RunLog
		$stmt = $this->pdo->query('UPDATE `RunLog` SET `type`= 4 WHERE logid = ' . $logid);
		
		return ;
	// end of CompressRunBlob
	}
	
	public function BlobExport($limit,$offset)
	{
		// first select all from RunBlob as array
		$sql = "SELECT * FROM `RunBlob` LIMIT :limit OFFSET :offset";
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute(['limit'=>$limit,'offset'=>$offset]);
		// now loop each row and decrypt Blob 
		while ($row = $stmt->fetch()) {
			$row['userdata'] = $this->PAIDecrypt($row['userdata']);
			$rows[] = $row;
		}		
		return (json_encode($rows));
	
	// end of BlobExport
	}

	public function BlobImport()
	{
		//read json into array
		// encrypt userdata
		// add to RunBlob without auto increment
	// end of BlobImport
	}

	public function GetRuns(&$checkmsg)
	{
		// queries the RunLog table and returns array of logid & filetime 
		$sql = "SELECT logid, filetime, ip FROM RunLog WHERE type = 1 OR type = 5 ORDER BY runtime desc";
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute();
		$result = $stmt->fetchALL(PDO::FETCH_ASSOC);
		
		return $result;
	// end of GetRuns
	}
	
	public function GetRates(&$checkmsg)
	{
		// queries the RateMaster table and returns array of rate class For Master
		$sql = "SELECT class FROM RateMaster ORDER BY class";
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute();
		$result = $stmt->fetchALL(PDO::FETCH_ASSOC);
		
		return $result;
	// end of GetRates
	}
	
	public function CheckUserID($userid,&$checkmsg)
	{
		// returns true if latest record for userid in RunBlob is Admin
		$sql = "SELECT * FROM `RunBlob` WHERE userid = ? order by logid desc limit 1" ;
		try {
			$stmt = $this->pdo->prepare($sql);
			} catch (PDOException $e) {
			$checkmsg = 'Prepare failed: ' . $e->getMessage();
			error_log($checkmsg);
			return false;
		}
		$stmt->execute([trim($userid)]);
		$result = $stmt->fetch();
		if ($result['adminaccess']) {
			return true;
		} else {
			return false;
		}
	// end of CheckUserID		
	}
	
// ------ all public functions above -------------------------------
	
	function CheckData() {
	//test for errors
	//Unit format wrong, Stack mismatch, Access mismatch, Phone format, 2nd Address format, Emergency contact phone, Owner access, Mailing blank
	//Offical Voter missing or duplicate
	// Added check in Gone/Sold

	//step thru User array
	foreach ($this->dbUser as $rowData) {

		if (stripos($rowData[self::iUnit],'gone') !== false) {

//		if (!(stripos($rowData[self::iUnit],'gone') === false)) {
			// check if Enabled
			if(!($rowData[self::iEnabled] == "No")){ 
				$this->addError('5','Moved & enabled',$rowData[self::iUnit],$rowData[self::iUser1LastName],'Moved but enabled');
			}
			// check if Access Public
			if(!(strcasecmp($rowData[self::iAccess],"PUBLIC")==0)){ 
				$this->addError('5','Not PUBLIC',$rowData[self::iUnit],$rowData[self::iUser1LastName],'Moved not Access PUBLIC');
			}
			// check if holdall email
			if((stripos($rowData[self::iEmail],"holdall@gmx")) === false){ 
				$this->addError('5','Wrong email',$rowData[self::iUnit],$rowData[self::iUser1LastName],'Moved email not holdall');
			}
		} elseif ((stripos($rowData[self::iUnit],'sold') !== false)) {
			// check if Enabled
			if(!($rowData[self::iEnabled] == "No")){ 
					$this->addError('5','Moved & enabled',$rowData[self::iUnit],$rowData[self::iUser1LastName],'Moved but enabled');
			}
			// check if Access Public
			if(!(strcasecmp($rowData[self::iAccess],"PUBLIC")==0)){ 
					$this->addError('5','Not PUBLIC',$rowData[self::iUnit],$rowData[self::iUser1LastName],'Moved not Access PUBLIC');
			}
			// check if holdall email
			if((stripos($rowData[self::iEmail],"holdall@gmx")) === false){ 
					$this->addError('5','Wrong email',$rowData[self::iUnit],$rowData[self::iUser1LastName],'Moved email not holdall');
			}
		} else {
			// check if not Enabled
			if(($rowData[self::iEnabled] == "No") && (stripos($rowData[self::iEmail],"holdall@gmx")) === false){ 
					$this->addError('8','Not enabled',$rowData[self::iUnit],$rowData[self::iUser1LastName],'Has email but not enabled');
			} 
			// check 2nd address format
			if (count(explode(',',$rowData[self::i2ndAddress]))!==2 && strlen(trim($rowData[self::i2ndAddress])) > 0 ){
				$this->addError('10','2ndAddress format',$rowData[self::iUnit],$rowData[self::iUser1LastName],$rowData[self::i2ndAddress]);
			}
			//check if T1 Pet doesn't have ESA or WSD-ESA
			if (strlen($rowData[self::iPets]) > 0) {
				if (preg_match("/(Tower 1)/", $rowData[self::iUnit])) {
					if (!preg_match("/(WSD)|(ESA)/", $rowData[self::iPets])) {
						$this->addError('6','Missing WSD/ESA',$rowData[self::iUnit],$rowData[self::iUser1LastName],'Need WSD/ESA info');
					}
				}
			}
			//check missing Emergency Contact
			if ($rowData[self::iEmergencyContact] == "") {
				$this->addError('19','Emergency contact',$rowData[self::iUnit],$rowData[self::iUser1LastName],'No Emergency Contact');
			}
			//check missing email address
			if ($rowData[self::iEmail] == "" ){
				$this->addError('2','Email address',$rowData[self::iUnit],$rowData[self::iUser1LastName],'No email address');
			}
			//check phone format
			if(!$this->CheckPhoneFormat($rowData[self::iHomePhone]))
			{
				$this->addError('12','Phone Format - Home',$rowData[self::iUnit],$rowData[self::iUser1LastName],$rowData[self::iHomePhone]);
			}
			if(!$this->CheckPhoneFormat($rowData[self::iUser1CellPhone]))
			{
				$this->addError('12','Phone Format - Cell',$rowData[self::iUnit],$rowData[self::iUser1LastName],$rowData[self::iUser1CellPhone]);
			}
			if(!$this->CheckPhoneFormat($rowData[self::iUser1WorkPhone]))
			{
				$this->addError('12','Phone Format - Work',$rowData[self::iUnit],$rowData[self::iUser1LastName],$rowData[self::iUser1WorkPhone]);
			}
			
			//check unit format & add owner/voter fields for easy access
			$this->CheckUnitMaster($rowData);
		}
	} 
	//check owner count in UnitMaster but not #115 since it is owned by Association
	$query1 = $this->pdo->prepare("select unit, count(unit) as owners FROM `UserUnit` where owner = 'yes' group by unit having count(unit) = 0");
	$query1->execute();
	while ($row = $query1->fetch(PDO::FETCH_ASSOC)) {
		$this->addError('1','No unit owner!',$row['unit'],$row['owners'],'');
		}
	$query1 = $this->pdo->prepare("select unit, count(unit) as owners FROM `UserUnit` where owner = 'yes' group by unit having count(unit) > 2");
	$query1->execute();
	while ($row = $query1->fetch(PDO::FETCH_ASSOC)) {
		$this->addError('16','Owner Count',$row['unit'],$row['owners'],'');
		}

		//check missing Voter in UnitMaster
	$query1 = $this->pdo->prepare("select unit, count(unit) as voters FROM `UserUnit` where voter = 'yes' group by unit having count(unit) = 0");
	$query1->execute();
	while ($row = $query1->fetch(PDO::FETCH_ASSOC)) {
		$this->addError('4','Missing Voter',$row['unit'],$row['voters'],'Check Official Voter');
		}


	//end of CheckData
	}
	
	function BuildMainArrays()
	{
	// loop thru db and build calculated fields like addr, voter, floor
	// also build Resident Listing, Staff, Sold, Gone, and Renter arrays
	foreach ($this->dbUser as &$rowData) {
	//skip if gone/sold or staff
		if (stripos($rowData[self::iUnit],'gone') !== false) {
			//build gone db
			$this->dbGone[]=$rowData;
			//keep columns in sync for Voter, Address, CityStateZip, Floor
			$rowData[] = "";
			$rowData[] = "";
			$rowData[] = "";
			$rowData[] = "";
		} elseif (stripos($rowData[self::iUnit],'sold') !== false) {
			//build sold db
			$this->dbSold[]=$rowData;
			//keep columns in sync for Voter, Address, CityStateZip, Floor
			$rowData[] = "";
			$rowData[] = "";
			$rowData[] = "";
			$rowData[] = "";
		} elseif
			(((stripos($rowData[self::iAccess],'A') !== false) &&
			(stripos($rowData[self::iAccess],'^ADMINWM') == false))||
			($rowData[self::iAccess]=='ADMINEM'))
			{
			// build staff db
			//write row - 6,3,4,5
			$this->dbStaff[]=array($rowData[6],$rowData[3],$rowData[4],$rowData[5],$rowData[45]);
			//keep columns in sync for Voter, Address, CityStateZip, Floor
			$rowData[] = "";
			$rowData[] = "";
			$rowData[] = "";
			$rowData[] = "";
		} else {
			// first get voter for this record and add a column
			$rowData[]= $this->GetVoter($rowData);
			
			// then build addr, citystate for mailings based on user settings
			$temp = $this->GetAddress($rowData);
			$rowData[] = $temp[0];
			$rowData[] = $temp[1];
			
			// then get floor for all units in this row
			$rowData[] = $this->GetFloor($rowData[self::iUnit]);
			$temp[0]="";
			$temp[1]="";			
			// copy certain fields if current renter to dbRenter
			if ($rowData[self::iAccess] == "MEMBER") {
				// If not a Partner add to dbRenter
				$temp = $this->GetLeaseDates($rowData);
				if ($rowData[self::iOfficialVoter] !== "Partner") {
					//write row - GetLeaseDates(38),6,3,8,10,4,33,34
					$this->dbRenter[]=array(
						$temp[1], $temp[0],
						$rowData[7],$rowData[6],$rowData[3],$rowData[8],
						$rowData[10],$this->GetEmail($rowData[4]),$rowData[33],$rowData[34]
					);
					if ($rowData[self::iOwner] == "Yes") {
						$this->addError('1','Owner Error',$rowData[self::iUnit],$rowData[self::iUser1LastName],'Owner with only member access');
					}
					// check if expired lease temp[1]
					if ($temp[1] < date('Y-m-d')) {
						$this->addError('2','Lease Expired',$rowData[self::iUnit],$rowData[self::iUser1LastName],'This unit lease expired');
					}
					
				}
			}	
			// If temp[1] empty then check IsAllRented
			if ($temp[1]=="") {
				if ($this->IsAllRented($rowData[self::iUnit])) {
					$temp[1]= "AllRented";
				}
			}
			// copy current resident user1 to dbRes if not already there
			if (!$this->IsIndbRes($rowData[6],$rowData[3],$rowData[7],$rowData[4],$rowData[8],$rowData[10])){
				//write row - 6,3,7,30, 8,10,4,33,5,LeaseEnd from above,34
				$this->dbRes[]=array(
					$rowData[6],$rowData[3],$rowData[7],$rowData[30],$rowData[8],
					$rowData[10],$rowData[4],$rowData[33],$rowData[5],$temp[1],$rowData[34]
				);
			}	
			// see if there is a User2
			if (strlen(trim($rowData[15])) > 0) {
				// now see if user 2 is already in dbRes before adding
				if (!$this->IsIndbRes($rowData[15],$rowData[14],$rowData[7],$rowData[16],$rowData[17],$rowData[18])){
					$this->dbRes[]=array(
						$rowData[15],$rowData[14],$rowData[7],$rowData[30],$rowData[17],
						$rowData[18],$rowData[16],$rowData[33],$rowData[5],$temp[1],$rowData[34]
					);
				}
			}
			// copy certain fields to dbPets
			if (strlen(trim($rowData[self::iPets])) > 0) {
				//write row - 7,6,3,37,8 or 10,4,33,34
				// decide if include email and phone based on user Profile settings if showInfo property is set = false
				$phone = "";
				$email = "";
				if ($this->showInfo) {
					$phone = $this->GetBestPhone($rowData);
					$email = $this->GetEmail($rowData[4]);
				} elseif ($rowData[self::iShowProfile]=='Yes') {
						if($rowData[self::iShowPhone]=='Yes') {
							$phone = $this->GetBestPhone($rowData);
						}
						if($rowData[self::iShowEmail]=='Yes') {
							$email = $this->GetEmail($rowData[4]);
						}
					}

				$this->dbPets[]=array(
					$rowData[7],$rowData[6],$rowData[3],$rowData[37], $phone,
					$email,$rowData[33],$rowData[34]
				);
			}
		}
	}
	return;
	// end of BuildMainArrays
	}

	function BuildVoterHdr ()
	{
		$this->hdrVoter = array('Bldg'=>'string','Unit'=>'string','LastName'=>'string','FirstName'=>'string', 'Address'=>'string','CityStateZip'=>'string');

	}
	function BuildListingHdr()
	{
		//setup Listing columns for dbRes & dbRenter that was build in BuildAddress
		$this->hdrRes = array('Last Name'=>'string', 'First Name'=>'string','Unit'=>'string','Owner'=>'string','Home Phone'=>'string','Cell Phone'=>'string','Email'=>'string','Emergency Contact'=>'string','Access'=>'string','Lease End'=>'string','Unit Watcher'=>'string');
		$this->hdrRenter = array('Lease End'=>'string', 'Lease Start'=>'string', 'Unit'=>'string','Last Name'=>'string', 'First Name'=>'string','Home Phone'=>'string','Cell Phone'=>'string','Email'=>'string','Emergency Contact'=>'string','Unit Watcher'=>'string');
		$this->hdrPets = array('Unit'=>'string','Last Name'=>'string', 'First Name'=>'string','Pets & WSD/ESA'=>'string','Phone'=>'string','Email'=>'string','Emergency Contact'=>'string','Unit Watcher'=>'string');
		return;
	}

	function BuildStaffHdr()
	{
		//setup columns for dbStaff that was build in BuildMainArrays
		$this->hdrStaff=array('Last Name'=>'string', 'First Name'=>'string','Email'=>'string','Access'=>'string','UserID'=>'string');
		return;
	}

	function BuildErrHdr()
	{
		//setup Error columns for dbErr
		$this->hdrErr = array('Level'=>'string', 'Function'=>'string','Unit'=>'string','Name'=>'string','Message'=>'string');
		return;
	}

	function BuildSlip() {
		// scan dbUser to build Slips and Kayak db and arrays
		// build header
		$temp = ($this->showInfo) ? 'Internal':'External';
		$this->hdrSlip = array('Dock'=>'string', 'Slip'=>'string','Class'=>'string','Rate'=>'string','Type'=>'string','Condition'=>'string', 'Name'=>'string','Unit'=>'string','Tenure'=>'string','Limbo'=>'string','Phone'=>'string','Email'=>'string', 'Emergency' => 'string');
		$this->hdrSlip2 = array('Dock'=>'string', 'Rack'=>'string','Class'=>'string','Rate'=>'string','Type'=>'string','Condition'=>'string', 'Name'=>'string','Unit'=>'string','Tenure'=>'string','Trained'=>'string','Phone'=>'string','Email'=>'string', 'Emergency'=>'string');
		$this->hdrWait = array('Date'=>'string','Owner'=>'string','Name'=>'string','Unit'=>'string','Category'=>'string','Phone'=>'string','Email'=>'string');
		$this->hdrWait2 = array('Date'=>'string','Owner'=>'string','Name'=>'string','Unit'=>'string','Number'=>'string','Phone'=>'string','Email'=>'string');
		
		// step thru each line of the file
		foreach ($this->dbUser as $row) {
			// skip if gone or sold
			if (stripos($row[7],"GONE")!== false) {
				continue;
			}
			if (stripos($row[7],"SOLD")!== false) {
				continue;
			}
			// if no slip then skip
			if (empty($row[36])){
				continue;
			}
			// if no unit with a slip then error & skip
			if (empty($row[7])){
				$this->addError('1','No Unit',$row[3] . ' ' . $row[6],$row[36],'Has slip but no unit');
				continue;
			}
			// check if owner has rented their unit and show error 
			// check only if this user is an owner
			if (stripos($row[self::iOwner],"yes")!== false) {			
				// check only if not just on waitlist or MS
				if (substr($row[36],0,1) !== 'W' && substr($row[36],0,1) !== '9') {
					// pass unit to IsAllRented
					if ($this->IsAllRented($row[7])) {
						//True log error
						$this->addError('1','Rented Error',$row[7],'Owner rented unit','Cancel kayak/slip lease ');
					}
				}
			}
			// check if comma in slip and error
			if (stripos($row[36],",")!== false) {
				//log error
				$this->addError('1','Slip format',$row[7],'$row[36]','Slip field has comma not semicolon');
			} else {
				// explode multiple slips
				$temp = explode (';',$row[36]);
				//step thru each slip
				foreach ($temp as $slip) {
					// check if waitlist and add to waitlist
					if (stripos($slip,"W")!== false) {
						// now add to WaitList table
						$wdate = date("Y.m.d",strtotime(substr(trim($slip),2,8)));
						$wnum = "1";
						if (strlen(trim($slip))== 12) {
							$wnum = substr(trim($slip),11,1);
						} 
						// decide if include email and phone based on user Profile settings if showInfo property is set = false
						$phone = "";
						$email = "";
						if ($this->showInfo) {
							$phone = $this->GetBestPhone($row);
							$email = $this->GetEmail($row[self::iEmail]);
						} elseif ($row[self::iShowProfile]=='Yes') {
								if($row[self::iShowPhone]=='Yes') {
									$phone = $this->GetBestPhone($row);
								}
								if($row[self::iShowEmail]=='Yes') {
									$email = $this->GetEmail($row[self::iEmail]);
								}
							}

						$sql = "INSERT INTO WaitList (type,unit, names, owner, date,  number, phone, email,userid)
								VALUES (:type,:unit,:names,:owner,:date,:number,:phone,:email,:userid)";
						$valarray = array(
								"type" => substr(trim($slip),1,1), 
								"unit" => $row[7], 
								"names" => $row[3] . ' ' . $row[6],
								"owner" => $row[30],
								"date" => $wdate,
								"number" => $wnum,
								"phone" => $phone,
								"email" => $email,
								"userid" => $row[45]
								);
								
						$statement = $this->pdo->prepare($sql);
						$statement->execute($valarray);
						
					} else {
						// if slip has L or T then strip off and set limbo  = true
						if (preg_match("/[TL]/", $slip)){
							$limbo = 1;
							if (preg_match("/[L]/", $slip)){
								$this->addError('1','Slip in Limbo',$row[self::iUnit],$slip,'Waiting to be vacated');
							}
							$slip = substr_replace(trim($slip) ,"",-1);
						} else {
							$limbo = 0;
						}
						// decide if include email and phone based on user Profile settings if showInfo property is set = false
						$phone = "";
						$email = "";
						if ($this->showInfo) {
							$phone = $this->GetBestPhone($row);
							$email = $this->GetEmail($row[self::iEmail]);
						} elseif ($row[self::iShowProfile]=='Yes') {
								if($row[self::iShowPhone]=='Yes') {
									$phone = $this->GetBestPhone($row);
								}
								if($row[self::iShowEmail]=='Yes') {
									$email = $this->GetEmail($row[self::iEmail]);
								}
							}
						// split slip at colon for tenure and error if no tenure
						if (stripos($slip,":")!== false) {
							$temp = explode (':',$slip);
							$slip = $temp[0];
							if (strlen($temp[1]) == 8) {
								$tenure = date("Y.m.d",strtotime($temp[1]));
							} else {
								$this->addError('7','Slip tenure',$row[self::iUnit],$temp[1],'Slip tenure format YYYYMMDD');
								$tenure = null;
							}	
						} else {	
							//missing tenure if N or S
							if (preg_match("/[NS]/", $slip)){
								if (preg_match("/[a-f]/", $slip)){
									$this->addError('21','No Rack tenure',$row[self::iUnit],$slip,'Rack tenure as :YYYYMMDD');
								} else {
									$this->addError('14','No Slip tenure',$row[self::iUnit],$slip,'Slip tenure as :YYYYMMDD');
								}
							}
							$tenure = null;
						}

						//Check if this slip is in SlipMaster report error
						$sql = "SELECT * FROM SlipMaster WHERE slipid = ?" ;
						$stmt = $this->pdo->prepare ($sql);
						$stmt->execute([trim($slip)]);
						$result = $stmt->fetch();
						if (!$result) {
							//found slip doesn't exists so report error
							$this->addError('1','Slip not found',$row[self::iUnit],$slip,'Slip not in SlipMaster');
						}
					
						//Check if this slip already assigned to this unit and report error
						$sql = "SELECT * FROM Slips WHERE slipid = ?" ;
						$stmt = $this->pdo->prepare ($sql);
						$stmt->execute([trim($slip)]);
						$result = $stmt->fetch();
						if ($result) {
							//found slip exists so report error
							$this->addError('1','Slip error',$row[self::iUnit],$result["unit"],'Double booked');
						} 
					
						//setup SQL statement for insert to Slips table
						$sql = "INSERT INTO Slips (unit, names, slipid, tenure, limbo, phone, email,emergency, userid)
							VALUES (:unit, :names, :slipid, :tenure, :limbo, :phone, :email,:emergency,:userid)";
						$valarray = array(
								"unit" => $row[7], 
								"names" => $row[3] . ' ' . $row[6],
								"slipid" => trim($slip),
								"limbo" => $limbo,
								"tenure" => $tenure,
								"phone" => $phone,
								"email" => $email,
								"emergency" => $row[self::iEmergencyContact],
								"userid" => $row[45]
								);
						$statement = $this->pdo->prepare($sql);
						$statement->execute($valarray);
						
					}
						
				}
			}
		}
		//now query to dbSlip
		$query1 = $this->pdo->prepare("SELECT dock, b.slipid, b.class, FORMAT(rate,0), b.type, b.scondition, a.names, unit, tenure, limbo, phone, email, emergency
							FROM SlipMaster b
							LEFT OUTER JOIN Slips a ON a.slipid = b.slipid
							JOIN RateMaster c ON b.class = c.class
							WHERE b.type = 'Slip' ORDER BY b.slipid");
		$query1->execute();
		$this->dbSlip = $query1->fetchALL(PDO::FETCH_ASSOC);
		
				
		//now add waitlist info at bottom of dbSlip array
		$this->dbSlip[]=array("");
		$this->dbSlip[]=array("Slip Wait List " . date('Ymd'));
		$this->dbSlip[]=array_keys($this->hdrWait);
		$query1 = $this->pdo->prepare("SELECT date, owner, names, unit, number, phone, email FROM WaitList where type = 'S' ORDER BY date");
		$query1->execute();
		$this->dbSlip = array_merge($this->dbSlip,$query1->fetchALL(PDO::FETCH_ASSOC));
		
		
		//now query to dbKayak
		$query1 = $this->pdo->prepare("SELECT dock, b.slipid, b.class, rate, b.type, b.scondition, a.names, unit, tenure, limbo, phone, email, emergency 
							FROM SlipMaster b
							LEFT OUTER JOIN Slips a ON a.slipid = b.slipid
							JOIN RateMaster c ON b.class = c.class
							WHERE b.type = 'Kayak' ORDER BY b.slipid");
		$query1->execute();
		$this->dbKayak = $query1->fetchALL(PDO::FETCH_ASSOC);
		$this->dbKayak[]=array("");
		$this->dbKayak[]=array("Kayak Wait List " . date('Ymd'));
		$this->dbKayak[]=array_keys($this->hdrWait2);
		$query1 = $this->pdo->prepare("SELECT date, owner, names, unit, number, phone, email FROM WaitList where type = 'K' ORDER BY date");
		$query1->execute();
		$this->dbKayak = array_merge($this->dbKayak,$query1->fetchALL(PDO::FETCH_ASSOC));

		//now add errors for vacant rack
		$sql = "SELECT dock, b.slipid, unit
							FROM SlipMaster b
							LEFT OUTER JOIN Slips a ON a.slipid = b.slipid
							WHERE b.type = 'Kayak' AND unit is null ORDER BY b.slipid";
		$query1 = $this->pdo->prepare($sql);
		$query1->execute();
		$result = $query1->fetchALL(PDO::FETCH_ASSOC);
		// now loop each row and add error 
		foreach ($result as $temp) {
			$this->addError('1','Vacant Rack',$temp['slipid'],'Fill rack','Fill from waitlist');
		}
			
		return;
		// end of BuildSlip
	}

	function BuildGrids()
	{
		//Build the resident grid
		//setup grid headers
		$this->hdrGridT1 = array("Floor"=>"string","Rembrandt-1"=>"string", "Monet-2"=>"string", "Renoir-3"=>"string", "Van Gogh-4"=>"string", "Van Gogh-5"=>"string", "Renoir-6"=>"string", "Monet-7"=>"string", "Cezanne-8"=>"string");

		$this->hdrGridT2 = array("Floor"=>"string","Rembrandt-9"=>"string", "Renoir-10"=>"string", "Renoir-11"=>"string", "Van Gogh-12"=>"string", "Van Gogh-14"=>"string", "Renoir-15"=>"string", "Renoir-16"=>"string", "Rembrandt-17"=>"string");

		// check for units with no Owner
		$sql = "Select t3.unit, lastname, floor, stack from UnitMaster t3  left join (SELECT unit, lastname FROM UserUnit where owner = 'yes'  group by unit, lastname) t1 on t3.unit = t1.unit where lastname is null and t3.unit != 'Tower 2 # 115' order by floor asc, stack asc;";
		$query1 = $this->pdo->prepare($sql);
		$query1->execute();
		// now loop thru all units w/o owners and error
		while ($row = $query1->fetch(PDO::FETCH_ASSOC)) {
			$this->addError('1','Owner Error',$row['unit'],'Unit has no owner','Missing owner');
		}

		
		//first do owners
		$sql = "Select t1.unit, lastname, floor, stack from UnitMaster t3  left join (SELECT unit, lastname FROM UserUnit where owner = 'yes'  group by unit, lastname) t1 on t3.unit = t1.unit order by floor desc, stack asc";
		$query1 = $this->pdo->prepare($sql);
		$query1->execute();
		// now loop thru all units allowing for no 13th floor and T1/T2 change
		while ($row = $query1->fetch(PDO::FETCH_ASSOC)) {
			$fl = intval($row['floor']);
			$st = intval($row['stack']);
			$this->dbGridT1[$fl][0] = $row['floor'];
			$this->dbGridT2[$fl][0] = $row['floor'];
			if ($st<=8) {
				//add owners to front of string 
				if (isset($this->dbGridT1[$fl][$st])) {
					$r = $row['lastname'] ."/" . $this->dbGridT1[$fl][$st];
				} else {
					$r = $row['lastname'] ;
				}
				$this->dbGridT1[$fl][$st] = $r;
			} elseif ($st<=17){
				//add owners to front of string
				if (isset($this->dbGridT2[$fl][$st])) {
					$r = $row['lastname'] ."/" . $this->dbGridT2[$fl][$st];
				} else {
					$r = $row['lastname'] ;
				}
				$this->dbGridT2[$fl][$st] = $r;
				if ($fl==1 AND $st==12) {$this->dbGridT2[1][14]="None";} // no#114
			}
		}

		//then do renters
		$sql = "Select t1.unit, lastname, floor, stack from UnitMaster t3 join (SELECT unit, lastname FROM UserUnit where owner = 'no'  group by unit, lastname) t1 on t3.unit = t1.unit order by floor desc, stack asc";
		$query1 = $this->pdo->prepare($sql);
		$query1->execute();
				// now loop thru all units allowing for no 13th floor and T1/T2 change
		while ($row = $query1->fetch(PDO::FETCH_ASSOC)) {
			$fl = intval($row['floor']);
			$st = intval($row['stack']);
			$r="";
			if ($st<=8) {
				//add renters in () to back of string
				if (isset($this->dbGridT1[$fl][$st])) {
					$r = " (" . $row['lastname'] . ")";
				} else {
					$r = "(" . $row['lastname'] . ")";
				}
				$this->dbGridT1[$fl][$st] .= $r;
			} elseif ($st<=17){
				//add renters in () to back of string
				if (isset($this->dbGridT2[$fl][$st])) {
					$r = " (" . $row['lastname'] . ")";
				} else {
					$r = "(" . $row['lastname'] . ")";
				}
				$this->dbGridT2[$fl][$st] .= $r;
			}
		}
	// end of BuildGrids
	}
	
	function BuildUnit() {
		// scan dbUser to build UnitUser db and arrays
		// build header
		$temp = ($this->showInfo) ? 'Internal':'External';
		$this->hdrUnit = array('Unit'=>'string', 'Owner'=>'string', 'Voter'=>'string','Lastname'=>'string','Firstname'=>'string','Email'=>'string','Cellphone'=>'string', 'Homephone'=>'string','Space'=>'string','Emergency Contact'=>'string','Unit Watcher'=>'string', 'Address'=>'string', 'CityStateZip'=>'string');
		
		// step thru each line of the file
		foreach ($this->dbUser as $row) {
			// skip if gone or sold
			if (stripos($row[7],"GONE")!== false) {
				continue;
			}
			if (stripos($row[7],"SOLD")!== false) {
				continue;
			}
			// if no unit then skip
			if (empty($row[self::iUnit])){
				continue;
			}
			// explode multiple units
			if ($temp = $this->GetFullUnit($row[self::iUnit])) {
				foreach ($temp as $unit) {
				// now add to UnitUser table
				// insert User1 if not already in table
				// decide if include email and phone based on user Profile settings if showInfo property is set = false
				$cphone = "";
				$hphone = "";
				$email = "";
				if ($this->showInfo) {
					$hphone = $row[self::iHomePhone];
					$cphone = $row[self::iUser1CellPhone];
					$email = $this->GetEmail($row[self::iEmail]);
				} elseif ($row[self::iShowProfile]=='Yes') {
						if($row[self::iShowPhone]=='Yes') {
							$hphone = $row[self::iHomePhone];
							$cphone = $row[self::iUser1CellPhone];
						}
						if($row[self::iShowEmail]=='Yes') {
							$email = $this->GetEmail($row[self::iEmail]);
						}
				}
				if ($row[self::iVoter] == $row[self::iFirstName] . " " . $row[self::iUser1LastName]) {
					$v = "Yes";
				} else {
					$v = "No";
				}
				// insert if not already in table
				if (!$this->IsInUserUnit($unit, $row[6], $row[3], $email)) {
					$sql = "INSERT INTO UserUnit (unit, owner, voter, lastname, firstname, email, cellphone, homephone, emergency, unitwatcher, address, citystatezip, userid)
							VALUES (:unit, :owner, :voter,:lname, :fname, :email, :cell, :home, :emer, :watch, :address, :citystatezip, :userid)";
					// execute the SQL statement
					$stmt = $this->pdo->prepare ($sql);
					if ($stmt->execute(array(
						"unit" => $unit,
						"owner" => $row[30],
						"voter" => $v,
						"lname" => $row[6],
						"fname" => $row[3],
						"email" => $email,
						"cell" => $cphone,
						"home" => $hphone,
						"emer" => substr($row[33],0,50),
						"watch" => substr($row[34],0,50),
						"address" => $row[47],
						"citystatezip" => $row[48],
						"userid" => $row[45]
						))) {
					} else {
						error_log ("Failed " . $unit );
					}
				}
				// insert User2 if exists
				if (strlen($row[self::iUser2LastName])>0 ){
					// decide if include email and phone based on user Profile settings if showInfo property is set = false
					$cphone = "";
					$email = "";
					if ($this->showInfo) {
						$cphone = $row[self::iUser2CellPhone];
						$email = $this->GetEmail($row[self::iUser2Email]);
					} elseif ($row[self::iShowProfile]=='Yes') {
							if($row[self::iShowPhone]=='Yes') {
								$cphone = $row[self::iUser2CellPhone];
							}
							if($row[self::iShowEmail]=='Yes') {
								$email = $this->GetEmail($row[self::iUser2Email]);
							}
					}
					if ($row[self::iVoter] == $row[self::iUser2FirstName] . " " . $row[self::iUser2LastName]) {
						$v = "Yes";
					} else {
						$v = "No";
					}
					if (!$this->IsInUserUnit($unit, $row[self::iUser2LastName], $row[self::iUser2FirstName], $email)) {
						$sql = "INSERT INTO UserUnit (unit, owner, voter, lastname, firstname, email, cellphone, homephone, emergency, unitwatcher, address, citystatezip, userid)
								VALUES (:unit, :owner, :voter,:lname, :fname, :email, :cell, :home, :emer, :watch, :address, :citystatezip, :userid)";
						// execute the SQL statement - if returns fail then report
						$stmt = $this->pdo->prepare ($sql);
						if ($stmt->execute(array(
							"unit" => $unit,
							"owner" => $row[30],
							"voter" => $v,
							"lname" => $row[self::iUser2LastName],
							"fname" => $row[self::iUser2FirstName],
							"email" => $email,
							"cell" => $cphone,
							"home" => $hphone,
							"emer" => substr($row[33],0,50),
							"watch" => substr($row[34],0,50),
							"address" => $row[47],
							"citystatezip" => $row[48],
							"userid" => $row[45]
							))) {
						} else {
							error_log ("Failed " . $unit );
						}
					}
					}
				}	
			}
		}
		//now query to dbUnit
		$query1 = $this->pdo->prepare("SELECT b.unit, a.owner, a.voter, a.lastname, a.firstname, a.email, a.cellphone, a.homephone, b.space, a.emergency, a.unitwatcher, a.address, a.citystatezip
							FROM UnitMaster b
							LEFT OUTER JOIN UserUnit a ON a.unit = b.unit
							ORDER BY b.unit");
		$query1->execute();
		$this->dbUnit = $query1->fetchALL(PDO::FETCH_ASSOC);
		
		// Now check if Owner = No and fill in with LeaseEnd or Partner by checking dbRenter
		foreach ($this->dbUnit as &$row) {
			if ($row['owner'] == "No") {
				if (in_array($row['unit'],array_column($this->dbRenter,2))) {
					$row['owner'] = $this->GetLeaseEnd($row['unit']);
				}
			}
		}
		
		//now query to build dbVoter
		$sql = "SELECT DISTINCT a.bldg, a.unit, b.lastname, b.firstname, b.address, b.citystatezip FROM UnitMaster a left join UserUnit b ON a.unit = b.unit AND b.voter = 'Yes' ORDER BY a.unit";
		$query1 = $this->pdo->prepare($sql);
		$query1->execute();
		$this->dbVoter = $query1->fetchALL(PDO::FETCH_ASSOC);

		//! now check dbVoter for missing voter or multiple voters
		//now add errors for missing voters
		$sql = "SELECT a.bldg, a.unit, b.lastname, b.firstname FROM UnitMaster a left join UserUnit b ON a.unit = b.unit AND b.voter = 'Yes' WHERE b.lastname is null AND a.unit != 'Tower 2 # 115' ORDER BY a.unit ASC";
		$query1 = $this->pdo->prepare($sql);
		$query1->execute();
		$result = $query1->fetchALL(PDO::FETCH_ASSOC);
		// now loop each row and add error 
		foreach ($result as $temp) {
			$this->addError('18','Voter Error',$temp['unit'],'No Voter','Missing voter');
		}
		
		//now check multiple voters
		$sql = "SELECT a.unit, b.lastname, COUNT(*) FROM UnitMaster a left join UserUnit b ON a.unit = b.unit AND b.voter = 'Yes' GROUP BY a.unit ORDER BY `COUNT(*)` DESC";
		$query1 = $this->pdo->prepare($sql);
		$query1->execute();
		$result = $query1->fetchALL(PDO::FETCH_ASSOC);
		// now loop each row and add error if count > 1 
		foreach ($result as $temp) {
			if ($temp['COUNT(*)']>1) {
				$this->addError('18','Voter Error',$temp['unit'],$temp['lastname'],'Multiple voters');
			}
		}
		
		
		//! now check UserUnit for missing owner

		return;
	// end of BuildUnit
	}

	function CreateXLFile() 
	{
		//Creates the XL file from all the arrays
		// Include the required Class file
		include('PAI_xlsxwriter.class.php');
		//decide on filename
		if ($this->fullRun && $this->showInfo) {
			$temp="";
		} elseif ($this->fullRun) {
			$temp = " External";
		} else {
			$temp = " Partial";		
		}
		$filename = "COBList" . date('Ymd') . $temp . ".xlsx";

		header('Content-disposition: attachment; filename="'.XLSXWriter::sanitize_filename($filename).'"');
//		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header("Content-Type: application/octet-stream;");
//		header('Content-Transfer-Encoding: binary');
		header('Pragma: public');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		
		//setup heading row style
		$hstyle = array( 'font'=>'Arial','font-size'=>10,'font-style'=>'bold', 'halign'=>'center', 'border'=>'bottom');
		$h1style = array( 'font'=>'Arial','font-size'=>12,'font-style'=>'bold', 'halign'=>'left', 'border'=>'bottom');
		
		//sort
		sort($this->dbErr);
		sort($this->dbRes);
		sort($this->dbPets);
		sort($this->dbRenter);

		//write header then sheet data and output file
		$writer = new XLSXWriter();
		$writer->setTitle('v' . self::iVersion . ' Exported:'. date('Y-m-d H:i:s',$this->fileTime));
		$writer->setAuthor('Chris Barlow, Pathfinder Associates, Inc. ' . self::iVersion);
		if ($this->fullRun && $this->showInfo) {
			$writer->setColWidths('Errors',array(10,20,30,20,40));
			$writer->writeSheetHeader('Errors',$this->hdrErr,true);
			$writer->writeSheetRow('Errors',array(date('m/d/y'),'Condo on the Bay Error Listing'),$h1style);
			$writer->writeSheetRow('Errors',array(" "));	//blank row
			$writer->writeSheetRow('Errors',array_keys($this->hdrErr),$hstyle);
			$writer->writeSheet($this->dbErr,'Errors',$this->hdrErr,true);
			
			$writer->setColWidths('Listing',array(20,15,20,10,15,15,30,30,30));
			$writer->writeSheetHeader('Listing',$this->hdrRes,true);
			$writer->writeSheetRow('Listing',array(date('m/d/y'),'Condo on the Bay Owner & Renter Listing'),$h1style);
			$writer->writeSheetRow('Listing',array(" "));	//blank row
			$writer->writeSheetRow('Listing',array_keys($this->hdrRes),$hstyle);
			$writer->writeSheet($this->dbRes,'Listing',$this->hdrRes,true);
			
			$writer->setColWidths('Renter',array(12,12,20,15,15,15,15,30,30,30));
			$writer->writeSheetHeader('Renter',$this->hdrRenter,true);
			$writer->writeSheetRow('Renter',array(date('m/d/y'),'Condo on the Bay Renter Listing'),$h1style);
			$writer->writeSheetRow('Renter',array(" "));	//blank row
			$writer->writeSheetRow('Renter',array_keys($this->hdrRenter),$hstyle);
			$writer->writeSheet($this->dbRenter,'Renter',$this->hdrRenter,true);
		}
		if ($this->fullRun)  {
			$writer->setColWidths('Units',array(20,10,10,20,15,30,15,15,10,30,30,30,30));
			$writer->writeSheetHeader('Units',$this->hdrUnit,true);
			$temp = ($this->showInfo) ? 'Internal':'External';
			$writer->writeSheetRow('Units',array(date('m/d/y'),$temp,'Condo on the Bay Unit Listing'),$h1style);
			$writer->writeSheetRow('Units',array(" "));	//blank row
			$writer->writeSheetRow('Units',array_keys($this->hdrUnit),$hstyle);
			$writer->writeSheet($this->dbUnit,'Units',$this->hdrUnit,true);
			
			$writer->setColWidths('Slips',array(12,10,15,10,6,15,20,20,10,6,6,20,40));
			$writer->writeSheetHeader('Slips',$this->hdrSlip,true);
			$writer->writeSheetRow('Slips',array(date('m/d/y'),$temp,'Condo on the Bay Slip Listing'),$h1style);
			$writer->writeSheetRow('Slips',array(" "));	//blank row
			$writer->writeSheetRow('Slips',array_keys($this->hdrSlip),$hstyle);
			$writer->writeSheet($this->dbSlip,'Slips',$this->hdrSlip,true);
			
			$writer->setColWidths('Kayaks',array(12,15,15,10,6,10,20,20,10,6,6,20,40));
			$writer->writeSheetHeader('Kayaks',$this->hdrSlip2,true);
			$writer->writeSheetRow('Kayaks',array(date('m/d/y'),$temp,'Condo on the Bay Kayak Listing'),$h1style);
			$writer->writeSheetRow('Kayaks',array(" "));	//blank row
			$writer->writeSheetRow('Kayaks',array_keys($this->hdrSlip2),$hstyle);
			$writer->writeSheet($this->dbKayak,'Kayaks',$this->hdrSlip2,true);
		}
		if ($this->fullRun && $this->showInfo) {
			$writer->setColWidths('Pets WSD-ESA',array(20,15,15,50,15,30,30,30));
			$writer->writeSheetHeader('Pets WSD-ESA',$this->hdrPets,true);
			$writer->writeSheetRow('Pets WSD-ESA',array(date('m/d/y'),$temp,'Condo on the Bay Pets & WSD/ESA Listing'),$h1style);
			$writer->writeSheetRow('Pets WSD-ESA',array(" "));	//blank row
			$writer->writeSheetRow('Pets WSD-ESA',array_keys($this->hdrPets),$hstyle);
			$writer->writeSheet($this->dbPets,'Pets WSD-ESA',$this->hdrPets,true);
			
			$writer->setColWidths('Staff',array(20,15,40,40,20));
			$writer->writeSheetHeader('Staff',$this->hdrStaff,true);
			$writer->writeSheetRow('Staff',array(date('m/d/y'),'Condo on the Bay Staff Listing'),$h1style);
			$writer->writeSheetRow('Staff',array(" "));	//blank row
			$writer->writeSheetRow('Staff',array_keys($this->hdrStaff),$hstyle);
			$writer->writeSheet($this->dbStaff,'Staff',$this->hdrStaff,true);
			
			$writer->setColWidths('Grid T1',array(10,20,20,20,20,20,20,20,20));
			$writer->writeSheetHeader('Grid T1',$this->hdrGridT1,true);
			$writer->writeSheetRow('Grid T1',array(date('m/d/y'),'Condo on the Bay T1 Grid'),$h1style);
			$writer->writeSheetRow('Grid T1',array(" "));	//blank row
			$writer->writeSheetRow('Grid T1',array_keys($this->hdrGridT1),$hstyle);
			$writer->writeSheet($this->dbGridT1,'Grid T1',$this->hdrGridT1,true);
			
			$writer->setColWidths('Grid T2',array(10,20,20,20,20,20,20,20,20));
			$writer->writeSheetHeader('Grid T2',$this->hdrGridT2,true);
			$writer->writeSheetRow('Grid T2',array(date('m/d/y'),'Condo on the Bay T2 Grid'),$h1style);
			$writer->writeSheetRow('Grid T2',array(" "));	//blank row
			$writer->writeSheetRow('Grid T2',array_keys($this->hdrGridT2),$hstyle);
			$writer->writeSheet($this->dbGridT2,'Grid T2',$this->hdrGridT2,true);
		}
		if ($this->showInfo) {
			$writer->setColWidths('Voters',array(15,30,30,30,40,30));
			$writer->writeSheetHeader('Voters',$this->hdrVoter,true);
			$writer->writeSheetRow('Voters',array_keys($this->hdrVoter),$hstyle);
			$writer->writeSheet($this->dbVoter,'Voters',$this->hdrVoter,false);
			
			$writer->writeSheetHeader('Users',$this->hdrUser,true);
			$writer->writeSheetRow('Users',array_keys($this->hdrUser),$hstyle);
			$writer->writeSheet($this->dbUser,'Users',$this->hdrUser,false);
		}
		if ($this->fullRun && $this->showInfo) {
			$writer->writeSheetRow('Sold',array_keys($this->hdrUser),$hstyle);
			$writer->writeSheet($this->dbSold,'Sold',$this->hdrUser,true);
			
			$writer->writeSheetRow('Gone',array_keys($this->hdrUser),$hstyle);
			$writer->writeSheet($this->dbGone,'Gone',$this->hdrUser,true);
		}
		$writer->writeToStdOut(); 
		unset($writer);
		return;	
	// end of CreateFile
	}

	

// ------ functions called by routines above -------------------------------
	
	function IsAllRented($units) {
	// explodes unit and returns True if in dbRenter
		// first get unit array
		$temp = $this->GetFullUnit($units);
		if ($temp) {
			foreach ($temp as $unit) {
				// return False if not in dbRenter and not Partner
				if (!in_array($unit,array_column($this->dbRenter,2))) {
					return false;
				}
			}
			// return true if found in dbRenter
			return true;
		}
		return false;
	// end of IsAllRented
	}
	
	function IsIndbRes($lname,$fname,$unit,$email,$hphone,$cphone) {
	// True if person is already in dbRes
		foreach ($this->dbRes as &$row) {
			if (trim($row[0]) == trim($lname) 
				AND trim($row[1]) == trim($fname) 
				AND trim($row[2]) == trim($unit)){
				// make sure best email is used
				if (strlen($email) > strlen($row[6])) {
					$row[6] = $email;
				}
				if (strlen($hphone) > strlen($row[4])) {
					$row[4] = $hphone;
				}
				if (strlen($cphone) > strlen($row[5])) {
					$row[5] = $cphone;
				}
				return true;
			}
		}
		return false;
	}
	
	function IsInUserUnit($unit, $lname, $fname, $email) {
	// returns false if unit.lname.fname not in table UserUnit
		$sql = "SELECT userunitid, email FROM UserUnit WHERE unit = :unit AND lastname = :lname AND firstname = :fname" ;
		$stmt = $this->pdo->prepare ($sql);
		$stmt->execute([$unit,$lname,$fname]);
		$result = $stmt->fetch();
		if (!$result) {
			return false;
		} else {
			//found user so now check if email matches
			if (strlen($email) > 0) {
				if (!($email == $result["email"])) {
					//update email
					$stmt = $this->pdo->query("UPDATE UserUnit SET email = '" . $email . "' WHERE userunitid = '" . $result["userunitid"] . "'");
				}
			}
			return true;
		}
	}
	
	function GetAddress($row) {
	// returns an array of addr, citystate for mailings based on user settings
	// updated 202305 for Email Me option
	if ($row[self::iMailings] == "2nd Address") {
		if ($temp = explode (',',$row[self::i2ndAddress])) {
			if (count($temp)!==2) {
				$temp[0]  = "";
				$temp[1]  = "";
				$this->addError('10','2ndAddress format',$row[self::iUnit],$row[self::iUser1LastName],$row[self::i2ndAddress]);
			}
		}
	} elseif ($row[self::iMailings] == "Email Me"){
		$temp[0] = $row[self::iEmail];
		$temp[1] = "";
	} else {
		switch (substr($row[self::iUnit],0, 7)) {
			Case "Tower 1":
				$temp[0] = "888 Blvd of the Arts " . substr($row[self::iUnit],8, 5);
				$temp[1] = "Sarasota FL 34236";
				break;
			Case "Tower 2":
				$temp[0] = "988 Blvd of the Arts " . substr($row[self::iUnit],8, 5);
				$temp[1] = "Sarasota FL 34236";
				break;
			Case "Marina ":
				$temp[0] = substr($row[self::iUnit],16, 3) . " Blvd of the Arts";
				$temp[1] = "Sarasota FL 34236";
				break;
			default:
				if(!str_contains($row[self::iAccess], "A")){
					$this->addError('1','Unit format',$row[self::iUnit],$row[self::iUser1LastName],'Unit has wrong association');
				}
				$temp[0] = "";
				$temp[1]  = "";
			}	
		}
	return $temp;
	// end of GetAddress
	}
	
	function GetVoter($row){
		// gets the name of unit voter
			If ($row[self::iOfficialVoter] == "Resident1") {
				$R = $row[self::iFirstName] . " " . $row[self::iUser1LastName];
			} elseif ($row[self::iOfficialVoter] == "Resident2") {
				$R = $row[self::iUser2FirstName] . " " . $row[self::iUser2LastName];
			} else {
				$R="";
			}
		return $R;
		// end of GetVoter
	}
	
	function GetFloor($units)
	{
	// REPLACE WITH DB QUERY?
	// return the floor based in the unit. for multi return both floors
	// change to get floor from UnitMaster
	//receives unit string as return common delimited floor(s)
	//unit can be Tower 1 # 708 or Tower 1 #1706/1103 or Tower 1 #1903/04/05 or Tower 1 #1003/Tower 2 # 202

	$temp = explode('/', $units,5);
	$F= "";
	foreach ($temp as $unit) {
		$unit = trim($unit);
		switch (strlen($unit)) {
			Case 19: //MS
				if (strlen($F)>0) {
					$F = $F . ",1";
				} else {
					$F = "1";
				}
				break;
			Case 13: //full unit
				if (strlen($F)>0) {
					$F = $F . "," . trim(substr($unit, 9, 2));
				} else {
					$F = trim(substr($unit, 9, 2));
				}
				break;
			Case 12: //missing space u Tower 1 #708
				// insert a space
				$F=substr($unit,0,9) & " " & substr($unit,9,3);
				break;
			Case 14:
				if (strlen($F)>0) {
					$F = $F . "," . substr($unit, 10, 2);
				} else {
					$F = substr($unit, 10, 2);
				}
				break;
			Case 4: //unit only
				if (strlen($F)>0) {
					$F = $F . "," . substr($unit, 0, 2);
				} else {
					$F = substr($unit, 1, 2);
				}
				break;
			Case 3: //unit only
				if (strlen($F)>0) {
					$F = $F . "," . substr($unit, 0, 1);
				} else {
					$F = substr($unit, 1, 1);
				}
				break;
			Case 2: //same floor as primary
				$F = $F . "," . substr($F, 0, 2);
				break;
			Case 1:
				$F = $F . "," . substr($F, 0, 1);
				break;
			default:
				$F = strlen($unit);
				$this->addError('1','Unit format',$units,'','Incorrect format - could not calc floor');
			}
		}
		return $F;
	// end of GetFloor
	}
	
	function LogRun(&$checkmsg, $type)
	{
		if ($this->logging){$this->addError("T65", "Start LogRun", $this->timeRun($this->sTime),"","");}
		//update RunLog table for this run
		$ip = $_SERVER['REMOTE_ADDR'] ;
		$sql = "INSERT INTO RunLog (ip,runtime, filetime,type,records)
				VALUES (
				'" . $ip  
				. "', '" . $this->runTime 
				. "', '" . $this->fileTime 
				. "', '" . $type 
				. "', '" . count($this->dbUser) 
				. "')";
		// execute the SQL statement - if returns fail then report
		$this->pdo->query($sql);
		$logid = $this->pdo->lastInsertId();
		// now log all the data for this run -- NOT FOR PARTIAL OR EXTERNAL OR DELTA
		if ($this->logging){$this->addError("T68", "Start LogData", $this->timeRun($this->sTime),"","");}
		if ($type == 1) {$this->LogData($logid);}
		//now send email
//		$this->LogEmail($checkmsg, "COBList run",$sql);
		return $logid;
	// end of LogRun
	}
	
	function LogEmail ($checkmsg, $subj, $body) {
		//now email but turn off error reporting if mail server not enabled
//		error_reporting(0);
		$to      = 'webmaster@condoonthebay.com';
		$subject = $subj;
		$message = $body;
		$headers = 'From: cbarlow@pathfinderassociatesinc.com' . "\r\n" .
			'Reply-To: cbarlow@pathfinderassociatesinc.com' . "\r\n" .
			'X-Mailer: PHP/' . phpversion();
		try {
			if (!mail($to, $subject, $message, $headers)) {
				throw new Exception('Mail failed');
			}
		} catch (Exception $e) {
			$checkmsg = $subj . ' Email failed: ' . $e->getMessage();
			error_log($checkmsg);
		}
		error_reporting(E_ALL);
		return;		
	}
	
	function LogData($logid){
		// log dbUser to table RunData with logid as key
		//$this->sTime = microtime(true);
		if ($this->logging){$this->addError("T80", "LogDataOpen", $this->timeRun($this->sTime),"","");}

		foreach ($this->dbUser as $values) {
			// create array with column keys
			$valarray = array(
			"Created" => $values[0], 
			"Username" => $values[1], 
			"Enabled" => $values[2], 
			"FirstName" => $values[3], 
			"Email" => $values[4], 
			"Access" => $values[5], 
			"User1LastName" => $values[6], 
			"Unit" => $values[7], 
			"HomePhone" => $values[8], 
			"User1WorkPhone" => $values[9], 
			"User1CellPhone" => $values[10], 
			"User1Occupation" => $values[11], 
			"User1Employer" => $values[12], 
			"User1Hobbies" => $values[13], 
			"User2FirstName" => $values[14], 
			"User2LastName" => $values[15], 
			"User2Email" => $values[16], 
			"User2WorkPhone" => $values[17], 
			"User2CellPhone" => $values[18], 
			"User2Occupation" => $values[19], 
			"User2Employer" => $values[20], 
			"User2Hobbies" => $values[21], 
			"Child1Name" => $values[22], 
			"Child2Name" => $values[23], 
			"Child3Name" => $values[24], 
			"Child4Name" => $values[25], 
			"Child1Birthdate" => $values[26], 
			"Child2Birthdate" => $values[27], 
			"Child3Birthdate" => $values[28], 
			"Child4Birthdate" => $values[29], 
			"Owner" => $values[30], 
			"Mailings" => $values[31], 
			"2ndAddress" => $values[32], 
			"EmergencyContact" => $values[33], 
			"UnitWatcher" => $values[34], 
			"Stack" => $values[35], 
			"Slip" => $values[36], 
			"Pets" => $values[37], 
			"OfficialVoter" => $values[38], 
			"ShowProfile" => $values[39], 
			"ShowEmail" => $values[40], 
			"ShowPhone" => $values[41], 
			"ShowChildren" => $values[42], 
			"AdminNotes" => $values[43], 
//			"LastLogin" => $values[44], 
			"UserID" => $values[45], 
			"Voter" => $values[46], 
			"Address" => $values[47], 
			"CityStateZip" => $values[48], 
			"Floor" => $values[49]
			);
						
			//insert into RunBlob
			$sql = "INSERT INTO RunBlob (logid, userid, adminaccess, userdata)
				VALUES (:logid, :UserID, :admin, :userdata)";
			$valarray = array(
			"logid" => $logid, 
			"UserID" => $valarray['UserID'], 
			"admin" => preg_match('/ADMIN/', $valarray['Access']),
			"userdata" => $this->PAIEncrypt($valarray)
			);
			
			$statement = $this->pdo->prepare($sql);
			$statement->execute($valarray);
			
		} 
		if ($this->logging){$this->addError("T84", "LogData done", $this->timeRun($this->sTime),"","");}

	// end of LogData
	}

	function timeRun ($startTime)
	{
		// returns float of msec since start time. If start time = 0 then return current time 
		if ($startTime == 0) {
			return microtime(true);
		} else {
			return microtime(true) - $startTime;
		}
	}
	
	function addError($level, $function, $unit, $name, $message)
	{
		// don't log errors if gone or sold
//		if ((stripos($unit,'gone') !== false) || (stripos($unit,'sold') !== false)) {
//		} else {
			$tmp = array(
				'level' => $level,
				'function'	=> $function,
				'unit'	=> $unit,
				'name'	=> $name,
				'message'	=> $message,
				);
			$this->dbErr[] = $tmp;
//		}
	return;
	}
	
function getFolder(){
	if (file_exists("COBfolder.php")) {include ("COBfolder.php");}
	if (!isset($pfolder)) {$pfolder="";}
	return $pfolder;
}
	
function opendb() {
	//function to open PDO database and return PDO object
	if (isset($this->pdo)) {return true;}
	// first include file containing host, db, user, password so not in www folder
	$pfolder=$this->getFolder();
	require ($pfolder . 'COBconnect.php');
	$charset = 'utf8';
	$dsn = "mysql:host={$host};dbname={$db};charset={$charset}";
	$opt = [
		PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
		PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
		PDO::ATTR_EMULATE_PREPARES   => false,
	];
	try {
		$this->pdo = new PDO($dsn, $user, $pass, $opt);
	} catch (PDOException $e) {
		$checkmsg = 'Connection failed: ' . $e->getMessage();
		error_log($checkmsg);
		return false;
	}
	return true;
}

function CheckUnitMaster ($row) {
	// check unit format against unit master and update owner, owner count, renter, voter fields
	if (strlen($row[self::iUnit])>0) {
		// explode unit
		$temp = $this->GetFullUnit($row[self::iUnit]);
		if ($temp) {
		$S="";
		foreach ($temp as $u) {
			//check each unit in UnitMaster and confirm Assoc, Floor, Stack
			$sql = "SELECT * FROM UnitMaster WHERE Unit = ?" ;
			$stmt = $this->pdo->prepare ($sql);
			$stmt->execute([trim($u)]);
			$result = $stmt->fetch();
			
			if ($result) {
				if (stripos($row[self::iOwner],"yes")!== false) {
					switch ($result['bldg']) {
					Case "Tower 1":
						if (strpos($row[self::iAccess], '^T1') === false) {
							$this->addError('1','Owner access',$u,$row[self::iUser1LastName],'T1 Owner without ^T1 access');
						}
						//calculate stack
						$S .= sprintf("%02d", $result['stack']) . ", ";
						break;
					Case "Tower 2":
						if (strpos($row[self::iAccess], '^T2') === false) {
							$this->addError('1','Owner access',$u,$row[self::iUser1LastName],'T2 Owner without ^T2 access');
						}
						$S .= sprintf("%02d", $result['stack']) . ", ";
						break;
					Case "Marina ":
						if (strpos($row[self::iAccess], '^MS') === false) {
							$this->addError('1','Owner access',$u,$row[self::iUser1LastName],'MS Owner without ^MS access');
						}
						$S .= sprintf("%02d", $result['stack']) . ", ";
						break;
					}
				} elseif (stripos($row[self::iOwner],"no")!== false) {
					//renters should not have owner access unless Partner
					if (!($row[self::iAccess] == "MEMBER") ) {
						if (!($row[self::iOfficialVoter] == "Partner") ) {
							$this->addError('3','Renter error',$u,$row[self::iUser1LastName],'Renter with wrong access');
						}
					}
					$S .= sprintf("%02d", $result['stack']) . ", ";
				} else {
					//Owner not Yes or No
						$this->addError('2','Owner field',$u,$row[self::iUser1LastName],'Owner field not Yes/No');
				}
			} else {
				// unit not in UnitMaster
					$this->addError('2','Unit error',$u,$row[self::iUser1LastName],'Unit not valid format');
				
			}
		}
		
		//check stack after removing last ,
		$S = substr($S, 0, -2);
		if ($row[self::iStack] !== $S && $row[self::iAccess] !== "PUBLIC"  && strlen($S)) { 
		//check if moved or sold by Access
			$temp = $row[self::iStack] . " should be " . $S;
			$this->addError('3','Stack error',$row[self::iUnit],$row[self::iUser1LastName],$temp);
		}
	}
	}
return;	
//end of CheckUnitMaster
}

function CheckPhoneFormat($p) {
	if (strlen(trim($p))>0) {
		if  (substr($p,0,1) == "+") { 	//international
			return true;
		} elseif (!preg_match('/^(\+1|001)?\(?([0-9]{3})\)?([ .-]?)([0-9]{3})([ .-]?)([0-9]{4})/',$p)) {
			return false;
		} else {
			return true;
		}
	} else {
		return true;
	}
}

function GetBestPhone($row){
	// function returns cell or other phone
	if (strlen($row[10])>0) {
		return $row[10];
	} elseif (strlen($row[8])>0) {
		return $row[8];
	} elseif (strlen($row[9])>0) {
		return $row[9];
	} else {
		$this->addError('4','No phone number',$row[self::iUnit],$row[self::iUser1LastName],'No phone number for this resident');
		return "";
	}
}
function GetEmail($email){
	// function returns email if not holdall@gmx
	if (stripos($email,"holdall@gmx") === false) {
		return $email;
	}  else {
		return "";
	}
}

function GetLeaseDates($row) {
	// return array of start date and end date and log error
	$D = explode('-', $row[self::iOfficialVoter]);
	// add check for partner v3.0.8
	if ($D[0] == "Partner"){
	//ignore error if partner of owner
		$D[1]="Partner ";
	} elseif (count($D) !== 2) {
		$temp = 'Wrong lease date format=' . $row[self::iOfficialVoter];
		$this->addError('5','Lease dates',$row[self::iUnit],$row[self::iUser1LastName],$temp);
		$D[1] = "Error";
	} elseif (strlen(trim($D[0])) > 8) {
		$temp = 'Wrong lease date format=' . $row[self::iOfficialVoter];
		$this->addError('5','Lease dates',$row[self::iUnit],$row[self::iUser1LastName],$temp);
	} else {
		$D[0] = date_format(date_create_from_format("m/d/y",$D[0]),"Y-m-d");
		$D[1] = date_format(date_create_from_format("m/d/y",$D[1]),"Y-m-d");
	}
	return $D;
}

function GetLeaseEnd($unit) {
	// seaches dbRenter for unit and return lease end from first column
	foreach ($this->dbRenter as $row) {
		if ($row[2] == $unit) {
			return $row[0];
		}
	}
	return null;
}

function GetFullUnit($unit) {
	// this returns an array of full units from abbreviated units
	$Units = array_filter(explode('/', $unit));
	$F = array();
	$T = "";
	foreach ($Units as $u) {
		$u = trim($u);
		switch (strlen($u)) {
			Case 19: //MS Marina Suites # 902
				$F[]=$u;
				$T = substr($u,0,15);
				break;
			Case 13: //full u Tower 1 # 708
				$F[]=$u;
				$T = substr($u,0,11);
				break;
			Case 12: //missing space u Tower 1 #708
				$this->addError('1','Unit format',$unit,$u,'Missing space after #');
				// insert a space
				$F[]=substr($u,0,9) & " " & substr($u,9,3);
				$T = substr($u,0,10);
				break;
			Case 4: //u only 1801
				$F[]= substr($T,0,9) . $u;
				break;
			Case 3: //u only 708
				$F[]= substr($T,0,9) . " " . $u;
				break;
			Case 2: //same floor as primary 02 or 12
				$F[]= $T . $u;
				break;
			Case 1: // 3
				$F[]= $T . "0" . $u;
				break;
			default:
				$this->addError('1','Unit format',$unit,count($F),'Incorrect format - could not calc floor');
				$F = false;
				return false;
			}
		}
	return $F;
}

function GetRunInfo($runID)
{
	// queries the RunLog table and returns row of logid Delta
	$query1 = $this->pdo->prepare("SELECT runtime, filetime, ip, records FROM RunLog WHERE logid = ?");
	$query1->execute([$runID]);
	$result = $query1->fetch();
	return $result;
// end of GetRunInfo
}

	function GetRunBlob($runID)
{
	// queries the RunBlob table and returns array of runid 
	$sql = "SELECT `userid` as `uid`, `userdata` FROM `RunBlob` WHERE `logid` = ?";
	$query1 = $this->pdo->prepare($sql);
	$query1->execute([$runID]);
	$result = $query1->fetchALL(PDO::FETCH_KEY_PAIR);
	// now loop each row and decrypt Blob 
	foreach ($result as $key =>$blob) {
		$rows[$key] = $this->PAIDecrypt($blob);
	}
	return $rows;
// end of GetRunBlob
}

	function GetRunUserID($rundata, $userid)
{
	// queries the RunData array and returns row of userid
	if (array_key_exists($userid, $rundata)) {
		return $rundata[$userid];
	}
//	foreach ($rundata as $row) {
//		if ($row['UserID'] == $userid) {
//		if ($row[45] == $userid) {
//			return $row;
//			break;
//		}
//	}
	return false;
// end of GetRunUserID
}
		
function BuildDeltaResponse() {
	//setup header for json
	$checkmsg="";
	$wasData = $this->GetRunInfo($this->wasDelta);
	$isData = $this->GetRunInfo($this->isDelta);
	$response = array ('Response' => array ( 
		'Title' => 'COBDelta', 
		'Version' => self::iVersion, 
		'Copyright' => 'Copyright 2018-2024 Pathfinder Associates, Inc.',
		'Termsofservice' => 'http://pathfinderassociatesinc.com/COB/TermsofService.pdf',
		'Documentation' => 'http://pathfinderassociatesinc.com/COB/Documentation.pdf',
		'Author' => 'Christopher Barlow',
		'Delta' => array ( 
			'WasInfo' => array ( 
				'WasID' => $this->wasDelta,
				'WasRunTime' => $wasData["runtime"],
				'WasFileTime' => $wasData["filetime"],
				'WasRunDate' => date("Y-m-d h:i",$wasData["runtime"]),
				'WasFileDate' => date("Y-m-d h:i",$wasData["filetime"]),
				'WasIP' => $wasData["ip"],
				'WasCount' => $wasData["records"]
			), 
			'IsInfo' => array ( 
				'IsID' => $this->isDelta,
				'IsRunTime' => $isData["runtime"],
				'IsFileTime' => $isData["filetime"],
				'IsRunDate' => date("Y-m-d h:i",$isData["runtime"]),
				'IsFileDate' => date("Y-m-d h:i",$isData["filetime"]),
				'IsIP' => $isData["ip"],
				'IsCount' => $isData["records"]
			) 
		) 
	) 
);

	$response["Delta"] = $this->BuildDeltaACD();
	$this->LogEmail($checkmsg, "COBDelta run",json_encode($response));
	return $response;
// end of BuildDeltaResponse
}

function BuildDeltaACD() {
	//loops through RunData to get records added, changed, or deleted
	//first loop IsID and find userid in WasID. If not found = Add, if found get Changes
		//now get all Is & Was from RunData in arrays
		$isData = $this->GetRunBlob($this->isDelta);	
		$wasData = $this->GetRunBlob($this->wasDelta);
		//loop thru is and look for was
		$delta = array();	//main delta of all ACD
		$summary = array(); //summary of key field changes
		$added = array ();	//all added
		$changed = array ();	//all changed
		$deleted = array ();	//all deleted
		$changeis = array();	// for each row
		$changewas = array();	// for each row
		
		foreach($isData as $row) {
			//look for userid in wasData
			$found = $this->GetRunUserID($wasData,$row['UserID']);
			if (is_array($found)) {
				//found so look for Is changes
				$cfields = array_diff_assoc($row, $found);
				if (!empty($cfields)) {
					$changeis = $cfields;
					//found so look for Was changes
					$cfields = array_diff_assoc($found, $row);
					$changewas = $cfields;
					$changed[] = array("UserID"=>$row['UserID'], "Unit"=>$row['Unit'], "Lastname"=>$row['User1LastName'], "Firstname"=>$row['FirstName'], "Fields"=>array("Is"=>$changeis,"Was"=>$changewas));
				}
			}else {
				//this record was added so return row
				$addis = $row;
					$added[] = array("UserID"=>$row['UserID'],"Unit"=>$row['Unit'],"Lastname"=>$row['User1LastName'], "Firstname"=>$row['FirstName'],"Fields"=>array("Is"=>$addis));
			}
		}
		
		// then loop WasID and find userid in IsID. if not found = Deleted
		foreach($wasData as $row) {
			//look for userid in wasData
			$found = $this->GetRunUserID($isData,$row['UserID']);
			if (is_array($found)) {
				//found so continue
			}else {
				//this record was deleted so return row
				$deletewas = $row;
					$deleted[] = array("UserID"=>$row['UserID'],"Unit"=>$row['Unit'],"Lastname"=>$row['User1LastName'], "Firstname"=>$row['FirstName'],"Fields"=>array("Was"=>$deletewas));
			}
		}
	// now build Summary array
	// first loop thru added array,then deleted, then changed 
	foreach($added as $rowfull) {
		$row = $rowfull['Fields']['Is'];
		$summary[]= array("Type"=>"Added",
			"Lastname"=>$rowfull['Lastname'], 
			"Firstname"=>$rowfull['Firstname'],
			"Unit"=>$rowfull['Unit'],
			"Enabled"=>$row['Enabled'],
			"Access"=>$row['Access'],
			"Email"=>$row['Email'],
			"Owner"=>$row['Owner']);
	}
	
	foreach($deleted as $rowfull) {
		$row = $rowfull['Fields']['Was'];
		$summary[]= array("Type"=>"Deleted",
			"Lastname"=>$rowfull['Lastname'], 
			"Firstname"=>$rowfull['Firstname'],
			"Unit"=>$rowfull['Unit'],
			"Enabled"=>$row['Enabled'],
			"Access"=>$row['Access'],
			"Email"=>$row['Email'],
			"Owner"=>$row['Owner']);
	}
	foreach($changed as $rowfull) {
		$unit="";
		$enabled = "";
		$access = "";
		$email="";
		$owner="";
		$row = $rowfull['Fields'];
		if (array_key_exists('Unit',$row['Is'])) {
			$unit = $row['Was']['Unit']." > ";
		}
		if (array_key_exists("Enabled",$row['Is'])) {
			$enabled = $row['Was']['Enabled']." > ".$row['Is']['Enabled'];
		}
		if (array_key_exists('Access',$row['Is'])) {
			$access = $row['Was']['Access']." > ".$row['Is']['Access'];
		}
		if (array_key_exists('Owner',$row['Is'])) {
			$owner = $row['Was']['Owner']." > ".$row['Is']['Owner'];
		}
		if (array_key_exists('Email',$row['Is'])) {
			$email = $row['Was']['Email']." > ".$row['Is']['Email'];
		}
		$summary[]= array("Type"=>"Changed",
			"Lastname"=>$rowfull['Lastname'], 
			"Firstname"=>$rowfull['Firstname'],
			"Unit"=>$unit . $rowfull['Unit'],
			"Enabled"=>$enabled,
			"Access"=>$access,
			"Email"=>$email,
			"Owner"=>$owner);
	}
	

	//then combine four arrays into Delta
	$delta =  array("Summary"=>$summary,"Added"=>$added, "Changed"=>$changed, "Deleted"=>$deleted);
	return $delta;
// end of BuildDeltaACD	
}

function WriteSKResponse() {
	// Writes encrypted JSON file of Slip, Kayak, Wait info
	// This is called at the end of each full COBList run
	// Creates encrypted file of JSON for the Slip & Kayak display
	//setup header for json including when COBList was run
	$checkmsg="";
//	$isData = $this->GetRunInfo($this->isDelta);
	$response = array ('Response' => array ( 
		'Title' => 'COBSKW',
		'Run' => date("d-M-Y "),
		'Version' => self::iVersion, 
		'Copyright' => 'Copyright 2018-2024 Pathfinder Associates, Inc.',
		'Termsofservice' => 'https://pathfinderassociatesinc.com/COB/TermsofService.pdf',
		'Documentation' => 'https://pathfinderassociatesinc.com/COB/Documentation.pdf',
		'Author' => 'Christopher Barlow',
		'Data' => $response["Data"] = $this->BuildSKDetail()
		) 
	);
			if ($this->logging){$this->addError("T601", "
			startWrite SKData", $this->timeRun($this->sTime),"","");}
	//now encrypt Response and write to file
	$file = $this->getFolder() . "SKWData.json";
	file_put_contents($file, json_encode($response));
			if ($this->logging){$this->addError("T61", "doneWrite SKData", $this->timeRun($this->sTime),"","");}

//	$this->LogEmail($checkmsg, "COBSKW run",json_encode($response));
	return $response;
// end of WriteSKResponse
}

function BuildSKDetail() {
	// builds detailed records for Slip, Kayak, Wait
	// TODO speed up this function with one query and array split funtions
	$data = array();	//main array of all slip kayak wait info
	$slips = array ();	//all slips
	$racks = array ();	//all racks

	//first get North Data
	$sql = "SELECT b.slipid, b.class, b.scondition, a.names, a.unit, a.limbo, a.phone, a.email
							FROM SlipMaster b
							LEFT OUTER JOIN Slips a ON a.slipid = b.slipid
							WHERE b.type = 'Slip' 
							AND b.dock = 'North Dock'
							ORDER BY b.slipid";
	$stmt = $this->pdo->prepare($sql);
	$stmt->execute();
	$slips["North"] = $stmt->fetchall(PDO::FETCH_ASSOC);
			if ($this->logging){$this->addError("T58", "Start SlipSouth", $this->timeRun($this->sTime),"","");}

	//then get South Data
	$sql = "SELECT b.slipid, b.class, b.scondition, a.names, a.unit, a.limbo, a.phone, a.email
							FROM SlipMaster b
							LEFT OUTER JOIN Slips a ON a.slipid = b.slipid
							WHERE b.type = 'Slip' 
							AND b.dock = 'South Dock'
							ORDER BY b.slipid";
	$stmt = $this->pdo->prepare($sql);
	$stmt->execute();
	$slips["South"] = $stmt->fetchall(PDO::FETCH_ASSOC);
	// then get Wait Data
	$sql = "SELECT date,number, names, unit, phone, email
							FROM WaitList
							WHERE type = 'S' 
							ORDER BY date";
	$stmt = $this->pdo->prepare($sql);
	$stmt->execute();
	$slips["Wait"] = $stmt->fetchall(PDO::FETCH_ASSOC);
			if ($this->logging){$this->addError("T59", "Start RackNorth", $this->timeRun($this->sTime),"","");}

	// then repeat for racks
	//first get North Data
	$sql = "SELECT b.slipid, b.class, b.scondition, a.names, a.unit, a.phone, a.email
							FROM SlipMaster b
							LEFT OUTER JOIN Slips a ON a.slipid = b.slipid
							WHERE b.type = 'Kayak' 
							AND b.dock = 'North Dock'
							ORDER BY b.slipid";
	$stmt = $this->pdo->prepare($sql);
	$stmt->execute();
	$racks["North"] = $stmt->fetchall(PDO::FETCH_ASSOC);
	//then get South Data
	$sql = "SELECT b.slipid, b.class, b.scondition, a.names, a.unit, a.phone, a.email
							FROM SlipMaster b
							LEFT OUTER JOIN Slips a ON a.slipid = b.slipid
							WHERE b.type = 'Kayak' 
							AND b.dock = 'South Dock'
							ORDER BY b.slipid";
	$stmt = $this->pdo->prepare($sql);
	$stmt->execute();
	$racks["South"] = $stmt->fetchall(PDO::FETCH_ASSOC);
	// then get Wait Data
	$sql = "SELECT date,number, names, unit, phone, email
							FROM WaitList
							WHERE type = 'K' 
							ORDER BY date";
	$stmt = $this->pdo->prepare($sql);
	$stmt->execute();
	$racks["Wait"] = $stmt->fetchall(PDO::FETCH_ASSOC);

	//then combine arrays into Data
	$data =  array("Slips"=>$slips, "Racks"=>$racks);
	return $data;
// end of BuildSKDetail	
}

function GetSKData($msg){
	// reads SKData file and returns results as array
	return json_decode(file_get_contents($this->getFolder() . "SKWData.json"));
// end of GetSKData	
}


function PAIEncrypt($blob) {
	return $this->paicrypt->encrypt(serialize($blob));
}

function PAIDecrypt($blob) {
	return unserialize($this->paicrypt->decrypt($blob));
}

function DeleteRecords() {
	// delete all records from UserUnit, Slips & WaitList table 
	$stmt = $this->pdo->query('DELETE FROM UserUnit');
	$stmt = $this->pdo->query('DELETE FROM Slips');
	$stmt = $this->pdo->query('DELETE FROM WaitList');
	return;
}

function delete_col(&$array, $offset) {
    return array_walk($array, function (&$v) use ($offset) {
        array_splice($v, $offset, 1);
    });
}
// end of class	
}
?>