<?php
/**
 *						AjaxCore 1.1.2
 *				http://ajaxcore.sourceforge.net/
 *
 *  AjaxCore is a PHP framework that aims the ease development of rich 
 *  AJAX applications, using Prototype's JavaScript standard library.
 *  
 *  Copyright 2007 Mauro Niewolski (niewolski@users.sourceforge.net)
 *
 *  Licensed under the Apache License, Version 2.0 (the "License");
 *  you may not use this file except in compliance with the License.
 *  You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 *  Unless required by applicable law or agreed to in writing, software
 *  distributed under the License is distributed on an "AS IS" BASIS,
 *  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *  See the License for the specific language governing permissions and
 *  limitations under the License.
 */

class AjaxCore
{
	//  compatible so far with PHP 4.4.3, tested with IE 6, Mozilla, Opera.
	var $currentfile;
	var $placeholder; 
	var $method="get"; // default method
	var $cache=false;
	var $updating;
	var $request;
	var $version="1.1.2";
	var $debug=false;
	var $lastbind;
	var $JSCode=array(); // for add specific behaviour
	
	/**
	* AjaxCore() 
	*
	* Class constructor.
	* @access protected
	* @param string $lastbind it's the name of the last JavaScript event triggered.
	*/
	function __construct($lastbind=false)
	{
		$this->lastbind=$lastbind;
		$this->parseCache();
		$this->lookForAction();
	}
	
	/**
	* setUpdating
	*
	* Sets an HTML code while the AJAX request is being made.
	* @access protected
	* @param string $code HTML code to show while making the request.
	*/
	function setUpdating($code)
	{
		$this->updating=$code;
	}

	/** 
	* setCurrentFile 
	*
	* Sets filename of the extended class that inherits of AjaxCore.
	* @access protected
	* @param string $file filename of the extended class.
	*/
	function setCurrentFile($file)
	{
		$this->currentfile=$file;
	}
	
	/**
	* setJSCode
	* 
	* Sets specific JavaScript code to execute before and after the AJAX request is made. 
	* @access public
	* @param string $id HTML object id
	* @param string $before JavaScript code to execute before the AJAX request is being made.
	* @param string $after JavaScript code to execute before the AJAX request is being made. 
	*/	
	function setJSCode($id,$before,$after)
	{
		$this->JSCode[$id]=array($before,$after);
	}
	
	/**
	* setDebug
	*
	* Set whether it should print JavaScript error when occurrs.
	* @access public
	* @param bool $debug boolean value.
	*/
	function setDebug($debug)
	{
		$this->debug=$debug;
	}
	
	/**
	* setPlaceHolder
	*
	* Sets the <Div> id that will be used as placeholder.
	* @access protected
	* @param string $placeholder <Div id=""> used to return Html results.
	*/
	function setPlaceHolder($placeHolder)
	{
		$this->placeholder=$placeHolder;
	}
	
	/**
	* setMethod
	*
	* Sets whether the method should be Get or Post.
	* @access protected
	* @param string $method Get or Post.
	*/
	function setMethod($method)
	{
		$this->method=$method;
	}
	
	/**
	* setCache
	*
	* Sets whether should use cache or not.
	* @access protected
	* @param bool $cache boolean value
	*/
	function setCache($cache)
	{
		$this->cache=$cache;
	}
	
	/**
	* parseCache
	*
	* Parses the current cache.
	* @access private
	*/
	function parseCache()
	{
		if($this->cache==false)
		{
			//header ("Cache-Control: no-cache, must-revalidate"); 
		}	
	}
	
	/**
	* getJSCode
	*
	* Returns string header JavaScript code for main placeHolder.
	* @access public
	*/
	function getJSCode()
	{
		$code=array();
		$code[]="<script>";
		$code[]="var lastbind='load';";
		$code[]="var timers=Array();";
		$code[]="var onload;";
		$code[]="function ".$this->placeholder."Response (originalRequest)";
		$code[]="{";
		$code[]="	try{";
		$code[]="		eval(originalRequest.responseText);";
		$code[]="	}";
		$code[]="	catch(e)";
		$code[]="	{";			
		if($this->debug)
			$code[]="alert(e.getMessage());"; 
		$code[]="	 \$('".$this->placeholder."').innerHTML = originalRequest.responseText;"; 
		$code[]="	}";
		$code[]="}";
		$code[]="</script>";	
		$appended="";
		foreach($code as $ech )
			$appended.=$ech;
		return $appended;
	}
	
	/**
	* bind
	*
	* Does the bind between an Html object and PHP function, request will be made when appropiate JavaScript event is triggered.
	* @access public
	* @param string $id Html ID object
	* @param string $event JavaScript event that will cause AJAX request ( onfocus onblur onmouseover onmouseout onmousedown onmouseup onsubmit onclick onload onchange onkeypress onkeydown onkeyup )
	* @param string $bindto PHP function that handles the AJAX request
	* @param string $params ID of the Html elements that needs to be send within the request, static values (not html elements ) should be sent as _XXX=YYY , whether XXX represents variable name, and YYY value.
	* @return string JavaScript code to handle the binding.
	*/
	function bind($id,$event,$bindto,$params="")
	{
		if(strlen($params)>0)
			$params.=",_AjaxCore=".$this->version;
		else
			$params.="_AjaxCore=".$this->version;
		if(!empty($id) && !empty($event) && !empty($bindto)) // check for empty
		{
			$code=array();
			$arrayparams=explode(",",$params);
			$code[]="<script type=\"text/javascript\">";
			$code[]="\$('$id').$event = function () {"; // object bind
			if(isset($this->JSCode[$id]))
				$code[]=$this->JSCode[$id][0]; // setJS before
			$code[]=" eval('var request= { ";
			if(!empty($params))
				foreach($arrayparams as $param)
					if(strpos($param,"=")!=0)
					{
						$const=explode("=",$param);
						$code[]=$const[0].": \'".$const[1]."\',";
					}
					else 
						$code[]="$param:\$F(\'$param\'),";
			$code[]="bind: \'$bindto\'";
			$code[]="  }');";
			if($this->lastbind)
				$code[]="lastbind = '$bindto';";
			$code[]=" var query = \$H(request);";
			if(!empty($this->updating))
				$code[]="\$('".$this->placeholder."').innerHTML = '".$this->updating."';";
			$code[]="AjaxCore(\"".$this->currentfile."\",\"".$this->method."\",query.toQueryString(),function(originalResponse){".$this->placeholder."Response(originalResponse);";
			if(isset($this->JSCode[$id]))
				$code[]=$this->JSCode[$id][1]; // setJS after
			$code[]="});";
			$code[]="};";
			$code[]="</script>";
			$appended="";
			foreach($code as $ech )
				$appended.=$ech;
			return $appended;
		}
	}

	/**
	* onLoad
	*
	* Does a request to a PHP function, request will be made when onLoad JavaScript event occurs.
	* @access public
	* @param string $bindto PHP function that handles the AJAX request
	* @param string $params ID of the Html elements that needs to be send within the request, static values (not html elements ) should be sent as _XXX=YYY , whether XXX represents variable name, and YYY value.
	* @param string $request type of request, bind, bindTimer, bindPeriodicalTimer
	* @param int $timerms timer expiration time in milliseconds (only for timer requests)
	* @return string JavaScript code to handle the binding.
	*/
	function onLoad($bindto,$params="",$request="bind",$timerms=300)
	{	
		if(strlen($params)>0)
			$params.=",_AjaxCore=".$this->version;
		else
			$params.="_AjaxCore=".$this->version;
			if(!empty($bindto)) // check for empty
			{
				$code=array();
				$arrayparams=explode(",",$params);
				$code[]="<script type=\"text/javascript\">";
				$code[]="onLoad = function () {"; // object bind
				if(isset($this->JSCode[$id]))
					$code[]=$this->JSCode['onLoad'][0]; // setJS before, there's no html ID, so we'll use onLoad name tag
				$code[]=" eval('var request= { ";
				if(!empty($params))
					foreach($arrayparams as $param)
						if(strpos($param,"=")!=0)
						{
							$const=explode("=",$param);
							$code[]=$const[0].": \'".$const[1]."\',";
						}
						else 
							$code[]="$param:\$F(\'$param\'),";
				$code[]="bind: \'$bindto\'";
				$code[]="  }');";
				if($this->lastbind)
					$code[]="lastbind = '$bindto';";
				$code[]=" var query = \$H(request);";
				if(!empty($this->updating))
					$code[]="\$('".$this->placeholder."').innerHTML = '".$this->updating."';";
				$code[]="AjaxCore(\"".$this->currentfile."\",\"".$this->method."\",query.toQueryString(),function(originalResponse){".$this->placeholder."Response(originalResponse);";
				if(isset($this->JSCode[$id]))
					$code[]=$this->JSCode['onLoad'][1]; // setJS before, there's no html ID, so we'll use onLoad name tag
				$code[]="});";
				if($request=="bindPeriodicalTimer")
					$code[]="timers['onLoad'].start();";
				$code[]="};";
				if($request=="bindTimer" || $request=="bindPeriodicalTimer")
				{
					$code[]="timers['onLoad']=new AjaxCoreTimer(onLoad,$timerms);";
				}
				$code[]="window.onload=onLoad";
				$code[]="</script>";
				$appended="";
				foreach($code as $ech )
					$appended.=$ech;
				return $appended;
			}
		}
	
	/**
	* bindTimer
	*
	* Does the bind between an Html object and PHP function, request will be made when onLoad JavaScript event occurs and timer expires.
	* @access public
	* @param string $id Html ID object
	* @param string $event JavaScript event that will cause AJAX request ( onfocus onblur onmouseover onmouseout onmousedown onmouseup onsubmit onclick onload onchange onkeypress onkeydown onkeyup )
	* @param string $bindto PHP function that handles the AJAX request
	* @param string $params ID of the Html elements that needs to be send within the request, static values (not html elements ) should be sent as _XXX=YYY , whether XXX represents variable name, and YYY value.
	* @param string $timername name of the timer
	* @param int $timerms expiration time in milliseconds 
	* @return string JavaScript code to handle the binding.
	*/
	function bindTimer($id,$event,$bindto,$timername,$timerms,$params="")
	{
	if(strlen($params)>0)
		$params.=",_AjaxCore=".$this->version;
	else
		$params.="_AjaxCore=".$this->version;
		if(!empty($id) && !empty($event) && !empty($bindto) && !empty($timername)&& !empty($timerms)) // para no agregar eventos que den error
		{
			$code=array();
			$arrayparams=explode(",",$params);
			$code[]="<script type=\"text/javascript\">";
			$code[]="timers['".$timername."Handle'] = function () {"; // object bind
			if(isset($this->JSCode[$id]))
				$code[]=$this->JSCode[$id][0]; // setJS before
			$code[]=" eval('var request= { ";
			if(!empty($params))
				foreach($arrayparams as $param)
					if(strpos($param,"=")!=0)
					{
						$const=explode("=",$param);
						$code[]=$const[0].": \'".$const[1]."\',";
					}
					else 
						$code[]="$param:\$F(\'$param\'),";
			$code[]="bind: \'$bindto\'";
			$code[]="  }');";
			if($this->lastbind)
				$code[]="lastbind = '$bindto';";
			$code[]=" var query = \$H(request);";
			if(!empty($this->updating))
				$code[]="\$('".$this->placeholder."').innerHTML = '".$this->updating."';";
			$code[]="AjaxCore(\"".$this->currentfile."\",\"".$this->method."\",query.toQueryString(),function(originalResponse){".$this->placeholder."Response(originalResponse);";
			if(isset($this->JSCode[$id]))
				$code[]=$this->JSCode[$id][1]; // setJS after
			$code[]="});";
			$code[]="};";
			$code[]="timers['$timername']=new AjaxCoreTimer(timers['".$timername."Handle'],$timerms);";
			$code[]="\$('$id').$event=function(){ ";
			$code[]=$this->startTimer($timername);
			$code[]="};";
			$code[]="</script>";
			$appended="";
			foreach($code as $ech )
				$appended.=$ech;
			return $appended;
		}		
		
	}

	/**
	* bindPeriodicalTimer
	*
	* Does the bind between an Html object and PHP function, request will be made when onLoad JavaScript event occurs and will keep repeating when timer expires.
	* @access public
	* @param string $id Html ID object
	* @param string $event JavaScript event that will cause AJAX request ( onfocus onblur onmouseover onmouseout onmousedown onmouseup onsubmit onclick onload onchange onkeypress onkeydown onkeyup )
	* @param string $bindto PHP function that handles the AJAX request
	* @param string $params ID of the Html elements that needs to be send within the request, static values (not html elements ) should be sent as _XXX=YYY , whether XXX represents variable name, and YYY value.
	* @param string $timername name of the timer
	* @param int $timerms expiration time in milliseconds 
	* @return string JavaScript code to handle the binding.
	*/
	function bindPeriodicalTimer($id,$event,$bindto,$timername,$timerms,$params="")
	{
		if(strlen($params)>0)
			$params.=",_AjaxCore=".$this->version;
		else
			$params.="_AjaxCore=".$this->version;
		$code=array();
		$arrayparams=explode(",",$params);
		$code[]="<script type=\"text/javascript\">";
		$code[]="timers['".$timername."Handle'] = function () {"; // object bind
		if(isset($this->JSCode[$id]))
			$code[]=$this->JSCode[$id][0]; // setJS before
		$code[]=" eval('var request= { ";
		if(!empty($params))
			foreach($arrayparams as $param)
				if(strpos($param,"=")!=0)
				{
					$const=explode("=",$param);
					$code[]=$const[0].": \'".$const[1]."\',";
				}
				else 
					$code[]="$param:\$F(\'$param\'),";
		$code[]="bind: \'$bindto\'";
		$code[]="  }');";
		if($this->lastbind)
			$code[]="lastbind = '$bindto';";
		$code[]=" var query = \$H(request);";
		if(!empty($this->updating))
			$code[]="\$('".$this->placeholder."').innerHTML = '".$this->updating."';";
		$code[]="AjaxCore(\"".$this->currentfile."\",\"".$this->method."\",query.toQueryString(),function(originalResponse){".$this->placeholder."Response(originalResponse);";
		if(isset($this->JSCode[$id]))
			$code[]=$this->JSCode[$id][1]; // setJS after
		$code[]="});";
		$code[]=$this->startTimer($timername);
		$code[]="};";
		$code[]="timers['$timername']=new AjaxCoreTimer(timers['".$timername."Handle'],$timerms);";
		$code[]="\$('$id').$event=function(){ ";
		$code[]=$this->startTimer($timername);
		$code[]="};";
		$code[]="</script>";
		$appended="";
		foreach($code as $ech )
			$appended.=$ech;
		return $appended;
	}		

	/**
	* lookForAction
	*
	* Determines what PHP function should be called upon each AJAX request
	* @access private
	*/
	function lookForAction()
	{
		$this->getRequest();
		if(!empty($this->request['bind']) && method_exists($this,$this->request['bind']))
		{
			$method=$this->request['bind'];
			$this->initialize();
			$this->$method();
		}
	}
	
	/**
	* intialize
	*
	* Method that is called just before any PHP function, useful to initialize databases and so on.
	* @access protected
	*/
	function initialize() {} // abstract
	
	/**
	* getRequest
	*
	* Returns Get or Post array.
	* @access protected
	* @return array request
	*/
	function getRequest()
	{
		if($this->method=="get")
			$this->request=&$_GET;
		else
			$this->request=&$_POST;	
	}
	
	
	/**
	* phpArrayToJS
	*
	* Converts an array from php to JavaScript.
	* @access public
	* @param array $array php array
	* @return string JavaScript array
	*/
	 function phpArrayToJS($array)
    {
        $items = array();
        foreach ($array as $key => $value) 
        {
            if (is_array($value))
                $items[]=$this->phpArrayToJS($value);
			else if (is_int($value))
                $items[]=$value;
			else
				$items[]="'".$this->escapeJS($value)."'";
        }
        return '[' . implode(',', $items) . ']';
    }

    /**
     * escapeJS (borrowed from Smarty)
     *
     * Escape the string to JavaScript
     * @access protected
     * @param string $string String unscaped
     * @return string escaped string
	 * @link http://smarty.php.net/manual/en/language.modifier.escape.php  escape (Smarty online manual)
	 * @author Monte Ohrt <monte at ohrt dot com>
     */
    function escapeJS($string)
    {
	 // escape quotes and backslashes, newlines, etc.
        return strtr($string, array('\\'=>'\\\\',"'"=>"\\'",'"'=>'\\"',"\r"=>'\\r',"\n"=>'\\n','</'=>'<\/'));
    }
	
	/**
	* alert
	*
	* Return JavaScript Alert Message
	* @access private
	* @param string $message message to alert
	*/
	function alert($message,$die=true)
	{	
		$message=$this->escapeJS($message);
		$alert="alert('$message');";
		if($die)
			die($alert);
		else return $alert;
	}
	
	/**
	*  arrayToString
	*
	* Returns a sentence form an array
	* @access protected
	* @param array $array of sentences
	* @return string string with sentences
	*/
	function arrayToString($array)
	{
		$app="";
		foreach ($array as $arr)
			$app.=$arr;
		return $app;
	}
	
	/**
	* startTimer
	*
	* Restarts a timer
	* @access public
	* @param string id is the timer id
	*/
	function startTimer($id)
	{
		 return "timers['$id'].start();";
	}
	
	/**
	* stopTimer
	*
	* Stops a timer
	* @access public
	* @param string id is the timer id
	*/
	function stopTimer($id)
	{
		 return "timers['$id'].reset();";
	}
	
	/**
	* htmlDisable
	*
	* Disables an html element
	* @access protected
	* @param string $element is the ID of the element
	* @return string sentence to disable element
	*/
	function htmlDisable($element)
	{
		return "\$('".$element."').disabled=true;";
	}
	
	/**
	* htmlEnable
	*
	* Enables an html element
	* @access protected
	* @param string $element is the ID of the element
	* @return string sentence to disable element
	*/
	function htmlEnable($element)
	{
		return "\$('".$element."').disabled=false;";
	}	
	
	/**
	* htmlInner
	*
	* Enables an html element
	* @access protected
	* @param string $element is the ID of the element
	* @param string $value is the text to put in
	* @return string sentence to disable element
	*/
	function htmlInner($element,$value)
	{
		return "\$('".$element."').innerHTML = '".$this->escapeJS($value)."';";
	}		
}
?>