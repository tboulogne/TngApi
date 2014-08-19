<?php
class Upavadi_TngContent
{

    private static $instance = null;
    protected $db;
    protected $currentPerson;
    protected $tables = array();
    protected $sortBy = null;

    /**
     * @var Upavadi_Shortcode_AbstractShortcode[]
     */
    protected $shortcodes = array();
    protected $domain;

    protected function __construct()
    {
        
    }

    public static function instance()
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * Add shortcodes
     */
    public function addShortcode(Upavadi_Shortcode_AbstractShortcode $shortcode)
    {
        $this->shortcodes[] = $shortcode;
    }

    public function initPlugin()
    {
        $templates = new Upavadi_Templates();
        foreach ($this->shortcodes as $shortcode) {
            $shortcode->init($this, $templates);
        }
    }

    public function getTngPath()
    {
        return esc_attr(get_option('tng-api-tng-path'));
    }

    public function getTngTables()
    {
        return $this->tables;
    }

    public function initTables()
    {
        $tngPath = $this->getTngPath();
        $configPath = $tngPath . DIRECTORY_SEPARATOR . "config.php";
        include $configPath;
        $vars = get_defined_vars();
        foreach ($vars as $name => $value) {
            if (preg_match('/_table$/', $name)) {
                $this->tables[$name] = $value;
            }
            if (preg_match('/tngdomain$/', $name)) {
                $this->domain = $value;
            }
        }
    }

    public function getDomain()
    {
        return $this->domain;
    }

    public function getDbLink()
    {
        return $this->db;
    }

    public function init()
    {
        if ($this->db) {
            return $this;
        }

        if ($this->currentPerson) {
            return $this;
        }

        // get_currentuserinfo();


        $dbHost = esc_attr(get_option('tng-api-db-host'));
        $dbUser = esc_attr(get_option('tng-api-db-user'));
        $dbPassword = esc_attr(get_option('tng-api-db-password'));
        $dbName = esc_attr(get_option('tng-api-db-database'));

        $db = mysqli_connect($dbHost, $dbUser, $dbPassword);
        mysqli_select_db($db, $dbName);
        $this->db = $db;
        $this->initTables();

        $tng_user_name = $this->getTngUserName();
        $query = "SELECT * FROM {$this->tables['users_table']} WHERE username='{$tng_user_name}'";
        $result = mysqli_query($db, $query) or die("Cannot execute query: $query");
        $row = $result->fetch_assoc();

        $this->currentPerson = $row['personID'];
        return $this;
    }

    public function query($sql)
    {
        $result = mysqli_query($this->db, $sql) or die("Cannot execute query: $sql");
        return $result;
    }

    public function initAdmin()
    {
        register_setting('tng-api-options', 'tng-api-email');
        register_setting('tng-api-options', 'tng-api-tng-page-id');
        register_setting('tng-api-options', 'tng-api-tng-path');
        register_setting('tng-api-options', 'tng-api-tng-photo-upload');
        register_setting('tng-api-options', 'tng-api-db-host');
        register_setting('tng-api-options', 'tng-api-db-user');
        register_setting('tng-api-options', 'tng-api-db-password');
        register_setting('tng-api-options', 'tng-api-db-database');

        add_settings_section('general', 'General', function() {
            
        }, 'tng-api');

        add_settings_field('tng-email', 'Notification Email Address', function () {
            $tngEmail = esc_attr(get_option('tng-api-email'));
            echo "<input type='text' name='tng-api-email' value='$tngEmail' />";
        }, 'tng-api', 'general');

        add_settings_section('tng', 'TNG', function() {
            echo "In order for the plug in work we need to know where the original TNG source files live";
        }, 'tng-api');

        add_settings_field('tng-path', 'TNG Path', function () {
            $tngPath = esc_attr(get_option('tng-api-tng-path'));
            echo "<input type='text' name='tng-api-tng-path' value='$tngPath' />";
        }, 'tng-api', 'tng');
        add_settings_field('tng-photo-upload', 'Photo Upload mediatypeID', function () {
            $tngPath = esc_attr(get_option('tng-api-tng-photo-upload'));
            echo "<input type='text' name='tng-api-tng-photo-upload' value='$tngPath' />";
        }, 'tng-api', 'tng');
        add_settings_section('db', 'Database', function() {
            echo "We also need to know where the TNG database lives";
        }, 'tng-api');
        add_settings_field('db-host', 'Hostname', function () {
            $dbHost = esc_attr(get_option('tng-api-db-host'));
            echo "<input type='text' name='tng-api-db-host' value='$dbHost' />";
        }, 'tng-api', 'db');
        add_settings_field('db-user', 'Username', function () {
            $dbUser = esc_attr(get_option('tng-api-db-user'));
            echo "<input type='text' name='tng-api-db-user' value='$dbUser' />";
        }, 'tng-api', 'db');
        add_settings_field('db-password', 'Password', function () {
            $dbPassword = esc_attr(get_option('tng-api-db-password'));
            echo "<input type='password' name='tng-api-db-password' value='$dbPassword' />";
        }, 'tng-api', 'db');
        add_settings_field('db-database', 'Database Name', function () {
            $dbName = esc_attr(get_option('tng-api-db-database'));
            echo "<input type='text' name='tng-api-db-database' value='$dbName' />";
        }, 'tng-api', 'db');
    }

    public function adminMenu()
    {
        add_options_page(
            "Options", "TngApi", "manage_options", "tng-api", array($this, "pluginOptions")
        );
    }

    function pluginOptions()
    {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }
        ?>
        <form method="POST" action="options.php">
            <?php
            settings_fields('tng-api-options'); //pass slug name of page, also referred
            //to in Settings API as option group name
            do_settings_sections('tng-api');  //pass slug name of page
            submit_button();
            ?>
        </form>
        <?php
    }

  
    
    public function getCurrentPersonId()
    {
        return $this->currentPerson;
    }

    public function getPerson($personId = null)
    {
        if (!$personId) {
            $personId = $this->currentPerson;
        }

        $sql = <<<SQL
SELECT *
FROM {$this->tables['people_table']}
WHERE personID = '{$personId}'
SQL;
        $result = $this->query($sql);
        $row = $result->fetch_assoc();

        return $row;
    }

    public function getFamily($personId = null)
    {

        if (!$personId) {
            $personId = $this->currentPerson;
        }

        $sql = <<<SQL
SELECT *
FROM {$this->tables['families_table']}
WHERE husband = '{$personId}' or wife = '{$personId}'
SQL;
        $result = $this->query($sql);
        $row = $result->fetch_assoc();

        return $row;
    }
/* Special event type 10 is called here*/
    public function getGotra($personId = null)
    {

        if (!$personId) {
            $personId = $this->currentPerson;
        }

        $sql = <<<SQL
		
SELECT *
FROM {$this->tables['events_table']}
where persfamID = '{$personId}' AND eventtypeID = "10"
SQL;
        $result = $this->query($sql);
        $row = $result->fetch_assoc();

        return $row;
    }
/* Special event type 10 is called here*/	
	public function getEventDisplay()
    {

        $sql = <<<SQL
		
SELECT *
FROM {$this->tables['eventtypes_table']}
where eventtypeID = "10"
SQL;
        $result = $this->query($sql);
        $row = $result->fetch_assoc();

        return $row;
    }

    public function getFamilyById($familyId)
    {
        $sql = <<<SQL
SELECT *
FROM {$this->tables['families_table']}
WHERE familyID = '{$familyId}'
SQL;

        $result = $this->query($sql);
        $row = $result->fetch_assoc();

        return $row;
    }

    public function getNotes($personId = null)
    {
        if (!$personId) {
            $personId = $this->currentPerson;
        }

        $sql = <<<SQL
SELECT *
FROM   {$this->tables['notelinks_table']} as nl
    LEFT JOIN {$this->tables['xnotes_table']} AS xl
              ON nl.ID = xl.ID
where persfamID = '{$personId}' AND secret="0"
       
SQL;
        $result = $this->query($sql);

        $rows = array();
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        return $rows;
    }

    public function getDefaultMedia($personId = null)
    {

        if (!$personId) {
            $personId = $this->currentPerson;
        }

        $sql = <<<SQL
		SELECT *
FROM {$this->tables['medialinks_table']}
JOIN {$this->tables['media_table']} USING (mediaID)
where personID = '{$personId}' AND defphoto = "1"
SQL;
        $result = $this->query($sql);
        $row = $result->fetch_assoc();

        return $row;
    }

    public function getAllPersonMedia($personId = null)
    {

        if (!$personId) {
            $personId = $this->currentPerson;
        }

        $sql = <<<SQL
SELECT *
FROM   {$this->tables['medialinks_table']} as ml
    LEFT JOIN {$this->tables['media_table']} AS m
              ON ml.mediaID = m.mediaID
where personID = '{$personId}' AND defphoto <> 1
       
ORDER  BY ml.ordernum
          
SQL;
        $result = $this->query($sql);

        $rows = array();
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        return $rows;
    }

    public function getProfileMedia($personId = null)
    {
        //get default media
        $defaultmedia = $this->getdefaultmedia($personId);
        //$mediaID = "../tng/photos/". $defaultmedia['thumbpath'];

        if ($defaultmedia['thumbpath'] == null AND $person['sex'] == "M") {
            $mediaID = "/img/male.jpg";
        }
        if ($defaultmedia['thumbpath'] == null AND $person['sex'] == "F") {
            $mediaID = "/img/female.jpg";
        }
        if ($defaultmedia['thumbpath'] !== null) {
            $mediaID = "/photos/" . $defaultmedia['thumbpath'];
        }
        return $this->getDomain() . $mediaID;
    }

    public function getChildren($familyId = null)
    {

        if (!$familyId) {
            return array();
        }

        $sql = <<<SQL
	SELECT *
FROM {$this->tables['children_table']}
WHERE familyID = '{$familyId}'
ORDER BY ordernum
SQL;
        $result = $this->query($sql);

        $rows = array();

        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }

        return $rows;
    }

    public function getFamilyUser($personId = null, $sortBy = null)
    {

        if (!$personId) {
            $personId = $this->currentPerson;
        }


        $sql = <<<SQL
SELECT*
		
	
FROM {$this->tables['families_table']}

WHERE (husband = '{$personId}' or wife = '{$personId}')
SQL;
        $result = $this->query($sql);
        $rows = array();

        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        if ($sortBy) {
            $this->sortBy = $sortBy;
            usort($rows, array($this, 'sortRows'));
        }
        return $rows;
    }

    public function sortRows($a, $b)
    {
        if ($a[$this->sortBy] > $b[$this->sortBy]) {
            return 1;
        }
        if ($a[$this->sortBy] < $b[$this->sortBy]) {
            return -1;
        }
        return 0;
    }

    public function getBirthdays($month)
    {

        $sql = <<<SQL
SELECT personid,
       firstname,
       lastname,
       birthdate,
       birthplace,
       gedcom
FROM   {$this->tables['people_table']}
WHERE  Month(birthdatetr) = {$month}
       AND living = 1
ORDER  BY Day(birthdatetr),
          lastname
SQL;
        $result = $this->query($sql);

        $rows = array();
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        return $rows;
    }

    public function getDeathAnniversaries($month)
    {
        $sql = <<<SQL
SELECT personid,
       firstname,
       lastname,
       deathdate,
       deathplace,
       gedcom,
       Year(Now()) - Year(deathdatetr) AS Years
FROM   {$this->tables['people_table']}
WHERE  Month(deathdatetr) = {$month}
       AND living = 0
ORDER  BY Day(deathdatetr),
          lastname
SQL;
        $result = $this->query($sql);

        $rows = array();
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        return $rows;
    }

    public function getDeathAnniversariesPlusOne()
    {
        return $this->getDeathAnniversaries('MONTH(ADDDATE(now(), INTERVAL 1 month))');
    }

    public function getDeathAnniversariesPlusTwo($month)
    {
        $sql = <<<SQL
SELECT personid,
       firstname,
       lastname,
       deathdate,
       deathplace,
       gedcom,
       Year(Now()) - Year(deathdatetr) AS Years
FROM   {$this->tables['people_table']}
WHERE  Month(deathdatetr) = MONTH(ADDDATE(now(), INTERVAL 2 month))
       AND living = 0
ORDER  BY Day(deathdatetr),
          lastname
SQL;
        $result = $this->query($sql);

        $rows = array();
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        return $rows;
    }

    public function getDeathAnniversariesPlusThree($month)
    {
        $sql = <<<SQL
SELECT personid,
       firstname,
       lastname,
       deathdate,
       deathplace,
       gedcom,
       Year(Now()) - Year(deathdatetr) AS Years
FROM   {$this->tables['people_table']}
WHERE  Month(deathdatetr) = MONTH(ADDDATE(now(), INTERVAL 3 month))
       AND living = 0
ORDER  BY Day(deathdatetr),
          lastname
SQL;
        $result = $this->query($sql);

        $rows = array();
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        return $rows;
    }

    public function getMarriageAnniversaries($month)
    {
        $sql = <<<SQL
SELECT h.gedcom,
	   h.personid AS personid1,
       h.firstname AS firstname1,
       h.lastname AS lastname1,
       w.personid AS personid2,
       w.firstname AS firstname2,
       w.lastname AS lastname2,
	   f.familyID,
       f.marrdate,
       f.marrplace,
       Year(Now()) - Year(marrdatetr) AS Years
FROM   {$this->tables['families_table']} as f
    LEFT JOIN {$this->tables['people_table']} AS h
              ON f.husband = h.personid
       LEFT JOIN {$this->tables['people_table']} AS w
              ON f.wife = w.personid
WHERE  Month(f.marrdatetr) = {$month}
       
ORDER  BY Day(f.marrdatetr)
          
SQL;
        $result = $this->query($sql);

        $rows = array();
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        return $rows;
    }


    public function searchPerson($searchFirstName, $searchLastName)
    {
        $wheres = array();
        if ($searchFirstName) {
            $wheres[] = "firstname LIKE '%{$searchFirstName}%'";
        }
        if ($searchLastName) {
            $wheres[] = "lastname LIKE '{$searchLastName}%'";
        }

        $rows = array();
        $where = null;
        if (count($wheres)) {
            $where = 'WHERE ' . implode(' AND ', $wheres);
        }
        $sql = <<<SQL
SELECT *
FROM {$this->tables['people_table']}
{$where}
ORDER BY firstname, lastname
LIMIT 100
SQL;

        $result = $this->query($sql);

        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        return $rows;
    }

    public function getTngUserName()
    {
        $user = $this->getTngUser();
        return $user['username'];
    }

    public function getTngUser()
    {
        $currentUser = wp_get_current_user();
        $userName = $currentUser->user_login;
        $query = "SELECT * FROM {$this->tables['users_table']} WHERE username='{$userName}'";
        $result = $this->query($query);
        $row = $result->fetch_assoc();
        if ($row) {
            return $row;
        }
        //ADDTB
        require_once(ABSPATH . 'wp-content/plugins/tng-wordpress-plugin/customconfig.php');
        wp_die(tng_login_form(""));
        
    }

    //ADDTB



    public function convertDate( $olddate ) {
    //additional month names (ie, different languages) may be added with same values in case multiple languages are used in the same database
    $months = array( "JAN"=>1, "FEB"=>2, "MAR"=>3, "APR"=>4, "MAY"=>5, "JUN"=>6, "JUL"=>7, "AUG"=>8, "SEP"=>9, "OCT"=>10, "NOV"=>11, "DEC"=>12 );
    $hebrewmonths = array( "TIS"=>1, "CHE"=>2, "HES"=>2, "KIS"=>3, "TEV"=>4, "TEB"=>4, "SHV"=>5, "SHE"=>5, "ADA"=>6, "VEA"=>7, "NIS"=>8, "IYA"=>9, "SIV"=>10, "TAM"=>11, "AB"=>12, "AV"=>12, "ELU"=>13 );
    //alternatives for "BEF" and "AFT" should be entered in these lists separated by commas
    $befarray = array( "BEF" );
    $aftarray = array( "AFT" );
    $lastday = array( 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
    $preferred_separator = "/";  //this character separates the components in a numeric date, as in "MM/DD/YYYY"
    $numeric_date_order = 0;  //0 = MM/DD/YYYY; 1 = DD/MM/YYYY
    
    if( $olddate ) {
        $olddate = strtoupper(trim( $olddate ));
        $dateparts = array();
        $dateparts = explode(" ",$olddate);

        $found = array_search( "TO", $dateparts );
        if( !$found ) $found = array_search( "AND", $dateparts );
        $ptr = $found ? $found - 1 : count($dateparts) - 1;

        $newparts = array();
        $newparts = explode($preferred_separator, $dateparts[$ptr] );
        //if number of parts is 3, insert them into array at $ptr, move $ptr up
        if( count( $newparts ) == 3 ) {
            $dateparts[$ptr++] = $newparts[0];
            $dateparts[$ptr++] = $newparts[1];
            $dateparts[$ptr] = $newparts[2];
            $reversedate = $numeric_date_order;
        }
        else
            $reversedate = 0;

        $slashpos = strpos($dateparts[$ptr],"/");
        if($slashpos) {
            $wholeyear1 = strtok($dateparts[$ptr],"/");
            $wholeyear2 = strtok("/");
            $len = -1 * strlen($wholeyear2);
            $len1 = strlen($wholeyear1);
            $century = substr($wholeyear1,0,$len1+$len);
            $year1 = substr($wholeyear1,$len1+$len);
            $year2 = $wholeyear2;
            if($year1 > $year2) $century++;
            $tempyear = $century . $year2;
        }
        else {
            $len = -1 * strlen($dateparts[$ptr]);
            if($len < -4) $len = -4;
            $tempyear = trim(substr($dateparts[$ptr],$len));
            $dash = strpos($tempyear,"-");
            if($dash !== false)
                $tempyear = substr($tempyear,$dash+1);
        }
        if( is_numeric( $tempyear ) ) {
            $newyear = $tempyear;
            $ptr--;
            
            $tempmonth = trim(substr(strtoupper($dateparts[$ptr]),0,3));
            //if it's in $months, or it's numeric and we're doing dd-mm-yyyy, proceed. If it's numeric and we're doing mm-dd-yyyy, then flip day and month
            $foundit = 0;
            if( $months[$tempmonth] ) {
                $newmonth = $months[$tempmonth];
                $foundit = 1;
            }
            elseif( $hebrewmonths[$tempmonth] ) {
                $newmonth = $hebrewmonths[$tempmonth];
                $foundit = 2;
            }
            elseif( is_numeric( $tempmonth ) && strlen($tempmonth) <= 2 ) {
                $newmonth = intval( $tempmonth );
                $foundit = 1;
            }
            if( $foundit ) {
                $ptr--;
                
                $tempday = $dateparts[$ptr];
            }

            if($foundit == 1) {
                //if we're doing mm/dd/yyyy, we need to switch month and day here
                //it could be numeric, or it could be in $months, if we've switched.
                if( $reversedate ) {
                    $temppart = $newmonth;
                    $newmonth = $tempday;
                    $tempday = $temppart;
                }
                if( is_numeric( $tempday ) && strlen($tempday) <= 2 ) {
                    $newday = sprintf( "%02d", $tempday );
                    $ptr--;
                    $str = substr(strtoupper($dateparts[$ptr]),0,3);
                    if( in_array( $str, $aftarray ) ) {
                        $newday++;
                        if( $newday > $lastday[$newmonth-1] ) {
                            $newday = 0;
                            if( $newmonth == 12 ) $newyear++;
                            $newmonth = $newmonth < 12 ? $newmonth + 1 : 1;
                        }
                    }
                    else if( in_array( $str, $befarray ) ) {
                        $newday --;
                    }
                }
                else {
                    $tempday2 = substr(strtoupper($tempday),0,3);
                    $newday = 0;
                    if( in_array( $tempday2, $aftarray ) ) {
                        if( $newmonth == 12 ) $newyear++;
                        $newmonth = $newmonth < 12 ? $newmonth + 1 : 1;
                    }
                }
            }
            elseif($foundit == 2 ) {
                //Hebrew
                if(!$tempday) $tempday = 1;
                $gregoriandate = JDtoGregorian( JewishToJD( $newmonth, $tempday, $newyear ) );
                $newdate = explode("/", $gregoriandate);
                $newyear = $newdate[2];
                $newmonth = $newdate[0];
                $newday= $newdate[1];
            }
            else {
                $newmonth = 0;
                $newday = 0;
                if( in_array( $tempmonth, $aftarray ) ) {
                    $newyear++;
                }
        }
        }
        $newdate = sprintf("%04d-%02d-%02d", $newyear,$newmonth,$newday);
    }
    else
        $newdate = "0000-00-00";
    return( $newdate );
}
    public function updateFamilyUser($data)
    {

       $marrdatetr = $this->convertDate($data["spousemarr_day"]);

        $sql = <<<SQL
UPDATE  {$this->tables['families_table']}
SET marrdatetr='{$marrdatetr}'
WHERE (husband = '{$data['personId']}' or wife = '{$data['personId']}')
SQL;
        $result = $this->query($sql);
       echo $sql; 
        return $result;
    }

}