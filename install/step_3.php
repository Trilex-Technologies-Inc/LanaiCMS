<?
    $_SESSION['cfg_title']=$_REQUEST['cfg_title'];
    $_SESSION['cfg_url']=$_REQUEST['cfg_url'];
    $_SESSION['cfg_dir']=$_REQUEST['cfg_dir'];
    $_SESSION['cfg_off']=$_REQUEST['cfg_off'];
    $_SESSION['cfg_log']=$_REQUEST['cfg_log'];
    $_SESSION['cfg_lang']=$_REQUEST['cfg_lang'];
    $_SESSION['cfg_offsettime']=$_REQUEST['cfg_offsettime'];
    $_SESSION['cfg_theme']=$_REQUEST['cfg_theme'];
    $_SESSION['username']=$_REQUEST['username'];
    $_SESSION['password']=$_REQUEST['password'];
    $_SESSION['cfg_email']=$_REQUEST['cfg_email'];
    $_SESSION['dbtype']=$_REQUEST['dbtype'];
    $_SESSION['dbhost']=$_REQUEST['dbhost'];
    $_SESSION['dbuser']=$_REQUEST['dbuser'];
    $_SESSION['dbpw']=$_REQUEST['dbpw'];
    $_SESSION['dbname']=$_REQUEST['dbname'];
    $_SESSION['tablepre']=$_REQUEST['tablepre'];
    $_SESSION['smtp_host']=$_REQUEST['smtp_host'];
    $_SESSION['smtp_port']=$_REQUEST['smtp_port'];
    $_SESSION['cfg_sendmail']=$_REQUEST['cfg_sendmail'];
    $_SESSION['cfg_footer']="&reg; Power by <a href=\"http://lanai.sf.net\" target=\"_blank\">Lanai Web Application Framework</a><br/>Lanai is Open Source software released under the <a href=\"license.txt\" title=\"GNU/GPL License\" target=\"_blank\">GNU/GPL license</a>.";

?>
<br />
<?

    include_once("../include/adodb/adodb.inc.php");
    $ADODB_CACHE_DIR=$_SESSION['cfg_dir']."/datacenter/cache/";
    $db=&ADONewConnection($_SESSION['dbtype']);
/* $charset = "SET NAMES 'utf8'"; 
    $db->query($charset);*/

    function dbexecute($title,$sql) {
        global $db;
        $rs=$db->execute($sql);
        //$db->debug=true;
        if ($rs) {
            ?><?=$title."&nbsp;&nbsp;["; ?><span style="color:green;"><?=_SETUP_OK; ?></span>]<?
        } else {
            ?><?=$title."&nbsp;&nbsp;["; ?><span style="color:red;"><?=_SETUP_FAILD; ?></span>]<?
        }
    }

    if ($db->NConnect($_SESSION['dbhost'],$_SESSION['dbuser'], $_SESSION['dbpw'], $_SESSION['dbname'])) {
?>
<b><?=_SETUP_CREATE_SYSTEM_TABLE; ?> :</b>
<ul>
    <li>
<?
$sql = "CREATE TABLE IF NOT EXISTS `".$_SESSION['tablepre']."user` (
    `userId` INT(11) NOT NULL AUTO_INCREMENT,

    `userFname` VARCHAR(100) DEFAULT NULL,
    `userLname` VARCHAR(100) DEFAULT NULL,

    `userAddress1` VARCHAR(150) DEFAULT NULL,
    `userAddress2` VARCHAR(150) DEFAULT NULL,

    `userCity` VARCHAR(100) DEFAULT NULL,
    `userState` VARCHAR(100) DEFAULT NULL,

    `cntId` CHAR(2) DEFAULT 'TH',

    `userZipcode` VARCHAR(15) DEFAULT NULL,

    `userPhone` VARCHAR(20) DEFAULT NULL,
    `userFax` VARCHAR(20) DEFAULT NULL,
    `userMobile` VARCHAR(20) DEFAULT NULL,

    `userEmail` VARCHAR(254) DEFAULT NULL,
    `userURL` VARCHAR(255) DEFAULT NULL,

    `userLogin` VARCHAR(50) DEFAULT NULL,
    `userPassword` VARCHAR(255) DEFAULT NULL,

    `userPrivilege` ENUM('a','m','u') NOT NULL DEFAULT 'u',

    `userCreated` DATETIME DEFAULT NULL,
    `userActive` ENUM('y','n') DEFAULT 'y',

    PRIMARY KEY (`userId`)
)";

dbexecute("Create Table Users",$sql);

?>
    <li>
<?
    $sql="CREATE TABLE IF NOT EXISTS `".$_SESSION['tablepre']."privilege` (
            `modAccess` enum('y','n') NOT NULL default 'y',
            `modId` int(10) unsigned NOT NULL,
            `userPrivilege` enum('a','u','m') NOT NULL
          )";
    dbexecute("Create Table Privilege",$sql);
?>
    <li>
<?
    $sql="CREATE TABLE IF NOT EXISTS `".$_SESSION['tablepre']."module` (
            `modId` int(10) unsigned NOT NULL auto_increment,
            `modTitle` varchar(50) NOT NULL,
            `modName` varchar(50) NOT NULL,
            `modActive` enum('y','n') default 'y',
            `modOrder` int(10) unsigned default '0',
            `modSetting` enum('y','n') NOT NULL default 'n',
            PRIMARY KEY  (`modId`)
          )";
    dbexecute("Create Table Module",$sql);

?>
    <li>
<?
    $sql="CREATE TABLE IF NOT EXISTS `".$_SESSION['tablepre']."block` (
            `blcId` int(10) unsigned NOT NULL auto_increment,
            `blcTitle` varchar(150) default NULL,
            `blcName` varchar(150) default NULL,
            `blcType` enum('b','r','c') NOT NULL default 'b',
            `blcRssUrl` varchar(255) default NULL,
            `blcRssRefesh` int(11) NOT NULL default '3600',
            `blcRssTime` int(11) NOT NULL,
            `blcContent` text,
            `blcPosition` enum('t','b','l','r','c') default 'l',
            `blcOrder` int(10) unsigned default '0',
            `blcActive` enum('y','n') default 'y',
            PRIMARY KEY  (`blcId`)
          )";
    dbexecute("Create Table Block",$sql);

?>
    <li>
<?
    $sql="CREATE TABLE IF NOT EXISTS `".$_SESSION['tablepre']."menu` (
            `mnuId` int(10) unsigned NOT NULL auto_increment,
            `mnuParentId` int(11) NOT NULL default '0',
            `mnuTitle` varchar(150) default NULL,
            `mnuUrl` varchar(255) default NULL,
            `mnuTarget` varchar(20) default NULL,
            `conId` int(10) unsigned NOT NULL default '0',
            `modId` int(10) unsigned NOT NULL default '0',
            `mnuType` enum('m','c','l') default 'l',
            `mnuActive` enum('y','n') default NULL,
            `mnuOrder` int(11) default '0',
            PRIMARY KEY  (`mnuId`)
          )";
    dbexecute("Create Table Menu",$sql);

?>
    <li>
<?
    $sql="CREATE TABLE IF NOT EXISTS `".$_SESSION['tablepre']."contact` (
            `conId` int(11) NOT NULL auto_increment,
            `conFname` varchar(40) default NULL ,
            `conLname` varchar(40) default NULL,
            `conPosition` varchar(80) default NULL,
            `conAddress1` varchar(80) default NULL,
            `conAddress2` varchar(80) default NULL,
            `conCity` varchar(50) default NULL,
            `conState` varchar(50) default NULL,
            `cntId` char(2) default 'TH',
            `conZipcode` varchar(30) default NULL,
            `conPhone` varchar(30) default NULL,
            `conFax` varchar(30) default NULL,
            `conMobile` varchar(30) default NULL,
            `conEmail` varchar(50) default NULL,
            `conURL` varchar(120) default NULL,
            `conActive` enum('y','n') default 'y',
            PRIMARY KEY  (`conId`)
          )";
    dbexecute("Create Table Contact",$sql);

?>
    <li>
<?
    $sql="CREATE TABLE IF NOT EXISTS `".$_SESSION['tablepre']."content` (
            `conId` int(10) unsigned NOT NULL auto_increment,
            `userId` int(11) NOT NULL,
            `conTitle` varchar(200) default NULL,
            `conBody1` text,
            `conBody2` text NOT NULL,
            `conCategory` char(1) NOT NULL default 'c',
            `conModified` timestamp NULL,
            `conActive` enum('y','n') default NULL,
            PRIMARY KEY  (`conId`)
          )";
    dbexecute("Create Table Content",$sql);

?>
    <li>
<?
    $sql="CREATE TABLE IF NOT EXISTS `".$_SESSION['tablepre']."news` (
            `nwsId` int(11) NOT NULL auto_increment,
            `chnId` int(11) NOT NULL default '0',
            `userId` int(11) NOT NULL,
            `nwsTitle` varchar(200) default NULL,
            `nwsPreface` text,
            `nwsBody` text,
            `nwsActive` enum('y','n') default 'y',
            `nwsCreate` datetime default NULL,
            `nwsModified` timestamp NULL default NULL,
            PRIMARY KEY  (`nwsId`)
          )";
    dbexecute("Create Table News",$sql);

?>
    <li>
<?
    $sql="CREATE TABLE IF NOT EXISTS `".$_SESSION['tablepre']."news_channel` (
            `chnId` int(11) NOT NULL auto_increment,
            `chnTitle` varchar(200) default NULL,
            `chnDescription` text,
            `chnActive` enum('y','n') NOT NULL default 'y',
            `chnModified` timestamp NULL default NULL,
            PRIMARY KEY  (`chnId`)
          )";
    dbexecute("Create Table News Channel",$sql);

?>
    <li>
<?
    $sql="CREATE TABLE IF NOT EXISTS `".$_SESSION['tablepre']."rss` (
            `rssId` int(10) unsigned NOT NULL auto_increment,
            `rssTitle` varchar(120) default NULL,
            `rssURL` varchar(80) default NULL,
            `rssReload` int(10) unsigned default NULL,
            `rssView` varchar(20) default 'list',
            `rssItemCount` int(10) unsigned default '5',
            `rssShowDescription` enum('y','n') default 'y',
            `rssNumColumn` int(10) unsigned default '2',
            `rssNumImage` int(10) unsigned default '5',
            `rssFixedImage` varchar(80) default NULL,
            `rssAlterImage` varchar(80) default NULL,
            `rssImageWidth` int(10) unsigned default NULL,
            `rssImageHeight` int(10) unsigned default NULL,
            `rssImageAlign` varchar(20) default 'left',
            `rssTarget` varchar(20) default '_blank',
            `rssOrder` int(10) unsigned default NULL,
            `rssActive` enum('y','n') default 'y',
            PRIMARY KEY  (`rssId`)
          )";
    dbexecute("Create Table RSS",$sql);
?>
    <li>
<?
    $sql="CREATE TABLE IF NOT EXISTS `".$_SESSION['tablepre']."country` (
            `cntId` char(2) NOT NULL,
            `cntName` varchar(100),
            PRIMARY KEY  (`cntId`)
          )";
    dbexecute("Create Table Country",$sql);
?>
    <li>
<?
    $sql="CREATE TABLE IF NOT EXISTS ".$_SESSION['tablepre']."poll (
					  pllId INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
					  pllTitle VARCHAR(200) NULL,
					  pllLag INTEGER UNSIGNED NULL,
					  pllActive ENUM('y','n') NULL DEFAULT 'y',
					  pllCreate TIMESTAMP NULL DEFAULT NULL,
					  PRIMARY KEY(pllId)
          )";
    dbexecute("Create Table Poll",$sql);
?>
    <li>
<?
    $sql="CREATE TABLE IF NOT EXISTS ".$_SESSION['tablepre']."poll_option (
					  ppoId INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
					  pllId INTEGER UNSIGNED NOT NULL,
					  ppoTitle VARCHAR(200) NULL,
					  ppoScore INTEGER UNSIGNED NULL DEFAULT 0,
					  PRIMARY KEY(ppoId)
          )";
    dbexecute("Create Table Poll Option ",$sql);
?>
    <li>
<?
    $sql="CREATE TABLE IF NOT EXISTS ".$_SESSION['tablepre']."poll_stat (
					  pllId INTEGER UNSIGNED NOT NULL,
					  pstIP VARCHAR(20) NULL,
					  pstTime BIGINT NULL
          )";
    dbexecute("Create Table Poll Stat ",$sql);
?>
    <li>
<?
    $sql="CREATE TABLE IF NOT EXISTS ".$_SESSION['tablepre']."tag (
			  tagId INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
			  tagWord VARCHAR(80) NOT NULL,
			  PRIMARY KEY(tagId)
          )";
    dbexecute("Create Table TAG ",$sql);
?>
    <li>
<?
    $sql="CREATE TABLE IF NOT EXISTS ".$_SESSION['tablepre']."item_tag (
			  tagId INTEGER UNSIGNED NOT NULL,
			  itmId INTEGER UNSIGNED NOT NULL,
			  itmType VARCHAR(20) NOT NULL
          )";
    dbexecute("Create Table Item TAG ",$sql);
?>
    <li>
<?
$sql = "CREATE TABLE IF NOT EXISTS ".$_SESSION['tablepre']."meta (
    mtaId INT(10) UNSIGNED NOT NULL DEFAULT '1',
    mtaKeywords VARCHAR(255) DEFAULT NULL,
    mtaDescription VARCHAR(255) DEFAULT NULL,
    mtaAbstract VARCHAR(100) DEFAULT NULL,
    mtaAuthor VARCHAR(75) DEFAULT NULL,
    mtaDistribution VARCHAR(20) DEFAULT NULL,
    mtaCopyright VARCHAR(255) DEFAULT NULL,
    mtaLogo VARCHAR(255) DEFAULT NULL,
    mtaFavicon VARCHAR(255) DEFAULT NULL,
    PRIMARY KEY (mtaId)
)";
    dbexecute("Create Table Meta-data",$sql);
?>
    <li>
<?
    $sql="CREATE TABLE IF NOT EXISTS ".$_SESSION['tablepre']."read (
			  catTitle VARCHAR(20) ,
			  redId INTEGER UNSIGNED NOT NULL,
			  redTotal INTEGER UNSIGNED NOT NULL DEFAULT 1
          )";
    dbexecute("Create Table Read",$sql);
?>
    <li>
<?
    $sql="CREATE TABLE IF NOT EXISTS ".$_SESSION['tablepre']."comment (
			  comId INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
			  catTitle VARCHAR(25) NOT NULL,
			  catId INTEGER UNSIGNED NOT NULL,
			  comDetail TEXT NULL,
			  comAuthor VARCHAR(80) NOT NULL,
			  comEmail VARCHAR(50) NOT NULL,
			  comDate TIMESTAMP NOT NULL,
			  PRIMARY KEY(comId)
          )";
    dbexecute("Create Table Comment",$sql);
?>
    <li>
<?
    $sql="CREATE TABLE IF NOT EXISTS ".$_SESSION['tablepre']."log (
			  logDatetime datetime NOT NULL,
			  logUAgent varchar(300) NOT NULL,
			  logIP varchar(15) NOT NULL,
			  pagId int(10) unsigned NOT NULL,
			  logState enum('h','v') NOT NULL,
			  KEY tbl_ln_log_FKIndex1 (pagId)
          )";
    dbexecute("Create Table Log",$sql);
?>
    <li>
<?
    $sql="CREATE TABLE IF NOT EXISTS ".$_SESSION['tablepre']."log_page (
			  pagId int(10) unsigned NOT NULL auto_increment,
			  pagTitle varchar(300) default NULL,
			  pagUrl varchar(300) default NULL,
			  PRIMARY KEY  (pagId)
          )";
    dbexecute("Create Table Page",$sql);
?>
    <li>
<?
    $sql="CREATE TABLE IF NOT EXISTS ".$_SESSION['tablepre']."log_stat (
			  statDate date NOT NULL,
			  statHit int(10) unsigned NOT NULL,
			  statVisit int(10) unsigned NOT NULL,
			  PRIMARY KEY  (statDate)
          )";
    dbexecute("Create Table Stat",$sql);
?>
    <li>
<?
    $sql="CREATE TABLE IF NOT EXISTS ".$_SESSION['tablepre']."banner (
			  banId int(10) unsigned NOT NULL auto_increment,
			  banTitle varchar(200) NOT NULL,
			  banDescription text NOT NULL,
			  banImage varchar(255) NOT NULL,
			  banURL varchar(255) NOT NULL,
			  banDate datetime NOT NULL,
			  banShow int(10) unsigned default '0',
			  banClick int(10) unsigned default '0',
			  PRIMARY KEY  (banId)
          )";
    dbexecute("Create Table Banner",$sql);
?>
    <li>
<?
$sql1 = "INSERT INTO ".$_SESSION['tablepre']."banner 
VALUES (NULL, 'example 1', 'description 1',
'https://wowslider.com/sliders/demo-93/data1/images/landscape.jpg',
'https://wowslider.com/sliders/demo-93/data1/images/landscape.jpg',
'2025-12-02 10:24:13', NULL, NULL)";
dbexecute("Insert Banner 1", $sql1);

$sql2 = "INSERT INTO ".$_SESSION['tablepre']."banner 
VALUES (NULL, 'example 2', 'description 2',
'https://wowslider.com/sliders/demo-93/data1/images/sunset.jpg',
'https://wowslider.com/sliders/demo-93/data1/images/sunset.jpg',
'2025-12-02 10:25:18', NULL, NULL)";
dbexecute("Insert Banner 2", $sql2);

$sql3 = "INSERT INTO ".$_SESSION['tablepre']."banner 
VALUES (NULL, 'example 3', 'description 3',
'https://fastly.picsum.photos/id/52/1024/480.jpg?hmac=EhPOe5u6CjvoQFyYjJFtpUOCAiW8-49KWTIgBmH4ct4',
'https://fastly.picsum.photos/id/52/1024/480.jpg?hmac=EhPOe5u6CjvoQFyYjJFtpUOCAiW8-49KWTIgBmH4ct4',
'2025-12-03 09:00:26', 0, 0)";
dbexecute("Insert Banner 3", $sql3);

?>
</ul>
<b><?=_SETUP_UPDATE_SYSTEM_TABLE; ?> :</b>
<ul>
    <li>
<?
    $sql="INSERT INTO `".$_SESSION['tablepre']."user` (`userId`, `userFname`, `userLname`, `userAddress1`, `userAddress2`, `userCity`, `userState`, `cntId`, `userZipcode`, `userPhone`, `userFax`, `userMobile`, `userEmail`, `userURL`, `userLogin`, `userPassword`, `userPrivilege`, `userCreated`, `userActive`)
            VALUES (1, 'Lanai', 'Core',
                ' ', ' ',
                ' ', ' ', 'TH', ' ', ' ', ' ',
                ' ', '".$_SESSION['cfg_email']."',
                ' ',
                '".$_SESSION['username']."', '".md5($_SESSION['password'])."',
                'a',
                NOW(), 'y')";
    dbexecute("Update Adminstrator information",$sql);
?>
    <li>
<?
    $sql="INSERT INTO `".$_SESSION['tablepre']."country` (`cntId`, `cntName`)
            VALUES ('AF', 'Afghanistan'),
            ('AL', 'Albania'),
            ('DZ', 'Algeria'),
            ('AS', 'American Samoa'),
            ('AD', 'Andorra'),
            ('AO', 'Angola'),
            ('AI', 'Anguilla'),
            ('AQ', 'Antarctica'),
            ('AG', 'Antigua And Barbuda'),
            ('AR', 'Argentina'),
            ('AM', 'Armenia'),
            ('AW', 'Aruba'),
            ('AU', 'Australia'),
            ('AT', 'Austria'),
            ('AZ', 'Azerbaijan'),
            ('BS', 'Bahamas'),
            ('BH', 'Bahrain'),
            ('BD', 'Bangladesh'),
            ('BB', 'Barbados'),
            ('BY', 'Belarus'),
            ('BE', 'Belgium'),
            ('BZ', 'Belize'),
            ('BJ', 'Benin'),
            ('BM', 'Bermuda'),
            ('BT', 'Bhutan'),
            ('BO', 'Bolivia'),
            ('BA', 'Bosnia Hercegovina'),
            ('BW', 'Botswana'),
            ('BV', 'Bouvet Island'),
            ('BR', 'Brazil'),
            ('IO', 'British Indian Ocean Territory'),
            ('BN', 'Brunei Darussalam'),
            ('BG', 'Bulgaria'),
            ('BF', 'Burkina Faso'),
            ('BI', 'Burundi'),
            ('KH', 'Cambodia'),
            ('CM', 'Cameroon'),
            ('CA', 'Canada'),
            ('CV', 'Cape Verde'),
            ('KY', 'Cayman Islands'),
            ('CF', 'Central African Republic'),
            ('TD', 'Chad'),
            ('CL', 'Chile'),
            ('CN', 'China'),
            ('CX', 'Christmas Island'),
            ('CC', 'Cocos (Keeling) Islands'),
            ('CO', 'Colombia'),
            ('KM', 'Comoros'),
            ('CG', 'Congo'),
            ('CD', 'Congo'),
            ('CK', 'Cook Islands'),
            ('CR', 'Costa Rica'),
            ('CI', 'Cote D''Ivoire'),
            ('HR', 'Croatia'),
            ('CU', 'Cuba'),
            ('CY', 'Cyprus'),
            ('CZ', 'Czech Republic'),
            ('CS', 'Czechoslovakia'),
            ('DK', 'Denmark'),
            ('DJ', 'Djibouti'),
            ('DM', 'Dominica'),
            ('DO', 'Dominican Republic'),
            ('TP', 'East Timor'),
            ('EC', 'Ecuador'),
            ('EG', 'Egypt'),
            ('SV', 'El Salvador'),
            ('GB', 'England'),
            ('GQ', 'Equatorial Guinea'),
            ('ER', 'Eritrea'),
            ('EE', 'Estonia'),
            ('ET', 'Ethiopia'),
            ('FK', 'Falkland Islands (Malvinas)'),
            ('FO', 'Faroe Islands'),
            ('FJ', 'Fiji'),
            ('FI', 'Finland'),
            ('FR', 'France'),
            ('FX', 'France'),
            ('GF', 'French Guiana'),
            ('PF', 'French Polynesia'),
            ('TF', 'French Southern Territories'),
            ('GA', 'Gabon'),
            ('GM', 'Gambia'),
            ('GE', 'Georgia'),
            ('DE', 'Germany'),
            ('GH', 'Ghana'),
            ('GI', 'Gibraltar'),
            ('GR', 'Greece'),
            ('GL', 'Greenland'),
            ('GD', 'Grenada'),
            ('GP', 'Guadeloupe'),
            ('GU', 'Guam'),
            ('GT', 'Guatemela'),
            ('GG', 'Guernsey'),
            ('GN', 'Guinea'),
            ('GW', 'Guinea-Bissau'),
            ('GY', 'Guyana'),
            ('HT', 'Haiti'),
            ('HM', 'Heard and McDonald Islands'),
            ('HN', 'Honduras'),
            ('HK', 'Hong Kong'),
            ('HU', 'Hungary'),
            ('IS', 'Iceland'),
            ('IN', 'India'),
            ('ID', 'Indonesia'),
            ('IR', 'Iran (Islamic Republic Of)'),
            ('IQ', 'Iraq'),
            ('IE', 'Ireland'),
            ('IM', 'Isle Of Man'),
            ('IL', 'Israel'),
            ('IT', 'Italy'),
            ('JM', 'Jamaica'),
            ('JP', 'Japan'),
            ('JE', 'Jersey'),
            ('JO', 'Jordan'),
            ('KZ', 'Kazakhstan'),
            ('KE', 'Kenya'),
            ('KI', 'Kiribati'),
            ('KP', 'Korea'),
            ('KR', 'Korea'),
            ('KW', 'Kuwait'),
            ('KG', 'Kyrgyzstan'),
            ('LA', 'Lao People''s Democratic Republic'),
            ('LV', 'Latvia'),
            ('LB', 'Lebanon'),
            ('LS', 'Lesotho'),
            ('LR', 'Liberia'),
            ('LY', 'Libyan Arab Jamahiriya'),
            ('LI', 'Liechtenstein'),
            ('LT', 'Lithuania'),
            ('LU', 'Luxembourg'),
            ('MO', 'Macau'),
            ('MK', 'Macedonia'),
            ('MG', 'Madagascar'),
            ('MW', 'Malawi'),
            ('MY', 'Malaysia'),
            ('MV', 'Maldives'),
            ('ML', 'Mali'),
            ('MT', 'Malta'),
            ('MH', 'Marshall Islands'),
            ('MQ', 'Martinique'),
            ('MR', 'Mauritania'),
            ('MU', 'Mauritius'),
            ('YT', 'Mayotte'),
            ('MX', 'Mexico'),
            ('FM', 'Micronesia'),
            ('MD', 'Moldova'),
            ('MC', 'Monaco'),
            ('MN', 'Mongolia'),
            ('MS', 'Montserrat'),
            ('MA', 'Morocco'),
            ('MZ', 'Mozambique'),
            ('MM', 'Myanmar'),
            ('NA', 'Namibia'),
            ('NR', 'Nauru'),
            ('NP', 'Nepal'),
            ('NL', 'Netherlands'),
            ('AN', 'Netherlands Antilles'),
            ('NT', 'Neutral Zone'),
            ('NC', 'New Caledonia'),
            ('NZ', 'New Zealand'),
            ('NI', 'Nicaragua'),
            ('NE', 'Niger'),
            ('NG', 'Nigeria'),
            ('NU', 'Niue'),
            ('NF', 'Norfolk Island'),
            ('MP', 'Northern Mariana Islands'),
            ('NO', 'Norway'),
            ('OM', 'Oman'),
            ('PK', 'Pakistan'),
            ('PW', 'Palau'),
            ('PS', 'Palestine'),
            ('PA', 'Panama'),
            ('PG', 'Papua New Guinea'),
            ('PY', 'Paraguay'),
            ('PE', 'Peru'),
            ('PH', 'Philippines'),
            ('PN', 'Pitcairn'),
            ('PL', 'Poland'),
            ('PT', 'Portugal'),
            ('PR', 'Puerto Rico'),
            ('QA', 'Qatar'),
            ('RE', 'Reunion'),
            ('RO', 'Romania'),
            ('RU', 'Russian Federation'),
            ('RW', 'Rwanda'),
            ('SH', 'Saint Helena'),
            ('KN', 'Saint Kitts And Nevis'),
            ('LC', 'Saint Lucia'),
            ('PM', 'Saint Pierre and Miquelon'),
            ('VC', 'Saint Vincent and The Grenadines'),
            ('WS', 'Samoa'),
            ('SM', 'San Marino'),
            ('ST', 'Sao Tome and Principe'),
            ('SA', 'Saudi Arabia'),
            ('SN', 'Senegal'),
            ('SC', 'Seychelles'),
            ('SL', 'Sierra Leone'),
            ('SG', 'Singapore'),
            ('SK', 'Slovakia'),
            ('SI', 'Slovenia'),
            ('SB', 'Solomon Islands'),
            ('SO', 'Somalia'),
            ('ZA', 'South Africa'),
            ('GS', 'South Georgia and The Sandwich Islands'),
            ('ES', 'Spain'),
            ('LK', 'Sri Lanka'),
            ('SD', 'Sudan'),
            ('SR', 'Suriname'),
            ('SJ', 'Svalbard and Jan Mayen Islands'),
            ('SZ', 'Swaziland'),
            ('SE', 'Sweden'),
            ('CH', 'Switzerland'),
            ('SY', 'Syrian Arab Republic'),
            ('TW', 'Taiwan'),
            ('TJ', 'Tajikista'),
            ('TZ', 'Tanzania'),
            ('TH', 'Thailand'),
            ('TG', 'Togo'),
            ('TK', 'Tokelau'),
            ('TO', 'Tonga'),
            ('TT', 'Trinidad and Tobago'),
            ('TN', 'Tunisia'),
            ('TR', 'Turkey'),
            ('TM', 'Turkmenistan'),
            ('TC', 'Turks and Caicos Islands'),
            ('TV', 'Tuvalu'),
            ('UG', 'Uganda'),
            ('UA', 'Ukraine'),
            ('AE', 'United Arab Emirates'),
            ('UK', 'United Kingdom'),
            ('US', 'United States'),
            ('UM', 'United States Minor Outlying Islands'),
            ('UY', 'Uruguay'),
            ('SU', 'USSR'),
            ('UZ', 'Uzbekistan'),
            ('VU', 'Vanuatu'),
            ('VA', 'Vatican City State'),
            ('VE', 'Venezuela'),
            ('VN', 'Vietnam'),
            ('VG', 'Virgin Islands (British)'),
            ('VI', 'Virgin Islands (U.S.)'),
            ('WF', 'Wallis and Futuna Islands'),
            ('WG', 'West Bank and Gaza'),
            ('EH', 'Western Sahara'),
            ('YE', 'Yemen'),
            ('YU', 'Yugoslavia'),
            ('ZR', 'Zaire'),
            ('ZM', 'Zambia'),
            ('ZW', 'Zimbabwe'),
            ('A1', 'Private Proxy'),
            ('A2', 'Satellite')";
    dbexecute("Update Country Data",$sql);
?>
    <li>
<?
$sql = "INSERT INTO `".$_SESSION['tablepre']."block` 
(`blcId`, `blcTitle`, `blcName`, `blcType`, `blcRssUrl`, `blcRssRefesh`, `blcRssTime`, `blcContent`, `blcPosition`, `blcOrder`, `blcActive`)
VALUES
(6, 'top', 'top', 'c', '', 0, 1764779424, '<div class=\"col-md-7\">
<h2 class=\"featurette-heading fw-normal lh-1\">First featurette heading. <span class=\"text-body-secondary\">It\'ll blow your mind.</span></h2>
<p class=\"lead\">Some great placeholder content for the first featurette here. Imagine some exciting prose here.</p>
</div>
<div class=\"col-md-5\"><svg aria-label=\"Placeholder: 500x500\" class=\"bd-placeholder-img bd-placeholder-img-lg featurette-image img-fluid mx-auto\" height=\"500\" preserveAspectRatio=\"xMidYMid slice\" role=\"img\" width=\"500\" xmlns=\"http://www.w3.org/2000/svg\">
<title>Placeholder</title>
<rect width=\"100%\" height=\"100%\" fill=\"var(--bs-secondary-bg)\"></rect>
<text x=\"50%\" y=\"50%\" fill=\"var(--bs-secondary-color)\" dy=\".3em\">500x500</text>
</svg></div>', 't', 1, 'y'),

(8, 'center', 'center', 'c', '', 0, 1764779561, '<div class=\"col-md-5\"><svg aria-label=\"Placeholder: 500x500\" class=\"bd-placeholder-img bd-placeholder-img-lg featurette-image img-fluid mx-auto\" height=\"500\" preserveAspectRatio=\"xMidYMid slice\" role=\"img\" width=\"500\" xmlns=\"http://www.w3.org/2000/svg\">
<title>Placeholder</title>
<rect width=\"100%\" height=\"100%\" fill=\"var(--bs-secondary-bg)\"></rect>
<text x=\"50%\" y=\"50%\" fill=\"var(--bs-secondary-color)\" dy=\".3em\">500x500</text>
</svg></div>
<div class=\"col-md-7\">
<h2 class=\"featurette-heading fw-normal lh-1\">And lastly, this one. <span class=\"text-body-secondary\">Checkmate.</span></h2>
<p class=\"lead\">And yes, this is the last block of representative placeholder content. Again, not really intended to be actually read, simply here to give you a better view of what this would look like with some actual content. Your content.</p>
</div>', 'c', 2, 'y'),

(9, 'bottom', 'bottom', 'c', '', 0, 1764779655, '<div class=\"col-md-7 order-md-2\">
<h2 class=\"featurette-heading fw-normal lh-1\">Oh yeah, it\'s that good. <span class=\"text-body-secondary\">See for yourself.</span></h2>
<p class=\"lead\">Another featurette? Of course. More placeholder content here to give you an idea of how this layout would work with some actual real-world content in place.</p>
</div>
<div class=\"col-md-5 order-md-1\"><svg aria-label=\"Placeholder: 500x500\" class=\"bd-placeholder-img bd-placeholder-img-lg featurette-image img-fluid mx-auto\" height=\"500\" preserveAspectRatio=\"xMidYMid slice\" role=\"img\" width=\"500\" xmlns=\"http://www.w3.org/2000/svg\">
<title>Placeholder</title>
<rect width=\"100%\" height=\"100%\" fill=\"var(--bs-secondary-bg)\"></rect>
<text x=\"50%\" y=\"50%\" fill=\"var(--bs-secondary-color)\" dy=\".3em\">500x500</text>
</svg></div>', 'b', 3, 'y')";

dbexecute("Update Block Data",$sql);
?>
    <li>
<?
    $sql="INSERT INTO ".$_SESSION['tablepre']."news_channel 
    			VALUES (1, 'General', 'News & Information in general category.', 'y', '2009-01-12 19:28:55');";
    dbexecute("Update Channel Sample Data",$sql);
?>
    <li>
<?
    $sql="INSERT INTO ".$_SESSION['tablepre']."news 
    			VALUES 	(1, 1, 1, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam eu felis. Nulla mattis massa eu erat. Nullam facilisis dolor a mi. Suspendisse libero ante, mollis ultrices, tincidunt a, lacinia non, libero. Ut ultricies lacus eu diam. Nunc sit amet est ac lacus hendrerit rutrum. Cras a metus. Fusce volutpat laoreet dolor. Integer ac massa. Maecenas id erat. Maecenas pulvinar, velit sed aliquam faucibus, elit nulla rhoncus magna, eu fermentum metus velit in sapien. Vivamus interdum rutrum tortor.</p>', '<p>Sed nunc. Duis suscipit ante sed libero. Suspendisse hendrerit sollicitudin enim. Ut at libero. Morbi rutrum adipiscing turpis. Ut lacinia magna at ante. Nulla suscipit augue eget sem. Donec hendrerit ullamcorper lacus. Proin non libero quis mi congue blandit. Donec consequat, quam mollis facilisis ultrices, odio ipsum commodo tortor, at hendrerit risus augue ut nulla. Vestibulum a odio. Donec interdum urna eu felis. Etiam tempus, nunc vel dignissim hendrerit, neque ante euismod justo, eget dignissim sapien diam ut tortor. Cras pharetra lobortis nisl. Mauris congue. Nam vel sem. Maecenas id neque. Maecenas blandit nulla. Maecenas lacinia ligula a tellus lacinia semper.</p>', 'y', '2009-01-12 19:31:18', '2009-01-12 19:31:18'),
						(2, 1, 1, 'Cras tortor. Cras et sem sed magna lobortis pharetra', '<p>Cras tortor. Cras et sem sed magna lobortis pharetra. Etiam a metus. Aenean id urna et ante ornare molestie. Mauris iaculis, tellus ac tempus vestibulum, nisl neque facilisis velit, in sagittis erat arcu vitae mauris. Nulla tellus. Sed convallis, ipsum sit amet mattis faucibus, pede enim auctor turpis, at pulvinar orci orci feugiat mauris. Sed lectus felis, interdum eu, condimentum nec, pretium eget, justo. Phasellus lobortis mauris ac quam. Praesent lacus enim, dictum et, vehicula at, lacinia et, dolor. Cras condimentum justo quis lorem. Aliquam arcu. Nullam nunc. Fusce eu elit. Nam posuere, lectus vulputate laoreet eleifend, risus nisl molestie metus, ut suscipit massa dolor tincidunt risus.</p>', '<p>Vivamus quis augue quis neque pharetra venenatis. Ut nisi pede, accumsan ut, aliquet ac, commodo ultricies, pede. Praesent nec pede id mauris suscipit porttitor. Mauris sollicitudin, est non varius tincidunt, velit diam hendrerit orci, eget consequat ligula lectus vel nisl. Praesent venenatis ante sed nisi egestas egestas. Donec tellus est, pharetra non, pellentesque semper, pellentesque sit amet, tortor. Sed eget turpis. Nullam felis urna, vehicula eget, consectetur non, bibendum ac, ante. Sed faucibus nunc tempor arcu. Fusce erat nulla, lobortis nec, adipiscing quis, blandit quis, orci. Aenean et risus. Morbi a quam et nibh bibendum tristique. Nullam nec felis eu mauris pharetra varius. Etiam at nibh. Vestibulum adipiscing.</p>', 'y', '2009-01-12 19:31:51', '2009-01-12 19:40:26');";
    dbexecute("Update News Sample Data",$sql);
?>

    <li>
<?
    $sql="INSERT INTO ".$_SESSION['tablepre']."poll 
    				VALUES	(1, 'What color do you like?', 86400, 'y', '2009-01-12 20:20:56');";
    dbexecute("Update Poll Sample Data",$sql);
?>
    <li>
<?
    $sql="INSERT INTO ".$_SESSION['tablepre']."poll_option 
    				VALUES	(1, 1, 'Red', 0),
							(2, 1, 'Green', 0),
							(3, 1, 'Blue', 0),
							(4, 1, 'Orange', 0),
							(5, 1, 'Pink', 0),
							(6, 1, 'Black', 0),
							(7, 1, 'White', 0),
							(8, 1, 'Gray', 0),
							(9, 1, 'Yellow', 0),
							(10, 1, '', 0),
							(11, 1, '', 0),
							(12, 1, '', 0);";
    dbexecute("Update Poll Items Sample Data",$sql);
?>
    <li>
<?
    $sql="INSERT INTO ".$_SESSION['tablepre']."contact 
    				VALUES	(1, 'Anuchit', 'Chalothorn', 'Project Manager', ' Software', '107 Moo 10 T.Suranaree', 'A.Muang', 'Nakhon Ratchasima', 'TH', '30000', '+66 44 214 187', '+66 44 214 187', '+ 66 898 433 717', 'anuchit@laniacms.com', 'http://www,lanaicms.com/', 'y');";
    dbexecute("Update Contact Sample Data",$sql);
?>
	<li>
<?
    $sql="INSERT INTO `".$_SESSION['tablepre']."module` (`modId`, `modTitle`, `modName`, `modActive`, `modOrder`, `modSetting`)
            VALUES  
					(1, 'block', 'block', 'y', 2, 'y'),
					(2, 'contact', 'contact', 'y', 2, 'y'),
					(3, 'content', 'content', 'y', 2, 'y'),
					(4, 'language', 'language', 'y', 2, 'y'),                   
					(5, 'member', 'member', 'y', 2, 'y'),
					(6, 'menu', 'menu', 'y', 2, 'y'),
					(7, 'module', 'module', 'y', 2, 'y'),
					(8, 'news', 'news', 'y', 2, 'y'),
					(9, 'rssthai', 'rssthai', 'y', 2, 'y'),
					(10, 'theme', 'theme', 'y', 2, 'y'),
					(11, 'sitemap', 'sitemap', 'y', 2, 'y'),
					(12, 'backup', 'backup', 'y', 2, 'y'),
					(14, 'poll', 'poll', 'y', 2, 'y'),
					(15, 'explorer', 'explorer', 'y', 2, 'y'),
					(20, 'config', 'config', 'y', 2, 'y'),
					(22, 'setting', 'setting', 'y', 2, 'y'),
					(23, 'sitemap', 'sitemap', 'y', 2, 'y'),
					(24, 'info', 'info', 'y', 2, 'y'),
					(25, 'carousel', 'carousel', 'y', 2, 'y'),
					(27, 'log', 'log', 'y', 2, 'y'),
					(30, 'search', 'search', 'y', 2, 'y')		
            ";
    dbexecute("Update Module Data",$sql);
?>
   <li>
<?
    $sql="INSERT INTO `".$_SESSION['tablepre']."privilege` (`modAccess`, `modId`, `userPrivilege`)
            VALUES ('y', 1, 'a'),
					('y', 2, 'a'),
					('y', 3, 'a'),
					('y', 4, 'a'),
					('y', 5, 'a'),
					('y', 6, 'a'),
					('y', 7, 'a'),
					('y', 8, 'a'),
					('y', 9, 'a'),
					('y', 10, 'a'),
					('y', 12, 'a'),
					('y', 14, 'a'),
					('y', 15, 'a'),
					('y', 20, 'a'),
					('y', 24, 'a'),
					('y', 25, 'a')
            ";
    dbexecute("Update Privilege Data",$sql);
?>
   <li>
<?
    $sql="INSERT INTO  `".$_SESSION['tablepre']."menu` (`mnuId`, `mnuParentId`, `mnuTitle`, `mnuUrl`, `mnuTarget`, `conId`, `modId`, `mnuType`, `mnuActive`, `mnuOrder`)
            VALUES  (1, 0, 'Home', '".$_SESSION['cfg_url']."', '', 0, 0, 'l', 'y', 1),
					(2, 0, 'News', NULL, NULL, 0, 8, 'm', 'y', 2),
					(5, 0, 'Poll', NULL, NULL, 0, 14, 'm', 'y', 5),
					(14, 0, 'Search', NULL, NULL, 0, 30, 'm', 'y', 9),
					(13, 0, 'Statistic', NULL, NULL, 0, 27, 'm', 'y', 11),
					(4, 0, 'Contact', NULL, NULL, 0, 2, 'm', 'y', 12),	
					(6, 0, 'Site Map', NULL, NULL, 0, 11, 'm', 'y', 13),					
					(7, 0, 'Login', 'module.php?modname=member&mf=memloginform', NULL, 0, 0, 'l', 'y', 14)					
            ";
    dbexecute("Update Menu Data",$sql);
?>
 <li>
<?
$sql = "INSERT INTO ".$_SESSION['tablepre']."meta
(mtaId, mtaKeywords, mtaDescription, mtaAbstract, mtaAuthor, mtaDistribution, mtaCopyright, mtaLogo, mtaFavicon)
VALUES (
    1,
    'lanaicms, opensharepoint, opensource, lanai, lanai-cms',
    'This is my site.',
    'This is my site.',
    'lanaicms@lanaicms.com/',
    'Global',
    'Copyright (c) 2007 to 2025',
    NULL,
    NULL
)";
?>
</ul>
<TABLE  ALIGN="right" >
<FORM METHOD="POST" ACTION="<?=$_SERVER['PHP_SELF']; ?>">
<TR>
	<TD ALIGN="RIGHT">
		<INPUT TYPE="hidden" NAME="step" VALUE="<?=($_REQUEST['step']-1)?>">
		<INPUT TYPE="button" class="btn btn-outline-secondary"  VALUE="< <?=_SETUP_BACK; ?>" onClick="javascript:history.back();">
	</TD>
	<TD>
		<INPUT TYPE="hidden" NAME="step" VALUE="<?=($_REQUEST['step']+1)?>">
        <INPUT TYPE="submit" class="btn btn-primary" VALUE="<?=_SETUP_CREATE_CONFIG; ?> >" >
	</TD>
</TR>
</FORM>
</TABLE>
<?
    } else {
    ?>
    <CENTER>
	<IMG SRC="../theme/default/images/worning.gif" ALIGN="absmiddle">&nbsp;<STRONG><?=_SETUP_CANNOT_CONNECT; ?> '<?=$_SESSION['dbname']; ?>' <?=_SETUP_CANNOT_CONNECT_REFRESH; ?></STRONG>
	</CENTER>
    <?
    }
?>