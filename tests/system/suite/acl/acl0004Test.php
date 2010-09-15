<?php
/**
 * @version		$Id$
 * @package		Joomla.SystemTest
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * Tests site login permissions per global configuration permission settings.
 */
require_once 'SeleniumJoomlaTestCase.php';

class Acl0004Test extends SeleniumJoomlaTestCase
{
	function testSiteLoginPermissions()
	{
		$this->setUp();
		$this->gotoAdmin();
		$this->doAdminLogin();
		
		//Set random salt 
		$salt = mt_rand();
		
		//Set message to be checked
		$message='You cannot access the private section of this site.';		
 
	    //Define test user	
		$username = 'ACL Test User' . $salt;
		$password = 'password' . $salt; 
		$login = 'acltestuser' . $salt;
		$email = $login . '@test.com';
		$group = 'Public';
		echo "Create $username and add to $group group.\n";
	    $this->createUser($username, $login, $password, $email, $group);

	    echo "Removing $username from Registered group.\n";	    
	    $this->changeAssignedGroup($username,$group="Registered");      
	    	    
		$this->jClick('Global Configuration: Permissions');
        echo "Setting all roles to inherit for $username.\n";
		$this->select("//tr[2]/td[1]/select", "label=...");
		$this->select("//tr[5]/td[1]/select", "label=...");
		$this->select("//tr[2]/td[2]/select", "label=...");
		//Do not deny Super Users Global Admin permission
		$this->select("//tr[3]/td[4]/select", "label=...");
		$this->select("//tr[2]/td[5]/select", "label=...");
		$this->select("//tr[6]/td[5]/select", "label=...");
		$this->select("//tr[2]/td[6]/select", "label=...");
		$this->select("//tr[2]/td[7]/select", "label=...");
		$this->select("//tr[7]/td[7]/select", "label=...");
		$this->select("//tr[2]/td[8]/select", "label=...");
		$this->select("//tr[8]/td[8]/select", "label=...");
	    $this->click("link=Save");
		$this->waitForPageToLoad("30000");
		try	{
			$this->assertTrue($this->isTextPresent("Configuration successfully saved."));
		} catch (PHPUnit_Framework_AssertionFailedError $e){
			array_push($this->verificationErrors, $this->getTraceFiles($e));
		}		

		$action="Site Login";
		$group = 'Public';			    
		$permission="Allow";		
		$this->setGlobalPermission($action,$group,$permission);    

	    $this->gotoSite();
	    $this->doFrontEndLogin($login,$password); 
		try {
			$this->assertTrue($this->isElementPresent("//form[@id='form-login'][contains(., '$username')]"), 'Message not displayed or message changed, SeleniumJoomlaTestCase line 31');			
	    } catch (PHPUnit_Framework_AssertionFailedError $e){
			array_push($this->verificationErrors, $this->getTraceFiles($e));
	    }	    	
    	$this->doFrontEndLogout();
	    
    	$this->gotoAdmin();		
		$this->jClick('Global Configuration: Permissions');	    
		$permission="Deny";
		$this->setGlobalPermission($action,$group,$permission);

	    $this->gotoSite();
	    $this->doFrontEndLogin($login,$password);
		$this->checkMessage($message);
		
	    $this->gotoAdmin();	
		$this->jClick('Global Configuration: Permissions');	    
		$permission="...";		
		$this->setGlobalPermission($action,$group,$permission);

	    $this->gotoSite();	    
	    $this->doFrontEndLogin($login,$password); 	
		$this->checkMessage($message);	
		
    	$this->gotoAdmin();
        $group='Manager';
		$this->changeAssignedGroup($username,$group);
        
    	$this->gotoAdmin();
		$this->jClick('Global Configuration: Permissions');		    
		$permission="Allow";		
		$this->setGlobalPermission($action,$group,$permission);	    

	    $this->gotoSite();	    
	    $this->doFrontEndLogin($login,$password); 
		try {
			$this->assertTrue($this->isElementPresent("//form[@id='form-login'][contains(., '$username')]"));			
	    } catch (PHPUnit_Framework_AssertionFailedError $e){
			array_push($this->verificationErrors, $this->getTraceFiles($e));
	    }	    	
    	$this->doFrontEndLogout();          

    	$this->gotoAdmin();
		$this->jClick('Global Configuration: Permissions');	    
		$permission="Deny";		
		$this->setGlobalPermission($action,$group,$permission);

	    $this->gotoSite();
	   	$this->doFrontEndLogin($login,$password);
		$this->checkMessage($message);
		
	    $this->gotoAdmin();	
		$this->jClick('Global Configuration: Permissions');	    
		$permission="...";		
		$this->setGlobalPermission($action,$group,$permission);

	    $this->gotoSite();	    
	    $this->doFrontEndLogin($login,$password);
		$this->checkMessage($message);  	
		
    	$this->gotoAdmin();
        $group='Administrator';
		$this->changeAssignedGroup($username,$group);
        
    	$this->gotoAdmin();
		$this->jClick('Global Configuration: Permissions');		    
		$permission="Allow";		
		$this->setGlobalPermission($action,$group,$permission);	    

	    $this->gotoSite();	    
	    $this->doFrontEndLogin($login,$password); 		
		try {
			$this->assertTrue($this->isElementPresent("//form[@id='form-login'][contains(., '$username')]"));			
	    } catch (PHPUnit_Framework_AssertionFailedError $e){
			array_push($this->verificationErrors, $this->getTraceFiles($e));
	    }	    	
    	$this->doFrontEndLogout();        
        	    
    	$this->gotoAdmin();	    
		$this->jClick('Global Configuration: Permissions');		    
		$permission="Deny";		
		$this->setGlobalPermission($action,$group,$permission);

	    $this->gotoSite();
	    $this->doFrontEndLogin($login,$password); 	
		$this->checkMessage($message);
		    	
	    $this->gotoAdmin();	
		$this->jClick('Global Configuration: Permissions');	    
		$permission="...";		
		$this->setGlobalPermission($action,$group,$permission);

	    $this->gotoSite();	    
	    $this->doFrontEndLogin($login,$password); 		
		$this->checkMessage($message);					  	
		
		$this->gotoAdmin();				

        $group='Super Users';
		$this->changeAssignedGroup($username,$group);
        
		$this->jClick('Global Configuration: Permissions');		    
		$permission="Allow";		
		$this->setGlobalPermission($action,$group,$permission);

	    $this->gotoSite();	    
	    $this->doFrontEndLogin($login,$password); 
		try {
			$this->assertTrue($this->isElementPresent("//form[@id='form-login'][contains(., '$username')]"));			
	    } catch (PHPUnit_Framework_AssertionFailedError $e){
			array_push($this->verificationErrors, $this->getTraceFiles($e));
	    }	    	
    	$this->doFrontEndLogout();        
        
    	$this->gotoAdmin();       	
		$this->jClick('Global Configuration: Permissions');		    
		$permission="Deny";		
		$this->setGlobalPermission($action,$group,$permission);

	    $this->gotoSite();
	    echo "Logging in to front end.\n";
	    $this->doFrontEndLogin($login,$password); 
		try {
			$this->assertTrue($this->isElementPresent("//form[@id='form-login'][contains(., '$username')]"));			
	    } catch (PHPUnit_Framework_AssertionFailedError $e){
			array_push($this->verificationErrors, $this->getTraceFiles($e));
	    }
		$this->doFrontEndLogout();	    
		    	
	    $this->gotoAdmin();	
		$this->jClick('Global Configuration: Permissions');	    
		$permission="...";		
		$this->setGlobalPermission($action,$group,$permission);

	    $this->gotoSite();	    
	    $this->doFrontEndLogin($login,$password);
		try {
			$this->assertTrue($this->isElementPresent("//form[@id='form-login'][contains(., '$username')]"));			
	    } catch (PHPUnit_Framework_AssertionFailedError $e){
			array_push($this->verificationErrors, $this->getTraceFiles($e));
	    }	    	
    	$this->doFrontEndLogout();
    	
    	$this->gotoAdmin();       
	    $group='Super Users';	    
		$this->changeAssignedGroup($username,$group);

	    $group='Administrator';	    
		$this->changeAssignedGroup($username,$group);

	    $group='Manager';	    
		$this->changeAssignedGroup($username,$group);

	    $group='Public';	    
		$this->changeAssignedGroup($username,$group);        
	    
	    $group='Registered';
		$this->changeAssignedGroup($username,$group);
        
		$this->jClick('Global Configuration: Permissions');		    
		$permission="Allow";		
		$this->setGlobalPermission($action,$group,$permission);

	    $this->gotoSite();	    
	    $this->doFrontEndLogin($login,$password); 
		try {
			$this->assertTrue($this->isElementPresent("//form[@id='form-login'][contains(., '$username')]"));			
	    } catch (PHPUnit_Framework_AssertionFailedError $e){
			array_push($this->verificationErrors, $this->getTraceFiles($e));
	    }	    	
    	$this->doFrontEndLogout();        
        
    	$this->gotoAdmin();       	
		$this->jClick('Global Configuration: Permissions');		    
		$permission="Deny";		
		$this->setGlobalPermission($action,$group,$permission);

	    $this->gotoSite();	    
	    $this->doFrontEndLogin($login,$password); 		
		$this->checkMessage($message);	   
		    	
	    $this->gotoAdmin();	
		$this->jClick('Global Configuration: Permissions');	    
		$permission="...";		
		$this->setGlobalPermission($action,$group,$permission);

	    $this->gotoSite();	    
	    $this->doFrontEndLogin($login,$password); 		
		$this->checkMessage($message);	

		$this->gotoAdmin();				

        $group='Author';
		$this->changeAssignedGroup($username,$group);
        
		$this->jClick('Global Configuration: Permissions');		    
		$permission="Allow";		
		$this->setGlobalPermission($action,$group,$permission);

	    $this->gotoSite();	    
	    $this->doFrontEndLogin($login,$password); 
		try {
			$this->assertTrue($this->isElementPresent("//form[@id='form-login'][contains(., '$username')]"));			
	    } catch (PHPUnit_Framework_AssertionFailedError $e){
			array_push($this->verificationErrors, $this->getTraceFiles($e));
	    }	    	
    	$this->doFrontEndLogout();        
        
    	$this->gotoAdmin();       	
		$this->jClick('Global Configuration: Permissions');		    
		$permission="Deny";		
		$this->setGlobalPermission($action,$group,$permission);

	    $this->gotoSite();	    
	    $this->doFrontEndLogin($login,$password); 		
		$this->checkMessage($message);		    
		    	
	    $this->gotoAdmin();	
		$this->jClick('Global Configuration: Permissions');	    
		$permission="...";		
		$this->setGlobalPermission($action,$group,$permission);

	    $this->gotoSite();	    
	    $this->doFrontEndLogin($login,$password); 		
		$this->checkMessage($message);	

		$this->gotoAdmin();				

        $group='Editor';
		$this->changeAssignedGroup($username,$group);
        
		$this->jClick('Global Configuration: Permissions');		    
		$permission="Allow";		
		$this->setGlobalPermission($action,$group,$permission);

	    $this->gotoSite();	    
	    $this->doFrontEndLogin($login,$password); 
		try {
			$this->assertTrue($this->isElementPresent("//form[@id='form-login'][contains(., '$username')]"));			
	    } catch (PHPUnit_Framework_AssertionFailedError $e){
			array_push($this->verificationErrors, $this->getTraceFiles($e));
	    }	    	
    	$this->doFrontEndLogout();        
        
    	$this->gotoAdmin();       	
		$this->jClick('Global Configuration: Permissions');		    
		$permission="Deny";		
		$this->setGlobalPermission($action,$group,$permission);

	    $this->gotoSite();	    
	    $this->doFrontEndLogin($login,$password); 		
		$this->checkMessage($message);		    
		    	
	    $this->gotoAdmin();	
		$this->jClick('Global Configuration: Permissions');	    
		$permission="...";		
		$this->setGlobalPermission($action,$group,$permission);

	    $this->gotoSite();	    
	    $this->doFrontEndLogin($login,$password); 		
		$this->checkMessage($message);	

		$this->gotoAdmin();
        $group='Publisher';
		$this->changeAssignedGroup($username,$group);
        
		$this->jClick('Global Configuration: Permissions');		    
		$permission="Allow";		
		$this->setGlobalPermission($action,$group,$permission);

	    $this->gotoSite();	    
	    $this->doFrontEndLogin($login,$password); 
		try {
			$this->assertTrue($this->isElementPresent("//form[@id='form-login'][contains(., '$username')]"));			
	    } catch (PHPUnit_Framework_AssertionFailedError $e){
			array_push($this->verificationErrors, $this->getTraceFiles($e));
	    }	    	
    	$this->doFrontEndLogout();        
        
    	$this->gotoAdmin();       	
		$this->jClick('Global Configuration: Permissions');		    
		$permission="Deny";		
		$this->setGlobalPermission($action,$group,$permission);

	    $this->gotoSite();	    
	    $this->doFrontEndLogin($login,$password); 		
		$this->checkMessage($message);		    
		    	
	    $this->gotoAdmin();	
		$this->jClick('Global Configuration: Permissions');	    
		$permission="...";		
		$this->setGlobalPermission($action,$group,$permission);

	    $this->gotoSite();	    
	    $this->doFrontEndLogin($login,$password); 		
		$this->checkMessage($message);	

		$this->gotoAdmin();
        $group='Shop Suppliers';
		$this->changeAssignedGroup($username,$group);
        
		$this->jClick('Global Configuration: Permissions');		    
		$permission="Allow";		
		$this->setGlobalPermission($action,$group,$permission);

	    $this->gotoSite();	    
	    $this->doFrontEndLogin($login,$password); 
		try {
			$this->assertTrue($this->isElementPresent("//form[@id='form-login'][contains(., '$username')]"));			
	    } catch (PHPUnit_Framework_AssertionFailedError $e){
			array_push($this->verificationErrors, $this->getTraceFiles($e));
	    }	    	
    	$this->doFrontEndLogout();        
        
    	$this->gotoAdmin();       	
		$this->jClick('Global Configuration: Permissions');		    
		$permission="Deny";		
		$this->setGlobalPermission($action,$group,$permission);

	    $this->gotoSite();	    
	    $this->doFrontEndLogin($login,$password); 		
		$this->checkMessage($message);		    
		    	
	    $this->gotoAdmin();	
		$this->jClick('Global Configuration: Permissions');	    
		$permission="...";		
		$this->setGlobalPermission($action,$group,$permission);

	    $this->gotoSite();	    
	    $this->doFrontEndLogin($login,$password); 		
		$this->checkMessage($message);	

		$this->gotoAdmin();
        $group='Customer Group';
		$this->changeAssignedGroup($username,$group);
        
		$this->jClick('Global Configuration: Permissions');		    
		$permission="Allow";		
		$this->setGlobalPermission($action,$group,$permission);

	    $this->gotoSite();	    
	    $this->doFrontEndLogin($login,$password); 
		try {
			$this->assertTrue($this->isElementPresent("//form[@id='form-login'][contains(., '$username')]"));			
	    } catch (PHPUnit_Framework_AssertionFailedError $e){
			array_push($this->verificationErrors, $this->getTraceFiles($e));
	    }	    	
    	$this->doFrontEndLogout();        
        
    	$this->gotoAdmin();       	
		$this->jClick('Global Configuration: Permissions');		    
		$permission="Deny";		
		$this->setGlobalPermission($action,$group,$permission);

	    $this->gotoSite();	    
	    $this->doFrontEndLogin($login,$password); 		
		$this->checkMessage($message);	    
		    	
	    $this->gotoAdmin();	
		$this->jClick('Global Configuration: Permissions');	    
		$permission="...";		
		$this->setGlobalPermission($action,$group,$permission);

	    $this->gotoSite();	    
	    $this->doFrontEndLogin($login,$password); 		
		$this->checkMessage($message);	       	
						
    	$this->gotoAdmin();	    
	    $this->deleteTestUsers();
		$this->restoreDefaultGlobalPermissions();	    
	    $this->doAdminLogOut();	
  }
}
?>