<?php
/**************************************************************************/
/* This is the language module with all the system messages               */
/*                                                                        */
/* If you made a translation, please go to the site and send to me        */
/* the translated file. Please keep the original text order by modules,   */
/* and just one message per line, also double check your translation!     */
/*                                                                        */
/* You need to change the second quoted phrase, not the capital one!      */
/*                                                                        */
/* If you need to use double quotes (") remember to add a backslash (\),  */
/* so your entry will look like: This is \"double quoted\" text.          */
/* And, if you use HTML code, please double check it.                     */
/**************************************************************************/
/*    Language : Thai                                                     */
/*    Translator : Mr.Wutthipron Eamjikuson <webmaster@siamhostdd.com>    */
/*																		  */
/*                                                                        */
/**************************************************************************/
	define("_SETUP_CHARSET","UTF-8");
    define("_SETUP_SELECT_LANGUAGE","กรุณาเลือกภาษาที่คุณต้องการ");
    define("_SETUP_LICENSE_AGREEMENT","คลิ๊กเพื่ออ่านข้อกำหนดลิขสิทธิการใช้งาน");
	define("_SETUP_ASK_AGREEMENT","คุณได้อ่านข้อมูลลิขสิทธิแบบ GNU/GPL เรียบร้อยแล้ว,");
	define("_SETUP_AGREE","ยอมรับ");
	define("_SETUP_DISAGREE","ไม่ยอมรับ");

    define("_SETUP_CHANGEPMSS","กรุณากำหนดสิทธิในการใช้งานต่าง ๆ และการเข้าถึงไดเร็คทรอรี่ต่าง ๆ รวมถึงการเข้าถึงการเขียนข้อมูลในเว็บเซิร์ฟเวอร์ของคุณด้วย.");
    define("_SETUP_WRITEABLE","เขียนข้อมูลได้");
    define("_SETUP_CREATE_LATER","สร้างไฟล์หลังจากตั้งค่าระบบสมบูรณ์แล้ว");
    define("_SETUP_CANNOTWRITE","เขียนข้อมูลไม่ได้");
    define("_SETUP_CONFIG","ตั้งค่าถัดไป");
    define("_SETUP_BACK","ย้อนกลับ");

    define("_SETUP_SITEINFO","ข้อมูลทั่วไปของเว็ปไซต์");
    define("_SETUP_ADMININFO","ข้อมูลผู้ดูแลระบบ");
    define("_SETUP_DATABASEINFO","ข้อมูลการเข้าถึงฐานข้อมูล");
    define("_SETUP_MAILINFO","ข้อมลระบบบริการเมล์");

    define("_SETUP_SITENAME","ชื่อเว็ปไซต์");
    define("_SETUP_URL","URL");
    define("_SETUP_DIR","ไดเรคทรอรี่");
    define("_SETUP_LANG","ภาษา");
    define("_SETUP_THEME","ธีม");
    define("_SETUP_TIMEZONE","ตั้งค่าเวลา");

    define("_SETUP_USERNAME","ชื่อผู้ใช้");
    define("_SETUP_PASSWORD","รหัสผ่าน");
    define("_SETUP_EMAIL","อีเมล์");

    define("_SETUP_DB","ฐานข้อมูล");
    define("_SETUP_DBHOST","โฮส");
    define("_SETUP_DBUSER","ชื่อผู้ใช้");
    define("_SETUP_DBPWD","รหัสผ่าน");
    define("_SETUP_DBNAME","ชื่อฐานข้อมูล");
    define("_SETUP_TABLEPRE","คำนำหน้าตาราง");
    define("_SETUP_EXISTING_TABLES","ตารางเดิม");
    define("_SETUP_USE_EXISTING_TABLES","ใช้ตารางเดิมและเก็บข้อมูลไว้");
    define("_SETUP_RECREATE_TABLES","สร้างตาราง CMS ใหม่");
    define("_SETUP_RECREATE_TABLES_WARNING","การสร้างตารางใหม่จะลบข้อมูล CMS เดิมที่ใช้คำนำหน้านี้อย่างถาวร");
    define("_SETUP_USING_EXISTING_TABLES","เก็บตาราง CMS เดิมไว้แล้ว ไม่มีการเปลี่ยนแปลงโครงสร้างหรือข้อมูลตัวอย่าง");

    define("_SETUP_SMHOST","SMTP โฮส");
    define("_SETUP_SMPORT","SMTP พอร์ต");
    define("_SETUP_SNT2ADMIN","ส่งค่าไปยังผู้ดูแลระบบ.");

	define("_SETUP_YES","ตกลง");
	define("_SETUP_NO","ไม่ตกลง");

    define("_SETUP_CREATE_TABLE","ปุ่มถัดไปเพื่อสร้างตารางข้อมูล");

    define("_SETUP_CREATE_SYSTEM_TABLE","สร้างตารางข้อมูลที่จำเป็นสำหรับระบบ");
    define("_SETUP_UPDATE_SYSTEM_TABLE","อัพเด็ดตารางสำหรับระบบ");

    define("_SETUP_OK","เรียบร้อย");
    define("_SETUP_FAILD","ล้มเหลว");

    define("_SETUP_CREATE_CONFIG","ปุ่มถัดไปเพื่อสร้างคอนฟิคไฟล์");
    define("_SETUP_CANNOT_CONNECT","ไม่สามารถเชื่อมต่อฐานข้อมูลได้,กรุณาสร้างฐานข้อมูลชื่อ");
    define("_SETUP_CANNOT_CONNECT_REFRESH","จากนั้นรีเฟรชหน้านี้ใหม่อีกครั้ง.");

    define("_SETUP_COPY_CODE","คัดลอกค่าคอนฟิกที่ได้เพื่อนำไปสร้างไฟล์");

    define("_SETUP_VERIFY_CONFIG","ปุ่มถัดไปเพื่อตรวจสอบไฟล์คอนฟิก");

   define("_SETUP_CLEANUP","ปุ่มถัดไปเพื่อเคลียร์ค่าระบบ");

   define("_SETUP_CHECK_CONFIG","ตรวจสอบไฟล์คอนฟิก");

   define("_SETUP_COMPLETE","ขอแสดงความยินดีด้วยการติดตั้ง LANAI-CORE เสร็จสมบูรณ์!");

   define("_SETUP_DELETEDIR","ให้ลบไดเร็คทรอรี่ 'install' จากระบบก่อนดำเนินการขั้นต่อไป!");

   define("_SETUP_GOTOHOME","ไปยังโฮมเพจของคุณ");
   define("_SETUP_GOTOADMINPAGE","ไปยังหน้าผู้ดูแลระบบ");

?>
