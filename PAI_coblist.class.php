<?php
/** PAI_coblist class file
 * package    PAI_COBList 20180423
 * @license   Copyright © 2018 Pathfinder Associates, Inc.
 * Public Methods: 
 *		CheckFile-checks uploaded CSV for format and size
 *		ProcessFile-main process to create working arrays/tables and create XLSX
 *		RunDelta-main process to compare two runs and create delta array
 *		GetRuns-returns runs from RunLog table
 *		CheckUserID-checks userid and access in last RunBlob for admin rights
 */
class COBList
{
    /**
     * main class
     */   
	// Private Variables //
		const iVersion = "4.0.2";
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
		private $hdrWait = array();
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
		$this->DeleteRecords();
		unset($this->pdo);
		unset ($this->paicrypt);
    }

	public function Checkfile(&$checkmsg)
    {
	// called by COBListmenu to upload the file and check for format/size & load to dbUser
		$checkmsg="";
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

		// now build proper address fields, floor, etc.
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
		}

		//now log this run
		// normal run
		$type = 1;
		// external run
		if (!$this->showInfo) {$type = 2;}
		// partial run
		if (!$this->fullRun) {$type = 3;}
		
		if ($this->logging){$this->addError("T60", "Start Log", $this->timeRun($this->sTime),"","");}
		$logid = $this->LogRun($checkmsg, $type);
		
		// now create Excel file
		if ($this->logging){$this->addError("T90", "Start CreateFile", $this->timeRun($this->sTime),"","");}
		$this->CreateXLFile();
		
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


	public function GetRuns(&$checkmsg)
	{
		// queries the RunLog table and returns array of logid & filetime For Delta
		$sql = "SELECT logid, filetime, ip FROM RunLog WHERE type = 1 ORDER BY filetime desc";
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

	//step thru User array
	foreach ($this->dbUser as $rowData) {
		if ((stripos($rowData[self::iUnit],'gone') !== false)) {
		} elseif (stripos($rowData[self::iUnit],'sold') !== false) {
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
				$this->addError('14','Emergency contact',$rowData[self::iUnit],$rowData[self::iUser1LastName],'No Emergency Contact');
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
		$this->addError('1','No unit owner!',$row['Unit'],$row['owners'],'');
		}
	$query1 = $this->pdo->prepare("select unit, count(unit) as owners FROM `UserUnit` where owner = 'yes' group by unit having count(unit) > 1");
	$query1->execute();
	while ($row = $query1->fetch(PDO::FETCH_ASSOC)) {
		$this->addError('16','Owner Count',$row['Unit'],$row['voters'],'');
		}

		//check missing Voter in UnitMaster
	$query1 = $this->pdo->prepare("select unit, count(unit) as voters FROM `UserUnit` where voter = 'yes' group by unit having count(unit) = 0");
	$query1->execute();
	while ($row = $query1->fetch(PDO::FETCH_ASSOC)) {
		$this->addError('4','Missing Voter',$row['Unit'],$row['voters'],'Check Official Voter');
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
			(((stripos($rowData[self::iAccess],'^A') !== false) &&
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
			
			// copy current resident to dbRes
			//write row - 6,3,7,30, 8,10,4,33,34
			$this->dbRes[]=array(
				$rowData[6],$rowData[3],$rowData[7],$rowData[30],$rowData[8],
				$rowData[10],$this->GetEmail($rowData[4]),$rowData[33],$rowData[34]
			);
		
			// copy certain fields if current renter to dbRenter
			if ($rowData[self::iAccess] == "MEMBER") {
				$temp = $this->GetLeaseDates($rowData);
				//write row - GetLeaseDates(38),6,3,8,10,4,33,34
				$this->dbRenter[]=array(
					$temp[1], $temp[0],
					$rowData[7],$rowData[6],$rowData[3],$rowData[8],
					$rowData[10],$this->GetEmail($rowData[4]),$rowData[33],$rowData[34]
				);
				if ($rowData[self::iOwner] == "Yes") {
					$this->addError('1','Owner Error',$rowData[self::iUnit],$rowData[self::iUser1LastName],'Owner with only member access');
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
		$this->hdrRes = array('Last Name'=>'string', 'First Name'=>'string','Unit'=>'string','Owner'=>'string','Home Phone'=>'string','Cell Phone'=>'string','Email'=>'string','Emergency Contact'=>'string','Unit Watcher'=>'string');
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
		$this->hdrSlip = array('Dock'=>'string', 'Slip'=>'string','Class'=>'string','Rate'=>'string','Type'=>'string','Condition'=>'string', 'Name'=>'string','Unit'=>'string','Lift'=>'string','Phone'=>'string','Email'=>'string');
		$this->hdrWait = array('Date'=>'string','Name'=>'string','Unit'=>'string','Number'=>'string');
		
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
			// check if owner has rented their unit and show error - TO BE DONE
			
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
					$sql = "INSERT INTO WaitList (type,unit, names, date, number,userid)
							VALUES (:type,:unit,:names,:date,:number,:userid)";
					$valarray = array(
							"type" => substr(trim($slip),1,1), 
							"unit" => $row[7], 
							"names" => $row[3] . ' ' . $row[6],
							"date" => $wdate,
							"number" => $wnum,
							"userid" => $row[45]
							);
							
					$statement = $this->pdo->prepare($sql);
					$statement->execute($valarray);
					
				} else {
					// if slip has L or T then strip off and set lift  = true
					if (preg_match("/[TL]/", $slip)){
						$lift = 1;
						$slip = substr_replace(trim($slip) ,"",-1);
					} else {
						$lift = 0;
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
					$sql = "INSERT INTO Slips (unit, names, slipid, lift, phone, email, userid)
						VALUES (:unit, :names, :slipid, :lift, :phone, :email,:userid)";
					$valarray = array(
							"unit" => $row[7], 
							"names" => $row[3] . ' ' . $row[6],
							"slipid" => trim($slip),
							"lift" => $lift,
							"phone" => $phone,
							"email" => $email,
							"userid" => $row[45]
							);
					$statement = $this->pdo->prepare($sql);
					$statement->execute($valarray);
					
				}
					
			}
		}
		//now query to dbSlip
		$query1 = $this->pdo->prepare("SELECT dock, b.slipid, b.class, rate, b.type, b.scondition, a.names, unit, lift, phone, email
							FROM SlipMaster b
							LEFT OUTER JOIN Slips a ON a.slipid = b.slipid
							JOIN RateMaster c ON b.class = c.class
							WHERE b.type = 'Slip' ORDER BY b.slipid");
		$query1->execute();
		$this->dbSlip = $query1->fetchALL(PDO::FETCH_ASSOC);
				
		//now add waitlist info at bottom of dbSlip array
		$this->dbSlip[]=array("");
		$this->dbSlip[]=array("Wait List");
		$this->dbSlip[]=array_keys($this->hdrWait);
		$query1 = $this->pdo->prepare("SELECT date, names, unit FROM WaitList where type = 'S' ORDER BY date");
		$query1->execute();
		$this->dbSlip = array_merge($this->dbSlip,$query1->fetchALL(PDO::FETCH_ASSOC));
		
		
		//now query to dbKayak
		$query1 = $this->pdo->prepare("SELECT dock, b.slipid, b.class, rate, b.type, b.scondition, a.names, unit, lift, phone, email 
							FROM SlipMaster b
							LEFT OUTER JOIN Slips a ON a.slipid = b.slipid
							JOIN RateMaster c ON b.class = c.class
							WHERE b.type = 'Kayak' ORDER BY b.slipid");
		$query1->execute();
		$this->dbKayak = $query1->fetchALL(PDO::FETCH_ASSOC);
		$this->dbKayak[]=array("");
		$this->dbKayak[]=array("Wait List");
		$this->dbKayak[]=array_keys($this->hdrWait);
		$query1 = $this->pdo->prepare("SELECT date, names, unit, number FROM WaitList where type = 'K' ORDER BY date");
		$query1->execute();
		$this->dbKayak = array_merge($this->dbKayak,$query1->fetchALL(PDO::FETCH_ASSOC));
		
			
		return;
		// end of BuildSlip
	}

	function BuildGrids()
	{
		//Build the resident grid
		//setup grid headers
		$this->hdrGridT1 = array("Floor"=>"string","Rembrandt-1"=>"string", "Monet-2"=>"string", "Renoir-3"=>"string", "Van Gogh-4"=>"string", "Van Gogh-5"=>"string", "Renoir-6"=>"string", "Monet-7"=>"string", "Cezanne-8"=>"string");

		$this->hdrGridT2 = array("Floor"=>"string","Rembrandt-9"=>"string", "Renoir-10"=>"string", "Renoir-11"=>"string", "Van Gogh-12"=>"string", "Van Gogh-14"=>"string", "Renoir-15"=>"string", "Renoir-16"=>"string", "Rembrandt-17"=>"string");

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
				// insert User1
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
				// insert User2 if exists
				if (strlen($row[15])>0 ){
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
					$sql = "INSERT INTO UserUnit (unit, owner, voter, lastname, firstname, email, cellphone, homephone, emergency, unitwatcher, address, citystatezip, userid)
							VALUES (:unit, :owner, :voter,:lname, :fname, :email, :cell, :home, :emer, :watch, :address, :citystatezip, :userid)";
					// execute the SQL statement - if returns fail then report
					$stmt = $this->pdo->prepare ($sql);
					if ($stmt->execute(array(
						"unit" => $unit,
						"owner" => $row[30],
						"voter" => $v,
						"lname" => $row[15],
						"fname" => $row[14],
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

		//now query to dbUnit
		$query1 = $this->pdo->prepare("SELECT b.unit, a.owner, a.voter, a.lastname, a.firstname, a.email, a.cellphone, a.homephone, b.space, a.emergency, a.unitwatcher, a.address, a.citystatezip
							FROM UnitMaster b
							LEFT OUTER JOIN UserUnit a ON a.unit = b.unit
							ORDER BY b.unit");
		$query1->execute();
		$this->dbUnit = $query1->fetchALL(PDO::FETCH_ASSOC);
		
		//now query to build dbVoter
		$sql = "SELECT a.bldg, a.unit, b.lastname, b.firstname, b.address, b.citystatezip FROM UnitMaster a left join UserUnit b ON a.unit = b.unit AND b.voter = 'Yes' ORDER BY a.unit";
		$query1 = $this->pdo->prepare($sql);
		$query1->execute();
		$this->dbVoter = $query1->fetchALL(PDO::FETCH_ASSOC);

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
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Transfer-Encoding: binary');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		
		//setup heading row style
		$hstyle = array( 'font'=>'Arial','font-size'=>10,'font-style'=>'bold', 'halign'=>'center', 'border'=>'bottom');
		$h1style = array( 'font'=>'Arial','font-size'=>12,'font-style'=>'bold', 'halign'=>'left', 'border'=>'bottom');
		
		//sort
		sort($this->dbErr);
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
			$writer->writeSheetRow('Errors',array_keys($this->hdrErr),$hstyle);
			$writer->writeSheet($this->dbErr,'Errors',$this->hdrErr,true);
			
			$writer->setColWidths('Listing',array(20,15,20,10,15,15,30,30,30));
			$writer->writeSheetHeader('Listing',$this->hdrRes,true);
			$writer->writeSheetRow('Listing',array(date('m/d/y'),'Condo on the Bay Owner & Renter Listing'),$h1style);
			$writer->writeSheetRow('Listing',array_keys($this->hdrRes),$hstyle);
			$writer->writeSheet($this->dbRes,'Listing',$this->hdrRes,true);
			
			$writer->setColWidths('Renter',array(12,12,20,15,15,15,15,30,30,30));
			$writer->writeSheetHeader('Renter',$this->hdrRenter,true);
			$writer->writeSheetRow('Renter',array(date('m/d/y'),'Condo on the Bay Renter Listing'),$h1style);
			$writer->writeSheetRow('Renter',array_keys($this->hdrRenter),$hstyle);
			$writer->writeSheet($this->dbRenter,'Renter',$this->hdrRenter,true);
		}
		if ($this->fullRun)  {
			$writer->setColWidths('Units',array(20,10,10,20,15,30,15,15,10,30,30,30,30));
			$writer->writeSheetHeader('Units',$this->hdrUnit,true);
			$temp = ($this->showInfo) ? 'Internal':'External';
			$writer->writeSheetRow('Units',array(date('m/d/y'),$temp,'Condo on the Bay Unit Listing'),$h1style);
			$writer->writeSheetRow('Units',array_keys($this->hdrUnit),$hstyle);
			$writer->writeSheet($this->dbUnit,'Units',$this->hdrUnit,true);
			
			$writer->setColWidths('Slips',array(12,15,15,10,6,15,20,20,6,20,40));
			$writer->writeSheetHeader('Slips',$this->hdrSlip,true);
			$writer->writeSheetRow('Slips',array(date('m/d/y'),$temp,'Condo on the Bay Slip Listing'),$h1style);
			$writer->writeSheetRow('Slips',array_keys($this->hdrSlip),$hstyle);
			$writer->writeSheet($this->dbSlip,'Slips',$this->hdrSlip,true);
			
			$writer->setColWidths('Kayaks',array(12,15,15,10,6,10,20,20,6,20,40));
			$writer->writeSheetHeader('Kayaks',$this->hdrSlip,true);
			$writer->writeSheetRow('Kayaks',array(date('m/d/y'),$temp,'Condo on the Bay Kayak Listing'),$h1style);
			$writer->writeSheetRow('Kayaks',array_keys($this->hdrSlip),$hstyle);
			$writer->writeSheet($this->dbKayak,'Kayaks',$this->hdrSlip,true);
		}
		if ($this->fullRun && $this->showInfo) {
			$writer->setColWidths('Pets WSD-ESA',array(20,15,15,50,15,30,30,30));
			$writer->writeSheetHeader('Pets WSD-ESA',$this->hdrPets,true);
			$writer->writeSheetRow('Pets WSD-ESA',array(date('m/d/y'),$temp,'Condo on the Bay Pets & WSD/ESA Listing'),$h1style);
			$writer->writeSheetRow('Pets WSD-ESA',array_keys($this->hdrPets),$hstyle);
			$writer->writeSheet($this->dbPets,'Pets WSD-ESA',$this->hdrPets,true);
			
			$writer->setColWidths('Staff',array(20,15,40,40,20));
			$writer->writeSheetHeader('Staff',$this->hdrStaff,true);
			$writer->writeSheetRow('Staff',array(date('m/d/y'),'Condo on the Bay Staff Listing'),$h1style);
			$writer->writeSheetRow('Staff',array_keys($this->hdrStaff),$hstyle);
			$writer->writeSheet($this->dbStaff,'Staff',$this->hdrStaff,true);
			
			$writer->setColWidths('Grid T1',array(10,20,20,20,20,20,20,20,20));
			$writer->writeSheetHeader('Grid T1',$this->hdrGridT1,true);
			$writer->writeSheetRow('Grid T1',array(date('m/d/y'),'Condo on the Bay T1 Grid'),$h1style);
			$writer->writeSheetRow('Grid T1',array_keys($this->hdrGridT1),$hstyle);
			$writer->writeSheet($this->dbGridT1,'Grid T1',$this->hdrGridT1,true);
			
			$writer->setColWidths('Grid T2',array(10,20,20,20,20,20,20,20,20));
			$writer->writeSheetHeader('Grid T2',$this->hdrGridT2,true);
			$writer->writeSheetRow('Grid T2',array(date('m/d/y'),'Condo on the Bay T2 Grid'),$h1style);
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
	
	function GetAddress($row) {
	// returns an array of addr, citystate for mailings based on user settings
	if ($row[self::iMailings] == "2nd Address") {
		if ($temp = explode (',',$row[self::i2ndAddress])) {
			if (count($temp)!==2) {
				$temp[0]  = "";
				$temp[1]  = "";
				$this->addError('10','2ndAddress format',$row[self::iUnit],$row[self::iUser1LastName],$row[self::i2ndAddress]);
			}
		}
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
				$this->addError('1','Unit format',$row[self::iUnit],$row[self::iUser1LastName],'Unit has wrong association');
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
	}
	
	function GetFloor($unit)
	{
	// REPLACE WITH DB QUERY?
	// return the floor based in the unit. for multi return both floors
	// change to get floor from UnitMaster
	//receives unit string as return common delimited floor(s)
	//unit can be Tower 1 # 708 or Tower 1 #1706/1103 or Tower 1 #1903/04/05 or Tower 1 #1003/Tower 2 # 202

	$Units = explode('/', $unit,5);
	$F= "";
	foreach ($Units as $unit) {
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
				$this->addError('1','Unit format',$unit,'','Incorrect format - could not calc floor');
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
		$this->LogEmail($checkmsg, "COBList run",$sql);
		return $logid;
	// end of LogRun
	}
	
	function LogEmail ($checkmsg, $subj, $body) {
		//now email but turn off error reporting if mail server not enabled
		error_reporting(0);
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
		if ((stripos($unit,'gone') !== false) || (stripos($unit,'sold') !== false)) {
		} else {
			$tmp = array(
				'level' => $level,
				'function'	=> $function,
				'unit'	=> $unit,
				'name'	=> $name,
				'message'	=> $message,
				);
			$this->dbErr[] = $tmp;
		}
	return;
	}

function opendb() {
	//function to open PDO database and return PDO object
	if (isset($this->pdo)) {return true;}
	// first include file containing host, db, user, password so not in www folder
	if (file_exists("COBfolder.php")) {include ("COBfolder.php");}
	if (!isset($pfolder)) {$pfolder="";}
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
					//renters should not have owner access
					if (!($row[self::iAccess] == "MEMBER") ) {
						$this->addError('2','Renter error',$u,$row[self::iUser1LastName],'Renter with wrong access');
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
		if (!preg_match('/^(\+1|001)?\(?([0-9]{3})\)?([ .-]?)([0-9]{3})([ .-]?)([0-9]{4})/',$p)) { 
//		if (!preg_match('\(?[2-9][0-8][0-9]\)?[-. ]?[1-9][0-9]{2}[-. ]?[0-9]{4}',$p)) {
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
		$D[1]="";
	} elseif (count($D) !== 2) {
		$temp = 'Wrong lease date format=' . $row[self::iOfficialVoter];
		$this->addError('5','Lease dates',$row[self::iUnit],$row[self::iUser1LastName],$temp);
		$D[1] = "Error";
	} elseif (strlen($D[0]) > 8) {
		$temp = 'Wrong lease date format=' . $row[self::iOfficialVoter];
		$this->addError('5','Lease dates',$row[self::iUnit],$row[self::iUser1LastName],$temp);
	} else {
		$D[0] = date_format(date_create_from_format("m/d/y",$D[0]),"Y-m-d");
		$D[1] = date_format(date_create_from_format("m/d/y",$D[1]),"Y-m-d");
	}
	return $D;
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
		'Copyright' => 'Copyright 2018 Pathfinder Associates, Inc.',
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
	
	//then combine three arrays into Delta
	$delta =  array("Added"=>$added, "Changed"=>$changed, "Deleted"=>$deleted);
	return $delta;
// end of BuildDeltaACD	
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